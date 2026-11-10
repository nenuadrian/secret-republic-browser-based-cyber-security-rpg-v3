#!/bin/sh
# Secret Republic V3 container entrypoint.
#
# With DB_HOST (MySQL) or DB_DRIVER=sqlite set, the database connection is
# taken from the environment, so /setup only asks for the admin account and a
# rebuilt container never sends visitors back to the full setup wizard.
set -e

APP_DIR=/var/www/app
DB_CONFIG="$APP_DIR/includes/database_info.php"

if [ ! -f "$DB_CONFIG" ] && { [ -n "${DB_HOST:-}" ] || [ "${DB_DRIVER:-}" = "sqlite" ]; }; then
	cp "$APP_DIR/docker/database_info.php" "$DB_CONFIG"
	chown www-data:www-data "$DB_CONFIG"
fi

exec "$@"
