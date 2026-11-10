---
title: Cron jobs
description: Schedule the Secret Republic V3 background jobs (resources, attacks, hourly, daily, monthly, rankings and the hackdown competition) with crontab or Docker.
---

Time-based parts of the game run as HTTP endpoints that a scheduler calls:

```text
https://your-game/cron/key1/<CRON_KEY>/<job>/true
```

`<CRON_KEY>` is the value of the [`CRON_KEY` setting](../configuration/). A wrong key returns a 404.

| Job | What it does | Suggested schedule |
| --- | ------------ | ------------------ |
| `resources` | Mines Data Points for players who own a server: recently active players by default, everyone else with `/all/true` | every minute, plus `/all/true` hourly |
| `attacks` | Finishes due tasks and resolves attacks between nodes, sending reports | every minute |
| `hourly` | Cleans up parties and updates zones | hourly |
| `rankings` | Recomputes player and organization rankings and achievements | hourly |
| `daily` | Lifts expired bans and clears caches, old logs, sessions and daily mission logs | daily |
| `hackdown` | The weekly hackdown competition (keyed to Saturday): pairs players in the arena, resolves their battles and expires entries older than 20 minutes | every 5 minutes |
| `hackdownEnd` | Computes the latest Saturday's hackdown rankings for users, organizations and zones | weekly, after the event (e.g. Sunday 00:05) |
| `monthly` | Monthly housekeeping | monthly |

Every job except `resources` and `attacks` records a row in the `debug_cron_logs` table, which the admin panel shows under **crons** (`/admin/view/crons`).

## crontab

```sh
* * * * *   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/resources/true   >/dev/null
10 * * * *  curl -fsS https://game.example.com/cron/key1/$CRON_KEY/resources/true/all/true >/dev/null
* * * * *   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/attacks/true     >/dev/null
0 * * * *   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/hourly/true      >/dev/null
5 * * * *   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/rankings/true    >/dev/null
0 3 * * *   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/daily/true       >/dev/null
*/5 * * * * curl -fsS https://game.example.com/cron/key1/$CRON_KEY/hackdown/true    >/dev/null
5 0 * * 0   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/hackdownEnd/true >/dev/null
0 4 1 * *   curl -fsS https://game.example.com/cron/key1/$CRON_KEY/monthly/true     >/dev/null
```

Define `CRON_KEY=...` at the top of the crontab, or paste the key into each line. With Docker, call
`http://localhost:8080/...` from the host. Hosting control panels usually offer the same scheduling with
`wget` or `curl`.
