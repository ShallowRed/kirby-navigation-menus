<?php

return function () {

  foreach (collection('navigation-menus') as $key => $menu) {
    $options[$key] = [
      'text' => $menu['label'],
      'value' => $key,
    ];
  }

  return [
    'name' => 'Menu de navigation',
    'icon' => 'sitemap',
    'preview' => 'fields',
    'wysiwyg' => true,
    'fields' => [
      'menu' => [
        'label' => 'Menu',
        'type' => 'radio',
        'options' => $options,
      ],
    ],
  ];
};
