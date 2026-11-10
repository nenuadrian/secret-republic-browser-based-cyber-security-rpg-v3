---
title: Testing & CI
description: Lint, PHPUnit tests, the Docker smoke test for MySQL and SQLite, and the GitHub Actions workflows that test, release and publish Secret Republic V3 and its docs.
---

## Local checks

```bash
composer install
composer lint     # php -l on every app file; fails on syntax errors AND deprecations
composer test     # PHPUnit
```

`./test.sh` runs all three. Run tests **from the repository root**: test files require app files with
relative paths. There is no autoloader in tests; each test `require_once`s the classes it needs.

## End-to-end smoke test

`tests/smoke.sh` drives a freshly started container:

1. the admin-only setup appears and asset URLs keep the right host and port
2. setup rejects invalid input, then installs the game and locks itself
3. the admin logs in, and 38 game and admin pages load
4. a new player registers and is logged in
5. the player changes their password in DNA and logs in with the new one
6. every cron job runs, and a wrong cron key gets a 404

```bash
# MySQL
docker compose down -v && docker compose up -d --build --wait
tests/smoke.sh http://localhost:8080

# SQLite
docker run -d --name sr-sqlite -p 8081:80 -e DB_DRIVER=sqlite \
  ghcr.io/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3:latest
tests/smoke.sh http://localhost:8081
```

## GitHub Actions

| Workflow | Trigger | What it does |
| -------- | ------- | ------------ |
| `ci.yml` | push / PR to `master` | Lint, PHPUnit and `composer audit` on PHP 8.5; Docker smoke tests on MySQL and SQLite |
| `docker.yml` | push to `master`, `v*` tags | Multi-arch (amd64 + arm64) image to `ghcr.io` |
| `release.yml` | `v*` tags | Tests, then standalone `.zip` / `.tar.gz` with all libraries and checksums, published as the Latest release (pre-release for `-rc` style tags) |
| `docs.yml` | changes under `docs/` or `screens/` | Builds this site and deploys it to GitHub Pages |

## Documentation site

The docs are an [Astro](https://astro.build) + [Starlight](https://starlight.astro.build) project in
`docs/`. Pages are Markdown/MDX in `docs/src/content/docs/`.

```bash
cd docs
npm install
npm run dev     # http://localhost:4321/secret-republic-browser-based-cyber-security-rpg-v3/
npm run build   # static site in docs/dist with sitemap and search index
```

Give every page a `title` and a descriptive `description`. They become the search-engine title, meta
description and social preview.
