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
                   
                </div>
            </div>
           <div class="content">
           
               <div class="subject">
                   <div class="pagename">
                       
                   </div>
               </div>
               
               
               <div class="text">
                	
                    
                  			  
                    
                </div>
           </div>
           <div class="clear">
           
           </div>
        </div>
       
   <?php include('inc/footer.php') ?>
