<?php
$navigationProps = $site->navigationProps($key);
?>
<?php if ($navigationProps && $navigationProps['navPages']->count() > 0) : ?>
<nav <?php echo attr($navigationProps['attrs'] ?? []); ?>>
  <ul>
    <?php foreach ($navigationProps['navPages'] as $navPage) : ?>
    <li>
      <?php echo $site->renderNavItem($navPage, $page); ?>
    </li>
    <?php endforeach; ?>
  </ul>
</nav>
<?php endif; ?>
