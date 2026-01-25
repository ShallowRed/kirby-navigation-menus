<?php

declare(strict_types=1);

namespace ShallowRed\NavigationMenus\Models;

use Kirby\Cms\Block;

/**
 * Navigation Button Block Model
 *
 * Delegates to ButtonBlock from pico-ui for consistent button rendering.
 */
class ButtonBlock extends Block
{
  /**
   * Create a ButtonBlock instance from nav-button data
   */
  public function toButtonBlock(): \ButtonBlock
  {
    return new \ButtonBlock([
      'type' => 'button',
      'content' => [
        'label' => $this->content()->label()->value(),
        'link' => $this->content()->link()->value(),
        'target' => $this->content()->target()->value(),
        'colorVariant' => $this->content()->colorVariant()->value() ?: 'primary',
        'styleVariant' => $this->content()->styleVariant()->value() ?: 'flat',
        'sizeVariant' => $this->content()->sizeVariant()->value() ?: 'md',
      ]
    ]);
  }

  /**
   * Delegate attrs to ButtonBlock
   */
  public function attrs(): array
  {
    return $this->toButtonBlock()->attrs();
  }

  /**
   * Delegate label to ButtonBlock
   */
  public function buttonLabel(): string
  {
    return $this->toButtonBlock()->label();
  }
}
