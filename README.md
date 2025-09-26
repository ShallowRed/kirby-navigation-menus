# Kirby Navigation Menus Plugin

A powerful and flexible navigation menu system for Kirby CMS that provides reusable navigation components with advanced features like dropdowns, mobile toggles, breadcrumbs, and comprehensive accessibility support.

## Features

- 🎯 **Reusable Navigation Menus**: Define navigation menus once, use them anywhere
- 📱 **Mobile-First Design**: Built-in mobile navigation with toggles
- ♿ **Accessibility Ready**: ARIA attributes, keyboard navigation, screen reader support
- 🎨 **Highly Configurable**: Extensive configuration options for styling and behavior
- 🌍 **Multilingual Support**: Full internationalization with English and French translations
- 🔒 **Security Built-in**: XSS protection, URL validation, external link handling
- 🎛️ **Block-Based**: Works seamlessly with Kirby's block editor
- 🔗 **Multiple Link Types**: Simple links, buttons, and dropdown menus

## Installation

### Via Composer (Recommended)

```bash
composer require shallowred/kirby-navigation-menus
```

### Manual Installation

1. Download and extract the plugin
2. Copy to `/site/plugins/kirby-navigation-menus`
3. Install the required dependency: `lukaskleinschmidt/kirby-snippet-controller`

### Dependencies

This plugin requires:
- `getkirby/composer-installer: ^1.1`
- `lukaskleinschmidt/kirby-snippet-controller: ^2.2`

## Quick Start

### 1. Configure Your Menus

Add your navigation menus to your site configuration:

```php
// site/config/config.php
return [
    'shallowred.navigation-menus.declared-navigation-menus' => [
        'main' => [
            'name' => 'main_navigation',
            'label' => 'Main Navigation'
        ],
        'footer' => [
            'name' => 'footer_navigation',
            'label' => 'Footer Navigation'
        ]
    ]
];
```

### 2. Add Navigation Fields to Your Blueprints

```yaml
# site/blueprints/site.yml
fields:
  main_navigation:
    label: Main Navigation
    type: blocks
    fieldsets:
      - navigation-menu-definer

  footer_navigation:
    label: Footer Navigation
    type: blocks
    fieldsets:
      - navigation-menu-picker
```

### 3. Use in Your Templates

```php
<!-- Display main navigation -->
<?php foreach (site()->getMenu('main')->toBlocks() as $block): ?>
  <?= $block ?>
<?php endforeach ?>

<!-- Or use the navigation pages directly -->
<?php $navPages = site()->navPages('main') ?>
<?php if ($navPages): ?>
  <nav aria-label="Main navigation">
    <ul>
      <?php foreach ($navPages as $navPage): ?>
        <li><?= site()->renderNavItem($navPage, $page) ?></li>
      <?php endforeach ?>
    </ul>
  </nav>
<?php endif ?>
```

## Configuration Options

### Default Behavior

```php
return [
    'shallowred.navigation-menus.defaults' => [
        'aria-label' => 'Main navigation',
        'layout' => 'horizontal',
        'has-toggler' => true,
        'wrapper' => 'nav',
        'auto-current-detection' => true,
        'button-variant' => 'primary',
    ]
];
```

### CSS & Styling

```php
return [
    'shallowred.navigation-menus.css' => [
        'current-page-class' => 'active',
        'nav-toggler-class' => 'menu-toggle',
        'dropdown-class' => 'dropdown-menu',
        'mobile-nav-class' => 'mobile-navigation',
        'show-current-icon' => true,
        'current-icon-html' => '<span class="current-indicator" aria-hidden="true">→</span>',
    ]
];
```

### Security Settings

```php
return [
    'shallowred.navigation-menus.security' => [
        'allow-external-links' => true,
        'allowed-schemes' => ['https', 'http', 'mailto', 'tel'],
        'max-dropdown-depth' => 2,
        'secure-external-links' => true,
    ]
];
```

### Mobile & Accessibility

```php
return [
    'shallowred.navigation-menus.mobile' => [
        'breakpoint' => '768px',
        'close-on-outside-click' => true,
    ],
    'shallowred.navigation-menus.accessibility' => [
        'enable-skip-link' => true,
        'skip-link-text' => 'Skip to main content',
        'focus-management' => true,
        'aria-expanded' => true,
    ]
];
```

## Navigation Item Types

### Simple Links

```yaml
# In your navigation menu block
items:
  - text: Home
    type: link
    link: page://home
    target: false
```

### Buttons

```yaml
items:
  - text: Get Started
    type: button
    link: page://contact
    variant: primary
    target: false
```

### Dropdown Menus

```yaml
items:
  - text: Services
    type: dropdown
    items:
      - text: Web Development
        type: link
        link: page://services/web-development
      - text: Consulting
        type: button
        link: page://services/consulting
        variant: outline
```

## Site Methods

### `site()->getMenu($key)`

Retrieves a navigation menu by its key.

```php
$mainNav = site()->getMenu('main');
if ($mainNav) {
    echo $mainNav->toBlocks();
}
```

### `site()->navPages($key)`

Gets navigation pages as a blocks collection.

```php
$navPages = site()->navPages('main');
foreach ($navPages as $navPage) {
    echo site()->renderNavItem($navPage, $page);
}
```

### `site()->renderNavItem($navPage, $currentPage)`

Renders a single navigation item with proper current page detection.

```php
echo site()->renderNavItem($navPage, $page);
```

### `site()->isValidUrl($url)`

Validates URLs according to security configuration.

```php
if (site()->isValidUrl($url)) {
    // URL is safe to use
}
```

## Page Methods

### `$page->isInMenu($key)`

Check if the current page is in a specific navigation menu.

```php
if ($page->isInMenu('main')) {
    echo "This page is in the main navigation";
}
```

### `$page->prevInMenu($key)` / `$page->nextInMenu($key)`

Navigate to the previous/next page in a navigation menu.

```php
$prevPage = $page->prevInMenu('main');
$nextPage = $page->nextInMenu('main');

if ($prevPage): ?>
  <a href="<?= $prevPage->url() ?>">← <?= $prevPage->title() ?></a>
<?php endif;

if ($nextPage): ?>
  <a href="<?= $nextPage->url() ?>"><?= $nextPage->title() ?> →</a>
<?php endif ?>
```

### `$page->hasPrevInMenu($key)` / `$page->hasNextInMenu($key)`

Check if previous/next pages exist in navigation.

```php
<?php if ($page->hasPrevInMenu('main')): ?>
  <!-- Show previous button -->
<?php endif ?>
```

## Advanced Usage

### Custom Navigation Rendering

```php
<!-- Custom navigation with advanced features -->
<?php $navPages = site()->navPages('main') ?>
<?php if ($navPages): ?>
  <nav class="main-navigation" aria-label="Main navigation">
    <?php if (option('shallowred.navigation-menus.accessibility.enable-skip-link')): ?>
      <a href="#main-content" class="skip-link">
        <?= t('shallowred.navigation-menus.accessibility.skip-link-text') ?>
      </a>
    <?php endif ?>

    <ul class="nav-list">
      <?php foreach ($navPages as $navPage): ?>
        <?php
        $isCurrentPage = $page->isCurrentPage($navPage);
        $currentClass = option('shallowred.navigation-menus.css.current-page-class', 'current');
        ?>
        <li class="nav-item <?= $isCurrentPage ? $currentClass : '' ?>">
          <?= site()->renderNavItem($navPage, $page) ?>
        </li>
      <?php endforeach ?>
    </ul>
  </nav>
<?php endif ?>
```

### Breadcrumb Navigation

```php
<!-- Enable breadcrumb mode in your navigation block -->
breadcrumb: true
layout: horizontal # Automatically set for breadcrumbs
```

### Mobile Navigation with JavaScript

```javascript
// Basic mobile navigation toggle
document.addEventListener('DOMContentLoaded', function() {
    const togglers = document.querySelectorAll('.nav-toggler');

    togglers.forEach(toggler => {
        toggler.addEventListener('click', function(e) {
            e.preventDefault();
            const nav = document.querySelector('#main-nav');
            const isExpanded = toggler.getAttribute('aria-expanded') === 'true';

            toggler.setAttribute('aria-expanded', !isExpanded);
            nav.classList.toggle('is-open');
        });
    });
});
```

## Styling Examples

### Basic CSS

```css
/* Navigation container */
.main-navigation {
    position: relative;
}

/* Navigation list */
.nav-list {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 1rem;
}

/* Current page indicator */
.nav-item.current {
    font-weight: bold;
}

.current-indicator {
    margin-left: 0.5rem;
    color: var(--primary-color);
}

/* Mobile navigation */
@media (max-width: 768px) {
    .nav-list {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        padding: 1rem;
    }

    .nav-list.is-open {
        display: flex;
    }

    .nav-toggler {
        display: block;
    }
}

/* Dropdown menus */
.dropdown {
    position: relative;
}

.dropdown summary {
    cursor: pointer;
}

.dropdown ul {
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 200px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0.5rem 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
```

## Troubleshooting

### Common Issues

1. **Navigation not appearing**: Check that your menu is properly configured in `site/config/config.php`
2. **Current page not detected**: Ensure `auto-current-detection` is enabled
3. **Mobile toggle not working**: Add JavaScript for mobile toggle functionality
4. **Dropdown menus not styling**: Check your CSS for `.dropdown` classes

### Debug Mode

Enable debug mode to see helpful warnings:

```php
return [
    'debug' => true,
    'shallowred.navigation-menus.debug.dev-warnings' => true,
    'shallowred.navigation-menus.debug.log-errors' => true,
];
```

### Performance Tips

- Use `site()->getMenu()` for simple content access
- Use `site()->navPages()` when you need to iterate over items
- Cache navigation rendering in production if needed
- Limit dropdown depth to 2 levels for better UX

## Security Considerations

- All user input is sanitized automatically
- URLs are validated against allowed schemes
- External links get `rel="noopener noreferrer"` automatically
- XSS protection built-in

## Multilingual Support

The plugin includes translations for English and French. Add your own translations:

```php
// site/languages/de.php
return [
    'shallowred.navigation-menus.navigation.aria-label' => 'Hauptnavigation',
    // Add more translations...
];
```

## License

MIT License. See LICENSE.md for details.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure PSR coding standards compliance
5. Submit a pull request

## Support

- [Documentation](README.md)
- [Issues](https://github.com/ShallowRed/kirby-navigation-menus/issues)
- [Kirby Community Forum](https://forum.getkirby.com)

## Development

*Add instructions on how to help working on the plugin (e.g. npm setup, Composer dev dependencies, etc.)*

## License

MIT

## Credits

- [Your Name](https://github.com/ghost)
