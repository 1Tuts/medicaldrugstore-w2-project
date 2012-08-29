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
                    لیست داروها
                    </div>
                    
                </div>
                 <div class="search1">
                    جستجو بر اساس نام دارو: <input type="text" class="search"/> 
                    جستجو بر اساس نام بیماری: <input type="text" class="search"/> 
                 </div>
                <div class="text">
                    <ul class="sefaresh">
                        <li>نوع بیماری</li>
                        <?php
                            echo $drugs_html['sicks'];
                        ?>
                    </ul>
                    <ul class="gheymat">
                        <li>قیمت</li>
                        <?php
                            echo $drugs_html['price'];
                        ?>
                    </ul>
                    <ul class="namekala">
                        <li> نام</li>
                        <?php
                            echo $drugs_html['name'];
                        ?>
                    </ul>
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
        
    <?php include('template/inc/footer.php')?>