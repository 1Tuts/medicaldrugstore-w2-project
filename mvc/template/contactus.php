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
                    	ارتباط با ما
                    </div>
                    
                </div>
                <div class="text">
                	 <div class="left">
                     <div class="address">
                        <h4>آدرس داروخانه</h4>
                        <p>مشهد-خیابان سجاد-بزرگمهر</p>
                        <p>تلفن:777777-0511</p>
                        <p>فکس:6666666-0511</p>
                        </div>
                       
                        <div class="form">
                        	<div class="title">
                            <h4>متن خود را تایپ کنید</h4>
                            </div>
                            <form action="?page=contact" method="post" id="msg" name="msg">
                        	<div class="name">
                            <input class="txt" type="text" placeholder="نام" dir="rtl" name="name" id="name"/>
                            </div>
                            <div class="name">
                            <input class="txt" type="text" placeholder="موضوع" dir="rtl" name="subject" id="subject"/>
                            </div>
                  	          <div class="email"> 
                            <input class="txt" type="text" placeholder="ایمیل" dir="rtl" name="mail" id="mail"/>
                            </div>
                            <div class="message">
                            	<textarea rows="6" cols="31" dir="rtl" style="border-radius:5px ;		 	                                 border-color:#36F; box-shadow:2px 2px 1px  #999999; text-shadow:1px 1px 1px #666666;"                                 name="matn" id="matn"></textarea>
                            </div>
                            <div class="send">
                            <input class="submit" type="submit" name="submit" value="ثبت"/>
                            </div>
                            </form>
                        </div>
                     
                    </div>
                	<div class="right">
                    	
                        <iframe width="250" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=36.318553,59.551493&amp;num=1&amp;hl=en&amp;ie=UTF8&amp;t=m&amp;ll=36.318445,59.551048&amp;spn=0.024205,0.021458&amp;z=14&amp;output=embed"></iframe>
                    </div>
                    <div class="clear">
                    </div>
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
    <?php include('template/inc/footer.php') ?>