<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>자유게시판</title>
  <script defer src="/assets/js/v2/view.js"></script>
  <?php echo link_tag('/assets/css/view.css'); ?>
</head>

<body>
  <div class="main">
    <div style="width: 100%; height: 30px; font-size:x-large; text-align:center">
      <a href="<?php echo "/board_v2" ?>">자유게시판</a>
    </div>
    <h2><?php echo  $board[0]['group_id'] . ". " . $board[0]['title']; ?></h2>
    <hr>
    <h3 style="text-align: center;">원글 입니다.</h3>
    <div class="post-container-origin">
      <div class="post-content"><?php echo $board[0]['content']; ?></div>
    </div>
    <hr style="margin: 20px auto 20px auto;">
    <div class="container-answer-btx"> <!-- 추후 css 추가 -->
      <!-- Content 출력 -->
      <?php
      $count = count($board);
      $id = $board[$count - 1]['id'];
      $group_id = $board[$count - 1]['group_id'];
      $tmp_board = array_slice($board, 1);
      $count -= 1;
      for ($i = 0; $i < $count; $i++): ?>
        <div class="post-container-answer">
          <?php if ($i == $count - 1) echo "현재 글 입니다.<hr>" ?>
          <div class="post-content"><?php echo $tmp_board[$i]['content']; ?></div>
        </div>
      <?php endfor ?>
      <!-- Content 출력 종료 -->
      <div class="btx-box">
        <div class="left">
          <form action="<?= $url = '/board_v2/view/' . $group_id;
                        site_url($url) ?>" method="post" style="display:inline;">
            <button type="submit">원글</button>
          </form>
        </div>
        <div class="middle-left">
          <form action="<?= site_url('board_v2') ?>" method="post" style="display:inline;">
            <button type="submit">이전 답글</button>
          </form>
        </div>
        <div class="middle-right">
          <form action="<?= site_url('board_v2') ?>" method="post" style="display:inline;">
            <button type="submit">다음 답글</button>
          </form>
        </div>
        <div class="right">
          <form action="<?= site_url('board_v2/set_content/' . $id) ?>" method="post" onsubmit="return submit_content(this);" style="display:inline;">
            <input type="hidden" name="content" value="">
            <button type="submit">답글 달기</button>
          </form>
        </div>
      </div>
    </div>
    <hr style="margin: 20px auto 20px auto;">
    <div class="post-container-comment">
      <button onclick="return open_comment(<?= $id ?>, 20, 1);">댓글창 열기</button>
      <button onclick="return close_comment();">댓글창 닫기</button>
      <div id="comment"></div>
    </div>
  </div>
</body>

</html>