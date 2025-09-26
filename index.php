<?php

use Kirby\Uuid\Uuid;

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

  'templates' => [
    'static-menu' => __DIR__ . '/templates/static-menu.php',
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
      // Validate input
      if (empty($key) || !is_string($key)) {
        return null;
      }

      try {
        $declaredMenus = collection('declared-navigation-menus');
        if (!$declaredMenus || !is_array($declaredMenus)) {
          return null;
        }

        $menu = $declaredMenus[$key] ?? null;
        if (!is_array($menu) || !isset($menu['name']) || empty($menu['name'])) {
          return null;
        }

        $navPages = $this->content()->get($menu['name']);
        if ($navPages && $navPages->isNotEmpty()) {
          return $navPages;
        }
      } catch (Exception $e) {
        // Log error in development mode
        if (option('debug', false)) {
          error_log("Navigation menu error for key '{$key}': " . $e->getMessage());
        }
      }

      return null;
    },

    'navPages' => function ($key) {
      // Validate input
      if (empty($key) || !is_string($key)) {
        return null;
      }

      try {
        $declaredMenus = collection('declared-navigation-menus');
        if (!$declaredMenus || !is_array($declaredMenus)) {
          return null;
        }

        $menu = $declaredMenus[$key] ?? null;
        if (!is_array($menu) || !isset($menu['name']) || empty($menu['name'])) {
          return null;
        }

        $navPages = $this->content()->get($menu['name']);
        if ($navPages && $navPages->isNotEmpty()) {
          return $navPages->toBlocks();
        }
      } catch (Exception $e) {
        // Log error in development mode
        if (option('debug', false)) {
          error_log("Navigation pages error for key '{$key}': " . $e->getMessage());
        }
      }

      return null;
    },

    'renderNavItem' => function ($navPage, $currentPage) {
      // Validate inputs
      if (!$navPage || !$currentPage) {
        return '';
      }

      try {
        $icon = !$currentPage->isCurrentPage($navPage)
          ? ''
          : Html::tag('span', '', ['class' => 'current-page-icon']);

        $link = $navPage->content()->link();
        if (!$link) {
          return '';
        }

        // Sanitize text content
        $text = strip_tags($navPage->content()->text()->value());

        if (empty($text) === true) {
          $linkedPage = $this->pages()->find($link);
          $text = $linkedPage ? strip_tags($linkedPage->title()->value()) : '';
        }

        if (empty($text) === true) {
          $linkValue = $link->value();
          // Basic URL validation and sanitization
          if (filter_var($linkValue, FILTER_VALIDATE_URL) || strpos($linkValue, 'page://') === 0) {
            $text = htmlspecialchars($linkValue, ENT_QUOTES, 'UTF-8');
          } else {
            return ''; // Invalid link
          }
        }

        $linkUrl = $link->toUrl();

        // Validate URL to prevent XSS
        if (!$this->isValidUrl($linkUrl)) {
          return '';
        }

        $isCurrent = $currentPage->isCurrentPage($navPage) || param('from') === $linkUrl;

        return Html::a(
            $linkUrl,
            [$icon . Html::span(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'))],
            [
              'aria-current' => $isCurrent ? 'page' : null,
              'tabindex' => $isCurrent ? '-1' : "0",
            ],
        );
      } catch (Exception $e) {
        if (option('debug', false)) {
          error_log("Render nav item error: " . $e->getMessage());
        }
        return '';
      }
    },

    'isValidUrl' => function ($url) {
      // Allow internal Kirby URLs, relative URLs, and valid external URLs
      if (empty($url)) {
        return false;
      }

      // Allow relative URLs and anchors
      if (strpos($url, '/') === 0 || strpos($url, '#') === 0) {
        return true;
      }

      // Allow page:// protocol for Kirby internal links
      if (strpos($url, 'page://') === 0) {
        return true;
      }

      // Validate external URLs and block dangerous protocols
      $parsed = parse_url($url);
      if (!$parsed || !isset($parsed['scheme'])) {
        return false;
      }

      $allowedSchemes = ['http', 'https', 'mailto', 'tel'];
      return in_array(strtolower($parsed['scheme']), $allowedSchemes);
    }
  ],

  'pageMethods' => [

    'isCurrentPage' => function ($navItem) {
      try {
        if (!$navItem || !$navItem->link()) {
          return false;
        }

        $page = page($navItem->link());
        if (!$page) {
          return false;
        }

        return $this->slug() === $page->slug();
      } catch (Exception $e) {
        if (option('debug', false)) {
          error_log("isCurrentPage error: " . $e->getMessage());
        }
        return false;
      }
    },

    'isInMenu' => function ($key) {
      try {
        // Validate input
        if (empty($key) || !is_string($key)) {
          return false;
        }

        $navPages = site()->navPages($key);
        if (!$navPages || $navPages->count() === 0) {
          return false;
        }

        // Check if current page has UUID
        $currentUuid = $this->content()->uuid();
        if (!$currentUuid || $currentUuid->isEmpty()) {
          return false;
        }

        $plucked = A::map($navPages->pluck('link'), function ($link) {
          return $link ? $link->value() : '';
        });

        // Remove empty values
        $plucked = array_filter($plucked);

        $uuid = 'page://' . $currentUuid->value();
        return in_array($uuid, $plucked);
      } catch (Exception $e) {
        if (option('debug', false)) {
          error_log("isInMenu error for key '{$key}': " . $e->getMessage());
        }
        return false;
      }
    },

    'prevInMenu' => function ($key) {
      try {
        // Validate input
        if (empty($key) || !is_string($key)) {
          return null;
        }

        $navPages = site()->navPages($key);
        if (!$navPages || $navPages->count() === 0) {
          return null;
        }

        // Check if current page has UUID
        $currentUuid = $this->content()->uuid();
        if (!$currentUuid || $currentUuid->isEmpty()) {
          return null;
        }

        $plucked = A::map($navPages->pluck('link'), function ($link) {
          return $link ? $link->value() : '';
        });

        // Remove empty values
        $plucked = array_filter($plucked);

        $uuid = 'page://' . $currentUuid->value();
        $index = array_search($uuid, $plucked);

        if ($index === false || $index === 0) {
          return null;
        }

        $prevItem = $navPages->nth($index - 1);
        if (!$prevItem || !$prevItem->link()) {
          return null;
        }

        return page($prevItem->link());
      } catch (Exception $e) {
        if (option('debug', false)) {
          error_log("prevInMenu error for key '{$key}': " . $e->getMessage());
        }
        return null;
      }
    },

    'nextInMenu' => function ($key) {
      try {
        // Validate input
        if (empty($key) || !is_string($key)) {
          return null;
        }

        $navPages = site()->navPages($key);
        if (!$navPages || $navPages->count() === 0) {
          return null;
        }

        // Check if current page has UUID
        $currentUuid = $this->content()->uuid();
        if (!$currentUuid || $currentUuid->isEmpty()) {
          return null;
        }

        $plucked = A::map($navPages->pluck('link'), function ($link) {
          return $link ? $link->value() : '';
        });

        // Remove empty values
        $plucked = array_filter($plucked);

        $uuid = 'page://' . $currentUuid->value();
        $index = array_search($uuid, $plucked);

        if ($index === false || $index >= $navPages->count() - 1) {
          return null;
        }

        $nextItem = $navPages->nth($index + 1);
        if (!$nextItem || !$nextItem->link()) {
          return null;
        }

        return page($nextItem->link());
      } catch (Exception $e) {
        if (option('debug', false)) {
          error_log("nextInMenu error for key '{$key}': " . $e->getMessage());
        }
        return null;
      }
    },

    'hasPrevInMenu' => function ($key) {
      return $this->prevInMenu($key) !== null;
    },

    'hasNextInMenu' => function ($key) {
      return $this->nextInMenu($key) !== null;
    },
  ],

  'routes' => [
    [
      'pattern' => 'menu',
      'action'  => function () {
          return Page::factory([
            'slug' => 'static-menu',
            'template' => 'static-menu',
            'model' => 'static-menu',
            'content' => [
              'title' => 'Menu statique',
              'uuid'  => Uuid::generate(),
            ]
          ]);
      }
    ]
  ],

]);
