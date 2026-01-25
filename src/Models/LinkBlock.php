<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Models;

use Kirby\Cms\Block;
use ShallowRed\NavigationMenus\Utils\Config;
use ShallowRed\NavigationMenus\Utils\UrlValidator;

/**
 * Navigation Link Block Model
 *
 * Handles navigation link items with proper security,
 * accessibility, and current page detection.
 */
class LinkBlock extends Block
{
  /**
   * Get the link text with fallback to URL
   */
  public function text(): string
  {
    $text = $this->content()->text()->value();

    if (empty($text)) {
      $text = $this->content()->link()->toUrl();
    }

    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
  }

  /**
   * Get the resolved link URL
   */
  public function href(): string
  {
    return UrlValidator::resolveKirbyUrl($this->content()->link()->toUrl());
  }

  /**
   * Check if link opens in new window
   */
  public function isTargetBlank(): bool
  {
    return $this->content()->target()->toBool();
  }

  /**
   * Check if this is an external link
   */
  public function isExternal(): bool
  {
    return UrlValidator::isExternal($this->href());
  }

  /**
   * Check if this link points to the current page
   */
  public function isCurrentPage(): bool
  {
    $currentPage = page();
    if (!$currentPage) {
      return false;
    }

    return $currentPage->url() === $this->href();
  }

  /**
   * Build CSS classes array
   */
  public function cssClasses(): string
  {
    $classes = [];

    $linkClass = Config::getCssValue('link-class');
    if ($linkClass) {
      $classes[] = $linkClass;
    }

    if ($this->isCurrentPage()) {
      $currentClass = Config::getCssValue('current-page-class');
      if ($currentClass) {
        $classes[] = $currentClass;
      }
    }

    if ($this->isExternal()) {
      $classes[] = 'external-link';
    }

    return implode(' ', array_filter($classes));
  }

  /**
   * Build link attributes array
   */
  public function attrs(): array
  {
    $attrs = [
      'href' => $this->href(),
    ];

    $classes = $this->cssClasses();
    if (!empty($classes)) {
      $attrs['class'] = $classes;
    }

    if ($this->isTargetBlank() || $this->isExternal()) {
      $attrs['target'] = '_blank';
      $attrs['rel'] = 'noopener noreferrer';
    }

    if ($this->isCurrentPage()) {
      $attrs['aria-current'] = 'page';
    }

    return $attrs;
  }
}
