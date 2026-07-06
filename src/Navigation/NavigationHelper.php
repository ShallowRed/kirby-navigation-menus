<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Navigation;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Cms\Field;
use Kirby\Content\Field as ContentField;
use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Utils\UrlValidator;

/**
 * Navigation helper for menu operations
 *
 * Provides high-level navigation operations like menu retrieval,
 * page detection, and navigation item rendering.
 */
final class NavigationHelper
{
    /**
     * Convert a blocks field to Blocks, normalizing legacy entries whose
     * fields are stored at the top level instead of inside a `content` key.
     * Kirby 4 hoisted those automatically, Kirby 5 no longer does.
     */
    public static function fieldToBlocks(ContentField $field): \Kirby\Cms\Blocks
    {
        $raw = \Kirby\Data\Json::decode($field->value() ?? '[]');

        if (is_array($raw) === false) {
            return $field->toBlocks();
        }

        $meta = ['id' => true, 'isHidden' => true, 'type' => true];
        $normalized = array_map(function ($item) use ($meta) {
            if (is_array($item) && isset($item['content']) === false) {
                return array_intersect_key($item, $meta) + [
                    'content' => array_diff_key($item, $meta),
                ];
            }
            return $item;
        }, $raw);

        return \Kirby\Cms\Blocks::factory($normalized, [
            'parent' => $field->parent(),
            'field'  => $field,
        ]);
    }

    /**
     * Get navigation menu by key
     */
    public static function getMenu(string $key): ?ContentField
    {
        $declaredMenus = Config::getDeclaredMenus();

        if (!isset($declaredMenus[$key])) {
            if (Config::showDevWarnings()) {
                trigger_error("Navigation menu '{$key}' not found in configuration", E_USER_WARNING);
            }
            return null;
        }

        $menuConfig = $declaredMenus[$key];
        $fieldName = $menuConfig['name'] ?? $key;

        return site()->content()->get($fieldName);
    }

    /**
     * Get navigation pages as blocks collection
     */
    public static function getNavPages(string $key): ?object
    {
        $menu = self::getMenu($key);

        if (!$menu || $menu->isEmpty()) {
            return null;
        }

        try {
            return $menu->toBlocks();
        } catch (\Exception $e) {
            if (Config::getDebug()['log-errors']) {
                error_log("NavigationMenus: Error getting nav pages for '{$key}': " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Render a single navigation item
     */
    public static function renderNavItem(object $navPage, Page $currentPage): string
    {
        try {
            return (string) $navPage;
        } catch (\Exception $e) {
            if (Config::showDevWarnings()) {
                trigger_error("Error rendering navigation item: " . $e->getMessage(), E_USER_WARNING);
            }
            return '';
        }
    }

    /**
     * Check if page is in navigation menu
     */
    public static function isPageInMenu(Page $page, string $menuKey): bool
    {
        $navPages = self::getNavPages($menuKey);

        if (!$navPages) {
            return false;
        }

        foreach ($navPages as $navPage) {
            if (self::isNavItemForPage($navPage, $page)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get previous page in navigation menu
     */
    public static function getPrevPageInMenu(Page $page, string $menuKey): ?Page
    {
        return self::getAdjacentPageInMenu($page, $menuKey, 'prev');
    }

    /**
     * Get next page in navigation menu
     */
    public static function getNextPageInMenu(Page $page, string $menuKey): ?Page
    {
        return self::getAdjacentPageInMenu($page, $menuKey, 'next');
    }

    /**
     * Check if page has previous page in menu
     */
    public static function hasPrevPageInMenu(Page $page, string $menuKey): bool
    {
        return self::getPrevPageInMenu($page, $menuKey) !== null;
    }

    /**
     * Check if page has next page in menu
     */
    public static function hasNextPageInMenu(Page $page, string $menuKey): bool
    {
        return self::getNextPageInMenu($page, $menuKey) !== null;
    }

    /**
     * Get pages from navigation menu
     */
    public static function getPagesFromMenu(string $menuKey): Pages
    {
        $pages = new Pages();
        $navPages = self::getNavPages($menuKey);

        if (!$navPages) {
            return $pages;
        }

        foreach ($navPages as $navPage) {
            $page = self::getPageFromNavItem($navPage);
            if ($page) {
                $pages->append($page->id(), $page);
            }
        }

        return $pages;
    }

    /**
     * Generate navigation HTML
     */
    public static function generateNavigationHtml(string $menuKey, array $options = []): string
    {
        $navPages = self::getNavPages($menuKey);

        if (!$navPages) {
            return '';
        }

        $defaults = [
            'wrapper' => 'nav',
            'list_tag' => 'ul',
            'item_tag' => 'li',
            'current_class' => Config::getCssValue('current-page-class'),
            'wrapper_class' => Config::getCssValue('wrapper-class'),
            'list_class' => Config::getCssValue('list-class'),
            'item_class' => Config::getCssValue('item-class'),
        ];

        $options = array_merge($defaults, $options);
        $currentPage = App::instance()->site()->page();

        $html = '';

        if ($options['wrapper']) {
            $wrapperClass = $options['wrapper_class'] ? " class=\"{$options['wrapper_class']}\"" : '';
            $html .= "<{$options['wrapper']}{$wrapperClass}>";
        }

        if ($options['list_tag']) {
            $listClass = $options['list_class'] ? " class=\"{$options['list_class']}\"" : '';
            $html .= "<{$options['list_tag']}{$listClass}>";
        }

        foreach ($navPages as $navPage) {
            $isCurrent = self::isNavItemForPage($navPage, $currentPage);
            $itemClass = $options['item_class'];

            if ($isCurrent && $options['current_class']) {
                $itemClass .= ' ' . $options['current_class'];
            }

            $itemClassAttr = $itemClass ? " class=\"{$itemClass}\"" : '';

            if ($options['item_tag']) {
                $html .= "<{$options['item_tag']}{$itemClassAttr}>";
            }

            $html .= self::renderNavItem($navPage, $currentPage);

            if ($options['item_tag']) {
                $html .= "</{$options['item_tag']}>";
            }
        }

        if ($options['list_tag']) {
            $html .= "</{$options['list_tag']}>";
        }

        if ($options['wrapper']) {
            $html .= "</{$options['wrapper']}>";
        }

        return $html;
    }

    /**
     * Get adjacent page in navigation menu
     */
    private static function getAdjacentPageInMenu(Page $page, string $menuKey, string $direction): ?Page
    {
        $pages = self::getPagesFromMenu($menuKey);

        if ($pages->count() === 0) {
            return null;
        }

        $currentIndex = null;
        foreach ($pages as $index => $navPage) {
            if ($navPage->is($page)) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === null) {
            return null;
        }

        $targetIndex = $direction === 'prev' ? $currentIndex - 1 : $currentIndex + 1;

        return $pages->nth($targetIndex);
    }

    /**
     * Check if nav item represents a specific page
     */
    private static function isNavItemForPage(object $navItem, Page $page): bool
    {
        $navPage = self::getPageFromNavItem($navItem);
        return $navPage && $navPage->is($page);
    }

    /**
     * Extract page from navigation item
     */
    private static function getPageFromNavItem(object $navItem): ?Page
    {
        // Handle different nav item structures
        if (method_exists($navItem, 'link')) {
            $link = $navItem->link();
            if ($link && method_exists($link, 'toPage')) {
                return $link->toPage();
            }
        }

        // Fallback for other structures
        if (method_exists($navItem, 'toPage')) {
            return $navItem->toPage();
        }

        return null;
    }
}
