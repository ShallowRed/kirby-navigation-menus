<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Models;

use Kirby\Cms\Page;
use ShallowRed\NavigationMenus\Navigation\NavigationHelper;
use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Utils\UrlValidator;

/**
 * Static Menu Page Model
 *
 * Handles static menu pages created by dynamic routes for no-JS mobile navigation.
 * Provides methods for menu retrieval, navigation, and security validation.
 */
class StaticMenuPage extends Page
{
    /**
     * Get the menu key from URL parameters
     */
    public function getMenuKey(): ?string
    {
        $menuKey = $this->kirby()->request()->get('menu');

        if (empty($menuKey) || !is_string($menuKey)) {
            return null;
        }

        // Validate menu key exists in declared menus
        $declaredMenus = Config::getDeclaredMenus();
        if (!array_key_exists($menuKey, $declaredMenus)) {
            return null;
        }

        return $menuKey;
    }

    /**
     * Get back URL with security validation
     */
    public function getBackUrl(): string
    {
        $backUrl = $this->kirby()->request()->get('back');

        // Validate back URL for security
        if (empty($backUrl) || !UrlValidator::isValid($backUrl)) {
            return site()->url();
        }

        return $backUrl;
    }

    /**
     * Get the navigation menu for this static page
     */
    public function getNavigationMenu(): ?\Kirby\Content\Field
    {
        $menuKey = $this->getMenuKey();

        if (!$menuKey) {
            return null;
        }

        return NavigationHelper::getMenu($menuKey);
    }

    /**
     * Check if navigation menu exists and has content
     */
    public function hasNavigationMenu(): bool
    {
        $menu = $this->getNavigationMenu();
        return $menu !== null && !$menu->isEmpty();
    }

    /**
     * Get menu title for display
     */
    public function getMenuTitle(): string
    {
        $menuKey = $this->getMenuKey();

        if (!$menuKey) {
            return 'Navigation Menu';
        }

        $declaredMenus = Config::getDeclaredMenus();
        $menuConfig = $declaredMenus[$menuKey] ?? [];

        return $menuConfig['label'] ?? ucfirst(str_replace(['-', '_'], ' ', $menuKey));
    }

    /**
     * Get navigation pages as blocks collection
     */
    public function getNavPages(): ?object
    {
        $menuKey = $this->getMenuKey();

        if (!$menuKey) {
            return null;
        }

        return NavigationHelper::getNavPages($menuKey);
    }

    /**
     * Check if we have navigation pages
     */
    public function hasNavPages(): bool
    {
        $navPages = $this->getNavPages();
        return $navPages !== null && $navPages->count() > 0;
    }

    /**
     * Get close button attributes for accessibility
     */
    public function getCloseButtonAttrs(): array
    {
        return [
            'aria-label' => 'Close navigation menu',
            'title' => 'Close navigation menu',
        ];
    }
}
