<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Models;

use Kirby\Cms\Block;

/**
 * Navigation Dropdown Block Model
 *
 * Handles dropdown navigation items with nested items.
 */
class DropdownBlock extends Block
{
  /**
   * Get the dropdown label text
   */
  public function text(): string
  {
    return htmlspecialchars($this->content()->text()->value(), ENT_QUOTES, 'UTF-8');
  }

  /**
   * Get nested navigation items as blocks
   */
  public function items(): \Kirby\Cms\Blocks
  {
    return $this->content()->items()->toBlocks();
  }

  /**
   * Get dropdown CSS class from config
   */
  public function dropdownClass(): string
  {
    return option('shallowred.navigation-menus.css.dropdown-class', 'dropdown');
  }

  /**
   * Check if aria-expanded should be included
   */
  public function hasAriaExpanded(): bool
  {
    return option('shallowred.navigation-menus.accessibility.aria-expanded', true);
  }
}
