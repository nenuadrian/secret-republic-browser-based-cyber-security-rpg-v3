---
title: Introduction
description: What Secret Republic V3 is, its main features, the technology it runs on and how it relates to the newer V4.
---

Secret Republic V3 is an open source, post-apocalyptic **cyber security RPG** for the browser. It is the
third full rewrite of a game that has been in development since 2012, and it is the most feature-rich
version: guilds, a party system, single- and multi-player missions, servers that players build and break
into, zone wars with elected leaders, forums, blogs and more.

## Main features

| Feature | Description |
| ------- | ----------- |
| Missions | UNIX terminal-style missions used for the story, organization dailies, the hackdown competition, training and jobs. See the [console guide](../../game/missions/). |
| Mission designer | Admin tool for missions with objectives, side objectives, hosts, services and dynamic syntax. See [Mission designer](../../admin/mission-designer/). |
| Skills & abilities | 16 point-based skills and 8 time-based abilities that boost player stats |
| Tutorial | 18 guided steps with rewards; players can skip steps |
| Rewards | Packaged rewards (money, points, items) that players accept and can review later |
| Servers | Multiple servers per player with upgradable hardware (motherboard, RAM, HDD, power source) and software that uses resources |
| The Grid & zones | Six zones of clusters and nodes; players start on a random node and can take over others |
| Attacks | Damage and spy attacks between nodes, with an attack simulator |
| Organizations | Player-owned guilds with ranks, internal forums, organization missions and wars |
| Hackdown | A scheduled competition built on missions |
| Community | Forums, blogs, threaded messaging, friends and referrals |
| Economy | Money, Data Points mined over time, and Alpha Coins (premium currency with a shop and coupons) |
| Rankings | Player and organization rankings, computed by a [cron job](../cron-jobs/) |
| Audio AI | Pre-recorded voice cues for in-game actions, which players can mute |
| Admin panel | Player management, mission design, hardware, software, achievements, email templates and statistics |

## Technology

| Layer | Version |
| ----- | ------- |
| Language | PHP 8.3 or newer; tested on **8.5**, which the Docker image uses |
| Templates | Smarty 4 |
| Database | MySQL 8.0+ (Docker uses **MySQL 9.7 LTS**) **or** a single SQLite file |
| Web server | Nginx + PHP-FPM in Docker; Apache (`.htaccess` included) or Nginx elsewhere |
| Libraries | MysqliDb, PHPMailer, jBBCode, Google reCAPTCHA, Mobile Detect |

## V3 or V4?

[Secret Republic V4](https://nenuadrian.github.io/secret-republic-browser-based-cyber-security-rpg-v4/)
is newer, mobile-first and built on FuelPHP, but has far fewer features. V3 is the one to pick if you want
the full game: servers, organizations, the Grid, wars and forums.

## Next steps

- [Quick start with Docker](../quick-start/)
- [Manual install](../manual-install/) for shared hosting or an existing server
- [Configuration](../configuration/) and [cron jobs](../cron-jobs/)
