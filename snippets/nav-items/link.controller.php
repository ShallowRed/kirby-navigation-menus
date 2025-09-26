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

  $linkAttrs = [
    'href' => $url,
    'class' => !empty($variant) ? htmlspecialchars($variant, ENT_QUOTES, 'UTF-8') : null,
    'target' => $isTargetBlank ? '_blank' : null,
    'rel' => $isTargetBlank ? 'noopener noreferrer' : null,
    'aria-current' => $isCurrentPage ? 'page' : null
  ];

  return compact([
    'item',
    'link',
    'linkAttrs',
    'text'
  ]);
};
