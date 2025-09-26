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
      // Use translation for default aria label
      $defaultAriaLabel = t('shallowred.navigation-menus.field.aria-label.default');
      $ariaLabel = strip_tags($this->content()->ariaLabel()->or($defaultAriaLabel));

      if ($this->isBreadcrumb()) {
        $ariaLabel = t('shallowred.navigation-menus.breadcrumb.aria-label');
      }
      $attrs['aria-label'] = htmlspecialchars($ariaLabel, ENT_QUOTES, 'UTF-8');
    } catch (Exception $e) {
      if (option('debug', false)) {
        error_log("navAttrs error: " . $e->getMessage());
      }
      $attrs['aria-label'] = t('shallowred.navigation-menus.navigation.aria-label');
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
    $attrs['aria-label'] = t('shallowred.navigation-menus.nav-toggler.aria-label');
    $attrs['aria-haspopup'] = 'true';
    $attrs['aria-controls'] = 'main-nav';
    $attrs['tabindex'] = '0';

    return $attrs;
  }
  public function menuIconClosed()
  {
    return t('shallowred.navigation-menus.menu-icon.closed');
  }
  public function menuIconOpen()
  {
    return t('shallowred.navigation-menus.menu-icon.open');
  }
}
