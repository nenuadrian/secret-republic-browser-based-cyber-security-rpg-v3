---
title: Admin panel
description: The Secret Republic V3 admin panel. Permission groups, promoting staff, managing players, missions, hardware, software, achievements, email templates, crons and statistics.
---

The admin panel lives at **`/admin`**. The account created by the setup wizard is a full administrator.

## Permission groups

Each player's group is `user_credentials.group_id`. Groups and their permissions are rows in the
`user_groups` table, which you can edit at `/admin/view/groups`.

| `group_id` | Name | Role |
| ---------- | ---- | ---- |
| `1` | Alpha CEO (Admin) | Full administrator (`cardinal` permission). Setup creates your account here. |
| `2` | (default players) | Every new registration (`defaultGroup` in `constants.php`) |
| `10` | (MM) Mission Manager | Can design missions |
| `11` | Alpha Advisor (S.Mod) | Super moderator: missions, player list, forums |

Promote a player with SQL:

```bash
docker compose exec db mysql -usecretrepublic -p secretrepublic \
  -e "UPDATE user_credentials SET group_id = 1 WHERE uid = (SELECT id FROM users WHERE username = 'theirname');"
```

They need to log out and back in to pick up the new permissions.

## Admin views

| URL | Manages |
| --- | ------- |
| `/admin` | Dashboard |
| `/admin/view/manageQuest` | [Mission designer](../mission-designer/): groups, missions, hosts, services, objectives |
| `/admin/view/hacker` | Player management |
| `/admin/view/registered` | Registered players (`/online/true` for players online now) |
| `/admin/view/groups` | Permission groups |
| `/admin/view/achievements` | Achievements |
| `/admin/view/levelRewards` | Rewards given on level-up |
| `/admin/view/data` | Level data |
| `/admin/view/hardware` · `/admin/view/software` | Server hardware and software catalogues |
| `/admin/view/servers` | Player servers |
| `/admin/view/attacks` | Attacks |
| `/admin/view/tasks` | Tasks |
| `/admin/view/emailTemplates` | Email templates |
| `/admin/view/mesall` | Message all players |
| `/admin/view/conversations` | Conversations |
| `/admin/view/crons` | Cron run log |
| `/admin/view/errors` | Error log |

Each view checks its own permission, so a Mission Manager sees only the mission tools.

## Duality (act as a player)

Admins with the `manageDuality` permission can play as another player to see exactly what they see.
Use the asterisk button on the player's admin page or profile
(`/admin/view/hacker/hid/<id>/duality/initiate`). Log out, or open `/admin/duality/deactivate`, to return to
your own account.
