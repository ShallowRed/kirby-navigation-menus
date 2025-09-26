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
    if ($this->isBreadcrumb()) {
      return 'horizontal';
    }
    
    $defaultLayout = option('shallowred.navigation-menus.defaults.layout', 'horizontal');
    return $this->content()->layout()->or($defaultLayout);
  }

  public function wrapper(): string
  {
    $defaultWrapper = option('shallowred.navigation-menus.defaults.wrapper', 'nav');
    
    // Override wrapper based on layout for backwards compatibility
    if ($this->layout() === 'vertical') {
      return 'aside';
    }
    
    return $defaultWrapper;
  }

  public function navAttrs(): array
  {
    $attrs = [];

    try {
      // Use configuration for default aria label, fallback to translation
      $configDefault = option('shallowred.navigation-menus.defaults.aria-label');
      $translationDefault = t('shallowred.navigation-menus.field.aria-label.default');
      $defaultAriaLabel = $configDefault ?: $translationDefault;
      
      $ariaLabel = strip_tags($this->content()->ariaLabel()->or($defaultAriaLabel));
      
      if ($this->isBreadcrumb()) {
        $ariaLabel = t('shallowred.navigation-menus.breadcrumb.aria-label');
      }
      $attrs['aria-label'] = htmlspecialchars($ariaLabel, ENT_QUOTES, 'UTF-8');
    } catch (Exception $e) {
      if (option('shallowred.navigation-menus.debug.log-errors', true) && option('debug', false)) {
        error_log("navAttrs error: " . $e->getMessage());
      }
      $attrs['aria-label'] = t('shallowred.navigation-menus.navigation.aria-label');
    }    return $attrs;
  }

  public function hasNavToggler(): bool
  {
    $defaultToggler = option('shallowred.navigation-menus.defaults.has-toggler', false);
    return $this->content()->hasNavToggler()->or($defaultToggler)->toBool();
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
    
    // Use configurable CSS class
    $togglerClass = option('shallowred.navigation-menus.css.nav-toggler-class', 'nav-toggler');
    $attrs['class'] = $togglerClass . ' button outline';
    
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
