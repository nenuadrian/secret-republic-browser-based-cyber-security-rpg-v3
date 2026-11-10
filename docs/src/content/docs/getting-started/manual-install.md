---
title: Manual install (LAMP / shared hosting)
description: Install Secret Republic V3 without Docker on Apache or Nginx with PHP 8.3+ and MySQL or SQLite, from a release archive or from source, using the web setup wizard.
---

## Requirements

- **PHP 8.3 or newer** (tested on 8.5) with `gd`, `mysqli`, `pdo_sqlite` and `mbstring`
- **Apache** with `mod_rewrite` and `AllowOverride All` (a `.htaccess` is included), or **Nginx**
- **MySQL 8.0 or newer** (tested on 9.7) with an **empty database**, or nothing at all if you use SQLite

## 1. Get the code

**Option A: release archive (no Composer needed).**
Download `secretrepublic-v3-<version>.zip` from the
[latest release](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/releases/latest).
It already contains every PHP library.

**Option B: from source.**

```bash
git clone https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3.git
cd secret-republic-browser-based-cyber-security-rpg-v3
composer install --no-dev --optimize-autoloader
```

## 2. Web server

Point the document root at **`public_html/`**. On hosts with a fixed web root, upload the whole project
and browse to `http://your-host/path/public_html/`.

The web server user must be able to write to **`includes/`**. Setup writes `includes/database_info.php`
there, Smarty compiles templates into `includes/templates_c` and `includes/cache`, and SQLite databases
default to `includes/local.sqlite`.

```bash
chmod -R ug+rwX includes
```

For Nginx, use the server block in [`docker/nginx.conf`](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3/blob/master/docker/nginx.conf)
and change `fastcgi_pass` to your PHP-FPM socket. It routes every request through `index.php` and blocks
`/includes`.

## 3. Run the setup wizard

Open the site. Until the game is installed, every page shows the setup form:

- **Database**: choose **MYSQL** (host, port, user, password, database) or **SQLITE** (file path relative to
  the project or absolute).
- **Admin account**: 4–15 letters or digits, a 4–20 character password, and an email.

Setup tests the connection, imports `includes/install/DB.sql` (99 tables plus starting content), writes
`includes/database_info.php` and creates your admin account (group 1). Then log in on the home page.

:::note
Setup never overwrites a database that already contains players. If an earlier attempt stopped half-way,
just run it again: it only replaces the game's own tables.
:::

![The setup page](../../../../../screens/setup.png)

## Configure by hand instead

Copy `includes/database_info.php.template` to `includes/database_info.php` and fill it in, or set the
`DB_*` environment variables (see [Configuration](../configuration/)). Then open the site: it shows the
admin-only setup and imports the schema for you.

## After installing

- Set a private `CRON_KEY` and schedule the [cron jobs](../cron-jobs/).
- Configure email and reCAPTCHA if you need them. See [Configuration](../configuration/).
