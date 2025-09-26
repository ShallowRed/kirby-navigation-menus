<?php

class NavMenuPickerBlock extends \Kirby\Cms\Block
{
  public function menu()
  {
    try {
      $key = $this->content()->menu()->value();

      // Validate key
      if (empty($key) || !is_string($key)) {
        return null;
      }

      $menu = site()->getMenu($key);
      return $menu;
    } catch (Exception $e) {
      if (option('debug', false)) {
        error_log("NavMenuPickerBlock error: " . $e->getMessage());
      }
      return null;
    }
  }
}
