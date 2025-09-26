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
  $variant = $item->content()->variant()->value();
  if (!empty($variant)) {
    $classes[] = $variant;
  }
  $style = $item->content()->style()->value();
  if (!empty($style)) {
    $classes[] = $style;
  }
  $cssClasses = implode(' ', array_filter($classes));

  $buttonAttrs = [
    'href' => $link,
    'class' => $cssClasses,
    'target' => $target ? '_blank' : null,
    'rel' => $target ? 'noopener noreferrer' : null,
    'role' => 'button',
    'aria-label' => $text,
  ];  return compact([
    'item',
    'link',
    'buttonAttrs',
    'text'
  ]);
};
