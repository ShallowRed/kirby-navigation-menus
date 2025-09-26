<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Models;

use Kirby\Cms\Block;
use ShallowRed\NavigationMenus\Utils\Config;
use Exception;

/**
 * Navigation Menu Definer Block Model
 *
 * Handles the configuration and rendering of navigation menu blocks
 * with support for breadcrumbs, mobile navigation, and accessibility features.
 */
class NavigationMenuDefinerBlock extends Block
{
    /**
     * Check if this is a breadcrumb navigation
     */
    public function isBreadcrumb(): bool
    {
        return $this->content()->breadcrumb()->toBool();
    }

    /**
     * Get the navigation layout
     */
    public function layout(): string
    {
        if ($this->isBreadcrumb()) {
            return 'horizontal';
        }
        
        $defaultLayout = Config::getDefault('layout') ?? 'horizontal';
        return $this->content()->layout()->or($defaultLayout)->toString();
    }    /**
     * Get the wrapper element type
     */
    public function wrapper(): string
    {
        $defaultWrapper = Config::getDefault('wrapper') ?? 'nav';

        // Override wrapper based on layout for backwards compatibility
        if ($this->layout() === 'vertical') {
            return 'aside';
        }

        return $defaultWrapper;
    }

    /**
     * Get navigation attributes
     */
    public function navAttrs(): array
    {
        $attrs = [];

        try {
            // Use configuration for default aria label, fallback to translation
            $configDefault = Config::getDefault('aria-label');
            $translationDefault = t('shallowred.navigation-menus.field.aria-label.default');
            $defaultAriaLabel = $configDefault ?: $translationDefault;

            $ariaLabel = strip_tags($this->content()->ariaLabel()->or($defaultAriaLabel)->toString());

            if ($this->isBreadcrumb()) {
                $ariaLabel = t('shallowred.navigation-menus.breadcrumb.aria-label');
            }

            $attrs['aria-label'] = htmlspecialchars($ariaLabel, ENT_QUOTES, 'UTF-8');
        } catch (Exception $e) {
            if (Config::showDevWarnings()) {
                trigger_error('NavigationMenus: Error setting aria-label: ' . $e->getMessage(), E_USER_WARNING);
            }
            $attrs['aria-label'] = 'Navigation';
        }

        // Add ID for mobile navigation
        if ($this->hasNavToggler()) {
            $navId = $this->content()->navId()->or('main-nav')->toString();
            $attrs['id'] = htmlspecialchars($navId, ENT_QUOTES, 'UTF-8');
        }

        return $attrs;
    }

    /**
     * Check if navigation has a mobile toggler
     */
    public function hasNavToggler(): bool
    {
        $defaultHasToggler = Config::getDefault('has-toggler') ?? true;
        return $this->content()->hasToggler()->toBool($defaultHasToggler);
    }

    /**
     * Get navigation toggler attributes
     */
    public function navTogglerAttrs(): array
    {
        if (!$this->hasNavToggler()) {
            return [];
        }

        $attrs = [];

        // Basic button attributes
        $attrs['type'] = 'button';
        $attrs['aria-expanded'] = 'false';

        // CSS class from configuration
        $togglerClass = Config::getCssValue('nav-toggler-class') ?: 'nav-toggler';
        $attrs['class'] = $togglerClass;

        // Accessibility
        if (Config::getAccessibility()['aria-expanded']) {
            $attrs['aria-controls'] = $this->content()->navId()->or('main-nav')->toString();
        }

        // Screen reader text
        if (Config::getAccessibility()['screen-reader-text']) {
            $attrs['aria-label'] = t('shallowred.navigation-menus.mobile.toggle-label');
        }

        return $attrs;
    }

    /**
     * Get closed menu icon HTML
     */
    public function menuIconClosed(): string
    {
        $defaultIcon = $this->content()->menuIconClosed()->or('☰')->toString();
        return htmlspecialchars($defaultIcon, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get open menu icon HTML
     */
    public function menuIconOpen(): string
    {
        $defaultIcon = $this->content()->menuIconOpen()->or('✕')->toString();
        return htmlspecialchars($defaultIcon, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get navigation CSS classes
     */
    public function navClasses(): array
    {
        $classes = [];

        // Base wrapper class
        $wrapperClass = Config::getCssValue('wrapper-class');
        if ($wrapperClass) {
            $classes[] = $wrapperClass;
        }

        // Layout class
        $classes[] = 'layout-' . $this->layout();

        // Breadcrumb class
        if ($this->isBreadcrumb()) {
            $classes[] = 'breadcrumb-nav';
        }

        // Mobile class
        if ($this->hasNavToggler()) {
            $mobileClass = Config::getCssValue('mobile-nav-class');
            if ($mobileClass) {
                $classes[] = $mobileClass;
            }
        }

        return array_filter($classes);
    }

    /**
     * Get navigation list CSS classes
     */
    public function navListClasses(): array
    {
        $classes = [];

        // Base list class
        $listClass = Config::getCssValue('list-class');
        if ($listClass) {
            $classes[] = $listClass;
        }

        // Layout specific classes
        $classes[] = 'nav-' . $this->layout();

        return array_filter($classes);
    }

    /**
     * Get skip link configuration
     */
    public function getSkipLink(): ?array
    {
        $accessibility = Config::getAccessibility();

        if (!$accessibility['enable-skip-link']) {
            return null;
        }

        return [
            'text' => $accessibility['skip-link-text'] ?? 'Skip to main content',
            'target' => '#main-content',
            'class' => 'skip-link',
        ];
    }

    /**
     * Get breadcrumb configuration
     */
    public function getBreadcrumbConfig(): array
    {
        if (!$this->isBreadcrumb()) {
            return [];
        }

        return [
            'show_home' => Config::getDefault('show-home-link') ?? true,
            'separator' => $this->content()->breadcrumbSeparator()->or('›')->toString(),
            'show_current' => $this->content()->showCurrentInBreadcrumb()->toBool(true),
        ];
    }
}
