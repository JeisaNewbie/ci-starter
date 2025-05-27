<?php echo link_tag('assets/css/create.css'); ?>
<?php echo validation_errors(); ?>
<script defer src="/assets/js/v2/create.js"></script>
<?php echo form_open('board_v2/create') ?>
<h2>게시글 생성</h2>

<label for="category" style="width: 100px;">Category</label>
<select id="categorySelector" name="category">
    <option value="NONE" selected>카테고리를 선택하세요.</option>
    <option value="GAME">GAME</option>
    <option value="MOVIE">MOVIE</option>
    <option value="MUSIC">MUSIC</option>
    <option value="SPORTS">SPORTS</option>
    <option value="TALK">TALK</option>
</select>

<label for="title" style="width: 100px;">Title</label>
<input type="input" name="title" /><br />

<label for="content">Content</label>
<textarea name="content"></textarea><br />

<input type="submit" name="submit" value="게시글 생성" />
<button type="button" onclick="location.href='/board_v2'">뒤로 가기</button>
</form>