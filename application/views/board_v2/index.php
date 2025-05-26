<!doctype html>

<head>
  <meta charset="UTF-8">
  <?php echo link_tag('/assets/css/index.css'); ?>

  <script>
    window.boardVars = {
      num: <?= json_encode($num) ?>,
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
    <div id="board_area_top" style="display:grid; grid-template-columns: 90px 80px; justify-content: end;">
      <!-- <div id="write_btn">
        <a href="/board_v2/login"><button>로그인</button></a>
      </div> -->
      <?php if ($username): ?>
        <div id="write_btn">
          <a href="/board_v2/logout"><button>로그아웃</button></a>
        </div>
      <?php else: ?>
        <div id="write_btn">
          <a href="/board_v2/login"><button>로그인</button></a>
        </div>
      <?php endif; ?>
      <div id="write_btn">
        <a href="/board_v2/create"><button>글쓰기</button></a>
      </div>
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
            <td width="70"><?php echo $board_item['group_id'] ?></td>
            <td width="500">
              <a href="<?php echo "/board_v2/view/" . $board_item['id'] ?>">
                <?php
                $depth = (int)$board_item['depth'];
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
      echo "<a href=\"/board_v2/index/$num/$i?search=$search\">$i </a>";
    }
    ?>
    <button onclick="return after()">다음</button>
    <select id="pageLimitSelector">
      <option value="1" <?= $num == 1 ? 'selected' : '' ?>>1</option>
      <option value="10" <?= $num == 10 ? 'selected' : '' ?>>10</option>
      <option value="20" <?= $num == 20 ? 'selected' : '' ?>>20</option>
      <option value="50" <?= $num == 50 ? 'selected' : '' ?>>50</option>
      <option value="100" <?= $num == 100 ? 'selected' : '' ?>>100</option>
    </select>
  </div>
</body>

</html>