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
                	<div class="pagename">
                   
                    </div>
                   
                   
                </div>
                <div class="text">
                	
                    
                   <?php if (!is_null($sel_subject)) { // subject selected ?>
			<?php echo $sel_subject['menu_name']; ?>
		<?php } elseif (!is_null($sel_page)) { // page selected ?>
			<?php echo $sel_page['menu_name']; ?>
			
				<?php echo $sel_page['content']; ?>
			
			<br />
			<a href="edit_page.php?page=<?php echo urlencode($sel_page['id']); ?>">ویرایش صفحه</a>
		<?php } else { // nothing selected ?>
			<h2>عنوان یا صفحه مورد نظر خود را  انتخاب کنید</h2>
		<?php } ?>
                   
                    
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
       
   <?php include('inc/footer.php') ?>
