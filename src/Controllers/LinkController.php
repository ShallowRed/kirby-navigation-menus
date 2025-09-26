<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Controllers;

use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Utils\UrlValidator;

/**
 * Link Navigation Item Controller
 *
 * Handles the rendering logic for navigation link items with security,
 * accessibility, and proper URL handling.
 */
return function (object $item): array {
    // Validate item and link
    if (!$item || !$item->content()->link()) {
        return [
            'item' => $item,
            'link' => null,
            'linkAttrs' => [],
            'text' => '',
            'isValid' => false,
        ];
    }

    $link = $item->content()->link();
    $url = UrlValidator::resolveKirbyUrl($link->toUrl());

    // Validate URL for security
    if (!UrlValidator::isValid($url)) {
        return [
            'item' => $item,
            'link' => $link,
            'linkAttrs' => [],
            'text' => t('shallowred.navigation-menus.error.invalid-link'),
            'isValid' => false,
        ];
    }

    $page = $link->toPage();
    $menuHref = get('from');
    $currentPage = page();
    $isCurrentPage = ($page && $currentPage && $currentPage->is($page)) || $menuHref === $url;
    $isTargetBlank = $item->content()->target()->toBool();
    $isExternal = UrlValidator::isExternal($url);

    // Sanitize and prepare text content
    $text = sanitizeText($item, $page, $url);

    // Prepare CSS classes
    $classes = buildCssClasses($item, $isCurrentPage, $isExternal);

    // Build link attributes
    $linkAttrs = buildLinkAttributes($url, $isTargetBlank, $isExternal, $classes, $text);

    return [
        'item' => $item,
        'link' => $link,
        'linkAttrs' => $linkAttrs,
        'text' => $text,
        'isCurrentPage' => $isCurrentPage,
        'isExternal' => $isExternal,
        'isValid' => true,
        'classes' => $classes,
    ];
};

/**
 * Sanitize and prepare text content
 */
function sanitizeText(object $item, ?object $page, string $url): string
{
    $text = strip_tags($item->content()->text()->value());

    if (empty($text)) {
        $text = $page ? strip_tags($page->title()->value()) : '';
    }

    if (empty($text)) {
        $text = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }

    return trim($text);
}

/**
 * Build CSS classes array
 */
function buildCssClasses(object $item, bool $isCurrentPage, bool $isExternal): array
{
    $classes = [];

    // Base link class from configuration
    $linkClass = Config::getCssValue('link-class');
    if ($linkClass) {
        $classes[] = $linkClass;
    }

    // Variant class if specified
    $variant = $item->content()->variant()->value();
    if (!empty($variant)) {
        $classes[] = 'variant-' . htmlspecialchars($variant, ENT_QUOTES, 'UTF-8');
    }

    // Current page class
    if ($isCurrentPage) {
        $currentClass = Config::getCssValue('current-page-class');
        if ($currentClass) {
            $classes[] = $currentClass;
        }
    }

    // External link class
    if ($isExternal) {
        $classes[] = 'external-link';
    }

    // Custom classes from content
    $customClasses = $item->content()->classes()->value();
    if (!empty($customClasses)) {
        $customClassArray = array_map('trim', explode(' ', $customClasses));
        $classes = array_merge($classes, array_filter($customClassArray));
    }

    return array_unique(array_filter($classes));
}

/**
 * Build link attributes array
 */
function buildLinkAttributes(string $url, bool $isTargetBlank, bool $isExternal, array $classes, string $text): array
{
    $attrs = ['href' => htmlspecialchars($url, ENT_QUOTES, 'UTF-8')];

    // CSS classes
    if (!empty($classes)) {
        $attrs['class'] = implode(' ', $classes);
    }

    // Target attribute
    if ($isTargetBlank || $isExternal) {
        $attrs['target'] = '_blank';
    }

    // External link security attributes
    if ($isExternal) {
        $externalAttrs = UrlValidator::getExternalLinkAttributes();
        $attrs = array_merge($attrs, $externalAttrs);

        // Add title for accessibility
        if (!isset($attrs['title'])) {
            $attrs['title'] = t('shallowred.navigation-menus.accessibility.external-link-title', ['title' => $text]);
        }
    }

    // Accessibility attributes
    $accessibility = Config::getAccessibility();
    if ($accessibility['focus-management']) {
        $attrs['tabindex'] = '0';
    }

    // Custom attributes from content
    $customAttrs = parseCustomAttributes($item);
    $attrs = array_merge($attrs, $customAttrs);

    return $attrs;
}

/**
 * Parse custom attributes from item content
 */
function parseCustomAttributes(object $item): array
{
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

                    if (!empty($key) && !in_array($key, ['href', 'onclick', 'onload'], true)) {
                        $attrs[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    }
                }
            }
        }
    }

    return $attrs;
}
