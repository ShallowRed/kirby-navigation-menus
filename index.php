<?php

require_once __DIR__ . '/models/navigation-menu-definer.php';
require_once __DIR__ . '/models/navigation-menu-picker.php';

Kirby::plugin('shallowred/navigation-menus', [

  'collections' => [
    'declared-navigation-menus' => function () {
      return option('shallowred.navigation-menus.declared-navigation-menus');
    },
  ],

  'blueprints' => [
    'blocks/navigation-menu-definer' => __DIR__ . '/blueprints/blocks/navigation-menu-definer.yml',
    'blocks/navigation-menu-picker' => include __DIR__ . '/blueprints/blocks/navigation-menu-picker.php',
    'fields/nav-items' => __DIR__ . '/blueprints/fields/nav-items.yml',
    'sections/declared-navigation-menus' => include __DIR__ . '/blueprints/sections/declared-navigation-menus.php',
  ],

  'snippets' => [
    'blocks/navigation-menu-picker' => __DIR__ . '/snippets/blocks/navigation-menu-picker.php',
    'blocks/navigation-menu-definer' => __DIR__ . '/snippets/blocks/navigation-menu-definer.php',
    'nav-items/dropdown' => __DIR__ . '/snippets/nav-items/dropdown.php',
    'nav-items/link' => __DIR__ . '/snippets/nav-items/link.php',
    'nav-items/link.controller' => __DIR__ . '/snippets/nav-items/link.controller.php',
    'nav-items/button' => __DIR__ . '/snippets/nav-items/button.php',
    'nav-items/button.controller' => __DIR__ . '/snippets/nav-items/button.controller.php',
  ],


  'blockModels' => [
    'navigation-menu-definer' => NavMenuDefinerBlock::class,
    'navigation-menu-picker' => NavMenuPickerBlock::class,
  ],

  'siteMethods' => [

    'getMenu' => function ($key) {
      $menu = collection('declared-navigation-menus')[$key] ?? null;
      if (is_array($menu) && isset($menu['name'])) {
        $navPages = $this->content()->get($menu['name']);
        if ($navPages) {
          return $navPages;
        }
        }
      return null;
    },

    'navPages' => function ($key) {
      $menu = collection('declared-navigation-menus')[$key] ?? null;
      if (is_array($menu) && isset($menu['name'])) {
        $navPages = $this->content()->get($menu['name']);
          if ($navPages) {
            return $navPages->toBlocks();
          }
        }
      return null;
    },

    'renderNavItem' => function ($navPage, $currentPage) {
      $icon = !$currentPage->isCurrentPage($navPage)
        ? ''
        : Html::tag('span', '', ['class' => 'current-page-icon']);

      $link = $navPage->content()->link();
      $text = $navPage->content()->text()->value();

      if (empty($text) === true) {
        $text = $this->pages()->find($link)?->title();
      }

      if (empty($text) === true) {
        $text = $link->value();
      }

      $isCurrent = $currentPage->isCurrentPage($navPage) || param('from') === $link->toUrl();

      return Html::a(
        $link->toUrl(),
        [$icon . Html::span($text)],
        [
          'aria-current' => $isCurrent ? 'page' : null,
          'tabindex' => $isCurrent ? '-1' : "0",
        ],
      );
    }
  ],

  'pageMethods' => [

    'isCurrentPage' => function ($navItem) {
      $page = page($navItem->link());
      return $this->slug() === $page->slug();
    },

    'isInMenu' => function ($key) {
      $navPages = site()->navPages($key) ?? null;
      if (!$navPages) {
        return false;
      }
      $plucked = A::map($navPages->pluck('link'), function ($link) {
        return $link->value();
      });
      $uuid = 'page://' . $this->content()->uuid()->value();
      $isInMenu = in_array($uuid, $plucked);
      return $isInMenu;
    },

    'prevInMenu' => function ($key) {
      $navPages = site()->navPages($key) ?? null;
      if (!$navPages) {
        return null;
      }

      $plucked = A::map($navPages->pluck('link'), function ($link) {
        return $link->value();
      });
      $uuid = 'page://' . $this->content()->uuid()->value();
      $index = array_search($uuid, $plucked);

      if ($index === 0) {
        return null;
      }

      return page($navPages->nth($index - 1)->link());
    },

    'nextInMenu' => function ($key) {
      $navPages = site()->navPages($key) ?? null;
      if (!$navPages) {
        return null;
      }

      $plucked = A::map($navPages->pluck('link'), function ($link) {
        return $link->value();
      });
      $uuid = 'page://' . $this->content()->uuid()->value();
      $index = array_search($uuid, $plucked);

      if ($index === $navPages->count() - 1) {
        return null;
      }

      return page($navPages->nth($index + 1)->link());
    },

    'hasPrevInMenu' => function ($key) {
      return $this->prevInMenu($key) !== null;
    },

    'hasNextInMenu' => function ($key) {
      return $this->nextInMenu($key) !== null;
    },
  ],
]);
