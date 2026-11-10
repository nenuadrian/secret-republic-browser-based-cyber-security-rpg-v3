---
title: Architecture
description: How the Secret Republic V3 codebase is organized. Vanilla PHP with a DI container, module-based routing, Smarty 4 templates, MysqliDb with a SQLite adapter, and the Docker setup.
---

Secret Republic V3 is vanilla PHP: no framework and no namespaces. It uses **Smarty 4** for templates,
**MysqliDb** for MySQL, and its own **SQLite adapter** that mirrors the MysqliDb API.

## Directory layout

```text
public_html/            web root: index.php (composition root), .htaccess, layout/ assets, images, audio
includes/
├─ class/               core classes: Container, Cardinal, Alpha, LoginSystem, UserClass, Tasks,
│                       Installer, SqliteDb, SqliteSchemaConverter, quests/ (mission console), ...
├─ modules/             one file or folder per route (see Routing)
├─ constants/           game balance: constants.php, skills.php, abilities.php, tutorial.php
├─ install/DB.sql       MySQL schema + starting content (converted on the fly for SQLite)
├─ header.php           per-request setup shared by every module
├─ functions.php        helpers (add_alert, GET, configs, ...)
├─ database_info.php    generated DB config (git-ignored)
├─ local_settings.php   optional machine-local overrides (git-ignored)
└─ vendor/              Composer libraries (git-ignored)
templates/              Smarty templates, grouped by module
docker/                 entrypoint, env-driven DB config, nginx, supervisord, php.ini
tests/                  PHPUnit tests and the end-to-end smoke test
scripts/                release packaging
docs/                   this documentation site (Astro + Starlight)
MISSION-GUIDES/         original mission designer and console PDFs
```

## Request flow

1. Nginx or Apache sends every non-file request to `public_html/index.php`.
2. `index.php` is the **composition root**. It builds a DI `Container` (`includes/class/Container.php`) and
   registers `smarty`, `config`, `db`, the lazy `uclass` / `taskclass` factories and `user`.
3. If `includes/database_info.php` is missing, or the database has no game tables, the request is routed
   to **setup**. Otherwise `Cardinal` connects to the database and loads `constants.php` as `config`.
4. `includes/header.php` starts the login system and exposes container state as the locals every module
   uses: `$GET, $tVars, $db, $config, $user, $uclass, $taskclass, $errors, $success, $info, $warnings,
   $messenger, $myModals, $voice, $pages, $url, $logged`.
5. The module runs and sets `$tVars['display'] = 'folder/template.tpl'`. `index.php` renders it with the
   footer.

## Routing

The **first URL segment** selects a module in `includes/modules/`:

- `/skills` → `includes/modules/skills.php`
- `/grid` → `includes/modules/grid/grid.php` (folder routes use `<folder>/<folder>.php`)
- unknown → `main/main.php`

The **remaining segments are key/value pairs** in `$GET`: `/forum/fid/7` gives `$GET['fid'] = '7'`. Query
string variables are merged in too.

Helpers: `$cardinal->show_404()` renders the 404 page (with a 404 status), and `$cardinal->redirect($url)`
redirects while keeping queued messages.

## Database layer

- **MySQL**: `Mysqlidb` (joshcam/mysqli-database-class). The connection disables `ONLY_FULL_GROUP_BY` for
  the game's queries.
- **SQLite**: `SqliteDb` is a PDO adapter with the same API (`where`, `orWhere`, `join`, `get`, `getOne`,
  `insert`, `update`, `delete`, `rawQuery`, …). It translates `RAND()` to `RANDOM()` and rewrites
  `UPDATE/DELETE … LIMIT`. `SqliteSchemaConverter` turns `DB.sql` into SQLite DDL at setup time.
- **Installer** (`includes/class/Installer.php`) holds the install checks, the schema import for both
  drivers, admin creation and config file generation used by `/setup`.

## Docker image

`php:8.5-fpm` with Nginx, managed by supervisord. The entrypoint writes `includes/database_info.php` from
`docker/database_info.php` when `DB_HOST` or `DB_DRIVER=sqlite` is set, so configuration lives in the
environment and survives rebuilds. PHP-FPM runs with `clear_env = no` so the game can read `DB_*`,
`SMTP_*`, `CRON_KEY`, `APP_URL` and the other variables.
