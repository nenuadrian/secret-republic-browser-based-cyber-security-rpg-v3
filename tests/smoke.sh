#!/usr/bin/env bash
# End-to-end smoke test against a freshly started Docker stack, where the
# database comes from DB_* environment variables (docker compose up, or the
# image run with DB_DRIVER=sqlite).
#
#   tests/smoke.sh [base-url]        (default http://localhost:8080)
#
# Runs the admin-only setup, logs in, checks game and admin pages, registers a
# player, changes their password, and calls the cron endpoints.
set -euo pipefail

BASE="${1:-http://localhost:8080}"
CRON_KEY="${CRON_KEY:-MDMwN2Q3OGRiYmM4Y2RkOWZjNTBmMzA4MzViZDZiNjQ=}"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT
BODY="$WORK/body.html"

ADMIN_USER="smokeadmin"
ADMIN_PASS="$(od -An -N8 -tx1 /dev/urandom | tr -d ' \n')"
PLAYER_USER="smokeplayer"
PLAYER_PASS="play1234"

fail() { echo "FAIL: $*" >&2; exit 1; }

# req <cookie-jar> <curl args...>  -> prints "<status> <redirect-url>"
req() { local jar="$WORK/$1"; shift; curl -s -o "$BODY" -c "$jar" -b "$jar" -w '%{http_code} %{redirect_url}\n' "$@"; }
logged_in() { grep -q 'logout' "$BODY"; }

echo "1. a fresh install shows the admin-only setup"
read -r code _ < <(req admin "$BASE/")
[[ "$code" == 200 ]] && grep -q 'DATABASE CONNECTION FOUND' "$BODY" || fail "setup page: $code"

echo "2. asset URLs keep the host and port, and load"
css="$(grep -o 'href="[^"]*/layout/css/custom.css[^"]*"' "$BODY" | head -1 | sed 's/^href="//; s/"$//')"
[[ "$css" == "$BASE/"* ]] || fail "asset URL '$css' does not start with $BASE"
read -r code _ < <(req admin "$css")
[[ "$code" == 200 ]] || fail "custom.css returned $code"

echo "3. setup rejects an invalid admin"
read -r code _ < <(req admin -d ADMIN_USER=ab -d ADMIN_PASS=x -d ADMIN_EMAIL=nope "$BASE/")
[[ "$code" == 200 ]] && grep -q 'alert-error' "$BODY" || fail "invalid admin was not rejected ($code)"

echo "4. setup installs the game"
read -r code _ < <(req admin --data-urlencode "ADMIN_USER=$ADMIN_USER" --data-urlencode "ADMIN_PASS=$ADMIN_PASS" -d ADMIN_EMAIL=admin@example.test "$BASE/")
[[ "$code" == 302 ]] || { grep -A3 'alert-error' "$BODY" | head; fail "setup POST returned $code"; }

echo "5. setup is locked afterwards"
read -r code _ < <(req guest "$BASE/setup")
[[ "$code" == 302 ]] || fail "/setup still reachable: $code"
read -r code _ < <(req guest "$BASE/")
grep -q 'DATABASE CONNECTION FOUND' "$BODY" && fail "home page still shows setup"

echo "6. admin logs in"
read -r code _ < <(req admin --data-urlencode "username=$ADMIN_USER" --data-urlencode "password=$ADMIN_PASS" -d process=1 "$BASE/")
[[ "$code" == 302 ]] || fail "admin login returned $code"

echo "7. game and admin pages respond"
for path in "" skills abilities servers grid rankings organization forum blogs conversations rewards dna \
            shop train achievements profile notes friends bank zones storage attacks hackdown job \
            alpha_coins data-points theWorld simulator search support frequently-asked-questions \
            admin admin/view/manageQuest admin/view/hacker admin/view/registered admin/view/groups \
            admin/view/achievements admin/view/crons; do
	read -r code _ < <(req admin "$BASE/$path")
	[[ "$code" == 200 ]] && logged_in || fail "/$path returned $code (or not logged in)"
	printf '   %-30s %s\n' "/$path" "$code"
done

echo "8. a new player can register and is logged in"
read -r code _ < <(req player -d "username=$PLAYER_USER" -d "password=$PLAYER_PASS" -d email=player@example.com -d zone=2 -d terms=1 -d process=1 "$BASE/register")
[[ "$code" == 302 ]] || { grep -A3 'alert-error' "$BODY" | head; fail "registration returned $code"; }
read -r code _ < <(req player "$BASE/")
logged_in || fail "player is not logged in after registering"

echo "9. password change in DNA, then log in with the new password"
read -r code _ < <(req player -d old="$PLAYER_PASS" -d new=newpass99 -d change=1 "$BASE/dna")
grep -q 'ACCESS DENIED' "$BODY" && fail "DNA rejected the correct old password"
req player "$BASE/logout" >/dev/null
read -r code _ < <(req player2 -d "username=$PLAYER_USER" -d password=newpass99 -d process=1 "$BASE/")
read -r code _ < <(req player2 "$BASE/")
logged_in || fail "could not log in with the changed password"

echo "10. cron endpoints"
for job in rankings resources attacks hourly daily; do
	read -r code _ < <(req guest "$BASE/cron/key1/$CRON_KEY/$job/true")
	[[ "$code" == 200 ]] || fail "cron $job returned $code"
done
read -r code _ < <(req guest "$BASE/cron/key1/wrong-key/rankings/true")
[[ "$code" == 404 ]] || fail "cron accepted or mishandled a wrong key: $code"

echo "OK - smoke test passed"
