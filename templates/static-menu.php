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
</head>

<body class="static-menu-page">
    <?php snippet('static-menu', ['page' => $page]) ?>
    You should override this template in your site to match your sites' html templating, including loading your CSS and JS assets as needed.
</body>
</html>
