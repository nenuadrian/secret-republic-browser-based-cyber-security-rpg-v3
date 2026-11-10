---
title: Quick start with Docker
description: Run Secret Republic V3 with Docker. A MySQL 9.7 stack with Docker Compose, or a single PHP 8.5 container with SQLite, and an admin account created in the browser.
---

There are two Docker options:

- **Docker Compose**: the game plus MySQL 9.7 LTS. Recommended.
- **Single container with SQLite**: no database server at all. Good for trying the game or small servers.

In both cases the database connection comes from environment variables, so the setup page only asks you
to create the admin account.

## Docker Compose (MySQL)

1. Clone the repository:

   ```bash
   git clone https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3.git
   cd secret-republic-browser-based-cyber-security-rpg-v3
   ```

2. _(Recommended)_ Create a `.env` with your own passwords and a `CRON_KEY`. See [Configuration](../configuration/):

   ```bash
   cp .env.example .env
   ```

3. Start the stack. `--wait` returns once both containers are healthy:

   ```bash
   docker compose up -d --build --wait
   ```

4. Open **http://localhost:8080** and create the admin account: a username of 4–15 letters or digits, a
   password of 4–20 characters, and an email address.
5. Log in with that account on the home page. The admin panel is at `/admin`.

| Service | Image | Purpose |
| ------- | ----- | ------- |
| `app` | built from `Dockerfile` (`php:8.5-fpm` + Nginx) | The game on port `8080` (`APP_PORT`) |
| `db` | `mysql:9.7` | Game database, stored in the `db_data` volume. Not published to the host. |

```bash
docker compose logs -f app           # Nginx + PHP-FPM logs
docker compose down                  # stop (data is kept)
docker compose down -v               # stop and DELETE the database
docker compose up -d --build --wait  # rebuild after pulling new code
```

## Single container (SQLite)

```bash
docker run -d --name secretrepublic -p 8080:80 \
  -e DB_DRIVER=sqlite -e SQLITE_PATH=includes/data/game.sqlite \
  -v secretrepublic-data:/var/www/app/includes/data \
  ghcr.io/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3:latest
```

The volume keeps the SQLite file when the container is replaced. Leave out `-v` and the two `-e` options for
a throwaway demo; the setup page then lets you pick MySQL or SQLite yourself.

To build the image locally instead of pulling it: `docker build -t secret-republic-v3 .`

## Pre-built images

Every push to `master` and every release tag publishes a multi-arch image (amd64 and arm64):

```bash
docker pull ghcr.io/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3:latest
```

## Next steps

- Schedule the [cron jobs](../cron-jobs/). Resources, attacks and rankings depend on them.
- Read [Deploying to production](../../admin/deployment/) before going public.
