<?php

class NavMenuPickerBlock extends \Kirby\Cms\Block
{
  public function menu()
  {
    $key = $this->content()->menu()->value();
    $menu = site()->getMenu($key);

    return $menu;
  }
}
