<<?php echo $block->wrapper() ?>>
  <nav <?php echo attr($block->navAttrs()) ?>>
    <?php if ($block->content()->brand()->isNotEmpty()) : ?>
    <ul class="nav-brand">
      <li><?php echo $block->content()->brand()->toBlocks() ?></li>
    </ul>
    <?php endif ?>
    <ul class="nav-items">
      <?php foreach ($block->content()->items()->toStructure() as $item) : ?>
      <li>
        <?php snippet('nav-items/' . $item->content()->type()->value(), compact('item')) ?>
      </li>
      <?php endforeach ?>
    </ul>
    <?php if ($block->hasNavToggler()) : ?>
    <ul class="nav-togglers" >
      <li>
        <a <?php echo attr($block->navTogglerAttrs()) ?>>
          <span class="nav-toggler__closed">
      <?php echo $block->menuIconClosed(); ?>
          </span>
          <span class="nav-toggler__open">
      <?php echo $block->menuIconOpen(); ?>
          </span>
        </a>
      </li>
    </ul>
    <?php endif ?>
  </nav>
</<?php echo $block->wrapper() ?>>
