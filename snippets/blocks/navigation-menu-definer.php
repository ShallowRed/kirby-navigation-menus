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
        <?php foreach ($block->content()->items()->toStructure() as $item): ?>
            <li>
                <?= snippet('nav-items/' . $item->content()->type()->value(), compact('item')) ?>
            </li>
        <?php endforeach ?>
    </ul>

    <?php if ($block->hasNavToggler()): ?>
        <ul class="nav-togglers">
            <li>
                <button <?= attr($block->navTogglerAttrs()) ?>>
                    <span class="nav-toggler__closed">
                        <?= $block->menuIconClosed() ?>
                    </span>
                    <span class="nav-toggler__open">
                        <?= $block->menuIconOpen() ?>
                    </span>
                </button>
            </li>
        </ul>

        <!-- No-JS fallback for mobile navigation -->
        <noscript>
            <ul class="nav-fallback">
                <li>
                    <a <?= attr($block->noscriptLinkAttrs()) ?>>
                        <?= $block->menuIconClosed() ?> Menu
                    </a>
                </li>
            </ul>
        </noscript>
    <?php endif ?>
</<?= $block->wrapper() ?>>
