<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../../login.php');
	exit;
}
include '../../includes/admin.php';
$admin = new Admin();

include '../../includes/language.php';
$lang = new Language('config', '../../includes/');

if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: ../access_denied.php');
	exit;
}

if(!$admin->does_user_have_access('add_page_sadmin_config_table', 'config')){
	header('Location: ../access_denied.php');
	exit;
}

if(isset($_POST['save']))
{
	if($_POST['name'] != ''){
		$new_table = $admin->new_table($_POST['name'], $_POST['display_name']);
		if($new_table)
		{
			header('Location: change_table.php?new_table=yes&table_id='.$new_table);
			exit;
		} else {
			$error = $admin->last_error();
		}
	} else  {
		$error = $lang->line('auth_config_table_name_error');
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_config_add_table_title');?></title>
<link rel="stylesheet" href="../assets/css/screen.css" type="text/css" media="screen" title="default" />
</head>
<body>
<div class="red_heading">
<h1><?php echo $lang->line('common_admin_dash');?></h1>
<p><a href="index.php"><?php echo $lang->line('common_configuration_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../../login.php?action=changepass"><?php echo $lang->line('common_change_pass_menu');?></a>&nbsp;&nbsp;&nbsp;<a href="../../login.php?action=logout"><?php echo $lang->line('common_sign_out_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../../user_guide/"><?php echo $lang->line('common_help_menu');?></a></p>
</div> 
<div class="clear">&nbsp;</div>
<div class="clear"></div>
<div id="content-outer">
	<div id="content">
		
		<ul id="crumbs">
		<li><a href="../index.php"><?php echo $lang->line('common_home');?></a></li>
		<li><a href="index.php"><?php echo $lang->line('common_configuration_menu');?></a></li>
		<li><?php echo $lang->line('auth_config_add_table_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_config_add_table_title');?></h1>
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
	echo '<p class="valid_box">' . $lang->line('auth_config_table_success_msg') . '</p>';
?>
<form method="post" action="add_table.php">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<tr>
<th valign="top"><label for="name"><?php echo $lang->line('auth_config_name_lable');?></th>
<td><input class="input" type="text" name="name" id="name" /></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_config_display_name_lable');?>:</th>
<td><input class="input" type="text" name="display_name" id="display_name" /></td>
</tr>
</tbody>
</table>
<p><input name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('common_save');?>" type="submit"></p>
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