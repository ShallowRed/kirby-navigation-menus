<?php

return function () {

  $fields = [];

  foreach (collection('declared-navigation-menus') as $menu) {
    $fields[$menu['name']] = [
      "width" => "1/2",
      "label" => $menu['label'],
      "type" => "blocks",
      "fieldsets" => [
        "navigation-menu-definer"
      ],
      "max" => 1,
    ];
  }

  return [
    'type' => 'fields',
    'fields' => $fields,
  ];
};
