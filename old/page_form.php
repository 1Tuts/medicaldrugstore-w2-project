<?php // this page is included by new_page.php and edit_page.php ?>
<?php if (!isset($new_page)) {$new_page = false;} ?>

<p>نام صفحه: <input type="text" name="menu_name" value="<?php echo $sel_page['menu_name']; ?>" id="menu_name" /></p>

<p>موقعیت: <select name="position">
	<?php
		if (!$new_page) {
			$page_set = get_pages_for_subject($sel_page['subject_id']);
			$page_count = mysql_num_rows($page_set);
		} else {
			$page_set = get_pages_for_subject($sel_subject['id']);
			$page_count = mysql_num_rows($page_set) + 1;
		}
		for ($count=1; $count <= $page_count; $count++) {
			echo "<option value=\"{$count}\"";
			if ($sel_page['position'] == $count) { echo " selected"; }
			echo ">{$count}</option>";
		}
	?>
</select></p>
<p>قابل مشاهده: 
	<input type="radio" name="visible" value="0"<?php 
	if ($sel_page['visible'] == 0) { echo " checked"; } 
	?> /> خیر
	&nbsp;
	<input type="radio" name="visible" value="1"<?php 
	if ($sel_page['visible'] == 1) { echo " checked"; } 
	?> /> بله
</p>
<p>متن:<br />
	<textarea name="content" rows="20" cols="80"><?php echo $sel_page['content']; ?></textarea>
</p>