<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../../login.php');
	exit;
}
include '../../includes/admin.php';
$admin = new Admin();

include '../../includes/language.php';
$lang = new Language('roles', '../../includes/');

if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: ../access_denied.php');
	exit;
}

if(!$admin->does_user_have_access('change_page_sadmin_roles', 'auth')){
	header('Location: ../access_denied.php');
	exit;
}

$error = '';
$success = false;

if(isset($_GET['role_id']))
{
	$role_id = intval($_GET['role_id']);
}

$perms = $admin->get_all_permission();

if(isset($_POST['save']))
{
	if($_POST['name'] != ''){
		$change_role = $admin->change_role($role_id, $_POST['name'], $_POST['home']);
		if($change_role)
		{
			$success = true;
			foreach($perms as $perm)
			{
				if(isset($_POST['perm_'.$perm['id']]))
				{
					$perm_id = intval($_POST['perm_'.$perm['id']]);
					$save = $admin->add_or_update_role_perm($role_id, $perm_id);
				} else {
					$delete = $admin->delete_role_perm($role_id, $perm['id']);
				}
			}
		} else {
			$error = $admin->last_error();
		}
	} else  {
		$error = $lang->line('auth_add_role_error_msg');
	}
}

$role = $admin->get_role($role_id);
$role_perms = $admin->get_role_permissions($role_id);

if(isset($_POST['delete'])){
	header("Location: delete.php?id=$role_id&item=role");
	exit;
}

$changable = $admin->is_it_changable('role', $role_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_edit_role_title');?></title>
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
		<li><a href="change_role_list.php"><?php echo $lang->line('common_role');?></a></li>
		<li><?php echo $lang->line('auth_edit_role_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_edit_role_title');?></h1>
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
	echo '<p class="info_box">' . $lang->line('auth_edit_role_cant_change') . '</p><div class="clear"></div>';
if($error)
	echo '<p class="error_box">'.$error.'.</p>';
if($success)
	echo '<p class="valid_box">' . $lang->line('auth_edit_role_success_msg') . '</p>';
?>
<form method="post" action="change_role.php?role_id=<?php echo $role_id;?>">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<tr>
<th valign="top"><?php echo $lang->line('common_name');?></th>
<td><input class="input" <?php echo (($changable) ? '' : 'disabled');?>  type="text" name="name" id="name" value="<?php echo (isset($_POST['name']) ? $_POST['name'] : $role['name']);?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_role_home_page');?></th>
<td><input class="input" <?php echo (($changable) ? '' : 'disabled');?>  type="text" name="home" id="home" value="<?php echo (isset($_POST['home']) ? $_POST['home'] : $role['homepage']);?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('common_permission');?></th>
<td>
<div class="scroll_checkboxes">
<?php
if($perms){
	foreach($perms as $perm)
	{
		if(isset($_POST['perm_'.$perm['id']]))
		{
			echo '<input checked="checked" type="checkbox" name="perm_'.$perm['id'].'" id="perm_'.$perm['id'].'" value="'.$perm['id'].'" /> '.$perm['name'].'<br/>';
		} else {
			$displayed = false;
			foreach($role_perms as $role_perm){
				if($role_perm['permission_id'] == $perm['id'])
				{
					$displayed = true;
					echo '<input checked="checked" type="checkbox" name="perm_'.$perm['id'].'" id="perm_'.$perm['id'].'" value="'.$perm['id'].'" /> '.$perm['name'].'<br/>';
				}
			}
			if(!$displayed)
				echo '<input type="checkbox" name="perm_'.$perm['id'].'" id="perm_'.$perm['id'].'" value="'.$perm['id'].'" /> '.$perm['name'].'<br/>';
		}
	}
} else {
echo $lang->line('auth_role_no_permissions');
}
?>
</div>
</td>
</tr>
</tbody>
</table>
<br/>
<p><input <?php echo (($changable) ? '' : 'disabled');?>  name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('common_save');?>" type="submit">
<?php if($admin->does_user_have_access('delete_page_sadmin_roles', 'auth')){?>
 <input <?php echo (($changable) ? '' : 'disabled');?>  name="delete" id="delete" class="crtbtn_rd" value="<?php echo $lang->line('common_delete');?>" type="submit">
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