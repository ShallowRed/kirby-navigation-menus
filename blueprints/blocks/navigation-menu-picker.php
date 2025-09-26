<?php

return function () {

  $options = [];
  
  try {
    $declaredMenus = collection('declared-navigation-menus');
    
    if ($declaredMenus && is_array($declaredMenus)) {
      foreach ($declaredMenus as $key => $menu) {
        // Validate menu structure and sanitize data
        if (is_array($menu) && isset($menu['label']) && !empty($key)) {
          $options[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = [
            'text' => htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8'),
            'value' => htmlspecialchars($key, ENT_QUOTES, 'UTF-8'),
          ];
        }
      }
    }
  } catch (Exception $e) {
    if (option('debug', false)) {
      error_log("Navigation menu picker blueprint error: " . $e->getMessage());
    }
  }
  
  // Provide fallback if no menus available
  if (empty($options)) {
    $options[''] = [
      'text' => 'No menus available',
      'value' => '',
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
        'required' => true,
      ],
    ],
  ];
};
