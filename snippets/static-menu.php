<?php
/**
 * Static Menu Snippet
 *
 * Renders the static menu content for no-JS mobile navigation.
 * Used by the static-menu template for better modularity.
 *
 * @var StaticMenuPage $page The static menu page instance
 */

use ShallowRed\NavigationMenus\Models\StaticMenuPage;

if (!($page instanceof StaticMenuPage)) {
    return;
}
?>

<header class="static-menu-header">
    <h1><?= esc($page->getMenuTitle()) ?></h1>
    <a href="<?= esc($page->getBackUrl()) ?>" class="close-button" <?php foreach ($page->getCloseButtonAttrs() as $attr => $value): ?><?= $attr ?>="<?= esc($value) ?>" <?php endforeach; ?>>
        <span aria-hidden="true">&times;</span>
        <span class="visually-hidden">Close menu</span>
    </a>
</header>

<main class="static-menu-content">
    <?php if ($page->hasNavigationMenu() && $page->hasNavPages()): ?>
        <nav class="static-navigation" role="navigation" aria-label="<?= esc($page->getMenuTitle()) ?>">
            <ul class="nav-list">
                <?php foreach ($page->getNavPages() as $navPage): ?>
                    <?= site()->renderNavItem($navPage, $page) ?>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php else: ?>
        <div class="no-menu-message">
            <p>No navigation menu found or menu is empty.</p>
            <a href="<?= esc($page->getBackUrl()) ?>">← Back to site</a>
        </div>
    <?php endif; ?>
</main>
