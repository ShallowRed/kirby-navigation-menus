<?php

return function () {

  $fields = [];

  foreach (collection('navigation-menus') as $menu) {
    // $fields[$menu['name']] = [
    //   "width" => "1/2",
    //   "type" => "pages",
    //   "label" => $menu['label'],
    // ];
    $fields[$menu['name']] = [
      "width" => "1/2",
      "label" => $menu['label'],
      "type" => "structure",
      "fields" => [
        "link" => [
          "label" => "Lien",
          "type" => "link",
          "options" => [
            "page",
            "url",
            "anchor",
          ],
        ],
        "text" => [
          "label" => "Texte affiché",
          "help" => "Optionnel, utilise le nom de la page ou l'url si vide",
          "type" => "text",
        ],
      ],
    ];
  }

  return [
    'type' => 'fields',
    'fields' => $fields,
  ];
};
