<?php include('template/inc/header.php') ?>
    	
        <div class="main">
        	<div class="menu">
                <div class="up">
                	<div class="pic1"> 
                    </div>
                </div>
                <div class="down">
                	<?php include('template/inc/menus.php') ?>
                </div>
            </div>
            <div class="content">
            	<div class="subject">
                	<div class="pagename">
                    جزئیات دارو
                    </div>
                    
                </div>
                
                <div class="text">
                    <?php
                        if(is_array($drug)){
                    ?>

                    Name : <?php echo $drug['name'] ?><br/>
                    Price : <?php echo $drug['price'] ?><br/>

                    <?php
                        }else{
                    ?>

                    <p>Not found</p>

                    <?php
                        }
                    ?>
                </div>
            <div class="clear">
            </div>
        </div>
        
    <?php include('template/inc/footer.php')?>