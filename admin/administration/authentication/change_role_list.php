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

if(isset($_GET['page']))
{
	$page = intval($_GET['page']);
	
} else {
	$page = 1;
}
$rows = 30;
$offset = ($page - 1) * $rows;

$roles = $admin->get_auth_paged_records('role', $offset, $rows);
$number_of_records = $admin->count_auth_table_records('role');
$number_of_pages = ceil( $number_of_records / $rows );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('common_role');?></title>
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
		<li><?php echo $lang->line('common_role');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('common_role');?>&nbsp;<span><a href="add_role.php" title="<?php echo $lang->line('auth_add_role_title');?>">[+]</a></span></h1>
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
<?php if($roles){?>
<table class="main">
<tr class="table-top">
<td class="for_main"><?php echo $lang->line('common_name');?></td>
</tr>
<?php
foreach($roles as $role)
{
	echo '<tr>';
	echo '<td class="for_main"><a href="change_role.php?role_id='.$role['id'].'">'.$role['name'].'</a></td>';
	echo '</tr>';
}
?>
</tbody>
</table>
<br/>
<table border="0" cellpadding="0" cellspacing="0" id="paging-table">
<tr>
<td>
<?php 
if($page > 1){
$last = $page - 1;
?>
<a href="change_role_list.php?page=1" class="page-far-left"></a>
<a href="change_role_list.php?page=<?php echo $last;?>" class="page-left"></a>
<?php }?>
<div id="page-info"><?php echo $lang->line('common_page');?> <strong><?php echo $page;?></strong> / <?php echo $number_of_pages;?></div>
<?php 
if($number_of_pages > $page){
$next = $page + 1;
?>
<a href="change_role_list.php?page=<?php echo $next;?>" class="page-right"></a>
<a href="change_role_list.php?page=<?php echo $number_of_pages;?>" class="page-far-right"></a>
<?php }?>
</td>
</tr>
</table>
<?php } else {
echo '<p>There are no roles defined yet....</p>';
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