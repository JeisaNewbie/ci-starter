<!doctype html>
<head>
<meta charset="UTF-8">
<?php echo link_tag('public_html/css/index.css'); ?>
<title>게시판</title>
</head>
<body>
<div id="board_area"> 
  <h1>자유게시판</h1>
  <h4>자유롭게 글을 쓸 수 있는 게시판입니다.</h4>
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
                        <a href="<?php echo "board/".$board_item['group_id'] ?>">
                            <?php echo $board_item['title']?>
                        </a>
                    </td>
                    <td width="120"><?php echo $board_item['created_at']?></td>
                </tr>
            </div>
        <?php endforeach ?>
    </table>
    <div id="write_btn">
      <a href="board/create"><button>글쓰기</button></a>
    </div>
  </div>
</body>
</html>