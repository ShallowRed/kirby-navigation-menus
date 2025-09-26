<?php 
$dropdownClass = option('shallowred.navigation-menus.css.dropdown-class', 'dropdown');
$ariaExpanded = option('shallowred.navigation-menus.accessibility.aria-expanded', true);
?>
<details class="<?= $dropdownClass ?>"<?= $ariaExpanded ? ' aria-expanded="false"' : '' ?>>
  <summary><?= htmlspecialchars($item->content()->text()->value(), ENT_QUOTES, 'UTF-8') ?></summary>
  <ul dir="rtl">
    <?php foreach ($item->content()->items()->toStructure() as $subItem) : ?>
    <li>
      <?php snippet('nav-items/' . $subItem->content()->type()->value(), ['item' => $subItem]) ?>
    </li>
    <?php endforeach ?>
  </ul>
</details>
