<?php echo link_tag('public_html/css/view.css'); ?>

<!-- <?php echo validation_errors(); ?> -->

<div class="main">
  <h2><?php echo  $board[0]['group_id'] . ". " . $board[0]['title']; ?></h2>
  <div class="post-container-origin">
    <div class="post-content">
      <?php echo $board[0]['content']; ?>
      <div class="btx-align">
        <div class="left">
          <form action="<?= site_url('board') ?>" method="post" style="display:inline;">
            <button type="submit">뒤로가기</button>
          </form>
        </div>
        <div class="right">
          <form action="<?= site_url('board/delete/' . $board[0]['id']) ?>" method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');" style="display:inline;">
            <button type="submit">삭제</button>
          </form>
        </div>
      </div>
      <div class="">
        <details>
          <summary>댓글 달기</summary>
          <form action="<?= site_url('board/set_comment/' . $board[0]['id'] . '/' . $board[0]['group_id']) ?>" method="post" style="display:inline;">
            <label for="content">댓글</label>
            <textarea name="content"></textarea><br />
            <button type="submit">댓글 달기</button>
          </form>
        </details>
        <details>
          <summary>답글 달기</summary>
          <form action="<?= site_url('board/set_comment/' . $board[0]['id'] . '/' . $board[0]['group_id']) ?>" method="post" style="display:inline;">
            <label for="content">답글</label>
            <textarea name="content"></textarea><br />
            <button type="submit">답글 달기</button>
          </form>
        </details>
      </div>
    </div>
    <div class="load-content">
      <form action="<?= site_url('board') ?>" method="post" style="display:inline;">
        <button type="submit">이전 답글</button>
      </form>
      <form action="<?= site_url('board') ?>" method="post" style="display:inline;">
        <button type="submit">다음 답글</button>
      </form>
    </div>
    <div>
      <?php foreach ($board as $board_item): ?>
        <div class="post-container-inside">
          <div class="post-content">
            <?php echo $board_item['content']; ?>
          </div>
          <div>
            <div class="right">
              <form action="<?= site_url('board/delete/' . $board_item['id']) ?>" method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');" style="display:inline;">
                <button type="submit">삭제</button>
              </form>
            </div>
            <div>
              <details>
                <summary>댓글 달기</summary>
                <form action="<?= site_url('board/set_comment/' . $board_item['id'] . '/' . $board_item['group_id']) ?>" method="post" style="display:inline;">
                  <label for="content">댓글</label>
                  <textarea name="content"></textarea><br />
                  <button type="submit">댓글 달기</button>
                </form>
              </details>
              <details>
                <summary>답글 달기</summary>
                <form action="<?= site_url('board/set_comment/' . $board_item['id'] . '/' . $board_item['group_id']) ?>" method="post" style="display:inline;">
                  <label for="content">답글</label>
                  <textarea name="content"></textarea><br />
                  <button type="submit">답글 달기</button>
                </form>
              </details>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>