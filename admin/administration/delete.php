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

$success = false;
$error = '';

if(isset($_GET['table_id']))
{
	$table_id = intval($_GET['table_id']);
}
if(isset($_GET['record_id']))
{
	$record_id = intval($_GET['record_id']);
}

$all_tables = $admin->get_all_tables();
$table = $admin->get_table($table_id);
$fields = $admin->get_table_fields($table_id);

if($table['displayname']){
	$display_name = $table['displayname'];
} else {
	$display_name = $table['name'];
}

if(!$admin->does_user_have_access('delete_page_'.$table['name'], 'tables')){
	header('Location: access_denied.php');
	exit;
}

if(isset($_POST['delete'])){
	foreach($fields as $field){
		if($field['input_type'] == 'pk'){
			$primary_key = $field['name'];
		}
		if($field['input_type'] == 'uf'){
			$record = $admin->get_record($table['name'], $record_id, $primary_key);
			$file = $field['place_to_store'] . $record[$field['name']];
			$exists = file_exists($file);
			if($exists){
				unlink($file);
			}
		}
	}
	
	$success = $admin->delete_a_record($table['name'], $record_id, $primary_key);
	if(!$success)
		$error .= $lang->line('tables_delete_error');
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('tables_delete_title');?> - <?php echo $display_name;?></title>
<link rel="stylesheet" href="assets/css/screen.css" type="text/css" media="screen" title="default" />
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
		<li><a href="index.php"><?php echo $lang->line('common_home');?></a></li>
		<li><a href="change_list.php?table_id=<?php echo $table_id;?>"><?php echo $display_name;?></a></li>
		<li><?php echo $lang->line('Delete Record');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('Delete Record');?> - <?php echo $display_name;?></h1>
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
	echo '<p class="error_box">'.$error.'.</p><div class="clear"></div>';
if($success)
	echo '<p class="valid_box">' . $lang->line('tables_delete_success') . '</p><div class="clear"></div>';
?>					
<form method="post" action="delete.php?table_id=<?php echo $table_id;?>&record_id=<?php echo $record_id;?>" enctype="multipart/form-data" name="add_form" id="add_form">
<p><?php echo $lang->line('tables_delete_text');?></p>
<br/>
<p><input name="delete" id="delete" class="crtbtn_rd" value="<?php echo $lang->line('common_delete');?>" type="submit"></p>
<div style="clear: both;"></div>
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