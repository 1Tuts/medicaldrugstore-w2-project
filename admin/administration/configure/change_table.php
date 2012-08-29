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

if(!$admin->does_user_have_access('change_page_sadmin_config_table', 'config')){
	header('Location: ../access_denied.php');
	exit;
}

$table_added_success_msg = false;
if(isset($_GET['new_table']))
{
	if($_GET['new_table'] == 'yes')
	{
		$table_added_success_msg = true;
	}
}

$success = false;
$error = '';
$pending = '';

if(isset($_GET['table_id']))
{
	$table_id = $_GET['table_id'];
}

if(isset($_POST['save']))
{
	if($_POST['name'] != ''){
		$change_table = $admin->change_table($table_id, $_POST['name'], $_POST['display_name']);
		if($change_table)
		{
			$success = true;
		} else {
			$error = $admin->last_error();
		}
	} else  {
		$error = $lang->line('auth_config_table_name_error');
	}
}

if(isset($_POST['delete'])){
	header("Location: delete_table.php?table_id=$table_id");
	exit;
}

$table = $admin->get_table($table_id);
$fields = $admin->get_table_fields($table_id);
$pending = $admin->is_table_complete($table_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_config_edit_table_title');?></title>
<link rel="stylesheet" href="../assets/css/screen.css" type="text/css" media="screen" title="default" />
<script type="text/javascript" src="../assets/js/jquery.js"></script>
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
<p><a href="index.php"><?php echo $lang->line('common_configuration_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../../login.php?action=changepass"><?php echo $lang->line('common_change_pass_menu');?></a>&nbsp;&nbsp;&nbsp;<a href="../../login.php?action=logout"><?php echo $lang->line('common_sign_out_menu');?></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="../../user_guide/"><?php echo $lang->line('common_help_menu');?></a></p>
</div> 
<div class="clear">&nbsp;</div>
<div class="clear"></div>
<div id="content-outer">
	<div id="content">
		
		<ul id="crumbs">
		<li><a href="../index.php"><?php echo $lang->line('common_home');?></a></li>
		<li><a href="index.php"><?php echo $lang->line('common_configuration_menu');?></a></li>
		<li><?php echo $lang->line('auth_config_edit_table_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_config_edit_table_title');?></h1>
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
if($table_added_success_msg)
	echo '<p class="valid_box">' . $lang->line('auth_config_table_edit_added_success_msg') . '</p><div class="clear"></div>';
if($pending)
	echo '<br/><p class="warning_box">' . $lang->line('auth_config_table_edit_pending_issues') . ': '.$pending.'</p>';
if($error)
	echo '<br/><p class="error_box">'.$error.'</p>';
if($success)
	echo '<br/><p class="valid_box">' . $lang->line('auth_config_table_edit_success_msg') . '</p>';
?>
<form method="post" action="change_table.php?table_id=<?php echo $table_id;?>">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<tr>
<th valign="top"><label for="name"><?php echo $lang->line('auth_config_name_lable');?></th>
<td><input class="input" type="text" name="name" id="name" value="<?php echo $table['name'];?>"/></td>
</tr>
<tr>
<th valign="top"><?php echo $lang->line('auth_config_display_name_lable');?>:</th>
<td><input class="input" type="text" name="display_name" id="display_name" value="<?php echo $table['displayname'];?>"/></td>
</tr>
</tbody>
</table>
<br/>
<p><input name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('common_save');?>" type="submit">
<?php if($admin->does_user_have_access('delete_page_sadmin_config_table', 'config')){?>
 <input name="delete" id="delete" class="crtbtn_rd" value="<?php echo $lang->line('common_delete');?>" type="submit"></p>
<?php }?>
</p>
</form>
<br/>
<br/>
<h2><a href="add_field.php?table_id=<?php echo $table_id;?>"><?php echo $lang->line('auth_config_table_edit_add_field');?></a></h2>
<br/><br/>
<?php
if($fields){
	echo '<table class="widget" cellspacing="0">
<tr>
<th>' . $lang->line('auth_config_table_edit_field') . '</th>
<th>' . $lang->line('auth_config_table_edit_input_type') . '</th>
<th>' . $lang->line('auth_config_table_edit_visible') . '</th>
<th>' . $lang->line('auth_config_table_edit_string_rep') . '</th>
<th></th>
</tr>';
	$counter = 1;
	$rows = count($fields);
	foreach($fields as $field)
	{
		if( $counter&1 ){
			echo '<tr>';
		} else {
			echo '<tr class="odd-row">';
		}
		echo '<td style="text-align: left;"><a href="change_field.php?field_id='.$field['id'].'">'.$field['name'].'</a></td>';
		switch ($field['input_type']) {
			case 'pk':
				echo "<td>" . $lang->line('auth_config_widget_option_pk') . "</td>";
				break;
			case 'ti':
				echo "<td>" . $lang->line('auth_config_widget_option_ti') . "</td>";
				break;
			case 'ta':
				echo "<td>" . $lang->line('auth_config_widget_option_ta') . "</td>";
				break;
			case 'we':
				echo "<td>" . $lang->line('auth_config_widget_option_we') . "</td>";
				break;
			case 'cb':
				echo "<td>" . $lang->line('auth_config_widget_option_cb') . "</td>";
				break;
			case 'so':
				echo "<td>" . $lang->line('auth_config_widget_option_so') . "</td>";
				break;
			case 'uf':
				echo "<td>" . $lang->line('auth_config_widget_option_uf') . "</td>";
				break;
			case 'dt':
				echo "<td>" . $lang->line('auth_config_widget_option_dt') . "</td>";
				break;
			case 'fk':
				echo "<td>" . $lang->line('auth_config_widget_option_fk') . "</td>";
				break;
		}
		if($field['display_on_change_list'] == 'y'){
			echo '<td><img src="../assets/images/tick_16.png" alt="' . $lang->line('auth_config_table_edit_yes') . '"></td>';
		} else {
				if($field['string_rep'] == 'y'){
					echo '<td><img src="../assets/images/tick_16.png" alt="' . $lang->line('auth_config_table_edit_yes') . '"></td>';
				} else {
					echo '<td><img src="../assets/images/delete_16.png" alt="' . $lang->line('auth_config_table_edit_no') . '"></td>';
				}
			
		}
		if($field['string_rep'] == 'y'){
				echo '<td><img src="../assets/images/tick_16.png" alt="' . $lang->line('auth_config_table_edit_yes') . '"></td>';
		} else {
				echo '<td></td>';
		}
		echo '<td><a href="change_field.php?field_id='.$field['id'].'" title="' . $lang->line('auth_config_table_edit') . '"><img src="../assets/images/pencil_16.png" alt="' . $lang->line('common_edit_img_alt') . '"></a></td>';
		echo '</tr>';
		$counter++;
	}
	echo '</table>';
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