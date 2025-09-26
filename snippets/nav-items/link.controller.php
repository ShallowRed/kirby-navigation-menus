<?php

return function ($item) {

  // Validate item and link
  if (!$item || !$item->content()->link()) {
    return [
      'item' => $item,
      'link' => null,
      'linkAttrs' => [],
      'text' => ''
    ];
  }

  $link = $item->content()->link();
  $url = $link->toUrl();

  // Validate URL for security
  if (!site()->isValidUrl($url)) {
    return [
      'item' => $item,
      'link' => $link,
      'linkAttrs' => [],
      'text' => t('shallowred.navigation-menus.error.invalid-link')
    ];
  }

  $page = $link->toPage();
  $menuHref = get('from');
  $isCurrentPage = $page && page()->is($page) || $menuHref === $url;
  $isTargetBlank = $item->content()->target()->toBool();

  // Sanitize text content
  $text = strip_tags($item->content()->text()->value());
  if (empty($text)) {
    $text = $page ? strip_tags($page->title()->value()) : '';
  }
  if (empty($text)) {
    $text = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
  }

  $variant = $item->content()->variant()->value();
  $classes = [];
  
  // Add variant class if specified
  if (!empty($variant)) {
    $classes[] = htmlspecialchars($variant, ENT_QUOTES, 'UTF-8');
  }
  
  // Add current page class if this is the current page
  if ($isCurrentPage) {
    $currentPageClass = option('shallowred.navigation-menus.css.current-page-class', 'current');
    $classes[] = $currentPageClass;
  }
  
  // Handle external links security
  $rel = null;
  if ($isTargetBlank) {
    $rel = 'noopener noreferrer';
  } else if (option('shallowred.navigation-menus.security.secure-external-links', true)) {
    // Check if it's an external link
    $parsed = parse_url($url);
    if (isset($parsed['host']) && $parsed['host'] !== parse_url(site()->url())['host']) {
      $rel = 'noopener noreferrer';
    }
  }
  
  $linkAttrs = [
    'href' => $url,
    'class' => !empty($classes) ? implode(' ', $classes) : null,
    'target' => $isTargetBlank ? '_blank' : null,
    'rel' => $rel,
    'aria-current' => $isCurrentPage ? 'page' : null
  ];  return compact([
    'item',
    'link',
    'linkAttrs',
    'text'
  ]);
};
