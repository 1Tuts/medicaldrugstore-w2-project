<?php
/**
 * Universal Admin - login.php
 *
 * Handles all the authentication functions for the Universal Admin. Login, Logout, Change password and Reset forgotten password.
 *
 * @package		Universal Admin
 * @author		Robert Nduati
 */
session_start();

include 'includes/admin.php';
$admin = new Admin();

include 'includes/language.php';
$lang = new Language('login', 'includes/');

$error = '';
$success = '';

if(isset($_GET['action'])){
	$action = $_GET['action'];
} else {
	$action = 'login';
}


if($action == 'logout'){
	$admin->logout();
	$success .= $lang->line('log_out_msg');
}

if(isset($_POST['login'])){
	if($admin->login($_POST['email'] , $_POST['pwd']))
	{
		if($_SESSION['sadmin_user_home']){
			header('Location: '.$_SESSION['sadmin_user_home']);
		} else {
			header('Location: administration/index.php');
		}
		exit;
	} else {
		$error .= $lang->line('log_in_error_msg');
	}
}

if(isset($_POST['forgot'])){
	$user_details = $admin->forgot_password($_POST['email']);
	if($user_details)
	{
		require_once('includes/class.phpmailer.php');
		$mail = new PHPMailerLite();
		$mail->IsMail();
		$body = '<body style="margin: 10px;"><div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;"><br>&nbsp;'. $lang->line('log_in_email_intro') .'<br><br>';
		$body .= ''. $lang->line('log_in_email_username') .': <b>'.$user_details['username'].'</b>';
		$body .= ''. $lang->line('log_in_email_password') .': <b>'.$user_details['password'].'</b>';
		$body .= '<br /><br />'. $lang->line('log_in_email_more_info') .'</div></body>';
		
		
		$mail->SetFrom($admin->webmaster_email(), $admin->webmaster_name());
		$address = $user_details['email'];
		$mail->AddAddress($address, '');
		$mail->Subject    = $_SERVER['SERVER_NAME'];
		$mail->AltBody    = $lang->line('common_email_alt_body');
		$mail->MsgHTML($body);
		$mail->Send();
		
		$success .= $lang->line('log_in_forgot_success');
	} else {
		$error .= $lang->line('log_in_forgot_error');
	}
}

if(isset($_POST['changepass'])){
	if($_SESSION['sadmin_logged_in'] == true){
		if($_POST['newpass'] == $_POST['cpass']){
			if($admin->change_password( $_SESSION['sadmin_user_id'] , $_POST['oldpass'] , $_POST['newpass']))
			{
				$success .= $lang->line('log_in_change_success');
			} else {
				$error .= $lang->line('log_in_change_pass_error');
			}
		} else {
			$error .= $lang->line('log_in_change_confirm_error');
		}
	} else {
		$error .= $lang->line('log_in_change_loggedin_error');
	}
}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en-US"><head>
<title>Universal Admin - <?php echo $lang->line('log_in_title');?></title>
<link rel="stylesheet" id="login-css" href="administration/assets/css/login.css" type="text/css" media="all">
</head><body class="login">

<?php
if($action == 'logout'){
?>
<div id="login"><h1>Universal Admin - <?php echo $lang->line('log_in_title');?></h1>
<br/>
<form name="loginform" id="loginform" action="<?php echo $admin->get_base_url();?>login.php?action=login" method="post">
	<?php
	if($success)
		echo '<p style="color: green;">'.$success.'</p>';
	?>
	<p>
		<label><?php echo $lang->line('log_in_username');?><br>
		<input name="email" id="email" class="input" size="20" type="text"></label>
	</p>
	<p>
		<label><?php echo $lang->line('log_in_password');?><br>
		<input name="pwd" id="pass" class="input" value="" size="20" type="password"></label>
	</p>
	<p class="submit">
		<input name="login" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('log_in_submit_login');?>" type="submit">
	</p>
</form>
<br/>
<p id="nav" style="padding-left:10px;">
<a href="login.php?action=forgot" ><?php echo $lang->line('log_in_forgotpass_link');?></a>
</p>
</div>
<?php
}
?>

<?php
if($action == 'login'){
?>
<div id="login"><h1>Universal Admin - <?php echo $lang->line('log_in_title');?></h1>
<br/>
<form name="loginform" id="loginform" action="<?php echo $admin->get_base_url();?>login.php?action=login" method="post">
	<?php
	if($error)
		echo '<p style="color: red;">'.$error.'</p>';
	if($success)
		echo '<p style="color: green;">'.$success.'</p>';
	?>
	<p>
		<label><?php echo $lang->line('log_in_username');?><br>
		<input name="email" id="email" class="input" size="20" type="text"></label>
	</p>
	<p>
		<label><?php echo $lang->line('log_in_password');?><br>
		<input name="pwd" id="pass" class="input" value="" size="20" type="password"></label>
	</p>
	<p class="submit">
		<input name="login" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('log_in_submit_login');?>" type="submit">
	</p>
</form>
<br/>
<p id="nav" style="padding-left:10px;">
<a href="login.php?action=forgot" ><?php echo $lang->line('log_in_forgotpass_link');?></a>
</p>
</div>
<?php
}
?>


<?php
if($action == 'changepass'){
?>
<div id="login"><h1>Universal Admin - <?php echo $lang->line('change_password_title');?></h1>
<br/>
<form name="loginform" id="loginform" action="<?php echo $admin->get_base_url();?>login.php?action=changepass" method="post">
	<?php
	if($error)
		echo '<p style="color: red;">'.$error.'</p>';
	if($success)
		echo '<p style="color: green;">'.$success.'</p>';
	?>
	<p>
		<label><?php echo $lang->line('change_password_old_password');?><br>
		<input name="oldpass" id="email" class="input" size="20" type="password"></label>
	</p>
	<p>
		<label><?php echo $lang->line('change_password_new_password');?><br>
		<input name="newpass" id="pass" class="input" value="" size="20" type="password"></label>
	</p>
	<p>
		<label><?php echo $lang->line('change_password_confirm_password');?><br>
		<input name="cpass" id="pass" class="input" value="" size="20" type="password"></label>
	</p>
	<p class="submit">
		<input name="changepass" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('log_in_submit_change');?>" type="submit">
	</p>
</form>
<br/>
</div>
<?php
}
?>


<?php
if($action == 'forgot'){
?>
<div id="login"><h1>Universal Admin - <?php echo $lang->line('forgot_password_title');?></h1>
<br/>
<form name="loginform" id="loginform" action="<?php echo $admin->get_base_url();?>login.php?action=forgot" method="post">
	<?php
	if($error)
		echo '<p style="color: red;">'.$error.'</p>';
	if($success)
		echo '<p style="color: green;">'.$success.'</p>';
	?>
	<p>
		<label><?php echo $lang->line('forgot_password_user_email');?><br>
		<input name="email" id="email" class="input" size="20" type="text"></label>
	</p>
	<p class="submit">
		<input name="forgot" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('log_in_submit_done');?>" type="submit">
	</p>
</form>
<br/>
</div>
<?php
}
?>


</body></html>
<?php $admin->disconnect();?>