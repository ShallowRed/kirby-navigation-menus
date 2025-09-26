<details class="dropdown">
  <summary><?php echo $item->content()->text()->value() ?></summary>
  <ul dir="rtl">
    <?php foreach ($item->content()->items()->toStructure() as $subItem) : ?>
    <li>
      <?php snippet('nav-items/' . $subItem->content()->type()->value(), ['item' => $subItem]) ?>
    </li>
    <?php endforeach ?>
  </ul>
</details>
