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

if(!$admin->does_user_have_access('add_page_sadmin_config_field', 'config')){
	header('Location: ../access_denied.php');
	exit;
}

$table_added_success_msg = false;
$error = '';

if(isset($_GET['new_table']))
{
	if($_GET['new_table'] == 'yes')
	{
		$table_added_success_msg = true;
	}
}

if(isset($_GET['successful']))
{
	$success_and_add = true;
}

if(isset($_GET['table_id']))
{
	$table_id = $_GET['table_id'];
} else {
	// header('Location: index.php');
	// exit();
}



if(isset($_POST['save']) OR isset($_POST['save_cont']))
{
	if($_POST['name'] == '')
		$error .= $lang->line('auth_config_field_name_error');
	if($_POST['input_type'] == '')
		$error .= $lang->line('auth_config_field_input_error');
	
	if($_POST['input_type'] == 'cb'){
		if($_POST['default_state'] == '')
			$error .= $lang->line('auth_config_field_cb_default_error');
		if($_POST['value_when_not_checked'] == '')
			$error .= $lang->line('auth_config_field_cb_checked_error');
		if($_POST['value_when_checked'] == '')
			$error .= $lang->line('auth_config_field_cb_not_checked_error');
	}
	
	if($_POST['input_type'] == 'so'){
		if($_POST['select_options'] == '')
			$error .= $lang->line('The options to be selected are required. ');
	}
	
	if($_POST['input_type'] == 'uf'){
		if($_POST['place_to_store'] == '')
			$error .= $lang->line('The path to the palce to store the file is required. ');
		
		// validate the path
	}
	
		
	if($error == ''){
		$new_field = $admin->new_field($table_id, 
										$_POST['name'], 
										$_POST['display_name'], 
										$_POST['input_type'],
										$_POST['is_required'],
										$_POST['string_rep'],
										$_POST['default_state'], 
										$_POST['value_when_not_checked'], 
										$_POST['value_when_checked'], 
										$_POST['select_options'], 
										$_POST['place_to_store'], 
										$_POST['allowed_extensions'], 
										$_POST['datetime_save_format'],
										$_POST['foreignkey_table'],
										$_POST['display_on_change_list'],
										$_POST['auto_save_timestamp_new'],
										$_POST['auto_save_timestamp_update']);
		if($new_field)
		{
			$success = true;
			if(isset($_POST['save_cont'])){
				header("Location: add_field.php?successful=1&table_id=$table_id");
				exit;
			}
		} else {
			$error .= $admin->last_error();
		}
	}
}
$tables = $admin->get_all_tables();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('auth_config_add_field_title');?></title>
<link rel="stylesheet" href="../assets/css/screen.css" type="text/css" media="screen" title="default" />
<script type="text/javascript">
function delete_rows(){
	var table = document.getElementById('id-form');
	var rowCount = table.rows.length;
	if(rowCount > 6){
		for(var i=6; i<rowCount; i++) {
			var row = table.rows[i];
			table.deleteRow(i);
			rowCount--;
			i--;
		}

	}
}
function add_fields(){
	delete_rows();
	var table = document.getElementById('id-form');
	var widget = document.getElementById('input_type').value;
	var rowCount = table.rows.length;
	if(widget == 'cb'){
		var row3 = table.insertRow(rowCount);
		var cell31 = row3.insertCell(0);
		cell31.innerHTML = '<label for="default_state"><?php echo $lang->line('auth_config_cb_default_lable');?>:</label>';
		var cell32 = row3.insertCell(1);
		cell32.innerHTML = '<select class="input" name="default_state" id="default_state"  title="<?php echo $lang->line('auth_config_cb_default_title');?>"><option value="notchecked"><?php echo $lang->line('auth_config_cb_default_not');?></option><option value="checked"><?php echo $lang->line('auth_config_cb_default_checked');?></option><option value=""><?php echo $lang->line('auth_config_select_one');?></option></select>';
		
		var row2 = table.insertRow(rowCount);
		var cell21 = row2.insertCell(0);
		cell21.innerHTML = '<label for="value_when_not_checked"><?php echo $lang->line('auth_config_cb_not_checked_lable');?>:</label>';
		var cell22 = row2.insertCell(1);
		cell22.innerHTML = '<input class="input" type="text" name="value_when_not_checked" id="value_when_not_checked"  title="<?php echo $lang->line('auth_config_cb_not_checked_title');?>"/>';
		
		var row = table.insertRow(rowCount);
		var cell1 = row.insertCell(0);
		cell1.innerHTML = '<label for="value_when_checked"><?php echo $lang->line('auth_config_cb_checked_lable');?>:</label>';
		var cell2 = row.insertCell(1);
		cell2.innerHTML = '<input class="input" type="text" name="value_when_checked" id="value_when_checked" title="<?php echo $lang->line('auth_config_cb_checked_title');?>"/>';
	}
	if(widget == 'so'){
		var row = table.insertRow(rowCount);
		var cell1 = row.insertCell(0);
		cell1.innerHTML = '<label for="select_options"><?php echo $lang->line('auth_config_options_lable');?>:</label>';
		var cell2 = row.insertCell(1);
		cell2.innerHTML = '<input class="input" type="text" name="select_options" id="select_options"  title="<?php echo $lang->line('auth_config_options_title');?>"/>';
	}
	if(widget == 'uf'){
		var row2 = table.insertRow(rowCount);
		var cell21 = row2.insertCell(0);
		cell21.innerHTML = '<label for="allowed_extensions"><?php echo $lang->line('auth_config_ext_lable');?>:</label>';
		var cell22 = row2.insertCell(1);
		cell22.innerHTML = '<input class="input" type="text" name="allowed_extensions" id="allowed_extensions"  title="<?php echo $lang->line('auth_config_ext_title');?>"/>';
		
		var row = table.insertRow(rowCount);
		var cell1 = row.insertCell(0);
		cell1.innerHTML = '<label for="place_to_store"><?php echo $lang->line('auth_config_path_lable');?>:</label>';
		var cell2 = row.insertCell(1);
		cell2.innerHTML = '<input class="input" type="text" name="place_to_store" id="place_to_store" title="<?php echo $lang->line('auth_config_path_title');?>"/>';
	}
	if(widget == 'dt'){
		var row3 = table.insertRow(rowCount);
		var cell31 = row3.insertCell(0);
		cell31.innerHTML = '<label for="auto_save_timestamp_update"><?php echo $lang->line('auth_config_auto_update_lable');?>:</label>';
		var cell32 = row3.insertCell(1);
		cell32.innerHTML = '<input value="y" type="checkbox" name="auto_save_timestamp_update" id="auto_save_timestamp_update"  title="<?php echo $lang->line('auth_config_auto_update_title');?>"/>';
		
		var row2 = table.insertRow(rowCount);
		var cell21 = row2.insertCell(0);
		cell21.innerHTML = '<label for="auto_save_timestamp_new"><?php echo $lang->line('auth_config_auto_create_lable');?>:</label>';
		var cell22 = row2.insertCell(1);
		cell22.innerHTML = '<input value="y" type="checkbox" name="auto_save_timestamp_new" id="auto_save_timestamp_new"  title="<?php echo $lang->line('auth_config_auto_create_title');?>"/>';
		
		var row = table.insertRow(rowCount);
		var cell1 = row.insertCell(0);
		cell1.innerHTML = '<label for="datetime_save_format"><?php echo $lang->line('auth_config_dt_format_lable');?>:</label>';
		var cell2 = row.insertCell(1);
		cell2.innerHTML = '<input class="input" type="text" name="datetime_save_format" id="datetime_save_format" title="<?php echo $lang->line('auth_config_dt_format_title');?>"/>';
	}
	if(widget == 'fk'){
		var row = table.insertRow(rowCount);
		var cell1 = row.insertCell(0);
		cell1.innerHTML = '<label for="foreignkey_table"><?php echo $lang->line('auth_config_fk_lable');?>:</label>';
		var cell2 = row.insertCell(1);
		<?php
		$tables_choice = '';
		foreach($tables as $a_table)
		{
			$tables_choice .= '<option value="'.$a_table['id'].'">'.$a_table['name'].'</option>';
		}
		?>
		cell2.innerHTML = '<select class="input" name="foreignkey_table" id="foreignkey_table" title="<?php echo $lang->line('auth_config_fk_title');?>"><option value=""><?php echo $lang->line('auth_config_select_one');?></option><?php echo $tables_choice;?></select>';
	}
}

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
		<li><a href="change_table.php?table_id=<?php echo $table_id;?>"><?php echo $lang->line('auth_config_back_to_table');?></a></li>
		<li><?php echo $lang->line('auth_config_add_field_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('auth_config_add_field_title');?></h1>
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
	echo '<p class="valid_box">' . $lang->line('auth_config_field_table_success_msg') . '</p><div class="clear"></div>';
if($error)
	echo '<p class="error_box">'.$error.'.</p><div class="clear"></div>';
if($success)
	echo '<p class="valid_box">' . $lang->line('auth_config_field_success_msg') . '</p><div class="clear"></div>';
if($success_and_add)
	echo '<p class="valid_box">' . $lang->line('auth_config_field_plus_success_msg') . '</p><div class="clear"></div>';
?>					
<form id="myform" method="post" action="add_field.php?table_id=<?php echo $table_id;?>">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<tr>
<td><label for="name"><?php echo $lang->line('auth_config_name_lable');?></label></td>
<td><input class="input" type="text" name="name" id="name" value="<?php echo ((isset($_POST['name'])) ? $_POST['name'] : '');?>" title="<?php echo $lang->line('auth_config_name_title');?>"/></td>
</tr>
<tr>
<td><label for="display_name"><?php echo $lang->line('auth_config_display_name_lable');?>:</label></td>
<td><input class="input" type="text" name="display_name" id="display_name" value="<?php echo ((isset($_POST['display_name'])) ? $_POST['display_name'] : '');?>" title="<?php echo $lang->line('auth_config_display_name_title');?>"/></td>
</tr>
<tr>
<td><label for="input_type"><?php echo $lang->line('auth_config_widget_lable');?>:</label></td>
<?php
if(isset($_POST['input_type'])){
?>
<td>
<select class="input" name="input_type" id="input_type" onchange="add_fields();" title="<?php echo $lang->line('auth_config_widget_title');?>">
	<option value=""><?php echo $lang->line('auth_config_select_one');?></option>
	<option value="pk" <?php echo (($_POST['input_type']=='pk') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_pk');?></option>
	<option value="ti" <?php echo (($_POST['input_type']=='ti') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_ti');?></option>
	<option value="ta" <?php echo (($_POST['input_type']=='ta') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_ta');?></option>
	<option value="we" <?php echo (($_POST['input_type']=='we') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_we');?></option>
	<option value="cb" <?php echo (($_POST['input_type']=='cb') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_cb');?></option>
	<option value="so" <?php echo (($_POST['input_type']=='so') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_so');?></option>
	<option value="uf" <?php echo (($_POST['input_type']=='uf') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_uf');?></option>
	<option value="dt" <?php echo (($_POST['input_type']=='dt') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_dt');?></option>
	<option value="fk" <?php echo (($_POST['input_type']=='fk') ? 'selected="selected"' : '');?>><?php echo $lang->line('auth_config_widget_option_fk');?></option>
</select>
</td>
<?php
} else {
?>
<td>
<select class="input" name="input_type" id="input_type" onchange="add_fields();" title="<?php echo $lang->line('auth_config_widget_title');?>">
	<option value=""><?php echo $lang->line('auth_config_select_one');?></option>
	<option value="pk"><?php echo $lang->line('auth_config_widget_option_pk');?></option>
	<option value="ti"><?php echo $lang->line('auth_config_widget_option_ti');?></option>
	<option value="ta"><?php echo $lang->line('auth_config_widget_option_ta');?></option>
	<option value="we"><?php echo $lang->line('auth_config_widget_option_we');?></option>
	<option value="cb"><?php echo $lang->line('auth_config_widget_option_cb');?></option>
	<option value="so"><?php echo $lang->line('auth_config_widget_option_so');?></option>
	<option value="uf"><?php echo $lang->line('auth_config_widget_option_uf');?></option>
	<option value="dt"><?php echo $lang->line('auth_config_widget_option_dt');?></option>
	<option value="fk"><?php echo $lang->line('auth_config_widget_option_fk');?></option>
</select>
</td>
<?php
}
?>
</tr>
<tr>
<td><label for="display_name"><?php echo $lang->line('auth_config_required_lable');?>:</label></td>
<?php
if(isset($_POST['is_required'])){
?>
<td><input value="y" checked="checked" type="checkbox" name="is_required" id="is_required" title="<?php echo $lang->line('auth_config_required_title');?>"/></td>
<?php
} else {
?>
<td><input value="y" type="checkbox" name="is_required" id="is_required" id="is_required" title="<?php echo $lang->line('auth_config_required_title');?>"/></td>
<?php
}
?>
</tr>
<tr>
<td><label for="display_name"><?php echo $lang->line('auth_config_string_rep_lable');?>:</label></td>
<?php
if(isset($_POST['string_rep'])){
?>
<td><input value="y" checked="checked" type="checkbox" name="string_rep" id="string_rep" title="<?php echo $lang->line('auth_config_string_rep_title');?>"/></td>
<?php
} else {
?>
<td><input value="y" type="checkbox" name="string_rep" id="string_rep" title="<?php echo $lang->line('auth_config_string_rep_title');?>"/></td>
<?php
}
?>
</tr>
<tr>
<td><label for="display_name"><?php echo $lang->line('auth_config_visible_lable');?>:</label></td>
<?php
if(isset($_POST['display_on_change_list'])){
?>
<td><input value="y" checked="checked" type="checkbox" name="display_on_change_list" id="display_on_change_list" title="<?php echo $lang->line('auth_config_visible_title');?>"/></td>
<?php
} else {
?>
<td><input value="y" type="checkbox" name="display_on_change_list" id="display_on_change_list" title="<?php echo $lang->line('auth_config_visible_title');?>"/></td>
<?php
}
?>
</tr>
<?php
if(isset($_POST['input_type'])){
	if($_POST['input_type'] == 'cb'){
		echo '<tr>
					<td><label for="value_when_checked">' . $lang->line('auth_config_cb_checked_lable')  . ':</label></td>
					<td><input class="input" value="'.$_POST['value_when_checked'].'" type="text" name="value_when_checked" id="value_when_checked" title="' . $lang->line('auth_config_cb_checked_title')  . '"/></td>
				</tr>';
		echo '<tr>
					<td><label for="value_when_not_checked">' . $lang->line('auth_config_cb_not_checked_lable')  . ':</label></td>
					<td><input class="input" value="'.$_POST['value_when_not_checked'].'" type="text" name="value_when_not_checked" id="value_when_not_checked"  title="' . $lang->line('auth_config_cb_not_checked_title')  . '"/></td>
				</tr>';
		echo '<tr><td><label for="default_state">' . $lang->line('auth_config_cb_default_lable')  . ':</label></td>';
		echo '<td><select class="input" name="default_state" id="default_state"  title="' . $lang->line('auth_config_cb_default_title')  . '">';
		if($_POST['default_state'] == 'notchecked'){
			echo '<option value="notchecked" selected="selected">' . $lang->line('auth_config_cb_default_not')  . '</option>';
		} else {
			echo '<option value="notchecked">' . $lang->line('auth_config_cb_default_not')  . '</option>';
		}
		if($_POST['default_state'] == 'checked'){
			echo '<option value="checked" selected="selected">' . $lang->line('auth_config_cb_default_checked')  . '</option>';
		} else {
			echo '<option value="checked">' . $lang->line('auth_config_cb_default_checked')  . '</option>';
		}
		echo '<option value="">' . $lang->line('auth_config_select_one')  . '</option></select></td></tr>';
	}
	if($_POST['input_type'] == 'so'){
		echo '<tr>
					<td><label for="select_options">' . $lang->line('auth_config_options_lable')  . ':</label></td>
					<td><input class="input" value="'.$_POST['select_options'].'" type="text" name="select_options" id="select_options"  title="' . $lang->line('auth_config_options_title')  . '"/></td>
				</tr>';
	}
	if($_POST['input_type'] == 'uf'){
		echo '<tr>
					<td><label for="place_to_store">' . $lang->line('auth_config_path_lable')  . ':</label></td>
					<td><input class="input" value="'.$_POST['place_to_store'].'" type="text" name="place_to_store" id="place_to_store" title="' . $lang->line('auth_config_path_title')  . '"/></td>
				</tr>';
		echo '<tr>
					<td><label for="allowed_extensions">' . $lang->line('auth_config_ext_lable')  . ':</label></td>
					<td><input class="input" value="'.$_POST['allowed_extensions'].'" type="text" name="allowed_extensions" id="allowed_extensions"  title="' . $lang->line('auth_config_ext_title')  . '"/></td>
				</tr>';
	}
	if($_POST['input_type'] == 'dt'){
		echo '<tr>
					<td><label for="datetime_save_format">' . $lang->line('auth_config_dt_format_lable')  . ':</label></td>
					<td><input class="input" value="'.$_POST['datetime_save_format'].'" type="text" name="datetime_save_format" id="datetime_save_format" title="' . $lang->line('auth_config_dt_format_title')  . '"/></td>
				</tr>';
		if($_POST['auto_save_timestamp_new']){
			echo '<tr>
					<td><label for="auto_save_timestamp_new">' . $lang->line('auth_config_auto_create_lable')  . ':</label></td>
					<td><input checked="checked" value="y" type="checkbox" name="auto_save_timestamp_new" id="auto_save_timestamp_new"  title="' . $lang->line('auth_config_auto_create_title')  . '"/></td>
				</tr>';
		} else {
			echo '<tr>
					<td><label for="auto_save_timestamp_new">' . $lang->line('auth_config_auto_create_lable')  . ':</label></td>
					<td><input value="y" type="checkbox" name="auto_save_timestamp_new" id="auto_save_timestamp_new"  title="' . $lang->line('auth_config_auto_create_title')  . '"/></td>
				</tr>';
		}
		if($_POST['auto_save_timestamp_update']){
			echo '<tr>
					<td><label for="auto_save_timestamp_update">' . $lang->line('auth_config_auto_update_lable')  . ':</label></td>
					<td><input checked="checked" value="y" type="checkbox" name="auto_save_timestamp_update" id="auto_save_timestamp_update"  title="' . $lang->line('auth_config_auto_update_title')  . '"/></td>
				</tr>';
		} else {
			echo '<tr>
					<td><label for="auto_save_timestamp_update">' . $lang->line('auth_config_auto_update_lable')  . ':</label></td>
					<td><input value="y" type="checkbox" name="auto_save_timestamp_update" id="auto_save_timestamp_update"  title="' . $lang->line('auth_config_auto_update_title')  . '"/></td>
				</tr>';
		}
	}
	if($_POST['input_type'] == 'fk'){
		echo '<tr><td><label for="foreignkey_table">' . $lang->line('auth_config_fk_lable')  . ':</label></td>';
		echo '<td><select class="input" name="foreignkey_table" id="foreignkey_table" title="' . $lang->line('auth_config_fk_title')  . '">';
		echo '<option value="">' . $lang->line('auth_config_select_one')  . '</option>';
		foreach($tables as $a_table)
		{
			if($_POST['foreignkey_table'] == $a_table['id']){
				echo '<option value="'.$a_table['id'].'" selected="selected">'.$a_table['name'].'</option>';
			} else {
				echo '<option value="'.$a_table['id'].'">'.$a_table['name'].'</option>';
			}
		}
		echo '</select></td></tr>';
	}

}
?>
</tbody>
</table>
<br/>
<p><input name="save_cont" id="submit_cont" class="crtbtn_gr" value="<?php echo $lang->line('auth_config_field_submit_addanother');?>" type="submit"> <input name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('auth_config_field_submit_add');?>" type="submit"></p>
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