<?php include('inc/header.php') ?>
        <div class="main">
        	<div class="menu">
                <div class="up">
                	<div class="pic1"> 
                    </div>
                </div>
                <div class="down">
                	<?php include('inc/menus.php') ?>
                </div>
            </div>
            <div class="content">
            	<div class="subject">
                	<div class="pagename">
                   نام دارو
                    </div>
                    
                </div>
                <div class="text">
                	<form action="?page=daru" method="post">
                    	<input type="hidden" name="name" />
                         <div class="user1" style="background-color:#09F"> 
                       بیماری مربوطه:<input type="text" name="bimari" class="signup"/>
                        </div> 
                         <div class="tozihat">
                        راهنمایی های عمومی:<textarea class="comment" name="rahnamai" ></textarea>
                        </div> 
                         <div class="tozihat">
                        هشدارها:<textarea class="comment" name="hoshdar" ></textarea>
                        </div> 
                         <div class="tozihat">
                        مقدار و نحوه مصرف دارو:<textarea class="comment" name="masraf" ></textarea>
                        </div> 
                         <div class="tozihat">
                        عوارض جانبی:<textarea class="comment" name="avarez" ></textarea>
                        </div> 
                        <div class="tozihat">
                       مسمومیت:<textarea class="comment" name="masmumiat" ></textarea>
                        </div> 
                        <div class="tozihat">
                      شرایط نگه داری:<textarea class="comment" name="sharayet" ></textarea>
                        </div>  
                       
                        <div class="butt">
                        	<input type="submit"  name="submit" value="ثبت اطلاعات" class="submit">
                            <input type="reset" value="پاک کردن فرم" class="submit">
                        </div>  
                        <br/>
                </div>
                </form>
            </div>
            <div class="clear">
            </div>
        </div>
        
   <?php include('inc/footer.php') ?>