---
title: Mission console
description: How to play Secret Republic V3 missions. The terminal commands for SSH, SMTP and SQL services, cracking, bouncing, DDoS and file handling.
---

Missions run in a terminal. When a mission starts you are logged in to the SSH service on **your own
machine** (host `IP_0`). Hosts receive random IPs for every run, so you discover them through the
mission story, files and emails.

## SSH (files)

Available on your own machine and on any SSH service you connect to.

| Command | Example | What it does |
| ------- | ------- | ------------ |
| `ifconfig` | `ifconfig` | Show the current host's IP, MAC and port |
| `ls` | `ls` | List files |
| `ls -a` | `ls -a` | List files including hidden ones (names starting with `.`) |
| `cat file` | `cat test.txt` | Show a file's contents (if not encrypted) |
| `rm file` | `rm test.txt` | Delete a file (if not encrypted) |
| `touch file` | `touch test.txt` | Create a runnable file |
| `run file params…` | `run test.virus user1 0000` | Run a file with parameters |
| `ps` | `ps` | Show running files |
| `kill id` | `kill 9434` | Stop a running process |
| `decrypt file` | `decrypt logs.data` | Decrypt a file (takes time) |
| `transfer file ip port` | `transfer file.log 132.2.3.44 100` | Move a file to another host's SSH service |
| `scp file ip port` | `scp file.log 23.23.3.3 100` | Copy a file to another host, keeping the original |
| `nmap ip` | `nmap 1.1.1.1` | List a host's services and ports |
| `crack ip port` | `crack 1.1.1.1 33` | Crack the protection of a service |
| `ssh ip port` | `ssh 1.1.1.1 22` | Log in to a remote SSH service (from your own machine) |
| `smtp ip port` | `smtp 1.1.1.1 25` | Log in to a remote mail (SMTP) service |
| `abort` | `abort` | Abort the current task, e.g. a crack |
| `logout` | `logout` | Leave the current host and return to your machine |
| `complete delusion` | `complete delusion` | Give up the mission |

### Bouncing and DDoS

| Command | What it does |
| ------- | ------------ |
| `bounce add ip` / `bounce remove ip` | Route your connections through a host whose services you've fully cracked. Your trace level rises more slowly, but each server logs the connection and can only be bounced through a limited number of times. |
| `bounce status` | List bounce servers and their remaining uses |
| `ddos add ip` / `ddos remove ip` | Add or remove a host from your DDoS list. More servers means a stronger attack. |
| `ddos status` | List DDoS servers and their remaining uses |
| `ddos initiate ip port` | Crash the service on that port |

## SMTP (email)

| Command | Example | What it does |
| ------- | ------- | ------------ |
| `get` | `get` | List emails |
| `read id` | `read 2` | Show an email |
| `delete id` | `delete 0` | Delete an email |
| `forward id address` | `forward 1 someone@example.com` | Forward an email |

## SQL (databases)

| Command | Example | What it does |
| ------- | ------- | ------------ |
| `show tables` | `show tables` | List tables |
| `drop TABLE` | `drop users` | Delete a table |
| `select * from TABLE` | `select * from users` | Show every row. Use exactly this form; a trailing `;` is fine. |
| `delete from TABLE where COLUMN = "VALUE"` | `delete from users where name = "Veverita Verde"` | Delete matching rows. Always put the value in double quotes. |

## Other services

**HTTP** services exist so that they can be DDoSed. **Unknown** services only need to be cracked, often as a
gate before the next objective.

Mission designers: everything above is configured in the [mission designer](../../admin/mission-designer/).
