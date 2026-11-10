# Secret Republic V3: Browser-Based Cyber Security RPG

[![CI](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/actions/workflows/ci.yml/badge.svg)](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/actions/workflows/ci.yml)
[![Docker image](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/actions/workflows/docker.yml/badge.svg)](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/pkgs/container/secret-republic-browser-based-cyber-security-rpg-v3)
[![Docs](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/actions/workflows/docs.yml/badge.svg)](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/)
[![Latest release](https://img.shields.io/github/v/release/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3?include_prereleases&sort=semver)](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/releases/latest)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

![Secret Republic](screens/cover.jpg)

**Secret Republic V3** is an open source, post-apocalyptic **cyber security RPG** for the browser. Players
break into servers from a terminal, build and upgrade their own machines, join organizations, wage wars
over zones of the Grid and compete in weekly hackdowns. An admin panel and mission designer are included.

**📖 Documentation: https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/**

## Features

- **Terminal missions** with SSH, SMTP, SQL and HTTP services, cracking, bouncing and DDoS, plus a full **mission designer**
- **Servers** with upgradable hardware and software, mining **Data Points**
- **Skills, abilities**, an 18-step **tutorial**, rewards, jobs and training
- **Organizations, wars, the Grid** (6 zones of nodes), attacks and the **hackdown** competition
- **Community**: forums, blogs, messaging, friends and rankings
- **Easy hosting**: PHP 8.5 with MySQL 9.7 or SQLite, one-command Docker and a web setup wizard

## Quick start

```bash
git clone https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3.git
cd secret-republic-browser-based-cyber-security-rpg-v3
docker compose up -d --build --wait
```

Open http://localhost:8080 and create your admin account. Prefer no database server? Run the image with
SQLite:

```bash
docker run -d -p 8080:80 -e DB_DRIVER=sqlite ghcr.io/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3:latest
```

Without Docker, download the ready-to-run archive from the
[latest release](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/releases/latest)
and follow the [manual install guide](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/getting-started/manual-install/).
Then schedule the [cron jobs](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/getting-started/cron-jobs/) and set your own `CRON_KEY`.

## Documentation

| | |
| --- | --- |
| **Getting started** | [Quick start](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/getting-started/quick-start/) · [Manual install](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/getting-started/manual-install/) · [Configuration](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/getting-started/configuration/) · [Cron jobs](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/getting-started/cron-jobs/) |
| **Playing** | [Features](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/game/features/) · [Mission console](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/game/missions/) · [Screenshots](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/game/screenshots/) |
| **Running a server** | [Admin panel](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/admin/admin-panel/) · [Mission designer](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/admin/mission-designer/) · [Deployment](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/admin/deployment/) · [Troubleshooting](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/admin/troubleshooting/) |
| **Development** | [Architecture](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/development/architecture/) · [Game content](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/development/game-content/) · [Testing & CI](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/development/testing/) · [Contributing](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/development/contributing/) |

## Screenshots

| Missions | The Grid | Mission designer |
| --- | --- | --- |
| ![Mission terminal](screens/3.jpg) | ![The Grid](screens/6.jpg) | ![Mission designer](screens/7.jpg) |

[More screenshots →](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v3/game/screenshots/)

## Development

```bash
composer install
composer lint     # syntax + deprecation check
composer test     # PHPUnit
tests/smoke.sh    # end-to-end check against a running container
```

The docs site is an Astro + Starlight project in [`docs/`](docs) (`cd docs && npm install && npm run dev`).

## V4

[Secret Republic V4](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v4) is
newer and mobile-first, but has far fewer features.

## Contributors

- [nenuadrian](https://github.com/nenuadrian): creator and main developer
- [SKSaki](https://github.com/SKSaki): first player and bug finder
- [oppiet30](https://github.com/oppiet30): PHP 8 and MySQL strict-mode fixes, SMTP settings, Smarty and release fixes

If your pull request is merged, you'll be added here. Thank you!

## License

[MIT](LICENSE). Please link back to this repository and contribute improvements upstream if you use the
code or assets. Read about [the journey of this project](https://medium.com/@adrian.n/secret-republic-open-sourced-hacker-simulation-futuristic-rpg-browser-based-game-php-843d393cb9d7)
or listen to the [audio trailer](https://www.youtube.com/watch?v=6thfiGb-b7c).
