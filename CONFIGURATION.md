# Configuration Options

This plugin offers comprehensive configuration options to customize behavior, styling, and security. Add these to your `site/config/config.php` file:

## Basic Configuration Example

```php
return [
    // ... your other Kirby config

    'shallowred.navigation-menus' => [
        'defaults' => [
            'layout' => 'horizontal',
            'has-toggler' => true,
            'aria-label' => 'Main navigation',
        ],
        'css' => [
            'current-page-class' => 'active',
            'nav-toggler-class' => 'hamburger-menu',
            'show-current-icon' => false,
        ],
        'declared-navigation-menus' => [
            'main' => [
                'name' => 'main_navigation',
                'label' => 'Main Navigation'
            ]
        ]
    ]
];
```

## Complete Configuration Reference

### Default Behavior
```php
'defaults' => [
    'aria-label' => null,              // Default ARIA label (null = use translation)
    'layout' => 'horizontal',          // 'horizontal' | 'vertical'
    'has-toggler' => false,            // Enable mobile toggle by default
    'wrapper' => 'nav',                // HTML wrapper element
    'auto-current-detection' => true,  // Auto-detect current page
    'button-variant' => 'default',     // Default button style
]
```

### CSS & Styling
```php
'css' => [
    'current-page-class' => 'current',                          // CSS class for current page
    'nav-toggler-class' => 'nav-toggler',                      // CSS class for mobile toggle
    'dropdown-class' => 'dropdown',                            // CSS class for dropdowns
    'mobile-nav-class' => 'mobile-nav',                        // CSS class for mobile nav
    'show-current-icon' => true,                               // Show current page icon
    'current-icon-html' => '<span class="current-page-icon" aria-hidden="true"></span>', // Current page icon HTML
]
```

### Security & Validation
```php
'security' => [
    'allow-external-links' => true,                            // Allow external URLs
    'allowed-schemes' => ['https', 'http', 'mailto', 'tel'],  // Allowed URL schemes
    'max-dropdown-depth' => 2,                                 // Maximum nesting levels
    'secure-external-links' => true,                           // Add rel="noopener noreferrer"
]
```

### Mobile & Accessibility
```php
'mobile' => [
    'breakpoint' => '768px',                    // Mobile breakpoint
    'close-on-outside-click' => true,          // Close menu when clicking outside
],
'accessibility' => [
    'enable-skip-link' => true,                // Add skip navigation link
    'skip-link-text' => 'Skip to main content', // Skip link text
    'focus-management' => true,                // Manage focus states
    'aria-expanded' => true,                   // Add aria-expanded to dropdowns
]
```

### Developer Experience
```php
'debug' => [
    'dev-warnings' => false,        // Show development warnings
    'log-errors' => true,           // Log errors (only in debug mode)
    'html-comments' => false,       // Add HTML comments for debugging
]
```

## Real-World Examples

### Small Business Website
```php
'shallowred.navigation-menus' => [
    'defaults' => [
        'has-toggler' => true,
        'aria-label' => 'Main menu',
    ],
    'css' => [
        'current-page-class' => 'active',
        'nav-toggler-class' => 'menu-btn',
        'show-current-icon' => false,
    ],
    'security' => [
        'max-dropdown-depth' => 1, // Keep navigation simple
    ]
]
```

### Portfolio Website
```php
'shallowred.navigation-menus' => [
    'defaults' => [
        'layout' => 'vertical',
        'has-toggler' => false,
    ],
    'css' => [
        'current-page-class' => 'current-work',
        'show-current-icon' => true,
        'current-icon-html' => '<span class="arrow">→</span>',
    ]
]
```

### Accessibility-First Website
```php
'shallowred.navigation-menus' => [
    'accessibility' => [
        'enable-skip-link' => true,
        'skip-link-text' => 'Skip to main content',
        'focus-management' => true,
    ],
    'css' => [
        'show-current-icon' => true,
    ],
    'security' => [
        'secure-external-links' => true,
    ]
]
```

## Benefits

- **🎯 Less Configuration**: Set defaults once, use everywhere
- **🎨 Consistent Styling**: Site-wide CSS class management
- **📱 Mobile Ready**: Configurable mobile behavior
- **♿ Accessible**: Built-in accessibility features
- **🔒 Secure**: Configurable security measures
- **🛠️ Developer Friendly**: Helpful debugging options
