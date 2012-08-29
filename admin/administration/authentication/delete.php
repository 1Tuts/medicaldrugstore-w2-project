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
if(isset($_GET['item']))
{
	$item = $_GET['item'];
} else {
	header('Location: index.php');
	exit;
}

$permission_key = 'delete_page_sadmin_'.$item.'s';
if(!$admin->does_user_have_access($permission_key, 'auth')){
	header('Location: ../access_denied.php');
	exit;
}

$success = false;
$error = '';

if(isset($_GET['id']))
{
	$id = intval($_GET['id']);
}


if(isset($_POST['delete'])){
	$success = $admin->delete_auth_record($item, $id);
	if(!$success)
		$error .= $lang->line('auth_user_delete_error');
}

switch ($item) {
	case 'user':
		$change_page_link = 'change_user_list.php';
		break;
	case 'role':
		$change_page_link = 'change_role_list.php';
		break;
	case 'permission':
		$change_page_link = 'change_perm_list.php';
		break;
}

$changable = $admin->is_it_changable($item, $id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_user_delete_item_title');?></title>
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
		<li><a href="<?php echo $change_page_link;?>"><?php echo $lang->line('auth_user_delete_change_list');?></a></li>
		<li><?php echo $lang->line('auth_user_delete_item_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_user_delete_item_title');?></h1>
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
	echo '<p class="info_box">' . $lang->line('auth_user_delete_can_not') . '</p><div class="clear"></div>';
if($error)
	echo '<p class="error_box">'.$error.'</p><div class="clear"></div>';
if($success)
	echo '<p class="valid_box">' . $lang->line('auth_user_delete_successful') . '</p><div class="clear"></div>';
?>
<?php if($changable){?>
<form method="post" action="delete.php?id=<?php echo $id;?>&item=<?php echo $item;?>" enctype="multipart/form-data" name="add_form" id="add_form">
<p><?php echo $lang->line('auth_user_delete_confirm');?></p>
<br/>
<p><input name="delete" id="delete" class="crtbtn_rd" value="<?php echo $lang->line('common_delete');?>" type="submit"></p>
</form>
<?php }?>		
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