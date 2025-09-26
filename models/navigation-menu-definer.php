<?php

class NavMenuDefinerBlock extends \Kirby\Cms\Block
{
  public function isBreadcrumb(): bool
  {
    return $this->content()->breadcrumb()->toBool();
  }

  public function layout(): string
  {
    return $this->isBreadcrumb() ? 'horizontal' : $this->content()->layout()->or('vertical');
  }

  public function wrapper(): string
  {
    return $this->layout() === 'vertical' ? 'aside' : 'div';
  }

  public function navAttrs(): array
  {
    $attrs = [];

    $ariaLabel = $this->content()->ariaLabel()->or('Menu de navigation');
    if ($this->isBreadcrumb()) {
      $ariaLabel = 'Fil d\'Ariane';
    }
    $attrs['aria-label'] = $ariaLabel;

    return $attrs;
  }
}
