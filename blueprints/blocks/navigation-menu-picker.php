<?php

return function () {

  foreach (collection('declared-navigation-menus') as $key => $menu) {
    $options[$key] = [
      'text' => $menu['label'],
      'value' => $key,
    ];
  }

  return [
    'name' => 'Réutiliser un menu de navigation global',
    'icon' => 'menu',
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
