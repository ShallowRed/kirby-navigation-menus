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

    <?php if (site()->homePage() && method_exists(site()->homePage(), 'css')): ?>
        <?= css(site()->homePage()->css()->toFiles()) ?>
    <?php endif; ?>

    <style>
        /* Basic styles for static menu */
        .static-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .close-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .static-menu-content {
            padding: 1rem;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-list li {
            border-bottom: 1px solid #eee;
        }

        .nav-list a {
            display: block;
            padding: 0.75rem 0;
            text-decoration: none;
            color: inherit;
        }

        .nav-list a:hover {
            background-color: #f5f5f5;
        }

        .no-menu-message {
            text-align: center;
            padding: 2rem;
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    </style>
</head>

<body class="static-menu-page">
    <?= snippet('static-menu', ['page' => $page]) ?>
</body>
</html>
