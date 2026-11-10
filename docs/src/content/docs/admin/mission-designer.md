---
title: Mission designer
description: Design Secret Republic V3 missions. Mission groups, objectives and side objectives, hosts, SSH/SMTP/SQL/HTTP services, files and emails, plus Q-Syntax for random IPs, strings and numbers.
---

Missions are designed in the admin panel at **`/admin/view/manageQuest`** (groups 1, 10 and 11). The original
PDF guides are in [`MISSION-GUIDES/`](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/tree/master/MISSION-GUIDES);
this page covers the same material.

## Mission groups

- **Parent quest**: a mission that must be completed first.
- **Order**: position relative to other groups, for players and admins.
- Groups are either normal or special-purpose (organization wars, zone wars, hackdown…).

## Missions

- **Order in group**: missions are listed by this.
- **Required parent quest**: a mission that must be completed first.
- **Type**: *Normal* (once), *Daily* (rewards every 24 hours) or *Repeatable* (story value, no rewards).
- **Energy**: energy needed to start.

## Objectives

When an objective is completed, the next one is shown until none are left.

- **Order**: the sequence. If two objectives share an order, one is picked at random. Orders don't need to
  be consecutive (10, 20, …).
- **Extra time**: added to the remaining time when the objective is completed.
- **Achievement**: awarded if the mission succeeds.
- **Story**: shown to the player; BBCode enabled.

An objective is complete when all its **compulsory side objectives** are complete.

## Side objectives

- **Type** and **Data**: the action that completes it (table below).
- **Group**: side objectives in the same group complete together; finishing one finishes the group.
- **Extra time**, **Compulsory** and **Achievement**: as for objectives.

| Type | Data | Example | Completed when |
| ---- | ---- | ------- | -------------- |
| `ping` | `IP_X` | `IP_1` | the IP is pinged |
| `nmap` | `IP_X` | `IP_2` | the IP is scanned |
| `crack` | `IP_X:port` | `IP_3:200` | that service is cracked |
| `connect` | `IP_X:port` | `IP_2:100` | the player connects (ssh, smtp, sql…) |
| `cat` | `filename` | `story.text` | the file is read |
| `delete_file` | `filename` | `test.txt` | the file is deleted, wherever it is |
| `decrypt_file` | `filename` | `log.data` | `decrypt` runs on the file |
| `transfer` | `filename\|IP_X:port` | `test.txt\|IP_1:22` | the file is transferred there (needs an SSH service) |
| `run_file` / `kill_file` | `filename\|IP_X:port` | `test.txt\|IP_1:22` | the file is run / killed there |
| `delete_logs` | `IP_X:port` | `IP_2:22` | `rm logs` runs there |
| `read_email` / `delete_email` | `ID\|IP_X:port` | `1\|IP_2:666` | that email is read / deleted |
| `forward_email` | `ID\|address\|IP_X:port` | `2\|USERNAME@example.com\|IP_2:33` | the email is forwarded to that address |
| `crash_service` | `IP_X:port` | `IP_3:80` | the service is crashed (DDoS) |
| `sql_delete` | `table\|rows\|IP_X:port` | `users\|1,2\|IP_1:200` | those rows (1-based) are deleted **in one statement** |
| `sql_drop` | `table\|IP_X:port` | `users\|IP_1:1433` | the table is dropped |

Wherever the data has `IP_X:port`, `*` is a wildcard: `IP_X:*`, `*:port` or `*:*`.

:::tip
To delete several SQL rows one by one, create one compulsory side objective per row instead.
:::

## Hosts

- **Hostname**: shown when pinging or connecting, e.g. `backup.tsrbank.com`.
- **Discovered**: visible in the player's host list from the start, without pinging.
- **MAC address**: random per run (shown by `ifconfig`).

Hosts are numbered from 0 for each mission. **Host 0 is the player's own machine**: it has a single SSH
file server, its files are available immediately, and the player returns there on `logout`. Every host gets
a random IP each run, referenced as `IP_X`.

## Services

Services run on a port of a host (not on host 0). Two services can't share a port; a later one replaces the
earlier one.

- **Port**: use realistic ports.
- **Encryption**: protection that must be cracked before connecting.
- **Welcome message**: shown after connecting; BBCode enabled (e.g. `FBI database server. Welcome, USERNAME.`).

| Service | Contents |
| ------- | -------- |
| **SSH** | Files and Linux-like commands (`ls`, `rm`, `run`, `kill`…) |
| **SMTP** | Emails |
| **SQL** | Tables |
| **HTTP** | Nothing; it exists so it can be DDoSed |
| **Unknown** | Nothing; a port to crack before the next objective |

### SSH files

- **Title**: file name (letters, digits and dots, no spaces). A leading `.` hides it except from `ls -a`.
- **Encryption**: whether it needs decrypting, and for how long.
- **Can run** / **Can't kill** / **Running** (running by default).
- **Required running on current host** / **on main server**: files that must be running, separated by `|`.
- **Parameters**: the expected run parameters separated by `|` (`run file.t 123 345` → `123|345`).
- **Required (side) objective**: the file doesn't exist until that objective is completed.
- **Burst**: the file runs, then stops itself. Useful for one-shot tools that unlock other files.
- **Execution time**: seconds before the file starts and triggers `run_file`.

### SMTP emails

IDs are assigned automatically from 0. Each email has a subject, a sender (name or address), BBCode content
and an optional **required (side) objective**.

### SQL tables

The **title** should be letters (optionally ending in a number). **Content** is pipe-separated with a header
row:

```text
log_id|ip|date
1|IP_3|13/2/2014
2|IP_4|14/2/2014
```

Column names should use letters, digits and `_`.

## Q-Syntax

These placeholders work in stories, welcome messages, file contents and emails, and are resolved for every
mission run.

| Syntax | Meaning | Example |
| ------ | ------- | ------- |
| `USERNAME` | The player's name | |
| `IP_X` | IP of host `X` | `IP_0` is the player's machine |
| `IHP_X_Y` | IP of host `X` with part `Y` hidden | `11.22.33.44` → `IHP_1_2` = `11.xx.33.44` |
| `IRP_X_Y` | IP of host `X` with only part `Y` revealed | `IRP_1_4` = `xx.xx.xx.44` |
| `base64(text)` | Base64 of the text | `base64(test)` → `dGVzdA==` |
| `RAND_S_X_Y` | First `Y` characters of random string `X` (50 characters, reusable by `X`) | `RAND_S_0_10` |
| `RAND_S_X_Y_Z` | Characters `Y` to `Z` (0-based) of random string `X` | `RAND_S_0_0_9` |
| `RAND_N_X_Y` | A random number between `X` and `Y` (not reusable) | `RAND_N_1000_9999` |

## Tips

- **Output files**: to reveal `foundIPs.txt` after `scanner.sh` runs, add an optional side objective
  completed by running `scanner.sh` and make it the required objective of `foundIPs.txt`.
- Use **Unknown** services as simple crack gates, for example a car's port that must be cracked before a
  file is run against it.
