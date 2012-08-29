var timerID = null;
var timerRunning = false;

function stopclock ()
{
  if(timerRunning)
  clearTimeout(timerID);
  timerRunning = false;
}

function showtime () 
{
  var now = new Date();
  var hours = now.getHours();
  var minutes = now.getMinutes();
  var seconds = now.getSeconds()

  var timeValue = "" + ((hours >12) ? hours -12 :hours)
  timeValue += ((minutes < 10) ? ":0" : ":") + minutes
  timeValue += ((seconds < 10) ? ":0" : ":") + seconds
  timeValue += (hours >= 12) ? " بعد از ظهر" : " قبل از ظهر"
  document.clock.face.value = timeValue;
  timerID = setTimeout("showtime()",1000);
  timerRunning = true;
}

function startclock () 
{

  stopclock();
  showtime();
}

//........................................

function showdate() {
    week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
    months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
    a = new Date();
    d= a.getDay();
    day= a.getDate();
    month = a.getMonth()+1;
    year= a.getYear();

	year = (year== 0)?2000:year;
	(year<1000)? (year += 2000):true;
    
	year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
	year-=100;
	switch (month) {
    	case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
    	case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
    	case 3: (day<21)? (month=12, day+=9):(month=1, day-=20);   break;
    	case 4: (day<21)? (month=1, day+=11):(month=2, day-=20);   break;
    	case 5:
    	case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
    	case 7:
    	case 8:
    	case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22);  break;
    	case 10:(day<23)? (month=7, day+=8):(month=8, day-=22);    break;
    	case 11:
    	case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21);  break;
       default:  	break;
	}
document.write(week[d]+" "+day+" "+months[month-1]+" "+ year);
}

//................................................
$(function(){
		
		function textValidate(val,n){
			return val.length>=n;
		}
		
		function  numberValidate(val){
			return !isNaN(val);
		}
		
		function  emailValidate(val){
			var emailCheck =/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
			return emailCheck.test(val);
		}
		
		$('#drug').submit(function(){
			var err = false,
				daru = $("#daru"),
				tedad = $("#tedad"),
				tarikh = $("#tarikh");
			
			
			if( textValidate( daru.val(), 1 ) ){
				daru.removeClass('err').addClass('ok');
				err=true;
			}else{
				daru.removeClass('ok').addClass('err');
				daru.focus();
			}
			
			if( textValidate( tedad.val(), 1 ) && numberValidate(tedad.val()) ){
				tedad.removeClass('err').addClass('ok');
				err=true;
			}else{
				tedad.removeClass('ok').addClass('err');
				tedad.focus();
			}
			
			if( textValidate( tarikh.val(), 8 ) ){
				tarikh.removeClass('err').addClass('ok');
				err=true;
			}else{
				tarikh.removeClass('ok').addClass('err');
				tarikh.focus();
			}
			
			return !err;
		});
		
		
		$('#reg').submit(function(){
			var err = false,
				user = $("#user"),
				pass = $("#pass"),
				tel = $("#tel"),
				email = $("#email"),
				addres = $("#addres");
			
			
			if( textValidate( user.val(), 3 ) ){
				user.removeClass('err').addClass('ok');
				err=true;
			}else{
				user.removeClass('ok').addClass('err');
				user.focus();
			}
			
			if( textValidate( pass.val(), 6 ) ){
				pass.removeClass('err').addClass('ok');
				err=true;
			}else{
				pass.removeClass('ok').addClass('err');
				pass.focus();
			}
			
			
			if( textValidate( tel.val(), 11 ) && numberValidate(tel.val()) ){
				tel.removeClass('err').addClass('ok');
				err=true;
			}else{
				tel.removeClass('ok').addClass('err');
				tel.focus();
			}
			
			if( textValidate( email.val(), 7 ) &&  emailValidate(email.val()) ){
				email.removeClass('err').addClass('ok');
				err=true;
			}else{
				email.removeClass('ok').addClass('err');
				email.focus();
			}
			
			if( textValidate( addres.val(), 20 ) ){
				addres.removeClass('err').addClass('ok');
				err=true;
			}else{
				addres.removeClass('ok').addClass('err');
				addres.focus();
			}
			
			
			return !err;
		});
		
		$('#login').submit(function(){
			var err = false,
				fname = $("#fname"),
				password = $("#password");
			
			if( textValidate( fname.val(), 3 ) ){
				fname.removeClass('err').addClass('ok');
				err=true;
			}else{
				fname.removeClass('ok').addClass('err');
				fname.focus();
			}
			
			if( textValidate( password.val(), 6 ) ){
				password.removeClass('err').addClass('ok');
				err=true;
			}else{
				password.removeClass('ok').addClass('err');
				password.focus();
			}
		});
		
		
		$('#msg').submit(function(){
			var err = false,
				name = $("#name"),
				subject = $("#subject"),
				mail = $("#mail"),
				matn = $("#matn");
			
			
			if( textValidate( name.val(), 3 ) ){
				name.removeClass('err').addClass('ok');
				err=true;
			}else{
				name.removeClass('ok').addClass('err');
				name.focus();
			}
			
			if( textValidate( subject.val(), 2 ) ){
				subject.removeClass('err').addClass('ok');
				err=true;
			}else{
				subject.removeClass('ok').addClass('err');
				subject.focus();
			}
			
			if( textValidate( mail.val(), 7 ) &&  emailValidate(mail.val()) ){
				mail.removeClass('err').addClass('ok');
				err=true;
			}else{
				mail.removeClass('ok').addClass('err');
				mail.focus();
			}
			
			if( textValidate( matn.val(), 5 ) ){
				matn.removeClass('err').addClass('ok');
				err=true;
			}else{
				matn.removeClass('ok').addClass('err');
				matn.focus();
			}
			
			
			return !err;
		});
		
	});