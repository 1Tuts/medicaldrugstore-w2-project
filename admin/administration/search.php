<?php
session_start();
if(!$_SESSION['sadmin_logged_in']){
	header('Location: ../login.php');
	exit;
}
include '../includes/admin.php';
include '../includes/actions.php';
$admin = new Admin();
$acts = new Actions();
$success = '';
$error = '';

include '../includes/language.php';
$lang = new Language('tables', '../includes/');

if(!$admin->user_has_permission('access_admin_dashboard'))
{
	header('Location: access_denied.php');
	exit;
}

if(isset($_GET['table_id']))
{
	$table_id = intval($_GET['table_id']);
}

if(isset($_GET['page']))
{
	$page = intval($_GET['page']);
	
} else {
	$page = 1;
}
$rows = 30;
$offset = ($page - 1) * $rows;


$table = $admin->get_table($table_id);
if($table['displayname']){
	$display_name = $table['displayname'];
} else {
	$display_name = $table['name'];
}

if(!$admin->does_user_have_access('change_page_'.$table['name'], 'tables')){
	header('Location: access_denied.php');
	exit;
}

$fields = $admin->get_table_fields($table_id);
foreach($fields as $field){
	if($field['input_type'] == 'pk'){
		$primary_key_field = $field['name'];
	}
}



if(isset($_POST['go']))
{
	if($_POST['action'] != ''){
		$action = $_POST['action'];
		$results = $acts->run_function($table['name'], $primary_key_field, $action);
		if($results['status'] == 'success'){
			$success .= $results['success_message'];
		} else {
			$error .= $results['error_message'];
		}
	}
}

if(isset($_POST['search']))
{
	if(($_POST['keyword'] != '') OR ($_POST['keyword'] != $lang->line('tables_search'))){
		$keyword_post = str_replace(' ', '+', $_POST['keyword']);
		header('Location: search.php?table_id='.$table_id.'&q='.$keyword_post);
		exit();
	}
}
$q = '';
if(isset($_POST['q']) OR isset($_GET['q'])){
	if(isset($_POST['q'])){
		$q = str_replace('+', ' ', $_POST['q']);
	} else {
		$q = str_replace('+', ' ', $_GET['q']);
	}
	
}

if(($q == '') OR ($q == 'Search...')){
	header('Location: change_list.php?table_id='.$table_id);
	exit();
}



$extra_serach_qry = "";
if(isset($_GET['order_field'])){
	$order = $admin->clean($_GET['order_field']);
	$extra_serach_qry = "ORDER BY $order ";
}

$extra_serach_qry = $extra_serach_qry . "LIMIT $offset, $rows";

$search_options = array();
$search_options['columns'] = '*';
$search_options['method'] = 'OR';
$search_options['extra_sql'] = $extra_serach_qry;

$count_options = array();
$count_options['columns'] = '*';
$count_options['method'] = 'OR';
$count_options['extra_sql'] = '';

$search_columns = array();

foreach($fields as $field){
	if($field['input_type'] != 'pk'){
		$search_columns[] = $field['name'];
	}
}

$records = $admin->search_table($table['name'], $search_columns, $q, $search_options);
$number_of_records = count($admin->search_table($table['name'], $search_columns, $q, $count_options));
$number_of_pages = ceil( $number_of_records / $rows );
$all_tables = $admin->get_all_tables();
$action_list = $acts->get_defined_functions();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Search Results - <?php echo $display_name;?></title>
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
		<li>Search Results</li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1>Search Results - <?php echo $display_name;?>&nbsp;<span><a href="add_form.php?table_id=<?php echo $table_id;?>" title="Create New Record">[+]</a></span></h1>
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
	echo '<p class="error_box">'.$error.'</p><div class="clear"></div><br/>';
if($success)
	echo '<p class="valid_box">'.$success.'</p><div class="clear"></div><br/>';
?>					
<?php if($records){?>    
<form action="search.php?table_id=<?php echo $table_id;?>&q=<?php echo $q;?>" method="post">     
<table>
<tbody>
<tr>
<td>
<select class="input" name="action" id="action">
<option value=""><?php echo $lang->line('tables_select_an_action');?></option>
<?php
foreach($action_list as $an_act)
{
	if(($an_act['table'] == 'all') OR ($an_act['table'] == $table['name']))
		echo '<option value="'.$an_act['function_name'].'">'.$an_act['display_message'].'</option>';
}
?>
<?php if($admin->does_user_have_access('delete_page_'.$table['name'], 'tables')){?>
<option value="delete"><?php echo $lang->line('common_delete');?></option>
<?php }?>
</select>
</td>
<td>&nbsp;&nbsp;<input type="submit" class="crtbtn_gr small_bt" value="GO" name="go">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>
<div>
	<input class="input" type="text" name="keyword" value="<?php echo $lang->line('tables_search');?>" onfocus="if (this.value == '<?php echo $lang->line('tables_search');?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo $lang->line('tables_search');?>';}" />
	&nbsp;&nbsp;<input class="crtbtn_gr small_bt" type="submit" name="search" value="GO" />
</div>
</td>
</tr>
</tbody>
</table>
<br/>
<table cellspacing="0" cellpadding="5px;" id="change_list"  class="main">
<tr class="table-top">
<td id="check" class="for_main"></td>
<?php
foreach($fields as $field){
	if($field['input_type'] != 'pk' AND $field['string_rep'] == 'y'){
		if($field['displayname'])
		{
			echo '<td class="for_main"><a href="search.php?q='.$q.'&table_id='.$table_id.'&order_field='.$field['name'].'">'.ucfirst($field['displayname']).'</a></td>';
		} else {
			echo '<td class="for_main"><a href="search.php?q='.$q.'&table_id='.$table_id.'&order_field='.$field['name'].'">'.ucfirst($field['name']).'</a></td>';
		}
	}
}
foreach($fields as $field){
	if($field['input_type'] != 'pk' AND $field['string_rep'] != 'y'){
		if($field['displayname'])
		{
			echo '<td class="for_main"><a href="search.php?q='.$q.'&table_id='.$table_id.'&order_field='.$field['name'].'">'.ucfirst($field['displayname']).'</a></td>';
		} else {
			echo '<td class="for_main"><a href="search.php?q='.$q.'&table_id='.$table_id.'&order_field='.$field['name'].'">'.ucfirst($field['name']).'</a></td>';
		}
	}
}
?>
</tr>
<?php
foreach($records as $record){
	$primary_key = $record[$primary_key_field];
	echo '<tr>';
	echo '<td id="check" class="for_main"><input type="checkbox" rel="action_check" value="'.$primary_key.'" name="check_'.$primary_key.'" id="check_'.$primary_key.'" /></td>';
	foreach($fields as $field){
		if($field['string_rep'] == 'y'){
			if($field['input_type'] == 'fk'){
				foreach($all_tables as $tables)
				{
					if($tables['id'] == $field['foreignkey_table'])
					{
						$linked_record = $admin->get_related_record($tables['name'], $record[$field['name']]);
						$string_rep = $admin->get_string_rep($tables['id']);
						if($string_rep){
							echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.$linked_record[$string_rep].'</a></td>';
						} else {
							echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.$tables['name'].'-record-'.$record[$field['name']].'</a></td>';
						}
					}
				}
			}
			if(($field['input_type'] == 'ti') OR ($field['input_type'] == 'cb')){
				echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.$record[$field['name']].'</td>';
			}
			if($field['input_type'] == 'ta' OR $field['input_type'] == 'we'){
				if(strlen($record[$field['name']]) > 30)
				{
					echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.strip_tags(substr($record[$field['name']],0,30)).'...</a></td>';
				} else {
					echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.strip_tags($record[$field['name']]).'</a></td>';
				}
			}
			if($field['input_type'] == 'so'){
				$options = $admin->get_options($field['id']);
				foreach($options as $option)
				{
					if($record[$field['name']] == $option['save'])
					{
						if($option['display']){
							echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.$option['display'].'</a></td>';
						} else {
							echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.$record[$field['name']].'</a></td>';
						}
						
					}
				}
			}
			if($field['input_type'] == 'uf'){
				echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.$record[$field['name']].'</a></td>';
			}
			
			if($field['input_type'] == 'dt'){
				if($field['datetime_save_format'])
				{
					if($field['datetime_display_format'])
					{
						echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.date($field['datetime_display_format'], $record[$field['name']]).'</a></td>';
					} else {
						echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.date($field['datetime_save_format'], $record[$field['name']]).'</a></td>';
					}
				} else {
					echo '<td class="for_main"><a href="change_form.php?table_id='.$table_id.'&record_id='.$primary_key.'">'.date("D F j, Y, g:i a", $record[$field['name']]).'</a></td>';
				}
				
			}
			
		}
	}
	foreach($fields as $field){
		if(($field['input_type'] != 'pk') AND ($field['string_rep'] != 'y')){
			if($field['input_type'] == 'fk'){
				foreach($all_tables as $tables)
				{
					if($tables['id'] == $field['foreignkey_table'])
					{
						$linked_record = $admin->get_related_record($tables['name'], $record[$field['name']]);
						$string_rep = $admin->get_string_rep($tables['id']);
						if($string_rep){
							echo '<td class="for_main">'.$linked_record[$string_rep].'</td>';
						} else {
							echo '<td class="for_main">'.$tables['name'].'-record-'.$record[$field['name']].'</td>';
						}
					}
				}
			}
			if(($field['input_type'] == 'ti') OR ($field['input_type'] == 'cb')){
				echo '<td class="for_main">'.$record[$field['name']].'</td>';
			}
			if($field['input_type'] == 'ta' OR $field['input_type'] == 'we'){
				if(strlen($record[$field['name']]) > 30)
				{
					echo '<td class="for_main">'.strip_tags(substr($record[$field['name']],0,30)).'...</td>';
				} else {
					echo '<td class="for_main">'.strip_tags($record[$field['name']]).'</td>';
				}
			}
			if($field['input_type'] == 'so'){
				$options = $admin->get_options($field['id']);
				foreach($options as $option)
				{
					if($record[$field['name']] == $option['save'])
					{
						if($option['display']){
							echo '<td class="for_main">'.$option['display'].'</td>';
						} else {
							echo '<td class="for_main">'.$record[$field['name']].'</td>';
						}
						
					}
				}
			}
			if($field['input_type'] == 'uf'){
				echo '<td class="for_main">'.$record[$field['name']].'</td>';
			}
			
			if($field['input_type'] == 'dt'){
				if($field['datetime_save_format'])
				{
					echo '<td class="for_main">'.$record[$field['name']].'</td>';
				} else {
					echo '<td class="for_main">'.date("D F j, Y, g:i a", $record[$field['name']]).'</td>';
				}
				
			}
			
		}
	}
	echo '</tr>';
}
?>
</table>
</form>   
<table border="0" cellpadding="0" cellspacing="0" id="paging-table">
<tr>
<td>
<?php 
if(isset($_GET['order_field'])){
	$order_qry = '&order_field='.$_GET['order_field'];
} else {
	$order_qry = '';
}
if($page > 1){
$last = $page - 1;
?>
<a href="search.php?table_id=<?php echo $table_id;?>&page=1&q=<?php echo $q;?><?php echo $order_qry;?>" class="page-far-left"></a>
<a href="search.php?table_id=<?php echo $table_id;?>&page=<?php echo $last;?>&q=<?php echo $q;?><?php echo $order_qry;?>" class="page-left"></a>
<?php }?>
<div id="page-info"><?php echo $lang->line('common_page');?> <strong><?php echo $page;?></strong> / <?php echo $number_of_pages;?></div>
<?php 
if($number_of_pages > $page){
$next = $page + 1;
?>
<a href="search.php?table_id=<?php echo $table_id;?>&page=<?php echo $next;?>&q=<?php echo $q;?><?php echo $order_qry;?>" class="page-right"></a>
<a href="search.php?table_id=<?php echo $table_id;?>&page=<?php echo $number_of_pages;?>&q=<?php echo $q;?><?php echo $order_qry;?>" class="page-far-right"></a>
<?php }?>
</td>
</tr>
</table>

<?php } else {
echo '<p>' . $lang->line('tables_no_records_match') . ': "'.$q.'"</p>';
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