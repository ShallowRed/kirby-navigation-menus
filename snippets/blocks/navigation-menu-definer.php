<?php
/**
 * Navigation Menu Definer Block Snippet
 *
 * Uses checkbox hack for CSS-only mobile menu toggle.
 * The checkbox is placed before the nav element to enable sibling selectors.
 */
$toggleId = 'nav-toggle-' . $block->id();
?>
<?php if ($block->hasNavToggler()): ?>
    <input type="checkbox" id="<?= $toggleId ?>" class="nav-toggle-input" aria-hidden="true">
<?php endif ?>

<<?= $block->wrapper() ?> <?= attr($block->navAttrs()) ?>>
    <?php if ($block->content()->brand()->isNotEmpty()): ?>
        <ul class="nav-brand">
            <li>
                <?= $block->content()->brand()->toBlocks() ?>
            </li>
        </ul>
    <?php endif ?>

    <ul class="nav-items" id="<?= $block->getUniqueNavId() ?>">
        <?php foreach ($block->items() as $item): ?>
            <li><?= $item ?></li>
        <?php endforeach ?>
    </ul>

    <?php if ($block->hasNavToggler()): ?>
        <ul class="nav-togglers">
            <li>
                <label for="<?= $toggleId ?>" <?= attr($block->navTogglerAttrs()) ?>>
                    <span class="nav-toggler__closed">
                        <?= $block->menuIconClosed() ?>
                    </span>
                    <span class="nav-toggler__open">
                        <?= $block->menuIconOpen() ?>
                    </span>
                </label>
            </li>
        </ul>
    <?php endif ?>
</<?= $block->wrapper() ?>>
