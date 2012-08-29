<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../login.php');
	exit;
}
include '../includes/admin.php';
$admin = new Admin();

include '../includes/language.php';
$lang = new Language('tables', '../includes/');

if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: access_denied.php');
	exit;
}

$tables = $admin->get_all_ready_tables();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('common_admin_dash');?></title>
<link rel="stylesheet" href="assets/css/screen.css" type="text/css" media="screen" title="default" />
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript">
 $(function() {
		/* For zebra striping */
        $("table tr:nth-child(odd)").addClass("odd-row");
		/* For cell text alignment */
		$("table td:first-child, table th:first-child").addClass("first");
		/* For removing the last border */
		$("table td:last-child, table th:last-child").addClass("last");
});
</script>
</head>
<body>
<div class="red_heading">
<h1><?php echo $lang->line('common_admin_dash');?></h1>
<p><a href="configure/"><?php echo $lang->line('common_configuration_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../login.php?action=changepass"><?php echo $lang->line('common_change_pass_menu');?></a>&nbsp;&nbsp;&nbsp;<a href="../login.php?action=logout"><?php echo $lang->line('common_sign_out_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../user_guide/"><?php echo $lang->line('common_help_menu');?></a></p>
</div> 
<div class="clear">&nbsp;</div>
<div class="clear"></div>
<div id="content-outer">
	<div id="content">
		
		<ul id="crumbs">
		<li><?php echo $lang->line('common_home');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('common_admin_dash');?></h1>
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
					
					
					

<table class="widget" cellspacing="0">
<tr>
<th colspan="3"><?php echo $lang->line('common_authentication_caps');?></th>
</tr>
<tr>
<?php if($admin->does_user_have_access('change_page_sadmin_users', 'auth')){?>
<td  width="50%" style="text-align: left;"><a href="authentication/change_user_list.php"><?php echo $lang->line('common_users_caps');?></a></td>
<?php } else {?>
<td width="50%" style="text-align: left;"><?php echo $lang->line('common_users_caps');?></td>
<?php }?>
<?php if($admin->does_user_have_access('add_page_sadmin_users', 'auth')){?>
<td width="25%"><a href="authentication/add_user.php" title="<?php echo $lang->line('common_create_users');?>"><img src="assets/images/plus_16.png" alt="<?php echo $lang->line('common_create_img_alt');?>"></a></td>
<?php } else {?>
<td width="25%"><img src="assets/images/plus_16.png" alt="<?php echo $lang->line('common_create_img_alt');?>"></td>
<?php }?>
<?php if($admin->does_user_have_access('change_page_sadmin_users', 'auth')){?>
<td width="25%"><a href="authentication/change_user_list.php" title="<?php echo $lang->line('common_edit_users');?>"><img src="assets/images/pencil_16.png" alt="<?php echo $lang->line('common_edit_img_alt');?>"></a></td>
<?php } else {?>
<td width="25%"><img src="assets/images/pencil_16.png" alt="<?php echo $lang->line('common_edit_img_alt');?>"></td>
<?php }?>
</tr>
<tr class="odd-row">
<?php if($admin->does_user_have_access('change_page_sadmin_roles', 'auth')){?>
<td style="text-align: left;"><a href="authentication/change_role_list.php"><?php echo $lang->line('common_roles_caps');?></a></td>
<?php } else {?>
<td style="text-align: left;"><?php echo $lang->line('common_roles_caps');?></td>
<?php }?>
<?php if($admin->does_user_have_access('add_page_sadmin_roles', 'auth')){?>
<td width="25%"><a href="authentication/add_role.php" title="<?php echo $lang->line('common_create_roles');?>"><img src="assets/images/plus_16.png" alt="<?php echo $lang->line('common_create_img_alt');?>"></a></td>
<?php } else {?>
<td width="25%"><img src="assets/images/plus_16.png" alt="<?php echo $lang->line('common_create_img_alt');?>"></td>
<?php }?>
<?php if($admin->does_user_have_access('change_page_sadmin_roles', 'auth')){?>
<td width="25%"><a href="authentication/change_role_list.php" title="<?php echo $lang->line('common_edit_roles');?>"><img src="assets/images/pencil_16.png" alt="<?php echo $lang->line('common_edit_img_alt');?>"></a></td>
<?php } else {?>
<td width="25%"><img src="assets/images/pencil_16.png" alt="<?php echo $lang->line('common_edit_img_alt');?>"></td>
<?php }?>
</tr>
<tr>
<?php if($admin->does_user_have_access('change_page_sadmin_permissions', 'auth')){?>
<td style="text-align: left;"><a href="authentication/change_perm_list.php"><?php echo $lang->line('common_permissions_caps');?></a></td>
<?php } else {?>
<td style="text-align: left;"><?php echo $lang->line('common_permissions_caps');?></td>
<?php }?>
<?php if($admin->does_user_have_access('add_page_sadmin_permissions', 'auth')){?>
<td width="25%"><a href="authentication/add_perm.php" title="<?php echo $lang->line('common_create_permissions');?>"><img src="assets/images/plus_16.png" alt="<?php echo $lang->line('common_create_img_alt');?>"></a></td>
<?php } else {?>
<td width="25%"><img src="assets/images/plus_16.png" alt="<?php echo $lang->line('common_create_img_alt');?>"></td>
<?php }?>
<?php if($admin->does_user_have_access('change_page_sadmin_permissions', 'auth')){?>
<td width="25%"><a href="authentication/change_perm_list.php" title="<?php echo $lang->line('common_edit_permissions');?>"><img src="assets/images/pencil_16.png" alt="<?php echo $lang->line('common_edit_img_alt');?>"></a></td>
<?php } else {?>
<td width="25%"><img src="assets/images/pencil_16.png" alt="<?php echo $lang->line('common_edit_img_alt');?>"></td>
<?php }?>
</tr>
</table>
<br/>
<br/>

	<?php
if($tables){
	echo '<table class="widget" cellspacing="0">
<tr>
<th colspan="3">' . $lang->line('common_manage_items_caps') . '</th>
</tr>';
	$counter = 1;
	$rows = count($tables);
	foreach($tables as $table)
	{
		$change_page_perm = $admin->does_user_have_access('change_page_'.$table['name'], 'tables');
		$add_page_perm = $admin->does_user_have_access('add_page_'.$table['name'], 'tables');
		
		if( $counter&1 ){
			echo '<tr>';
		} else {
			echo '<tr class="odd-row">';
		}
		if($change_page_perm){
			if($table['displayname'])
			{
				echo '<td width="50%" style="text-align: left;"><a href="change_list.php?table_id='.$table['id'].'">'.strtoupper($table['displayname']).'</a></td>';
			} else {
				echo '<td width="50%" style="text-align: left;"><a href="change_list.php?table_id='.$table['id'].'">'.strtoupper($table['name']).'</a></td>';
			}
		} else {
			if($table['displayname'])
			{
				echo '<td width="50%" style="text-align: left;">'.strtoupper($table['displayname']).'</td>';
			} else {
				echo '<td width="50%" style="text-align: left;">'.strtoupper($table['name']).'</td>';
			}
		}
		if($add_page_perm){
			echo '<td width="25%"><a href="add_form.php?table_id='.$table['id'].'" title="' . $lang->line('common_create_records') . '"><img src="assets/images/plus_16.png" alt="' . $lang->line('common_create_img_alt') . '"></a></td>';
		} else {
			echo '<td width="25%"><img src="assets/images/plus_16.png" alt="' . $lang->line('common_create_img_alt') . '"></td>';
		}
		if($change_page_perm){
			echo '<td width="25%"><a href="change_list.php?table_id='.$table['id'].'" title="' . $lang->line('common_edit_records') . '"><img src="assets/images/pencil_16.png" alt="' . $lang->line('common_edit_img_alt') . '"></a></td>';
		} else {
			echo '<td width="25%"><img src="assets/images/pencil_16.png" alt="' . $lang->line('common_edit_img_alt') . '"></td>';
		}
		echo '</tr>';
		$counter++;
	}
	echo '
</table>
			   ';
}
?>				
					
					
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