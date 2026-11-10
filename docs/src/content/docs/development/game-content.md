---
title: Game content
description: Change Secret Republic V3 game content. Skills, abilities and tutorial steps in includes/constants, new static pages, new modules and missions.
---

## Skills, abilities and settings

Game balance lives in `includes/constants/`:

| File | Contents |
| ---- | -------- |
| `skills.php` | The 16 skills (global `$theskills`) |
| `abilities.php` | The 8 abilities (`abilities()` function) |
| `tutorial.php` | The 18 tutorial steps (global `$tutorialSteps`) and their checks |
| `constants.php` | Game-wide settings, loaded as the `config` service |

:::caution[Tests count these]
`tests/SkillsTest.php` expects exactly 16 skills and `tests/TutorialTest.php` exactly 18 tutorial steps.
Update the tests when you add or remove entries.
:::

## Tutorial steps

Edit `includes/constants/tutorial.php`. Every step **N** needs a matching `tutorial_step_N_check()`
function in the same file, which decides when the step is complete. `TutorialTest` enforces this.

## Static pages

Create `templates/pages/NAME.tpl` (copy `templates/pages/template.tpl`) and it is served at `/pages/NAME`.
Names may contain letters, digits, `-` and `_`.

## New modules

Add `includes/modules/hello.php` and it is served at `/hello`. Set `$tVars['display']` to a template in
`templates/`, and read URL parameters from `$GET` (`/hello/id/5` gives `$GET['id'] = '5'`). Use
`$cardinal->mustLogin()` for pages that need a logged-in player. See [Architecture](../architecture/).

## Missions

Missions are content in the database, built with the in-game [mission designer](../../admin/mission-designer/).

## Schema changes

The schema and starting content are in `includes/install/DB.sql`, the format of a phpMyAdmin export. It must
stay importable on MySQL **and** convertible to SQLite. The unit tests import it into SQLite and check the
table count, so run `composer test` after changing it.
