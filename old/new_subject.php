<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

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
                	
                   
                </div>
                <div class="text">
                	
                   <h2>ثبت موضوع جدید</h2>
			<form action="create_subject.php" method="post">
				<p>نام موضوع: 
					<input type="text" name="menu_name" value="" id="menu_name" class="txt" />
				</p>
				<p>موقعیت 
					<select name="position">
						<?php
							$subject_set = get_all_subjects();
							$subject_count = mysql_num_rows($subject_set);
							// $subject_count + 1 b/c we are adding a subject
							for($count=1; $count <= $subject_count+1; $count++) {
								echo "<option value=\"{$count}\">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p>قابل مشاهده:
					<input type="radio" name="visible" value="0" /> خیر
					&nbsp;
					<input type="radio" name="visible" value="1" />بله
				</p>
				<input type="submit" class="button" value="ثبت" />
			</form>
			<br />
			<a href="content.php">لغو</a>
                    
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
       
   <?php include('inc/footer.php') ?>
