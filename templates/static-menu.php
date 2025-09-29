<?php
/**
 * Static Menu Template
 *
 * Template for static menu pages created by dynamic routes.
 * Provides no-JS fallback for mobile navigation.
 *
 * @var StaticMenuPage $page The static menu page instance
 */

use ShallowRed\NavigationMenus\Models\StaticMenuPage;
?>
<!DOCTYPE html>
<html lang="<?= site()->language() ? site()->language()->code() : 'en' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($page->getMenuTitle()) ?> | <?= esc(site()->title()) ?></title>
      <?php echo vite()->css("vite.entry.js"); ?>
</head>

<body class="static-menu-page no-js">
    <?= snippet('static-menu', ['page' => $page]) ?>
    <?php echo vite()->js("vite.entry.js");?>
</body>
</html>
