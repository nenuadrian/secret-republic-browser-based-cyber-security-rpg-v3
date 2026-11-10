---
title: Contributing
description: How to contribute to Secret Republic V3. Development setup, guidelines, pull requests and contributors.
---

Issues and pull requests are welcome: bug fixes, missions, balance changes, docs and translations.

## Development setup

```bash
git clone https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3.git
cd secret-republic-browser-based-cyber-security-rpg-v3
composer install
docker compose up -d --build --wait     # http://localhost:8080
```

## Guidelines

- Target **PHP 8.3+**; CI runs PHP 8.5 and `composer lint` must stay free of deprecations.
- Keep shared state in the container (`includes/class/Container.php`), not in new globals.
- Keep `includes/install/DB.sql` working on both MySQL and SQLite.
- New tests go in `tests/` and `require_once` what they need. Add new routes to `tests/smoke.sh`.
- Update these docs (`docs/src/content/docs/`) when behaviour or configuration changes.

## Pull requests

1. Fork and create a branch.
2. Run `./test.sh`. For changes to setup, Docker or pages, also run the
   [smoke test](../testing/#end-to-end-smoke-test).
3. Open a PR against `master` describing the change and how you tested it.

## Contributors

- [nenuadrian](https://github.com/nenuadrian): creator and main developer
- [SKSaki](https://github.com/SKSaki): first player and bug finder
- [oppiet30](https://github.com/oppiet30): PHP 8 and MySQL strict-mode fixes, SMTP settings, Smarty and release fixes

If your pull request is merged, you'll be added here. Thank you!

## License

MIT. If you use the code or assets, please link back to the
[repository](https://github.com/nenuadrian/secret-republic-browser-based-cyber-security-rpg-v3).
