<?php

return function ($item) {

  $link = $item->content()->link()->toUrl();
  $target = $item->content()->target()->toBool();
  $text = $item->content()->text()->or($link);

  $classes = ['button'];
  $classes[] = $item->content()->style()->value();
  $cssClasses = implode(' ', $classes);

  $buttonAttrs = [
    'href' => $link,
    'class' => $item->content()->variant()->value(),
    'target' => $target ? '_blank' : null,
    'rel' => $target ? 'noopener noreferrer' : null,

    'class' => $cssClasses,
    'role' => 'button',
    'aria-label' => $text,
  ];

  return compact([
    'item',
    'link',
    'buttonAttrs',
    'text'
  ]);
};
