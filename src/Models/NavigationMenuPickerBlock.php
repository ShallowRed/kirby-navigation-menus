<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Models;

use Kirby\Cms\Block;
use Kirby\Content\Field as ContentField;
use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Navigation\NavigationHelper;
use Exception;

/**
 * Navigation Menu Picker Block Model
 *
 * Handles the selection and rendering of pre-defined navigation menus.
 * Provides reusable navigation components that can be placed throughout the site.
 */
class NavigationMenuPickerBlock extends Block
{
    /**
     * Get the selected navigation menu
     */
    public function menu(): ?ContentField
    {
        try {
            $key = $this->getMenuKey();

            if (!$key) {
                return null;
            }

            return NavigationHelper::getMenu($key);
        } catch (Exception $e) {
            $this->logError('Error getting menu', $e);
            return null;
        }
    }

    /**
     * Get the menu key from content
     */
    public function getMenuKey(): ?string
    {
        $key = $this->content()->menu()->value();

        // Validate key
        if (empty($key) || !is_string($key)) {
            if (Config::showDevWarnings()) {
                trigger_error('NavigationMenus: Invalid or empty menu key in picker block', E_USER_WARNING);
            }
            return null;
        }

        // Check if menu exists in configuration
        $declaredMenus = Config::getDeclaredMenus();
        if (!isset($declaredMenus[$key])) {
            if (Config::showDevWarnings()) {
                trigger_error("NavigationMenus: Menu '{$key}' not found in declared menus", E_USER_WARNING);
            }
            return null;
        }

        return $key;
    }

    /**
     * Get navigation pages from the selected menu
     */
    public function getNavPages(): ?object
    {
        $key = $this->getMenuKey();

        if (!$key) {
            return null;
        }

        return NavigationHelper::getNavPages($key);
    }

    /**
     * Check if the selected menu exists and has content
     */
    public function hasMenu(): bool
    {
        return $this->menu() !== null;
    }

    /**
     * Check if the selected menu has navigation items
     */
    public function hasNavPages(): bool
    {
        $navPages = $this->getNavPages();
        return $navPages !== null && (method_exists($navPages, 'count') ? $navPages->count() > 0 : !empty($navPages));
    }

    /**
     * Get menu configuration
     */
    public function getMenuConfig(): array
    {
        $key = $this->getMenuKey();

        if (!$key) {
            return [];
        }

        $declaredMenus = Config::getDeclaredMenus();
        return $declaredMenus[$key] ?? [];
    }

    /**
     * Get menu label for display
     */
    public function getMenuLabel(): string
    {
        $config = $this->getMenuConfig();
        return $config['label'] ?? $this->getMenuKey() ?? 'Unknown Menu';
    }

    /**
     * Get menu description
     */
    public function getMenuDescription(): string
    {
        $config = $this->getMenuConfig();
        return $config['description'] ?? '';
    }

    /**
     * Render the navigation menu HTML
     */
    public function renderMenu(array $options = []): string
    {
        $key = $this->getMenuKey();

        if (!$key || !$this->hasNavPages()) {
            return '';
        }

        try {
            return NavigationHelper::generateNavigationHtml($key, $options);
        } catch (Exception $e) {
            $this->logError('Error rendering menu HTML', $e);
            return '';
        }
    }

    /**
     * Get menu statistics for debugging
     */
    public function getMenuStats(): array
    {
        $stats = [
            'key' => $this->getMenuKey(),
            'exists' => $this->hasMenu(),
            'has_items' => $this->hasNavPages(),
            'item_count' => 0,
        ];

        if ($this->hasNavPages()) {
            $navPages = $this->getNavPages();
            if (method_exists($navPages, 'count')) {
                $stats['item_count'] = $navPages->count();
            }
        }

        return $stats;
    }

    /**
     * Validate the menu picker configuration
     */
    public function validate(): array
    {
        $errors = [];

        $key = $this->getMenuKey();
        if (!$key) {
            $errors[] = 'No menu key specified';
            return $errors;
        }

        if (!$this->hasMenu()) {
            $errors[] = "Menu '{$key}' not found or empty";
        }

        if (!$this->hasNavPages()) {
            $errors[] = "Menu '{$key}' has no navigation items";
        }

        return $errors;
    }

    /**
     * Log error with appropriate level based on configuration
     */
    private function logError(string $message, Exception $e): void
    {
        $fullMessage = "NavigationMenus: {$message}: " . $e->getMessage();

        if (Config::getDebug()['log-errors']) {
            error_log($fullMessage);
        }

        if (Config::showDevWarnings()) {
            trigger_error($fullMessage, E_USER_WARNING);
        }
    }
}
