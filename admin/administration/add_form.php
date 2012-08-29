<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../login.php');
	exit;
}
include '../includes/admin.php';
$admin = new Admin();

include '../includes/validation.php';
$validation = new Validation();

include '../includes/language.php';
$lang = new Language('tables', '../includes/');


if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: access_denied.php');
	exit;
}

$success = false;
$error = '';

if(isset($_GET['successful']))
{
	$success_and_add = true;
}
if(isset($_GET['table_id']))
{
	$table_id = intval($_GET['table_id']);
}

$all_tables = $admin->get_all_tables();
$table = $admin->get_table($table_id);

if(!$admin->does_user_have_access('add_page_'.$table['name'], 'tables')){
	header('Location: access_denied.php');
	exit;
}

$fields = $admin->get_table_fields($table_id);
if($table['displayname']){
	$display_name = $table['displayname'];
} else {
	$display_name = $table['name'];
}
if(isset($_POST['save']) OR isset($_POST['save_cont']))
{
	
	// check to make sure that the fields are there when they are required.
	foreach($fields as $field){
		if($field['displayname']){
			$field_name = $field['displayname'];
		} else {
			$field_name = $field['name'];
		}
		$value_to_validate = '';
		if($field['input_type'] != 'pk'){
			
			if($field['input_type'] != 'uf'){
				if($field['is_required'] == 'y' AND $_POST[$field['name']] == '')
					$error .= " $field_name" . $lang->line('tables_is_required');
				$value_to_validate = $_POST[$field['name']];
			} else {
				if($field['is_required'] == 'y' AND $_FILES[$field['name']]["name"] == '')
					$error .= " $field_name" . $lang->line('tables_is_required');
				if(($_FILES[$field['name']]["name"] != '') AND ($admin->valid_file_extension($_FILES[$field['name']]["name"], $field['allowed_extensions']) == false))
					$error .= " $field_name" . $lang->line('tables_is_invalid');
				$value_to_validate = $_FILES[$field['name']]["name"];
			}
			if($value_to_validate){
				$validation_errors = $validation->run_validation($table['name'], $field['name'], $field_name, $value_to_validate);
				if($validation_errors)
					$error .= $validation_errors;
			}
		}
	}
	
	
	
	if($error == ''){
		// prepare the array of values to put in the database
		$values = array();
		$value_count = 0;
		foreach($fields as $field){
			if($field['input_type'] != 'pk'){
				if($_POST[$field['name']] != '')
				{
					$value[$value_count]['key'] = $field['name'];
					if(($field['input_type'] == 'ti') OR ($field['input_type'] == 'ta') OR ($field['input_type'] == 'we') OR ($field['input_type'] == 'so') OR ($field['input_type'] == 'fk')){
						$value[$value_count]['value'] = $_POST[$field['name']];
					}
					if($field['input_type'] == 'cb'){
						$value[$value_count]['value'] = $field['value_when_checked'];
					}
					if($field['input_type'] == 'dt'){
						$timestamp = $admin->convert_date_to_timestamp($_POST[$field['name']]);
						if($field['datetime_save_format']){
							$value[$value_count]['value'] = date($field['datetime_save_format'], $timestamp);
						} else {
							$value[$value_count]['value'] = $timestamp;
						}
					}
					$value_count++;
				}
				
				if(($field['input_type'] == 'dt')){
					if(($field['auto_save_timestamp_new'] == 'y') OR ($field['auto_save_timestamp_update'] == 'y')){
						$value[$value_count]['key'] = $field['name'];
						if($field['datetime_save_format']){
							$value[$value_count]['value'] = date($field['datetime_save_format'], time());
						} else {
							$value[$value_count]['value'] = time();
						}
						$value_count++;
					}
				}
				
				
				if(($_POST[$field['name']] == '') AND ($field['input_type'] == 'cb'))
				{
					
					$value[$value_count]['key'] = $field['name'];
					$value[$value_count]['value'] = $field['value_when_not_checked'];
					$value_count++;
				}
				if(($_FILES[$field['name']]["name"] != '') AND ($field['input_type'] == 'uf'))
				{
					
					$value[$value_count]['key'] = $field['name'];
					$value[$value_count]['value'] = $admin->set_filename($field['place_to_store'], $admin->clean_file_name($_FILES[$field['name']]["name"]));
					move_uploaded_file($_FILES[$field['name']]["tmp_name"],$field['place_to_store'] . $value[$value_count]['value']);
					$value_count++;
				}
			}
		}
		$new_record = $admin->save_new_record($table['name'], $value);
		if($new_record)
		{
			$success = true;
			if(isset($_POST['save_cont'])){
				header("Location: add_form.php?successful=1&table_id=$table_id");
				exit;
			}
		} else {
			$error .= $lang->line('common_something_went_wrong');
		}
	}
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('tables_add_record_title');?> - <?php echo $display_name;?></title>
<link rel="stylesheet" href="assets/css/screen.css" type="text/css" media="screen" title="default" />
<link rel="stylesheet" href="assets/js/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/js/jwysiwyg/jquery.wysiwyg.js"></script>
<script type="text/javascript" language="javascript" src="assets/js/datepicker.js"></script>
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
		<li><?php echo $lang->line('tables_add_record_title');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('tables_add_record_title');?> - <?php echo $display_name;?></h1>
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
	echo '<p class="valid_box">' . $lang->line('tables_add_record_success') . '</p><div class="clear"></div>';
if($success_and_add)
	echo '<p class="valid_box">' . $lang->line('tables_add_record_success_plus') . '</p><div class="clear"></div>';
?>
						
<form method="post" action="add_form.php?table_id=<?php echo $table_id;?>" enctype="multipart/form-data" name="add_form" id="add_form">
<table border="0" cellpadding="0" cellspacing="0"  id="id-form">
<tbody>
<?php
	foreach($fields as $field){
		echo '<tr>';
		if($field['input_type'] != 'pk'){
			if($field['is_required'] == 'y')
			{
				$required_star = '<span style="color: #ff0000;">*</span>';
			} else {
				$required_star = '';
			}
			if(($field['auto_save_timestamp_new'] != 'y') AND ($field['auto_save_timestamp_update'] != 'y')){
				if($field['displayname']){
					echo '<th valign="top">'.ucfirst($field['displayname']).$required_star.'</th>';
				} else {
					echo '<th valign="top">'.ucfirst($field['name']).$required_star.'</th>';
				}
			}
			if($field['input_type'] == 'ti'){
				if($_POST[$field['name']])
				{
					echo '<td><input class="input" type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.$_POST[$field['name']].'" /></td>';
				} else {
					echo '<td><input class="input" type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="" /></td>';
				}
			}
			if($field['input_type'] == 'cb'){
				if(($field['default_state'] == 'checked') OR isset($_POST[$field['name']])){
					echo '<td><input checked="checked" type="checkbox" name="'.$field['name'].'" id="'.$field['name'].'" value="1" /></td>';
				} else {
					echo '<td><input type="checkbox" name="'.$field['name'].'" id="'.$field['name'].'" value="1" /></td>';
				}
			}
			if($field['input_type'] == 'ta'){
				if($_POST[$field['name']])
				{
					echo '<td><textarea class="txarea" name="'.$field['name'].'" id="'.$field['name'].'" rows="10" cols="61">'.$_POST[$field['name']].'</textarea></td>';
				} else {
					echo '<td><textarea class="txarea" name="'.$field['name'].'" id="'.$field['name'].'" rows="10" cols="61"></textarea></td>';
				}
			}
			if($field['input_type'] == 'we'){
				if($_POST[$field['name']])
				{
					echo '<td><textarea name="'.$field['name'].'" id="'.$field['name'].'" rows="10" cols="61">'.$_POST[$field['name']].'</textarea></td>';
				} else {
					echo '<td><textarea name="'.$field['name'].'" id="'.$field['name'].'" rows="10" cols="61"></textarea></td>';
				}
				?>
<script type="text/javascript">
(function($)
{
  $('#<?php echo $field['name'];?>').wysiwyg({
    controls: {
      strikeThrough : { visible : true },
      underline     : { visible : true },
      
      separator00 : { visible : true },
      
      justifyLeft   : { visible : true },
      justifyCenter : { visible : true },
      justifyRight  : { visible : true },
      justifyFull   : { visible : true },
      
      separator01 : { visible : true },
      
      indent  : { visible : true },
      outdent : { visible : true },
      
      separator02 : { visible : true },
      
      subscript   : { visible : true },
      superscript : { visible : true },
      
      separator03 : { visible : true },
      
      undo : { visible : true },
      redo : { visible : true },
      
      separator04 : { visible : true },
      
      insertOrderedList    : { visible : true },
      insertUnorderedList  : { visible : true },
      insertHorizontalRule : { visible : true },
      
      h4mozilla : { visible : true && $.browser.mozilla, className : 'h4', command : 'heading', arguments : ['h4'], tags : ['h4'], tooltip : "Header 4" },
      h5mozilla : { visible : true && $.browser.mozilla, className : 'h5', command : 'heading', arguments : ['h5'], tags : ['h5'], tooltip : "Header 5" },
      h6mozilla : { visible : true && $.browser.mozilla, className : 'h6', command : 'heading', arguments : ['h6'], tags : ['h6'], tooltip : "Header 6" },
      
      h4 : { visible : true && !( $.browser.mozilla ), className : 'h4', command : 'formatBlock', arguments : ['<H4>'], tags : ['h4'], tooltip : "Header 4" },
      h5 : { visible : true && !( $.browser.mozilla ), className : 'h5', command : 'formatBlock', arguments : ['<H5>'], tags : ['h5'], tooltip : "Header 5" },
      h6 : { visible : true && !( $.browser.mozilla ), className : 'h6', command : 'formatBlock', arguments : ['<H6>'], tags : ['h6'], tooltip : "Header 6" },
      
      separator07 : { visible : true },
      
      cut   : { visible : true },
      copy  : { visible : true },
      paste : { visible : true }
    }
  });
})(jQuery);
    </script>
				<?php
			}
			if($field['input_type'] == 'so'){
				echo '<td><select class="input" name="'.$field['name'].'" id="'.$field['name'].'">';
				$options = $admin->get_options($field['id']);
				
				echo '<option value="">' . $lang->line('common_select_one') . '</option>';
				foreach($options as $option)
				{
					if(isset($_POST[$field['name']]) AND ($_POST[$field['name']] == $option['save']))
					{
						echo '<option value="'.$option['save'].'" selected="selected">'.$option['display'].'</option>';
					} else {
						echo '<option value="'.$option['save'].'">'.$option['display'].'</option>';
					}
				}
				echo '</select></td>';
			}
			if($field['input_type'] == 'uf'){
				if($_POST[$field['name']])
				{
					echo '<td><input class="input" value="'.$_POST[$field['name']].'" type="file" name="'.$field['name'].'" id="'.$field['name'].'" value="" /></td>';
				} else {
					echo '<td><input class="input" type="file" name="'.$field['name'].'" id="'.$field['name'].'" value="" /></td>';
				}
			}
			if($field['input_type'] == 'dt'){
				if(($field['auto_save_timestamp_new'] != 'y') AND ($field['auto_save_timestamp_update'] != 'y')){
					if($_POST[$field['name']])
					{
						echo '<td><input class="input" value="'.$_POST[$field['name']].'" onfocus="javascript:NewCssCal(\''.$field['name'].'\',\'ddmmyyyy\',\'arrow\',true,24,false);"  type="text" name="'.$field['name'].'" id="'.$field['name'].'"  /></td>';
					} else {
						echo '<td><input class="input" onfocus="javascript:NewCssCal(\''.$field['name'].'\',\'ddmmyyyy\',\'arrow\',true,24,false);"  type="text" name="'.$field['name'].'" id="'.$field['name'].'"  /></td>';
					}
				}
			}
			if($field['input_type'] == 'fk'){
				echo '<td><select class="input" name="'.$field['name'].'" id="'.$field['name'].'">';
				echo '<option value="">' . $lang->line('common_select_one') . '</option>';
				$options = $admin->get_records_string_rep($field['foreignkey_table']);
				foreach($options as $option)
				{
					if(strlen($option['display']) > 20)
					{
						$option_display_name = strip_tags(substr($option['display'],0,30));
					} else {
						$option_display_name = strip_tags($option['display']);
					}
					if(isset($_POST[$field['name']]) AND ($_POST[$field['name']] == $option['id']))
					{
						echo '<option value="'.$option['id'].'" selected="selected">'.$option_display_name.'</option>';
					} else {
						echo '<option value="'.$option['id'].'">'.$option_display_name.'</option>';
					}
				}
				echo '</select></td>';
			}
		}
		echo '</tr>';
	}
?>
</tbody>
</table>
<p><span style="color: #ff0000;">*</span> - <?php echo $lang->line('tables_required');?></p>
<br/>
<p><input name="save_cont" id="submit_cont" class="crtbtn_gr" value="<?php echo $lang->line('tables_save_and_another');?>" type="submit"> <input name="save" id="submit" class="crtbtn_gr" value="<?php echo $lang->line('tables_save_and_finish');?>" type="submit"></p>
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