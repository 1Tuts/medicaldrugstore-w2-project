<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../../login.php');
	exit;
}
include '../../includes/admin.php';
$admin = new Admin();

include '../../includes/language.php';
$lang = new Language('permissions', '../../includes/');

if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: ../access_denied.php');
	exit;
}

if(!$admin->does_user_have_access('change_page_sadmin_permissions', 'auth')){
	header('Location: ../access_denied.php');
	exit;
}

$error = '';
$success = false;

if(isset($_GET['perm_id']))
{
	$perm_id = intval($_GET['perm_id']);
}

if(isset($_POST['save']))
{
	if($_POST['name'] != '' OR $_POST['permkey'] != ''){
		$change = $admin->change_permission($perm_id, $_POST['name'], $_POST['permkey']);
		if($change)
		{
			$success = true;
		} else {
			$error = $admin->last_error();
		}
	} else  {
		$error = $lang->line('auth_add_perm_error_msg');
	}
}

$perm = $admin->get_permission($perm_id);

if(isset($_POST['delete'])){
	header("Location: delete.php?id=$perm_id&item=permission");
	exit;
}

$changable = $admin->is_it_changable('permission', $perm_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_edit_perm_title');?></title>
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
		<li><a href="change_perm_list.php"><?php echo $lang->line('common_permission');?></a></li>
		<li><?php echo $lang->line('auth_edit_perm_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_edit_perm_title');?></h1>
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
if(!$changable)
	echo '<p class="info_box">' . $lang->line('auth_edit_perm_cant_change') . '</p><div class="clear"></div>';
if($error)
	echo '<p class="error_box">'.$error.'.</p>';
if($success)
	echo '<p class="valid_box">' . $lang->line('auth_edit_perm_success_msg') . '</p>';
?>
<form method="post" action="change_perm.php?perm_id=<?php echo $perm_id;?>">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<tr>
<th valign="top"><?php echo $lang->line('common_name');?></th>
<td><input class="input" <?php echo (($changable) ? '' : 'disabled');?> type="text" size="50" name="name" id="name" value="<?php echo (isset($_POST['name']) ? $_POST['name'] : $perm['name']);?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_perm_key');?></th>
<td><input class="input" <?php echo (($changable) ? '' : 'disabled');?> type="text" size="50" name="permkey" id="permkey" value="<?php echo (isset($_POST['permkey']) ? $_POST['permkey'] : $perm['permkey']);?>"/></td>
</tr>
</tbody>
</table>
<br/>
<p><input <?php echo (($changable) ? '' : 'disabled');?> name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('common_save');?>" type="submit">
<?php if($admin->does_user_have_access('delete_page_sadmin_permissions', 'auth')){?>
 <input <?php echo (($changable) ? '' : 'disabled');?> name="delete" id="delete" class="crtbtn_rd" value="<?php echo $lang->line('common_delete');?>" type="submit">
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