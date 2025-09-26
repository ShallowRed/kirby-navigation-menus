<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Utils;

use Kirby\Cms\Url;
use Kirby\Toolkit\Str;

/**
 * URL validation and security utilities
 *
 * Provides secure URL validation, sanitization, and external link detection
 * with configurable security policies.
 */
final class UrlValidator
{
    /**
     * Validate a URL according to security configuration
     */
    public static function isValid(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Skip validation if disabled in config
        if (!Config::getSecurity()['validate-urls']) {
            return true;
        }

        // Check for obvious malicious patterns
        if (self::containsMaliciousPatterns($url)) {
            return false;
        }

        // Parse URL and validate scheme
        $parsed = parse_url($url);
        if ($parsed === false) {
            return false;
        }

        // Allow relative URLs and Kirby page URLs
        if (!isset($parsed['scheme'])) {
            return true;
        }

        // Check if scheme is allowed
        $allowedSchemes = Config::getAllowedSchemes();
        return in_array(strtolower($parsed['scheme']), array_map('strtolower', $allowedSchemes), true);
    }

    /**
     * Check if URL is external
     */
    public static function isExternal(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Relative URLs are not external
        if (!str_contains($url, '://')) {
            return false;
        }

        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['host'])) {
            return false;
        }

        $currentHost = Url::host();
        return strtolower($parsed['host']) !== strtolower($currentHost);
    }

    /**
     * Sanitize URL for safe output
     */
    public static function sanitize(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Remove any dangerous characters
        $url = Str::slug($url, '-', 'a-zA-Z0-9._~:/?#[]@!$&\'()*+,;=%-');

        // Ensure proper encoding
        return filter_var($url, FILTER_SANITIZE_URL) ?: '';
    }

    /**
     * Get security attributes for external links
     */
    public static function getExternalLinkAttributes(): array
    {
        if (!Config::getSecurity()['secure-external-links']) {
            return [];
        }

        return [
            'rel' => 'noopener noreferrer',
            'target' => '_blank',
        ];
    }

    /**
     * Process URL and return safe attributes
     */
    public static function processUrl(string $url): array
    {
        $attributes = ['href' => $url];

        if (!self::isValid($url)) {
            return [];
        }

        if (self::isExternal($url)) {
            if (!Config::allowExternalLinks()) {
                return [];
            }

            $attributes = array_merge($attributes, self::getExternalLinkAttributes());
        }

        return $attributes;
    }

    /**
     * Check for malicious patterns in URL
     */
    private static function containsMaliciousPatterns(string $url): bool
    {
        $maliciousPatterns = [
            'javascript:',
            'data:',
            'vbscript:',
            'onload=',
            'onerror=',
            'onclick=',
            '<script',
            '%3Cscript',
        ];

        $lowerUrl = strtolower($url);
        foreach ($maliciousPatterns as $pattern) {
            if (str_contains($lowerUrl, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve Kirby page URL
     */
    public static function resolveKirbyUrl(string $url): string
    {
        if (str_starts_with($url, 'page://')) {
            $pageId = substr($url, 7);
            $page = page($pageId);
            return $page ? $page->url() : '';
        }

        return $url;
    }
}
