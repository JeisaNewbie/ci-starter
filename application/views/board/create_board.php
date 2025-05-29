<?php echo link_tag('assets/css/create.css'); ?>
<?php echo validation_errors(); ?>
<script defer src="/assets/js/v2/create.js"></script>
<?php echo form_open('board/create_board') ?>
<h2>게시글 생성</h2>

<label for="category" style="width: 100px;">Category</label>
<select id="categorySelector" name="category">
    <option value="NONE" selected>카테고리를 선택하세요.</option>
    <option value="GAME">게임</option>
    <option value="MOVIE">영화</option>
    <option value="MUSIC">음악</option>
    <option value="SPORTS">스포츠</option>
    <option value="TALK">잡담</option>
</select>

<label for="title" style="width: 100px;">Title</label>
<input type="input" name="title" /><br />

<label for="content">Content</label>
<textarea name="content"></textarea><br />

<input type="submit" name="submit" value="게시글 생성" />
<button type="button" onclick="location.href='/board'">뒤로 가기</button>
</form>