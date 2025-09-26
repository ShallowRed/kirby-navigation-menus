# Configuration Reference

This document provides a comprehensive reference for all configuration options available in the Kirby Navigation Menus plugin.

## Quick Configuration Example

```php
// site/config/config.php
return [
    // Define your navigation menus
    'shallowred.navigation-menus.declared-navigation-menus' => [
        'main' => [
            'name' => 'main_navigation',
            'label' => 'Main Navigation',
            'description' => 'Primary site navigation'
        ],
        'footer' => [
            'name' => 'footer_navigation',
            'label' => 'Footer Navigation'
        ]
    ],

    // Customize defaults
    'shallowred.navigation-menus.defaults.layout' => 'horizontal',
    'shallowred.navigation-menus.defaults.has-toggler' => true,

    // Security settings
    'shallowred.navigation-menus.security.allow-external-links' => true,
    'shallowred.navigation-menus.security.secure-external-links' => true,
];
```

## Configuration Categories

### 1. Default Behavior (`defaults.*`)

Controls the default behavior of navigation menus when no specific values are provided.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `aria-label` | string | `"Main navigation"` | Default ARIA label for navigation |
| `layout` | string | `"horizontal"` | Default layout (`horizontal` or `vertical`) |
| `has-toggler` | bool | `true` | Enable mobile navigation toggler by default |
| `wrapper` | string | `"nav"` | Default HTML wrapper element |
| `auto-current-detection` | bool | `true` | Automatically detect current page |
| `button-variant` | string | `"primary"` | Default button variant |
| `breadcrumb` | bool | `false` | Enable breadcrumb mode by default |
| `show-home-link` | bool | `true` | Show home link in breadcrumbs |

**Example:**
```php
'shallowred.navigation-menus.defaults' => [
    'aria-label' => 'Site Navigation',
    'layout' => 'vertical',
    'has-toggler' => false,
    'wrapper' => 'aside',
    'button-variant' => 'secondary',
]
```

### 2. CSS & Styling (`css.*`)

Customize CSS classes and styling elements.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `current-page-class` | string | `"current"` | CSS class for current page |
| `nav-toggler-class` | string | `"nav-toggler"` | CSS class for mobile toggler |
| `dropdown-class` | string | `"dropdown"` | CSS class for dropdown menus |
| `mobile-nav-class` | string | `"mobile-nav"` | CSS class for mobile navigation |
| `show-current-icon` | bool | `false` | Show icon for current page |
| `current-icon-html` | string | `"→"` | HTML for current page icon |
| `wrapper-class` | string | `"navigation-wrapper"` | CSS class for navigation wrapper |
| `list-class` | string | `"navigation-list"` | CSS class for navigation list |
| `item-class` | string | `"navigation-item"` | CSS class for navigation items |
| `link-class` | string | `"navigation-link"` | CSS class for navigation links |
| `button-class` | string | `"navigation-button"` | CSS class for navigation buttons |

**Example:**
```php
'shallowred.navigation-menus.css' => [
    'current-page-class' => 'active',
    'nav-toggler-class' => 'menu-toggle',
    'wrapper-class' => 'main-nav',
    'show-current-icon' => true,
    'current-icon-html' => '<span class="current-indicator">•</span>',
]
```

### 3. Security & Validation (`security.*`)

Configure security and URL validation settings.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `allow-external-links` | bool | `true` | Allow external links in navigation |
| `allowed-schemes` | array | `['https', 'http', 'mailto', 'tel']` | Allowed URL schemes |
| `max-dropdown-depth` | int | `2` | Maximum dropdown nesting level |
| `secure-external-links` | bool | `true` | Add security attributes to external links |
| `validate-urls` | bool | `true` | Validate URLs for security |
| `sanitize-html` | bool | `true` | Sanitize HTML output |

**Example:**
```php
'shallowred.navigation-menus.security' => [
    'allow-external-links' => false, // Block all external links
    'allowed-schemes' => ['https'], // Only HTTPS allowed
    'max-dropdown-depth' => 1, // No nested dropdowns
    'secure-external-links' => true,
]
```

### 4. Mobile Navigation (`mobile.*`)

Configure mobile-specific navigation behavior.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `breakpoint` | string | `"768px"` | Mobile breakpoint for CSS |
| `close-on-outside-click` | bool | `true` | Close menu when clicking outside |
| `enable-touch-gestures` | bool | `true` | Enable touch/swipe gestures |
| `menu-direction` | string | `"left"` | Mobile menu slide direction |

**Example:**
```php
'shallowred.navigation-menus.mobile' => [
    'breakpoint' => '992px',
    'close-on-outside-click' => false,
    'menu-direction' => 'right',
]
```

### 5. Accessibility (`accessibility.*`)

Configure accessibility features and compliance.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enable-skip-link` | bool | `false` | Enable skip to main content link |
| `skip-link-text` | string | `"Skip to main content"` | Text for skip link |
| `focus-management` | bool | `true` | Manage keyboard focus |
| `aria-expanded` | bool | `true` | Use aria-expanded for dropdowns |
| `screen-reader-text` | bool | `true` | Include screen reader text |
| `keyboard-navigation` | bool | `true` | Enable keyboard navigation |

**Example:**
```php
'shallowred.navigation-menus.accessibility' => [
    'enable-skip-link' => true,
    'skip-link-text' => 'Skip to navigation',
    'focus-management' => true,
    'keyboard-navigation' => true,
]
```

### 6. Debug & Development (`debug.*`)

Configure debugging and development features.

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `dev-warnings` | bool | `false` | Show development warnings |
| `log-errors` | bool | `false` | Log errors to PHP error log |
| `show-performance` | bool | `false` | Log performance metrics |
| `validate-structure` | bool | `false` | Validate navigation structure |

**Example:**
```php
'shallowred.navigation-menus.debug' => [
    'dev-warnings' => true, // Only in development
    'log-errors' => true,
    'show-performance' => true,
]
```

### 7. Declared Navigation Menus (`declared-navigation-menus`)

Define your navigation menus and their configuration.

**Structure:**
```php
'shallowred.navigation-menus.declared-navigation-menus' => [
    'menu_key' => [
        'name' => 'field_name', // Required: field name in site content
        'label' => 'Display Name', // Required: human-readable label
        'description' => 'Optional description',
    ]
]
```

**Example:**
```php
'shallowred.navigation-menus.declared-navigation-menus' => [
    'main' => [
        'name' => 'main_navigation',
        'label' => 'Main Navigation',
        'description' => 'Primary site navigation shown in header'
    ],
    'footer' => [
        'name' => 'footer_links',
        'label' => 'Footer Links',
        'description' => 'Links displayed in site footer'
    ],
    'sidebar' => [
        'name' => 'sidebar_navigation',
        'label' => 'Sidebar Navigation',
        'description' => 'Secondary navigation for sidebar'
    ]
]
```

## Environment-Specific Configuration

### Development Environment

```php
// site/config/config.localhost.php
return [
    'debug' => true,
    'shallowred.navigation-menus.debug.dev-warnings' => true,
    'shallowred.navigation-menus.debug.log-errors' => true,
    'shallowred.navigation-menus.debug.show-performance' => true,
];
```

### Production Environment

```php
// site/config/config.yourdomain.com.php
return [
    'debug' => false,
    'shallowred.navigation-menus.security.validate-urls' => true,
    'shallowred.navigation-menus.security.sanitize-html' => true,
];
```

## Advanced Configuration Examples

### Complete Accessibility Setup

```php
return [
    'shallowred.navigation-menus.accessibility' => [
        'enable-skip-link' => true,
        'skip-link-text' => 'Skip to main content',
        'focus-management' => true,
        'aria-expanded' => true,
        'screen-reader-text' => true,
        'keyboard-navigation' => true,
    ],
    'shallowred.navigation-menus.css' => [
        'wrapper-class' => 'accessible-navigation',
        'current-page-class' => 'current-page',
        'show-current-icon' => true,
        'current-icon-html' => '<span class="sr-only">Current page: </span>',
    ]
];
```

### Mobile-First Configuration

```php
return [
    'shallowred.navigation-menus.defaults' => [
        'has-toggler' => true,
        'layout' => 'vertical', // Better for mobile
    ],
    'shallowred.navigation-menus.mobile' => [
        'breakpoint' => '1024px', // Larger breakpoint
        'close-on-outside-click' => true,
        'enable-touch-gestures' => true,
        'menu-direction' => 'left',
    ],
    'shallowred.navigation-menus.css' => [
        'nav-toggler-class' => 'hamburger-menu',
        'mobile-nav-class' => 'mobile-navigation',
    ]
];
```

### Security-Focused Configuration

```php
return [
    'shallowred.navigation-menus.security' => [
        'allow-external-links' => false, // Internal links only
        'allowed-schemes' => ['https'], // HTTPS only
        'max-dropdown-depth' => 1, // Flat navigation
        'secure-external-links' => true,
        'validate-urls' => true,
        'sanitize-html' => true,
    ]
];
```

## Configuration Validation

Use the built-in API endpoint to validate your configuration:

```
GET /api/navigation-menus/validate-config
```

This returns information about each declared menu including:
- Whether the menu exists
- Whether it has navigation items
- Configuration details

## Performance Considerations

- Set `debug.*` options to `false` in production
- Use `max-dropdown-depth` to limit complexity
- Enable caching in production environments
- Consider using `validate-structure` only during development

## Troubleshooting Configuration

1. **Menus not appearing**: Check `declared-navigation-menus` configuration
2. **Debug not working**: Ensure `debug` is `true` in main Kirby config
3. **CSS classes not applying**: Verify CSS configuration options
4. **External links blocked**: Check `security.allow-external-links` setting
5. **Mobile toggle not working**: Ensure JavaScript is implemented for toggle functionality

## Migration from Previous Versions

If upgrading from older versions, note these changes:

- Configuration keys now use consistent dot notation
- Some default values have changed for better accessibility
- New security options are enabled by default
- CSS classes have been standardized with prefixes
