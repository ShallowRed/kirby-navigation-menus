<?php

declare(strict_types=1);

/**
 * Kirby Navigation Menus Plugin
 * 
 * A powerful and flexible navigation menu system for Kirby CMS
 * with advanced features and comprehensive configuration options.
 * 
 * @package ShallowRed\NavigationMenus
 * @author  ShallowRed
 * @license MIT
 */

use Kirby\Cms\App;
use Kirby\Uuid\Uuid;
use ShallowRed\NavigationMenus\Models\NavigationMenuDefinerBlock;
use ShallowRed\NavigationMenus\Models\NavigationMenuPickerBlock;
use ShallowRed\NavigationMenus\Navigation\NavigationHelper;
use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Utils\UrlValidator;

// Autoloader for plugin classes
spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'ShallowRed\\NavigationMenus\\')) {
        $file = __DIR__ . '/src/' . str_replace(['ShallowRed\\NavigationMenus\\', '\\'], ['', '/'], $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Load legacy model files for backwards compatibility
if (file_exists(__DIR__ . '/models/navigation-menu-definer.php')) {
    require_once __DIR__ . '/models/navigation-menu-definer.php';
}
if (file_exists(__DIR__ . '/models/navigation-menu-picker.php')) {
    require_once __DIR__ . '/models/navigation-menu-picker.php';
}

Kirby::plugin('shallowred/navigation-menus', [

    'options' => [
        // Default behavior configuration
        'shallowred.navigation-menus.defaults.aria-label' => 'Main navigation',
        'shallowred.navigation-menus.defaults.layout' => 'horizontal',
        'shallowred.navigation-menus.defaults.has-toggler' => true,
        'shallowred.navigation-menus.defaults.wrapper' => 'nav',
        'shallowred.navigation-menus.defaults.auto-current-detection' => true,
        'shallowred.navigation-menus.defaults.button-variant' => 'primary',
        'shallowred.navigation-menus.defaults.breadcrumb' => false,
        'shallowred.navigation-menus.defaults.show-home-link' => true,

        // CSS & styling configuration
        'shallowred.navigation-menus.css.current-page-class' => 'current',
        'shallowred.navigation-menus.css.nav-toggler-class' => 'nav-toggler',
        'shallowred.navigation-menus.css.dropdown-class' => 'dropdown',
        'shallowred.navigation-menus.css.mobile-nav-class' => 'mobile-nav',
        'shallowred.navigation-menus.css.show-current-icon' => false,
        'shallowred.navigation-menus.css.current-icon-html' => '<span class="current-indicator" aria-hidden="true">→</span>',
        'shallowred.navigation-menus.css.wrapper-class' => 'navigation-wrapper',
        'shallowred.navigation-menus.css.list-class' => 'navigation-list',
        'shallowred.navigation-menus.css.item-class' => 'navigation-item',
        'shallowred.navigation-menus.css.link-class' => 'navigation-link',
        'shallowred.navigation-menus.css.button-class' => 'navigation-button',

        // Security & validation
        'shallowred.navigation-menus.security.allow-external-links' => true,
        'shallowred.navigation-menus.security.allowed-schemes' => ['https', 'http', 'mailto', 'tel'],
        'shallowred.navigation-menus.security.max-dropdown-depth' => 2,
        'shallowred.navigation-menus.security.secure-external-links' => true,
        'shallowred.navigation-menus.security.validate-urls' => true,
        'shallowred.navigation-menus.security.sanitize-html' => true,

        // Mobile & accessibility
        'shallowred.navigation-menus.mobile.breakpoint' => '768px',
        'shallowred.navigation-menus.mobile.close-on-outside-click' => true,
        'shallowred.navigation-menus.mobile.enable-touch-gestures' => true,
        'shallowred.navigation-menus.mobile.menu-direction' => 'left',

        'shallowred.navigation-menus.accessibility.enable-skip-link' => false,
        'shallowred.navigation-menus.accessibility.skip-link-text' => 'Skip to main content',
        'shallowred.navigation-menus.accessibility.focus-management' => true,
        'shallowred.navigation-menus.accessibility.aria-expanded' => true,
        'shallowred.navigation-menus.accessibility.screen-reader-text' => true,
        'shallowred.navigation-menus.accessibility.keyboard-navigation' => true,

        // Developer experience & debugging
        'shallowred.navigation-menus.debug.dev-warnings' => false,
        'shallowred.navigation-menus.debug.log-errors' => false,
        'shallowred.navigation-menus.debug.show-performance' => false,
        'shallowred.navigation-menus.debug.validate-structure' => false,

        // Declared navigation menus
        'shallowred.navigation-menus.declared-navigation-menus' => [],
    ],

    'translations' => [
        'en' => require_once __DIR__ . '/translations/en.php',
        'fr' => require_once __DIR__ . '/translations/fr.php',
    ],

    'collections' => [
        'declared-navigation-menus' => function (): array {
            return Config::getDeclaredMenus();
        },
    ],

    'blueprints' => [
        'blocks/navigation-menu-definer' => __DIR__ . '/blueprints/blocks/navigation-menu-definer.yml',
        'blocks/navigation-menu-picker' => include __DIR__ . '/blueprints/blocks/navigation-menu-picker.php',
        'fields/nav-items' => __DIR__ . '/blueprints/fields/nav-items.yml',
        'sections/declared-navigation-menus' => include __DIR__ . '/blueprints/sections/declared-navigation-menus.php',
    ],

    'templates' => [
        'static-menu' => __DIR__ . '/templates/static-menu.php',
    ],

    'snippets' => [
        'blocks/navigation-menu-picker' => __DIR__ . '/snippets/blocks/navigation-menu-picker.php',
        'blocks/navigation-menu-definer' => __DIR__ . '/snippets/blocks/navigation-menu-definer.php',
        'nav-items/dropdown' => __DIR__ . '/snippets/nav-items/dropdown.php',
        'nav-items/link' => __DIR__ . '/snippets/nav-items/link.php',
        'nav-items/link.controller' => __DIR__ . '/src/Controllers/LinkController.php',
        'nav-items/button' => __DIR__ . '/snippets/nav-items/button.php',
        'nav-items/button.controller' => __DIR__ . '/src/Controllers/ButtonController.php',
    ],

    'blockModels' => [
        'navigation-menu-definer' => NavigationMenuDefinerBlock::class,
        'navigation-menu-picker' => NavigationMenuPickerBlock::class,
    ],

    'siteMethods' => [
        /**
         * Get navigation menu by key
         */
        'getMenu' => function (string $key): ?\Kirby\Content\Field {
            return NavigationHelper::getMenu($key);
        },

        /**
         * Get navigation pages as blocks collection
         */
        'navPages' => function (string $key): ?object {
            return NavigationHelper::getNavPages($key);
        },

        /**
         * Render a single navigation item
         */
        'renderNavItem' => function (object $navPage, \Kirby\Cms\Page $currentPage): string {
            return NavigationHelper::renderNavItem($navPage, $currentPage);
        },

        /**
         * Validate URL for security
         */
        'isValidUrl' => function (?string $url): bool {
            return UrlValidator::isValid($url);
        },

        /**
         * Generate complete navigation HTML
         */
        'renderNavigation' => function (string $menuKey, array $options = []): string {
            return NavigationHelper::generateNavigationHtml($menuKey, $options);
        },

        /**
         * Get pages from navigation menu
         */
        'getNavMenuPages' => function (string $menuKey): \Kirby\Cms\Pages {
            return NavigationHelper::getPagesFromMenu($menuKey);
        },
    ],

    'pageMethods' => [
        /**
         * Check if page is in navigation menu
         */
        'isInMenu' => function (string $menuKey): bool {
            return NavigationHelper::isPageInMenu($this, $menuKey);
        },

        /**
         * Get previous page in navigation menu
         */
        'prevInMenu' => function (string $menuKey): ?\Kirby\Cms\Page {
            return NavigationHelper::getPrevPageInMenu($this, $menuKey);
        },

        /**
         * Get next page in navigation menu
         */
        'nextInMenu' => function (string $menuKey): ?\Kirby\Cms\Page {
            return NavigationHelper::getNextPageInMenu($this, $menuKey);
        },

        /**
         * Check if page has previous page in menu
         */
        'hasPrevInMenu' => function (string $menuKey): bool {
            return NavigationHelper::hasPrevPageInMenu($this, $menuKey);
        },

        /**
         * Check if page has next page in menu
         */
        'hasNextInMenu' => function (string $menuKey): bool {
            return NavigationHelper::hasNextPageInMenu($this, $menuKey);
        },
    ],

    'hooks' => [
        'page.render:before' => function (\Kirby\Cms\Page $page): void {
            // Performance tracking in debug mode
            if (Config::getDebug()['show-performance']) {
                $page->navigationRenderStart = microtime(true);
            }
        },

        'page.render:after' => function (\Kirby\Cms\Page $page, string $html): void {
            // Log performance metrics in debug mode
            if (Config::getDebug()['show-performance'] && isset($page->navigationRenderStart)) {
                $renderTime = microtime(true) - $page->navigationRenderStart;
                error_log("NavigationMenus: Page render time: {$renderTime}s for {$page->url()}");
            }
        },
    ],

    'validators' => [
        'navigationUrl' => function (string $url): bool {
            return UrlValidator::isValid($url);
        },
    ],

    'api' => [
        'routes' => [
            [
                'pattern' => 'navigation-menus/validate-config',
                'method' => 'GET',
                'action' => function (): array {
                    $declaredMenus = Config::getDeclaredMenus();
                    $validation = [];

                    foreach ($declaredMenus as $key => $menu) {
                        $validation[$key] = [
                            'exists' => NavigationHelper::getMenu($key) !== null,
                            'has_items' => NavigationHelper::getNavPages($key) !== null,
                            'config' => $menu,
                        ];
                    }

                    return [
                        'status' => 'success',
                        'menus' => $validation,
                        'config_summary' => [
                            'total_menus' => count($declaredMenus),
                            'debug_mode' => Config::isDebugMode(),
                            'external_links_allowed' => Config::allowExternalLinks(),
                        ],
                    ];
                },
            ],
        ],
    ],
]);

/**
 * Register legacy class aliases for backwards compatibility
 */
if (!class_exists('NavMenuDefinerBlock', false)) {
    class_alias(NavigationMenuDefinerBlock::class, 'NavMenuDefinerBlock');
}

if (!class_exists('NavMenuPickerBlock', false)) {
    class_alias(NavigationMenuPickerBlock::class, 'NavMenuPickerBlock');
}