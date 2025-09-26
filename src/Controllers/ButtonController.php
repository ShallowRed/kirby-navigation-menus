<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Controllers;

use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Utils\UrlValidator;

/**
 * Button Navigation Item Controller
 *
 * Handles the rendering logic for navigation button items with proper
 * styling, security, and accessibility features.
 */
return function (object $item): array {

    /**
     * Sanitize and prepare button text
     */
    $sanitizeButtonText = function (object $item, string $link): string {
        $text = strip_tags($item->content()->text()->value());

        if (empty($text)) {
            $text = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
        }

        return trim($text);
    };

    /**
     * Get button variant with fallback to configuration default
     */
    $getButtonVariant = function (object $item): string {
        $variant = $item->content()->variant()->value();

        if (empty($variant)) {
            $variant = Config::getDefault('button-variant') ?? 'primary';
        }

        return htmlspecialchars($variant, ENT_QUOTES, 'UTF-8');
    };

    /**
     * Build button CSS classes array
     */
    $buildButtonClasses = function (object $item) use ($getButtonVariant): array {
        $classes = [];

        // Base button class from configuration
        $buttonClass = Config::getCssValue('button-class') ?: 'button';
        $classes[] = $buttonClass;

        // Variant class
        $variant = $getButtonVariant($item);
        if (!empty($variant)) {
            $classes[] = $buttonClass . '--' . $variant;
            $classes[] = $variant; // For backwards compatibility
        }

        // Style class (additional styling)
        $style = $item->content()->style()->value();
        if (!empty($style)) {
            $classes[] = htmlspecialchars($style, ENT_QUOTES, 'UTF-8');
        }

        // Size class
        $size = $item->content()->size()->value();
        if (!empty($size)) {
            $classes[] = $buttonClass . '--' . htmlspecialchars($size, ENT_QUOTES, 'UTF-8');
        }

        // State classes
        if ($item->content()->disabled()->toBool()) {
            $classes[] = $buttonClass . '--disabled';
            $classes[] = 'disabled';
        }

        // Custom classes from content
        $customClasses = $item->content()->classes()->value();
        if (!empty($customClasses)) {
            $customClassArray = array_map('trim', explode(' ', $customClasses));
            $classes = array_merge($classes, array_filter($customClassArray));
        }

        return array_unique(array_filter($classes));
    };

    /**
     * Parse custom attributes from button item content
     */
    $parseButtonCustomAttributes = function (object $item): array {
        $attrs = [];

        // Check for custom attributes in content
        if (method_exists($item->content(), 'attrs')) {
            $customAttrs = $item->content()->attrs()->value();
            if (!empty($customAttrs)) {
                // Parse simple key=value pairs
                $pairs = explode(' ', $customAttrs);
                foreach ($pairs as $pair) {
                    if (str_contains($pair, '=')) {
                        [$key, $value] = explode('=', $pair, 2);
                        $key = trim($key);
                        $value = trim($value, '"\'');

                        // Security: prevent dangerous attributes
                        $dangerousAttrs = ['onclick', 'onload', 'onerror', 'onmouseover', 'onfocus', 'onblur'];
                        if (!empty($key) && !in_array($key, $dangerousAttrs, true)) {
                            $attrs[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                        }
                    }
                }
            }
        }

        // Button-specific attributes
        if (method_exists($item->content(), 'role')) {
            $role = $item->content()->role()->value();
            if (!empty($role)) {
                $attrs['role'] = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');
            }
        }

        return $attrs;
    };

    /**
     * Build button attributes array
     */
    $buildButtonAttributes = function (string $link, bool $target, bool $isExternal, array $classes, string $text) use ($parseButtonCustomAttributes, $item): array {
        $attrs = [];

        // Basic button attributes
        $attrs['type'] = 'button';
        $attrs['onclick'] = sprintf("window.%s('%s')", $target || $isExternal ? 'open' : 'location.href =', htmlspecialchars($link, ENT_QUOTES, 'UTF-8'));

        // CSS classes
        if (!empty($classes)) {
            $attrs['class'] = implode(' ', $classes);
        }

        // External link security and accessibility
        if ($isExternal) {
            // Add title for accessibility
            $attrs['title'] = t('shallowred.navigation-menus.accessibility.external-link-title', ['title' => $text]);

            // Add ARIA label for screen readers
            $attrs['aria-label'] = $text . ' ' . t('shallowred.navigation-menus.accessibility.external-link-suffix');
        }

        // Accessibility attributes
        $accessibility = Config::getAccessibility();
        if ($accessibility['focus-management']) {
            $attrs['tabindex'] = '0';
        }

        if ($accessibility['keyboard-navigation']) {
            $attrs['onkeydown'] = "if(event.key==='Enter'||event.key===' '){this.click();}";
        }

        // Disabled state
        $disabled = method_exists($item->content(), 'disabled') && $item->content()->disabled()->toBool();
        if ($disabled) {
            $attrs['disabled'] = 'disabled';
            $attrs['aria-disabled'] = 'true';
            unset($attrs['onclick'], $attrs['onkeydown']);
        }

        // Custom attributes from content
        $customAttrs = $parseButtonCustomAttributes($item);
        $attrs = array_merge($attrs, $customAttrs);

        return $attrs;
    };

    // Validate item and link
    if (!$item || !$item->content()->link()) {
        return [
            'item' => $item,
            'link' => '',
            'buttonAttrs' => [],
            'text' => t('shallowred.navigation-menus.error.invalid-button'),
            'isValid' => false,
        ];
    }

    $link = UrlValidator::resolveKirbyUrl($item->content()->link()->toUrl());

    // Validate URL for security
    if (!UrlValidator::isValid($link)) {
        return [
            'item' => $item,
            'link' => $link,
            'buttonAttrs' => [],
            'text' => t('shallowred.navigation-menus.error.invalid-link'),
            'isValid' => false,
        ];
    }

    $target = $item->content()->target()->toBool();
    $isExternal = UrlValidator::isExternal($link);

    // Sanitize and prepare text content
    $text = $sanitizeButtonText($item, $link);

    // Build button classes
    $classes = $buildButtonClasses($item);

    // Build button attributes
    $buttonAttrs = $buildButtonAttributes($link, $target, $isExternal, $classes, $text);

    return [
        'item' => $item,
        'link' => $link,
        'buttonAttrs' => $buttonAttrs,
        'text' => $text,
        'isExternal' => $isExternal,
        'isValid' => true,
        'classes' => $classes,
        'variant' => $getButtonVariant($item),
    ];
};


