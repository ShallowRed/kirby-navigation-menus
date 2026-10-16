# Changelog

All notable changes to this plugin are documented in this file. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [2.0.0](https://github.com/ShallowRed/kirby-navigation-menus/compare/0.1.5...v2.0.0) - 2026-10-16

### Breaking changes

- Menus are declared with the `shallowred.navigation-menus.declared-navigation-menus` option and the `declared-navigation-menus` collection. The `shallowred.navigation-menus.menus` option and the `navigation-menus` collection are removed.
- A menu field is a `blocks` field. Menus stored in a `structure` field with `link` and `text` entries must be converted to blocks.
- The `navigation-menu` block, the `navigation-menus` section and the `navigation-menu` snippet are removed. Use the `navigation-menu-definer`, `navigation-menu-picker` and `nav-dropdown` blocks and the `declared-navigation-menus` section.
- Menu items use the `link` and `button` block types, which the site has to provide.
- The plugin requires PHP 8.1 and `lukaskleinschmidt/kirby-snippet-controller` ^2.2.

### Added

- `navigation-menu-definer` block with brand, optional brand link, items, ARIA label, id, layout, breadcrumb mode and a CSS-only mobile toggle.
- `navigation-menu-picker` block to render a menu declared elsewhere, and `nav-dropdown` block for nested items.
- `static-menu` page model and template, used as a fallback when JavaScript is disabled.
- `site()->getMenu()`, `site()->renderNavigation()` and `site()->getNavMenuPages()` methods.
- Configuration options for defaults, CSS classes, URL validation, mobile behaviour, accessibility and debugging, documented in `CONFIGURATION.md`.
- English and French Panel translations.

### Fixed

- Blocks saved without a `content` wrapper by Kirby 4 render under Kirby 5.

## [0.1.5](https://github.com/ShallowRed/kirby-navigation-menus/releases/tag/0.1.5)

Last release of the 0.x series.

