<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>
<?php
		if (intval($_GET['subj']) == 0) {
			redirect_to("content.php");
		}
		if (isset($_POST['submit'])) {
			$errors = array();

			$required_fields = array('menu_name', 'position', 'visible');
			foreach($required_fields as $fieldname) {
				if (!isset($_POST[$fieldname]) || (empty($_POST[$fieldname]) && !is_numeric($_POST[$fieldname]))) { 
					$errors[] = $fieldname; 
				}
			}
			$fields_with_lengths = array('menu_name' => 30);
			foreach($fields_with_lengths as $fieldname => $maxlength ) {
				if (strlen(trim(mysql_prep($_POST[$fieldname]))) > $maxlength) { $errors[] = $fieldname; }
			}
			
			if (empty($errors)) {
				// Perform Update
				$id = mysql_prep($_GET['subj']);
				$menu_name = mysql_prep($_POST['menu_name']);
				$position = mysql_prep($_POST['position']);
				$visible = mysql_prep($_POST['visible']);
				
				$query = "UPDATE subjects SET 
							menu_name = '{$menu_name}', 
							position = {$position}, 
							visible = {$visible} 
						WHERE id = {$id}";
				$result = mysql_query($query, $connection);
				if (mysql_affected_rows() == 1) {
					// Success
					$message = "The subject was successfully updated.";
				} else {
					// Failed
					$message = "The subject update failed.";
					$message .= "<br />". mysql_error();
				}
				
			} else {
				// Errors occurred
				$message = "There were " . count($errors) . " errors in the form.";
			}
			
			
			
			
		} // end: if (isset($_POST['submit']))
?>
<?php find_selected_page(); ?>
<?php include('inc/header.php') ?>
    	
        <div class="main">
        	<div class="menu">
                <div class="up">
                	<div class="pic1"> 
                    </div>
                </div>
                <div class="down">
                	<?php echo navigation($sel_subject, $sel_page); ?>
                    <br/>
                    <div class="newlink">
                    	<a href="new_subject.php" >+اضافه کردن موضوع جدید</a>
                    </div>
                </div>
            </div>
            <div class="content">
            	<div class="subject">
                	<div class="pagename">
                   
                    </div>
                   
                   
                </div>
                <div class="text">
                	
                   <h2>ویرایش موضوع: <?php echo $sel_subject['menu_name']; ?></h2>
			<?php if (!empty($message)) {
				echo "<p class=\"message\">" . $message . "</p>";
			} ?>
			<?php
			// output a list of the fields that had errors
			if (!empty($errors)) {
				echo "<p class=\"errors\">";
				echo "Please review the following fields:<br />";
				foreach($errors as $error) {
					echo " - " . $error . "<br />";
				}
				echo "</p>";
			}
			?>
			<form action="edit_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" method="post">
				<p>نام موضوع:
					<input type="text" name="menu_name" value="<?php echo $sel_subject['menu_name']; ?>" id="menu_name" />
				</p>
				<p>موقعیت: 
					<select name="position">
						<?php
							$subject_set = get_all_subjects();
							$subject_count = mysql_num_rows($subject_set);
							// $subject_count + 1 b/c we are adding a subject
							for($count=1; $count <= $subject_count+1; $count++) {
								echo "<option value=\"{$count}\"";
								if ($sel_subject['position'] == $count) {
									echo " selected";
								} 
								echo ">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p>قابل مشاهده: 
					<input type="radio" name="visible" value="0"<?php 
					if ($sel_subject['visible'] == 0) { echo " checked"; } 
					?> /> خیر
					&nbsp;
					<input type="radio" name="visible" value="1"<?php 
					if ($sel_subject['visible'] == 1) { echo " checked"; } 
					?> /> بله
				</p>
				<input type="submit" name="submit" value="ویرایش موضوع" />
				&nbsp;&nbsp;
				<a href="delete_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" onclick="return confirm('آیا مطمئن هستید؟');">حذف موضوع</a>
			</form>
			<br />
			<a href="content.php">لغو کردن</a>
			<div style="margin-top: 2em; border-top: 1px solid #000000;">
				<h3>صفحه های موجود در موضوع:</h3>
				<ul>
<?php 
	$subject_pages = get_pages_for_subject($sel_subject['id']);
	while($page = mysql_fetch_array($subject_pages)) {
		echo "<li><a href=\"content.php?page={$page['id']}\">
		{$page['menu_name']}</a></li>";
	}
?>
				</ul>
				<br />
				 <a href="new_page.php?subj=<?php echo $sel_subject['id']; ?>">اضافه کردن صفحه جدید به موضوع+</a>
			</div>
                    
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
       
   <?php include('inc/footer.php') ?>
