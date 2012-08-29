<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>
<?php
	// make sure the subject id sent is an integer
	if (intval($_GET['subj']) == 0) {
		redirect_to('content.php');
	}

	include_once("inc/form_functions.php");

	// START FORM PROCESSING
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
		// initialize an array to hold our errors
		$errors = array();
	
		// perform validations on the form data
		$required_fields = array('menu_name', 'position', 'visible', 'content');
		$errors = array_merge($errors, check_required_fields($required_fields, $_POST));
		
		$fields_with_lengths = array('menu_name' => 30);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
		
		// clean up the form data before putting it in the database
		$subject_id = mysql_prep($_GET['subj']);
		$menu_name = trim(mysql_prep($_POST['menu_name']));
		$position = mysql_prep($_POST['position']);
		$visible = mysql_prep($_POST['visible']);
		$content = mysql_prep($_POST['content']);
	
		// Database submission only proceeds if there were NO errors.
		if (empty($errors)) {
			$query = "INSERT INTO pages (
						menu_name, position, visible, content, subject_id
					) VALUES (
						'{$menu_name}', {$position}, {$visible}, '{$content}', {$subject_id}
					)";
			if ($result = mysql_query($query, $connection)) {
				// as is, $message will still be discarded on the redirect
				$message = "The page was successfully created.";
				// get the last id inserted over the current db connection
				$new_page_id = mysql_insert_id();
				redirect_to("content.php?page={$new_page_id}");
			} else {
				$message = "The page could not be created.";
				$message .= "<br />" . mysql_error();
			}
		} else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		}
		// END FORM PROCESSING
	}
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
                	
                    
                  <h2>ایجاد صفحه جدید</h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="new_page.php?subj=<?php echo $sel_subject['id']; ?>" method="post">
				<?php $new_page = true; ?>
				<?php include "page_form.php" ?>
				<input type="submit" name="submit" value="ثبت" />
				<a href="edit_subject.php?subj=<?php echo $sel_subject['id']; ?>">لغو کردن</a><br />
			</form>
			<br />
                   
                    
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
       
   <?php include('inc/footer.php') ?>
