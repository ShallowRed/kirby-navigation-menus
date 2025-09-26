<?php

return function ($item) {

  // Validate item and link
  if (!$item || !$item->content()->link()) {
    return [
      'item' => $item,
      'link' => '',
      'buttonAttrs' => [],
      'text' => t('shallowred.navigation-menus.error.invalid-button')
    ];
  }

  $link = $item->content()->link()->toUrl();

  // Validate URL for security
  if (!site()->isValidUrl($link)) {
    return [
      'item' => $item,
      'link' => $link,
      'buttonAttrs' => [],
      'text' => t('shallowred.navigation-menus.error.invalid-link')
    ];
  }  $target = $item->content()->target()->toBool();

  // Sanitize text content
  $text = strip_tags($item->content()->text()->value());
  if (empty($text)) {
    $text = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
  }

  $classes = ['button'];

  // Use configured default variant if none specified
  $variant = $item->content()->variant()->value();
  if (empty($variant)) {
    $variant = option('shallowred.navigation-menus.defaults.button-variant', 'default');
  }
  if (!empty($variant)) {
    $classes[] = $variant;
  }

  $style = $item->content()->style()->value();
  if (!empty($style)) {
    $classes[] = $style;
  }
  $cssClasses = implode(' ', array_filter($classes));

  // Handle external links security
  $rel = null;
  if ($target) {
    $rel = 'noopener noreferrer';
  } else if (option('shallowred.navigation-menus.security.secure-external-links', true)) {
    // Check if it's an external link
    $parsed = parse_url($link);
    if (isset($parsed['host']) && $parsed['host'] !== parse_url(site()->url())['host']) {
      $rel = 'noopener noreferrer';
    }
  }

  $buttonAttrs = [
    'href' => $link,
    'class' => $cssClasses,
    'target' => $target ? '_blank' : null,
    'rel' => $rel,
    'role' => 'button',
    'aria-label' => $text,
  ];  return compact([
    'item',
    'link',
    'buttonAttrs',
    'text'
  ]);
};
