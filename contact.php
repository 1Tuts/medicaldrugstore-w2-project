<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php  
$to="hamishebahar.1989@gmail.com";
$name=$_POST['name'];
$subject=$_POST['subject'];
$email=$_POST['email'];
$message=$_POST['message'];
if(isset($_POST['submit'])){
	mail($to,$subject,$message,"FROM <".$email .">");
	echo "thank you";	
}
?>
</body>
</html>