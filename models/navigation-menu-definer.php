<?php

use Kirby\Http\Query;

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

    try {
      // Sanitize aria label
      $ariaLabel = strip_tags($this->content()->ariaLabel()->or('Menu de navigation'));
      if ($this->isBreadcrumb()) {
        $ariaLabel = 'Fil d\'Ariane';
      }
      $attrs['aria-label'] = htmlspecialchars($ariaLabel, ENT_QUOTES, 'UTF-8');
    } catch (Exception $e) {
      if (option('debug', false)) {
        error_log("navAttrs error: " . $e->getMessage());
      }
      $attrs['aria-label'] = 'Navigation menu';
    }

    return $attrs;
  }

  public function hasNavToggler(): bool
  {
    return $this->content()->hasNavToggler()->toBool();
  }

  public function navTogglerAttrs(): array
  {
    $attrs = [];

    $menuHref = get('from');
    if (!isset($menuHref)) {
      $params = new Query([
        'from' => page()->url(),
      ]);
      $menuHref = '/menu/?' . $params->toString();
    }

    $attrs['href'] = $menuHref;
    $attrs['role'] = 'button';
    $attrs['id'] = 'nav-toggler';
    $attrs['class'] = 'nav-toggler button outline';
    $attrs['aria-label'] = 'Ouvrir le menu de navigation';
    $attrs['aria-haspopup'] = 'true';
    $attrs['aria-controls'] = 'main-nav';
    $attrs['tabindex'] = '0';

    return $attrs;
  }
  public function menuIconClosed()
  {
    return 'Menu';
  }
  public function menuIconOpen()
  {
    return 'Fermer';
  }
}
