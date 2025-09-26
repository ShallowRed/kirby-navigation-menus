<<?php echo $block->wrapper() ?>>
  <nav <?php echo attr($block->navAttrs()) ?>>
    <?php if ($block->content()->brand()->isNotEmpty()) : ?>
    <ul>
      <li><?php echo $block->content()->brand()->toBlocks() ?></li>
    </ul>
    <?php endif ?>
    <ul>
      <?php foreach ($block->content()->items()->toStructure() as $item) : ?>
      <li>
        <?php snippet('nav-items/' . $item->content()->type()->value(), compact('item')) ?>
      </li>
      <?php endforeach ?>
    </ul>
  </nav>
</<?php echo $block->wrapper() ?>>
