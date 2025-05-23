<?php echo link_tag('assets/css/create.css'); ?>
<?php echo validation_errors(); ?>

<?php echo form_open('board_v2/create') ?>
    <h2>게시글 생성</h2>
    <label for="title">Title</label>
    <input type="input" name="title" /><br />

    <label for="content">Content</label>
    <textarea name="content"></textarea><br />

    <input type="submit" name="submit" value="Create board item" />

</form>