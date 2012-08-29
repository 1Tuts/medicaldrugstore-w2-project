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

$tables = $admin->get_all_tables();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang->line('common_configuration_menu');?></title>
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
		<li><?php echo $lang->line('common_configuration_menu');?></li>
		</ul>
		<br/>
		<div id="page-heading">
			<h1><?php echo $lang->line('common_configuration_menu');?></h1>
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
<?php if($admin->does_user_have_access('add_page_sadmin_config_table', 'config')){?>
			<h2><a href="add_table.php" title="<?php echo $lang->line('auth_config_index_add_table');?>"><img src="../assets/images/plus_16.png" alt=""> <?php echo $lang->line('auth_config_index_add_table_cap');?></a></h2><br/>
			<?php }?>
					
<?php
if($tables){
	echo '<table class="widget" cellspacing="0">
<tr>
<th colspan="2">' . $lang->line('auth_config_index_add_table_heading') . '</th>
</tr>';
	$counter = 1;
	$rows = count($tables);
	foreach($tables as $table)
	{
		
		if( $counter&1 ){
			echo '<tr>';
		} else {
			echo '<tr class="odd-row">';
		}
		if($admin->does_user_have_access('change_page_sadmin_config_table', 'config')){
			echo '<td style="text-align: left;"><a href="change_table.php?table_id='.$table['id'].'">'.$table['name'].'</a></td>';
			echo '<td width="25%"><a href="change_table.php?table_id='.$table['id'].'" title="' . $lang->line('auth_config_table_edit') . '"><img src="../assets/images/pencil_16.png" alt="' . $lang->line('common_edit_img_alt') . '"></a></td>';
		} else {
			echo '<td style="text-align: left;"><a href="change_table.php?table_id='.$table['id'].'">'.$table['name'].'</a></td>';
			echo '<td width="25%"><a href="change_table.php?table_id='.$table['id'].'" title="' . $lang->line('auth_config_table_edit') . '"><img src="../assets/images/pencil_16.png" alt="' . $lang->line('common_edit_img_alt') . '"></a></td>';
		}
		echo '</tr>';
		$counter++;
	}
	echo '</table>';
} else {
	echo '<p>' . $lang->line('auth_config_index_no_tables') . '<p>';
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