<!doctype html>

<head>
  <meta charset="UTF-8">
  <?php echo link_tag('/assets/css/index.css'); ?>

  <script>
    window.boardVars = {
      category: <?= json_encode($category) ?>,
      num: <?= json_encode($num) ?>,
      data_num: <?= json_encode($data_num) ?>,
      before: <?= json_encode($pages['before']) ?>,
      after: <?= json_encode($pages['after']) ?>,
      search: <?= json_encode($search) ?>,
      start_page: <?= json_encode($pages['start_page']) ?>,
      end_page: <?= json_encode($pages['end_page']) ?>
    };
  </script>
  <script defer src="/assets/js/v2/index.js"></script>
  <title>게시판</title>
</head>
<?php $username = $this->session->userdata('username'); ?>

<body>
  <div id="board_area">
    <h1>자유게시판</h1>
    <h4>자유롭게 글을 쓸 수 있는 게시판입니다.</h4>
    <select id="pageLimitSelector">
      <option value="1" <?= $num == 1 ? 'selected' : '' ?>>1</option>
      <option value="10" <?= $num == 10 ? 'selected' : '' ?>>10</option>
      <option value="20" <?= $num == 20 ? 'selected' : '' ?>>20</option>
      <option value="50" <?= $num == 50 ? 'selected' : '' ?>>50</option>
      <option value="100" <?= $num == 100 ? 'selected' : '' ?>>100</option>
    </select>
    <select id="categorySelector" name="category">
      <option value="ALL" <?= $category == 'ALL' ? 'selected' : '' ?>>전체</option>
      <option value="GAME" <?= $category == 'GAME' ? 'selected' : '' ?>>게임</option>
      <option value="MOVIE" <?= $category == 'MOVIE' ? 'selected' : '' ?>>영화</option>
      <option value="MUSIC" <?= $category == 'MUSIC' ? 'selected' : '' ?>>음악</option>
      <option value="SPORTS" <?= $category == 'SPORTS' ? 'selected' : '' ?>>스포츠</option>
      <option value="TALK" <?= $category == 'TALK' ? 'selected' : '' ?>>잡담</option>
    </select>
    <button id="reload" type="button">조회</button>
    <div id="board_area_top" style="width: 500px; float: right; display:grid; grid-template-columns: 80px 90px; justify-content: end;">
      <?php if ($username): ?>
        <div id="write_btn">
          <a href="/board_v2/create"><button>글쓰기</button></a>
        </div>
      <?php else: ?>
        <div></div>
      <?php endif; ?>
      <?php if ($username): ?>
        <div id="write_btn">
          <a href="/board_v2/logout"><button>로그아웃</button></a>
        </div>
      <?php else: ?>
        <div id="write_btn">
          <a href="/board_v2/login"><button>로그인</button></a>
        </div>
      <?php endif; ?>
    </div>

    <table class="list-table">
      <thead>
        <tr>
          <th width="70">번호</th>
          <th width="500">제목</th>
          <th width="100">작성일</th>
        </tr>
      </thead>
      <?php foreach ($board as $board_item): ?>
        <div>
          <tr>
            <td width="70"><?php $board_num = $board_item['depth'] > 0 ? '' : $data_num; $data_num--; echo $board_num ?></td>
            <td width="500">
              <a href="<?php echo "/board_v2/view/" . $board_item['id'] ?>">
                <?php
                $depth = $board_item['depth'];
                $indent = str_repeat("&emsp;", $depth);
                $prefix = $depth > 0 ? '<span style="color: gray;">↳ </span>' : '';
                echo $indent . $prefix . htmlspecialchars($board_item['title']);
                ?>
              </a>
            </td>
            <td width="120"><?php echo $board_item['created_at'] ?></td>
          </tr>
        </div>
      <?php endforeach ?>
    </table>
  </div>
  <div class="search-container" data-num="<?= $num ?>" data-page="<?= $pages['before'] ?>">
    <input id="searchInput" type="text" class="search-input" placeholder="검색어를 입력하세요">
    <button onclick="return search()">검색</button>
  </div>
  <div id="write_btn" class="btx_box">
    <button onclick="return before()">이전</button>
    <?php
    for ($i = $pages['start_page']; $i <= $pages['end_page']; $i++) {
      echo "<a href=\"/board_v2/index?category=$category&search=$search&num=$num&page=$i\">$i </a>";
    }
    ?>
    <button onclick="return after()">다음</button>

  </div>
</body>

</html>