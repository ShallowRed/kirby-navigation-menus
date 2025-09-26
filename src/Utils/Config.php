<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Utils;

use Kirby\Cms\App;

/**
 * Configuration helper for navigation menus
 *
 * Provides centralized access to plugin configuration options
 * with type-safe defaults and validation.
 */
final class Config
{
    private const PREFIX = 'shallowred.navigation-menus';

    /**
     * Get a configuration option with fallback to default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return App::instance()->option(self::PREFIX . '.' . $key, $default);
    }

    /**
     * Get default configuration values
     */
    public static function getDefaults(): array
    {
        return self::get('defaults', [
            'aria-label' => 'Main navigation',
            'layout' => 'horizontal',
            'has-toggler' => true,
            'wrapper' => 'nav',
            'auto-current-detection' => true,
            'button-variant' => 'primary',
            'breadcrumb' => false,
            'show-home-link' => true,
        ]);
    }

    /**
     * Get CSS configuration
     */
    public static function getCss(): array
    {
        return self::get('css', [
            'current-page-class' => 'current',
            'nav-toggler-class' => 'nav-toggler',
            'dropdown-class' => 'dropdown',
            'mobile-nav-class' => 'mobile-nav',
            'show-current-icon' => false,
            'current-icon-html' => '',
            'wrapper-class' => 'navigation-wrapper',
            'list-class' => 'navigation-list',
            'item-class' => 'navigation-item',
            'link-class' => 'navigation-link',
            'button-class' => 'navigation-button',
        ]);
    }

    /**
     * Get security configuration
     */
    public static function getSecurity(): array
    {
        return self::get('security', [
            'allow-external-links' => true,
            'allowed-schemes' => ['https', 'http', 'mailto', 'tel'],
            'max-dropdown-depth' => 2,
            'secure-external-links' => true,
            'validate-urls' => true,
            'sanitize-html' => true,
        ]);
    }

    /**
     * Get mobile configuration
     */
    public static function getMobile(): array
    {
        return self::get('mobile', [
            'breakpoint' => '768px',
            'close-on-outside-click' => true,
            'enable-touch-gestures' => true,
            'menu-direction' => 'left',
        ]);
    }

    /**
     * Get accessibility configuration
     */
    public static function getAccessibility(): array
    {
        return self::get('accessibility', [
            'enable-skip-link' => false,
            'skip-link-text' => 'Skip to main content',
            'focus-management' => true,
            'aria-expanded' => true,
            'screen-reader-text' => true,
            'keyboard-navigation' => true,
        ]);
    }

    /**
     * Get debug configuration
     */
    public static function getDebug(): array
    {
        return self::get('debug', [
            'dev-warnings' => false,
            'log-errors' => false,
            'show-performance' => false,
            'validate-structure' => false,
        ]);
    }

    /**
     * Get declared navigation menus
     */
    public static function getDeclaredMenus(): array
    {
        return self::get('declared-navigation-menus', []);
    }

    /**
     * Check if a specific feature is enabled
     */
    public static function isEnabled(string $feature): bool
    {
        return (bool) self::get($feature, false);
    }

    /**
     * Get a specific default value
     */
    public static function getDefault(string $key): mixed
    {
        $defaults = self::getDefaults();
        return $defaults[$key] ?? null;
    }

    /**
     * Get a specific CSS value
     */
    public static function getCssValue(string $key): string
    {
        $css = self::getCss();
        return (string) ($css[$key] ?? '');
    }

    /**
     * Check if external links are allowed
     */
    public static function allowExternalLinks(): bool
    {
        $security = self::getSecurity();
        return (bool) ($security['allow-external-links'] ?? true);
    }

    /**
     * Get allowed URL schemes
     */
    public static function getAllowedSchemes(): array
    {
        $security = self::getSecurity();
        return $security['allowed-schemes'] ?? ['https', 'http', 'mailto', 'tel'];
    }

    /**
     * Get maximum dropdown depth
     */
    public static function getMaxDropdownDepth(): int
    {
        $security = self::getSecurity();
        return (int) ($security['max-dropdown-depth'] ?? 2);
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebugMode(): bool
    {
        return App::instance()->option('debug', false) === true;
    }

    /**
     * Check if development warnings are enabled
     */
    public static function showDevWarnings(): bool
    {
        if (!self::isDebugMode()) {
            return false;
        }

        $debug = self::getDebug();
        return (bool) ($debug['dev-warnings'] ?? false);
    }
}
