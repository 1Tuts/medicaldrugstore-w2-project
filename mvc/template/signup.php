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
                   عضویت در سایت
                    </div>
                    
                </div>
                <div class="text">
                	<form action="sign.php" method="post">
                	    <div class="user">
                        نام کاربری<input type="text" name="user" class="signup"/>
                        </div>  
                        <div class="password">
                        رمز عبور<input type="password" name="pass" class="signup"/>
                        </div>
                        <div class="tel">
                        شماره تلفن<input type="tel" name="tel" class="signup"/>
                        </div> 
                        <div class="mail">
                        آدرس اینترنتی<input type="email" name="mail" class="signup"/>
                        </div>
                        <div class="butt">
                        	<input type="submit"  name="submit" value="ثبت اطلاعات" class="submit">
                            <input type="reset" value="پاک کردن فرم" class="submit">
                        </div>  
                </div>
                </form>
            </div>
            <div class="clear">
            </div>
        </div>
        
   <?php include('template/inc/footer.php') ?>