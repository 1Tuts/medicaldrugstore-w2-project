<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../../login.php');
	exit;
}
include '../../includes/admin.php';
$admin = new Admin();

include '../../includes/language.php';
$lang = new Language('users', '../../includes/');

if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: ../access_denied.php');
	exit;
}

if(!$admin->does_user_have_access('change_page_sadmin_users', 'auth')){
	header('Location: ../access_denied.php');
	exit;
}

$error = '';
$success = false;

$roles = $admin->get_all_role();

if(isset($_GET['user_id']))
{
	$user_id = intval($_GET['user_id']);
}

if(isset($_POST['save']))
{
	if($_POST['email'] == '')
		$error .= $lang->line('auth_user_email_error');
	if($_POST['role'] == '')
		$error .= $lang->line('auth_user_role_error');
	
	if($error == ''){
		$change_user = $admin->change_user($user_id, $_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['role']);
		if($change_user)
		{
			$success = true;
		} else {
			$error .= $lang->line('common_something_went_wrong');
		}
	}
}

$user = $admin->get_user($user_id);

if(isset($_POST['delete'])){
	header("Location: delete.php?id=$user_id&item=user");
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_edit_user_title');?></title>
<link rel="stylesheet" href="../assets/css/screen.css" type="text/css" media="screen" title="default" />
</head>
<body>
<div class="red_heading">
<h1><?php echo $lang->line('common_admin_dash');?></h1>
<p><a href="../configure/"><?php echo $lang->line('common_configuration_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../../login.php?action=changepass"><?php echo $lang->line('common_change_pass_menu');?></a>&nbsp;&nbsp;&nbsp;<a href="../../login.php?action=logout"><?php echo $lang->line('common_sign_out_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../../user_guide/"><?php echo $lang->line('common_help_menu');?></a></p>
</div> 
<div class="clear">&nbsp;</div>
<div class="clear"></div>
<div id="content-outer">
	<div id="content">
		
		<ul id="crumbs">
		<li><a href="../index.php"><?php echo $lang->line('common_home');?></a></li>
		<li><a href="index.php"><?php echo $lang->line('common_authentication');?></a></li>
		<li><a href="change_user_list.php"><?php echo $lang->line('common_user');?></a></li>
		<li><?php echo $lang->line('auth_edit_user_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_edit_user_title');?></h1>
		</div>

		<table border="0" width="100%" cellpadding="0" cellspacing="0" id="content-table">
			<tr>
				<th rowspan="3" class="sized"></th>
				<th class="topleft"></th>
				<td id="tbl-border-top">&nbsp;</td>
				<th class="topright"></th>
				<th rowspan="3" class="sized"></th>
			</tr>
			<tr>
				<td id="tbl-border-left"></td>
				<td>
				<!--  start content-table-inner ...................................................................... START -->
				<div id="content-table-inner">
		
					<!--  start table-content  -->
					<div id="table-content">
<?php
if($error)
	echo '<p class="error_box">'.$error.'.</p>';
if($success)
	echo '<p class="valid_box">' . $lang->line('auth_edit_user_success_msg') . '</p>';
?>
<form method="post" action="change_user.php?user_id=<?php echo $user_id;?>">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<tr>
<th valign="top"><?php echo $lang->line('auth_user_first_name');?>:</th>
<td><input class="input" type="text" name="fname" id="fname" value="<?php echo (isset($_POST['fname']) ? $_POST['fname'] : $user['fname']);?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_user_last_name');?>:</th>
<td><input class="input" type="text" name="lname" id="lname" value="<?php echo (isset($_POST['lname']) ? $_POST['lname'] : $user['lname']);?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_user_username');?>:</th>
<td><input class="input" type="text" name="username" id="username" value="<?php echo $user['username'];?>" disabled /></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_user_email');?>:</th>
<td><input class="input" type="text" name="email" id="email" value="<?php echo (isset($_POST['email']) ? $_POST['email'] : $user['email']);?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_user_role');?>:</th>
<td>
<select class="input" name="role" id="role">
<option value=""><?php echo $lang->line('auth_user_select');?></option>
<?php
foreach($roles as $role)
{
	if(isset($_POST['role']) AND ($_POST['role'] == $role['id']))
	{
		echo '<option selected="selected" value="'.$role['id'].'">'.$role['name'].'</option>';
	} else {
		if($user['role_id'] == $role['id'])
		{
			echo '<option selected="selected" value="'.$role['id'].'">'.$role['name'].'</option>';
		} else {
			echo '<option value="'.$role['id'].'">'.$role['name'].'</option>';
		}
	}
}
?>
</select>
</td>
</tr>
</tbody>
</table>
<br/>
<p><input name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('common_save');?>" type="submit">
<?php if($admin->does_user_have_access('delete_page_sadmin_users', 'auth')){?>
 <input name="delete" id="delete" class="crtbtn_rd" value="<?php echo $lang->line('common_delete');?>" type="submit">
<?php }?>
</p>
</form>	
					</div>
					<!--  end table-content  -->
	
					<div class="clear"></div>
		 
			</div>
			<!--  end content-table-inner ............................................END  -->
			</td>
			<td id="tbl-border-right"></td>
		</tr>
		<tr>
			<th class="sized bottomleft"></th>
			<td id="tbl-border-bottom">&nbsp;</td>
			<th class="sized bottomright"></th>
		</tr>
	</table>
	<div class="clear">&nbsp;</div>

	</div>
	<!--  end content -->
</div>
<!--  end content-outer........................................................END -->

    
   
<div id="footer">
	<div id="footer-left">Universal PHP Admin &copy; <a href="http://codecanyon.net/user/robertnduati">robertnduati</a></div>
	<div class="clear">&nbsp;</div>
</div>
 
</body>
</html>
<?php $admin->disconnect();?>