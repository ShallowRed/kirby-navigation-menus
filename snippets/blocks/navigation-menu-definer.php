<?php
/**
 * Navigation Menu Definer Block Snippet
 */
?>
<<?= $block->wrapper() ?> <?= attr($block->navAttrs()) ?>>
    <?php if ($block->content()->brand()->isNotEmpty()): ?>
        <ul class="nav-brand">
            <li>
                <?php if ($block->shouldBrandBeLinked()): ?>
                    <a <?= attr($block->getBrandLinkAttrs()) ?>>
                        <?= $block->content()->brand()->toBlocks() ?>
                    </a>
                <?php else: ?>
                    <?= $block->content()->brand()->toBlocks() ?>
                <?php endif ?>
            </li>
        </ul>
    <?php endif ?>

    <ul class="nav-items" id="<?= $block->getUniqueNavId() ?>">
        <?php foreach ($block->content()->items()->toBlocks() as $item): ?>
            <li><?= $item ?></li>
        <?php endforeach ?>
    </ul>

    <?php if ($block->hasNavToggler()): ?>
        <ul class="nav-togglers">
            <li>
                <a href="<?= $block->getStaticMenuUrl() ?>" <?= attr($block->navTogglerAttrs()) ?>>
                    <span class="nav-toggler__closed">
                        <?= $block->menuIconClosed() ?>
                    </span>
                    <span class="nav-toggler__open">
                        <?= $block->menuIconOpen() ?>
                    </span>
                </a>
            </li>
        </ul>
    <?php endif ?>
</<?= $block->wrapper() ?>>
