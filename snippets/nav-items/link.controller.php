<?php

return function ($item) {

  $link = $item->content()->link()->toUrl();
  $target = $item->content()->target()->toBool();
  $text = $item->content()->text()->or($link);

  $linkAttrs = [
    'href' => $link,
    'class' => $item->content()->variant()->value(),
    'target' => $target ? '_blank' : null,
    'rel' => $target ? 'noopener noreferrer' : null,
  ];

  return compact([
    'item',
    'link',
    'linkAttrs',
    'text'
  ]);
};
