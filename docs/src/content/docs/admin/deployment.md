---
title: Deploying to production
description: Run Secret Republic V3 on the internet. Secrets, HTTPS behind a reverse proxy with APP_URL, backups for MySQL and SQLite, upgrades, logs and release archives.
---

## Checklist

- [ ] Strong `DB_PASS` and `DB_ROOT_PASSWORD` in `.env`
- [ ] Your own `CRON_KEY`, and the [cron jobs](../../getting-started/cron-jobs/) scheduled
- [ ] HTTPS in front of the app, with `APP_URL` set to the public URL
- [ ] `APP_TIMEZONE` set to your community's timezone
- [ ] SMTP configured if you want account emails
- [ ] `contact_email` and other details in `includes/constants/constants.php`
- [ ] Database backups scheduled

## HTTPS with a reverse proxy

The container serves plain HTTP on port 80. Put a reverse proxy in front of it and tell the game its public
address, because it builds every link and asset URL from it:

```bash
# .env
APP_URL=https://game.example.com/
APP_PORT=127.0.0.1:8080
```

A [Caddy](https://caddyserver.com) `Caddyfile` that gets and renews certificates automatically:

```text
game.example.com {
    reverse_proxy localhost:8080
}
```

## Backups

**MySQL (Compose):**

```bash
docker compose exec -T db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction "$MYSQL_DATABASE"' \
  > backup-$(date +%F).sql
```

Restore with `docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < backup.sql`.

**SQLite:** copy the database file (e.g. `includes/data/game.sqlite`) while the game is quiet, or use
`sqlite3 game.sqlite ".backup backup.sqlite"`.

## Upgrading

```bash
git pull
docker compose up -d --build --wait
```

The database lives in the `db_data` volume and survives rebuilds. The game's configuration is re-created
from the environment on every start, so a rebuilt container never shows the setup wizard again.

## Logs

| Log | Command |
| --- | ------- |
| Nginx + PHP-FPM | `docker compose logs -f app` |
| MySQL | `docker compose logs -f db` |
| Cron runs | `/admin/view/crons` |

## Without Docker

Every [GitHub release](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/releases)
has `secretrepublic-v3-<version>.zip` and `.tar.gz` archives with every PHP library included. Follow the
[manual install](../../getting-started/manual-install/) guide.

## Publishing a release (maintainers)

Releases use date-based tags (`vYYYY.MM`, plus `.N` for a second release in the same month). Tag
`master` and push the tag. GitHub Actions tests the code, builds the archives, publishes the release as
_Latest_ and pushes a Docker image with the same tag.

```bash
git checkout master
git pull
git tag -a v2026.11 -m "Secret Republic V3 v2026.11"
git push origin v2026.11
```

Tags with a suffix, such as `v2026.12-rc1`, become pre-releases. To build the archives locally, run
`composer install --no-dev --optimize-autoloader`, then `scripts/build-release.sh v2026.11`.
