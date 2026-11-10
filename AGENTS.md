# AGENTS.md

Vanilla PHP ("~MVC") browser-based cyber security RPG: no framework, no namespaces, Smarty 4
templates, MysqliDb (`Mysqlidb`) on MySQL with a SQLite shim (`SqliteDb` / `SqliteSchemaConverter`).
Fastest way to experiment: Docker (`docker compose up -d --build --wait`, app on :8080, MySQL 9.7), or
the standalone image with SQLite: `docker run -p 8080:80 -e DB_DRIVER=sqlite <image>`.
User/admin docs: https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/

## Commands (run from repo root)

- Install deps: `composer install`
  - `smarty/smarty` is pinned `^4.5.7` (patched line — 4.5.3 was blocked by Composer
    security advisories, see GHSA-cq55-c7wv-pxmq / CVE-2026-62992).
  - Vendor lives in `includes/vendor` (custom `vendor-dir`), not `vendor/`.
  - `composer.lock` is gitignored; installs resolve latest matching versions.
- Tests: `composer test` (runs `phpunit tests`). Must run from root — test files
  `require` app files with CWD-relative paths (`./includes/...`, `../includes/...`);
  running phpunit from elsewhere breaks them. Currently 138 tests pass.
- Single test file: `./includes/vendor/bin/phpunit tests/SqliteDbTest.php`
- Lint: `composer lint` (`php -l` on every app file; also fails on PHP deprecations).
- `./test.sh` runs install + lint + tests.
- End-to-end: `tests/smoke.sh [base-url]` against a fresh container (admin-only setup, login, 38 pages,
  registration, password change, cron). CI runs it for MySQL (compose) and SQLite.
- There is no `phpunit.xml`; tests `require_once` the classes they need explicitly
  (no autoloader). Do the same in new tests.

## How the app is wired (read before touching code)

- `public_html/index.php` is the composition root. It builds a DI `Container`
  (`includes/class/Container.php`), registers services (`smarty`, `config`, `db`, lazy
  `uclass`/`taskclass` factories, `user`), then `require`s `includes/header.php` and the module.
- `includes/header.php` aliases container state to plain locals every module uses:
  `$GET, $tVars, $db, $config, $user, $uclass, $taskclass, $errors, $success, $info,
  $warnings, $messenger, $myModals, $voice, $pages, $url, $logged` (mutable ones by reference).
- Base class `Alpha` (`includes/class/alpha.class.php`) and `Cardinal`
  (`includes/class/cardinal.php`, defines `ABSPATH`, loads DB config + `config` service + `URL`)
  read/write the container; helper functions in `includes/functions.php`
  (`add_alert()`, `GET()`, `configs()`, `there_are_errors()`, ...) use `global $container`.
  Centralize new shared state in the container, not new globals.
- Modules (URL targets) live in `includes/modules/`. Route = first URL segment;
  folder routes resolve to `includes/modules/<folder>/<folder>.php` (e.g. `/grid`).
  Missing module → `main/main`; if `includes/database_info.php` is absent → forced `setup`.
  Trailing URL segments are key/value pairs stored in `$GET` (e.g. `/hello/world/x` →
  `$GET['world'] = 'x'`).
- A module renders by setting `$tVars['display'] = 'pages/foo.tpl'`;
  call `$cardinal->show_404()` for missing/forbidden input. `$cardinal->redirect()` for redirects.

## Game content & balance

- `includes/constants/` drives game balance:
  - `abilities.php` (`abilities()` function), `skills.php` (global `$theskills`),
    `tutorial.php` (global `$tutorialSteps`), `constants.php` (config array → `config` service).
- Test coupling: `tests/SkillsTest.php` asserts exactly 16 skills; `tests/TutorialTest.php`
  asserts exactly 18 tutorial steps AND that every step N has a `tutorial_step_N_check()`
  function defined in `tutorial.php`. Changing a constant that feeds these breaks tests.
- New static page without code: drop `NAME.tpl` in `templates/pages/` → served at
  `/pages/NAME` (name must be alphanumeric plus `-`/`_`).
- Missions content/instructions: see `MISSION-GUIDES/` (PDFs).

## Database

- `includes/database_info.php` (gitignored) holds the DB config. `/setup` writes it (via
  `Installer::buildConfigFile`); the Docker entrypoint copies `docker/database_info.php` (reads
  `DB_*`/`SQLITE_PATH` env vars) when `DB_HOST` or `DB_DRIVER=sqlite` is set.
- Installed = config exists AND the `users` table exists (`Installer::isInstalledCached`, marker in
  `includes/cache/.installed`). Config present but no tables → admin-only setup (`SR_SETUP_MODE`;
  `header.php` skips DB-dependent work). Setup refuses databases that already have players and only
  drops the game's own tables when retrying.
- MySQL schema: `includes/install/DB.sql` (99 tables; tests count them). It must also convert to
  SQLite via `SqliteSchemaConverter` — avoid `--` comment lines directly before statements.
- The setup admin gets `user_credentials.group_id = 1` (full admin). Promote others the same way.
- MySQL connections disable `ONLY_FULL_GROUP_BY` per session (`Cardinal` constructor).

## Background processes & docs

- Cron endpoints (`includes/modules/cron/cron.php`): `/cron/key1/<CRON_KEY>/<job>/true` with job in
  `attacks|hourly|daily|hackdown|hackdownEnd|monthly|rankings|resources`. The key is
  `$config['cron_key']` (env `CRON_KEY`, falls back to the old public default). Schedules are in the docs.
- Config env overrides (`includes/constants/constants.php`): `APP_URL` (base URL; otherwise built from
  `HTTP_HOST`), `APP_TIMEZONE`, `CRON_KEY`, `SMTP_*`, `RECAPTCHA_*`. `includes/local_settings.php` can
  `putenv()` them.
- Docs: Astro + Starlight site in `docs/` (hand-written pages in `docs/src/content/docs/`, deployed by
  `.github/workflows/docs.yml`). `cd docs && npm install && npm run build`.
- CI (`ci.yml`) runs PHP 8.5 only; Docker uses `php:8.5-fpm-trixie` + Nginx + supervisord. PHPUnit 12.
- Releases: push a `vYYYY.MM` tag → `release.yml` builds standalone archives via
  `scripts/build-release.sh`; `docker.yml` publishes the image with the same tag.