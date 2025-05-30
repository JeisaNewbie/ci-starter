<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>자유게시판</title>
  <script defer src="/assets/js/v2/view.js"></script>
  <?php echo link_tag('/assets/css/view.css'); ?>
  <?php $username = $this->session->userdata('username'); ?>
</head>

<body>
  <div class="main">
    <div style="width: 100%; height: 30px; font-size:x-large; text-align:center">
      <a href="<?php echo "/board" ?>">자유게시판</a>
    </div>
    <h2><?php echo '제목: ' . $board[0]['title']; ?></h2>
    <hr>
    <h3 style="text-align: center;">원글 입니다.</h3>
    <div class="post-container-origin">
      <div class="post-content"><?php echo $board[0]['content']; ?></div>
    </div>
    <hr style="margin: 20px auto 20px auto;">
    <div class="container-answer-btx">
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
        <?php if ($id != $group_id): ?>
          <div class="left">
            <form action="<?= $url = '/board/view/' . $group_id;
                          site_url($url) ?>" method="post" style="display:inline;">
              <button type="submit">원글</button>
            </form>
          </div>
        <?php else: ?>
          <div></div>
        <?php endif; ?>
        <div class="right">
          <?php if ($username): ?>
            <button onclick="return submit_content(<?= $id ?>);">답글 달기</button>
          <?php else: ?>
            <div></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="post-container-comment">
      <!-- onclick="return open_comment(<?= $id ?>, 10, 1, <?= $username != null ? 1 : null ?>);" -->
      <button id="open_comment_btx" data-id= <?= $id ?> data-username= <?= $username ?>>댓글창 열기</button>
      <button onclick="return close_comment();">댓글창 닫기</button>
      <?php if ($username): ?>
        <button onclick="return update_content(<?= $id ?>);" type="button">수정</button>
      <?php else: ?>
        <div></div>
      <?php endif; ?>
      <?php if ($username): ?>
        <button onclick="return delete_board(<?= $id ?>);" type="button">삭제</button>
      <?php else: ?>
        <div></div>
      <?php endif; ?>
      <div id="comment"></div>
    </div>
  </div>
</body>

</html>