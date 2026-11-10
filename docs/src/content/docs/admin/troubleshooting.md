---
title: Troubleshooting
description: Fixes for common Secret Republic V3 problems. Setup errors, missing CSS or images, 404s on every page, admin access, email, cron and SQLite issues.
---

## Styles and images don't load / links point to the wrong address

The game builds every URL from the address the browser used. Behind a reverse proxy, a non-standard port
or HTTPS termination, set `APP_URL` to the public address, e.g. `APP_URL=https://game.example.com/`.

## Every page except the home page is a 404

URL rewriting isn't active. On Apache, enable `mod_rewrite` and `AllowOverride All` for `public_html`. On
Nginx, use the server block in `docker/nginx.conf`.

## Setup says it can't connect to MySQL

- In Docker Compose the host is `db`, not `localhost`.
- Check the user, password and database name, and that the user has rights on that database.

## "That database already contains a Secret Republic game with players"

Setup never overwrites a game that has players. Use a new, empty database, or restore your existing
`includes/database_info.php`.

## Setup page shows up again

The game shows setup whenever `includes/database_info.php` is missing or the database has no game tables.
Check that `includes/` is writable and that the file exists, or (Docker) that the `DB_*` variables are set.

## I can't open the admin panel

Your account needs `group_id = 1` in `user_credentials` (see [Admin panel](../admin-panel/)). Log out and in
again after changing it.

## Password change or reset didn't work

Earlier versions saved changed and reset passwords in a format the login didn't accept. Current versions
accept those passwords and upgrade them on the next login, so simply log in again.

## Emails are never sent

Email is off until `SMTP_HOST` is set. See [Configuration](../../getting-started/configuration/#email).

## Rankings, resources or the hackdown never update

Schedule the [cron jobs](../../getting-started/cron-jobs/) and make sure the URLs use your `CRON_KEY`. A wrong
key returns a 404.

## SQLite: "unable to open database file"

The web server needs write access to the folder containing the SQLite file, not just the file. In Docker,
mount a volume at `/var/www/app/includes/data` and set `SQLITE_PATH=includes/data/game.sqlite`.

## Where are the errors?

`docker compose logs -f app` shows PHP errors. Outside Docker, check your PHP-FPM or Apache error log.
