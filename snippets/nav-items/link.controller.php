<?php

return function ($item) {

  $link = $item->content()->link();
  $url = $link->toUrl();
  $page = $link->toPage();
  $menuHref = get('from');
  $isCurrentPage = page()->is($page) || $menuHref === $url;
  $isTargetBlank = $item->content()->target()->toBool();

  $text = $item->content()->text()->or($page->title())->or($url);

  $linkAttrs = [
    'href' => $url,
    'class' => $item->content()->variant()->value(),
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
