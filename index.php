<?php

Kirby::plugin('shallowred/navigation-menus', [

  'collections' => [
    'navigation-menus' => function () {
      return option('shallowred.navigation-menus.menus');
    },
  ],

  'blueprints' => [
    'blocks/navigation-menu' => include __DIR__ . '/blueprints/blocks/navigation-menu.php',
    'sections/navigation-menus' => include __DIR__ . '/blueprints/sections/navigation-menus.php',
  ],

  'snippets' => [
    'blocks/navigation-menu' => __DIR__ . '/snippets/blocks/navigation-menu.php',
    'navigation-menu' => __DIR__ . '/snippets/navigation-menu.php',
  ],

  'siteMethods' => [

    'navPages' => function ($key) {
      $menu = collection('navigation-menus')[$key] ?? null;
      if (is_array($menu) && isset($menu['name'])) {
        $navPages = $this->content()->get($menu['name']);
          if ($navPages) {
            return $navPages->toStructure();
          }
        }
      return null;
    },

    'navigationProps' => function ($key) {
      $menu = collection('navigation-menus')[$key] ?? null;
      $navPages = $this->navPages($key);
      return [
        'navPages' => $navPages ?? null,
        'attrs' => [
          'id' => $menu['id'],
          'aria-label' => $menu['ariaLabel'],
        ],
      ];
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

      $isCurrent = $currentPage->isCurrentPage($navPage);

      return Html::a(
        $link->toUrl(),
        [$icon . Html::span($text)],
        [
          'aria-current' => $isCurrent ? 'page' : null,
          'tabindex' => $isCurrent ? '-1' : null,
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
