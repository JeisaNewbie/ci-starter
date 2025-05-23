<!doctype html>

<head>
  <meta charset="UTF-8">
  <?php echo link_tag('/public_html/css/index.css'); ?>
  <title>게시판</title>
</head>

<body>
  <div id="board_area">
    <h1>자유게시판</h1>
    <h4>자유롭게 글을 쓸 수 있는 게시판입니다.</h4>
    <div id="write_btn">
      <a href="/board_v2/create"><button>글쓰기</button></a>
    </div>
    <table class="list-table">
      <thead>
        <tr>
          <th width="70">번호</th>
          <th width="500">제목</th>
          <!-- <th width="120">글쓴이</th> -->
          <th width="100">작성일</th>
          <!-- 추천수 항목 추가 -->
          <!-- <th width="100">추천수</th>
                  <th width="100">조회수</th> -->
        </tr>
      </thead>
      <?php foreach ($board as $board_item): ?>
        <div>
          <tr>
            <td width="70"><?php echo $board_item['group_id'] ?></td>
            <td width="500">
              <a href="<?php echo "/board_v2/view/" . $board_item['id'] ?>">
                <?php echo str_repeat("&emsp;", $board_item['depth']) . $board_item['title'] ?>
              </a>
            </td>
            <td width="120"><?php echo $board_item['created_at'] ?></td>
          </tr>
        </div>
      <?php endforeach ?>
    </table>
    <div id="write_btn" class="btx_box">
      <button onclick="before()">이전</button>
      <?php 
      $current_page = ($page_num <= 10 ? $page_num : 10);
      for ($i = 1, $j = $num; $i <= $current_page; $i++) 
      {
        echo "<a href=\"/board_v2/index/$j/$i\">$i </a>";
      }
      ?>
      <button onclick="after()">다음</button>
      <select id="pageLimitSelector">
        <option value="1" <?= $j == 1 ? 'selected' : '' ?>>1</option>
        <option value="10" <?= $j == 10 ? 'selected' : '' ?>>10</option>
        <option value="20" <?= $j == 20 ? 'selected' : '' ?>>20</option>
        <option value="50" <?= $j == 50 ? 'selected' : '' ?>>50</option>
        <option value="100" <?= $j == 100 ? 'selected' : '' ?>>100</option>
      </select>
    </div>
  </div>
</body>
<script>
  document.getElementById('pageLimitSelector').addEventListener('change', function() {
    const limit = this.value;
    const page = 1;
    window.location.href = `/board_v2/index/${limit}/${page}`;
  });

  function before() {
    alert('hello!');
  };

  function after() {
    if ($page_num < 10)
      return;
    $page_num -= 10;
    return $page_num <= 10 ? $page_num : 10;
  };
</script>
</html>