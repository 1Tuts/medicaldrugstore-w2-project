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
                   فرم سفارش دارو
                    </div>
                    
                </div>
                <div class="text">
                <form action="?page=order" method="post" id="drug" name="drug">
                	<div class="order"><p> برای سفارش داروی مورد نظر فرم زیر را پر کنید:<br/><br/></p>
           	نام دارو:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="daru" class="txt" name="daru" /><br/><br/>
           تعداد دارو:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text"  id="tedad" class="txt" name="tedad" /><br/><br/>
                        تاریخ تحویل دارو:<input type="date" id="date" class="txt" name="date" /><br/><br/><br/>
                        <div style="text-align:center;">
                        <input type="submit" class="submit" name="submit" value="ثبت سفارش"/>
                        <input type="reset" class="submit" name="reset" value="پاک کردن فرم"/>
                    	</div>
                    </div>
                </form>
                <div class="login"> 
                   <p style="font-weight:bold;"> برای سفارش دارو ابتدا وارد سایت شوید </p>
                   <br/>
                    <br/>
                    <div class="sign">عضویت در سایت</div>
                    <div class="log">ورود به سایت</div>
                    <br/>
                    <br/>
                        <div class="register">
                            <form action="?page=sign" method="post" id="reg" name="reg">
                                <div class="user2">
                                نام کاربری<input type="text" name="user" class="signup reqd" id="user"/>
                                </div>  
                                <div class="user2">
                                رمز عبور<input type="password" name="pass" class="signup" id="pass"/>
                                </div>
                                <div class="user2">
                                شماره تلفن<input type="tel" name="tel" class="signup" id="tel"/>
                                </div> 
                                <div class="user2">
                                آدرس اینترنتی<input type="email" name="mail" class="signup" id="email"/>
                                </div>
                                <div class="user2">
                                محل سکونت<textarea class="signup" id="addres" name="addres"></textarea>
                                </div>

                                <div class="butt">
                                    <input type="submit"  name="submit" value="ثبت اطلاعات" class="submit">
                                    <input type="reset" value="پاک کردن فرم" class="submit">
                                </div>  
                       
                        </form>
                    </div>
                    <div class="enter">
                    		<form action="?page=enter" method="post" id="login">
                    			<div class="user">
                                نام کاربری<input type="text" name="fname" class="signup" id="fname"/>
                                </div>  
                                <div class="user">
                                رمز عبور<input type="password" name="password" class="signup" id="password"/>
                                </div>
                                 <div style="margin-right:80px;">
                                 <input type="submit"  name="submit" value="ورود " class="submit"/>
                                 </div>
                    		</form>
                     </div>
           	</div>
            </div>
            <div class="clear">
            </div>
        </div>
        
   <?php include('inc/footer.php') ?>