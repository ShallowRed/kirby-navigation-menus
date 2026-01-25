<details class="<?= $block->dropdownClass() ?>"<?= $block->hasAriaExpanded() ? ' aria-expanded="false"' : '' ?>>
  <summary><?= $block->text() ?></summary>
  <ul dir="rtl">
    <?php foreach ($block->items() as $item): ?>
    <li><?= $item ?></li>
    <?php endforeach ?>
  </ul>
</details>
