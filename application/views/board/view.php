<?php echo link_tag('public_html/css/view.css'); ?>
<div class="post-container">
  <h2><?php echo $board_item['title']; ?></h2>
  <div class="post-content">
    <?php echo $board_item['content']; ?>
  </div>
</div>
