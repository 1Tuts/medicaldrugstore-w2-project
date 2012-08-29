<?php
/**
 * Universal Admin - login.php
 *
 * 
 *
 * @package		Universal Admin
 * @author		Robert Nduati
 */
session_start();

include 'includes/admin.php';
$admin = new Admin();

$error = '';
$success = '';

if(isset($_POST['submit'])){
	if($_POST['email'] == '')
		$error .= ' The email is required.';
	if($_POST['username'] == '')
		$error .= ' The username is required.';
	if($_POST['pass'] == '')
		$error .= ' The password is required.';
	if($_POST['pass'] != $_POST['cpass'])
		$error .= ' The confirmation password does not match the password.';
	
	if($error == ''){
		
mysql_query("CREATE TABLE IF NOT EXISTS sadmin_fields (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL,
  displayname varchar(200) DEFAULT NULL,
  table_id int(11) NOT NULL,
  input_type varchar(2) NOT NULL,
  is_required varchar(1) DEFAULT NULL,
  string_rep varchar(2) DEFAULT NULL,
  default_state varchar(12) DEFAULT NULL,
  value_when_not_checked varchar(200) DEFAULT NULL,
  value_when_checked varchar(200) DEFAULT NULL,
  select_options varchar(400) DEFAULT NULL,
  place_to_store varchar(200) DEFAULT NULL,
  allowed_extensions varchar(200) DEFAULT NULL,
  datetime_save_format varchar(100) DEFAULT NULL,
  foreignkey_table int(11) DEFAULT NULL,
  display_on_change_list varchar(1) DEFAULT NULL,
  auto_save_timestamp_new varchar(1) DEFAULT NULL,
  auto_save_timestamp_update varchar(1) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

mysql_query("CREATE TABLE IF NOT EXISTS sadmin_permissions (
  id int(11) NOT NULL AUTO_INCREMENT,
  name text NOT NULL,
  permkey text NOT NULL,
  editable varchar(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

mysql_query("CREATE TABLE IF NOT EXISTS sadmin_roles (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  homepage varchar(200) DEFAULT NULL,
  editable varchar(1) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

mysql_query("CREATE TABLE IF NOT EXISTS sadmin_roles_permissions (
  id int(11) NOT NULL AUTO_INCREMENT,
  role_id int(11) NOT NULL,
  permission_id int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

mysql_query("CREATE TABLE IF NOT EXISTS sadmin_tables (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  displayname varchar(100) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

mysql_query("CREATE TABLE IF NOT EXISTS sadmin_users (
  id int(11) NOT NULL AUTO_INCREMENT,
  fname varchar(20) DEFAULT NULL,
  lname varchar(20) DEFAULT NULL,
  username varchar(20) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(32) NOT NULL,
  role_id int(11) NOT NULL,
  dateadded bigint(20) NOT NULL,
  lastlogin bigint(20) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
		
		
		$everything_skeleton_key = $admin->new_permission('Can do everything everywhere', 'can_do_everything_everywhere', 'n');
		$access_admin_dash = $admin->new_permission('Access Admin Dashboard', 'access_admin_dashboard', 'n');
		$config_skeleton_key = $admin->new_permission('Can do everything relating to configuration', 'can_do_everything_config', 'n');
		$auth_skeleton_key = $admin->new_permission('Can do everything relating to authentication', 'can_do_everything_auth', 'n');
		$tables_skeleton_key = $admin->new_permission('Can do everything relating to tables', 'can_do_everything_tables', 'n');
		$add_change_delete_tables = $admin->new_permission('Add, Change and Delete table configurations', 'add_change_delete_sadmin_config_table', 'n');
		$add_tables = $admin->new_permission('Add table configurations', 'add_sadmin_config_table', 'n');
		$change_tables = $admin->new_permission('Change table configurations', 'change_sadmin_config_table', 'n');
		$delete_tables = $admin->new_permission('Delete table configurations', 'delete_sadmin_config_table', 'n');
		$add_change_delete_fields = $admin->new_permission('Add, Change and Delete field configurations', 'add_change_delete_sadmin_config_field', 'n');
		$add_fields = $admin->new_permission('Add field configurations', 'add_sadmin_config_field', 'n');
		$change_fields = $admin->new_permission('Change field configurations', 'change_sadmin_config_field', 'n');
		$delete_fields = $admin->new_permission('Delete field configurations', 'delete_sadmin_config_field', 'n');
		$add_change_delete_users = $admin->new_permission('Add, Change and Delete users', 'add_change_delete_sadmin_users', 'n');
		$add_users = $admin->new_permission('Add users', 'add_sadmin_users', 'n');
		$change_users = $admin->new_permission('Change users', 'change_sadmin_users', 'n');
		$delete_users = $admin->new_permission('Delete users', 'delete_sadmin_users', 'n');
		$add_change_delete_roles = $admin->new_permission('Add, Change and Delete roles', 'add_change_delete_sadmin_roles', 'n');
		$add_roles = $admin->new_permission('Add roles', 'add_sadmin_roles', 'n');
		$change_roles = $admin->new_permission('Change roles', 'change_sadmin_roles', 'n');
		$delete_roles = $admin->new_permission('Delete roles', 'delete_sadmin_roles', 'n');
		$add_change_delete_permissions = $admin->new_permission('Add, Change and Delete permissions', 'add_change_delete_sadmin_permissions', 'n');
		$add_permissions = $admin->new_permission('Add permissions', 'add_sadmin_permissions', 'n');
		$change_permissions = $admin->new_permission('Change permissions', 'change_sadmin_permissions', 'n');
		$delete_permissions = $admin->new_permission('Delete permissions', 'delete_sadmin_permissions', 'n');
		$super_admin_role = $admin->new_role('Super Admin', '', 'y');
		$super_admin_role_permission = $admin->new_role_perm($super_admin_role, $everything_skeleton_key);
		$super_admin_role_permission_acces_dash = $admin->new_role_perm($super_admin_role, $access_admin_dash);
		$new_user = $admin->new_user('', '', $_POST['username'],  $_POST['email'],  $_POST['pass'], $super_admin_role);
		if($new_user)
			$success .= ' Your account has been successfully created. Delete the install.php file and then navigate to the universal admin root folder to get started.';
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en-US"><head>
<title>Universal Admin - Install</title>
<style>
*{margin:0;padding:0;}
body{
background-color:#f9f9f9;
padding-top:30px;
font:11px Arial,"Bitstream Vera Sans",sans-serif;
}
a {
color: green;
}
a:hover {
color: red;
}
h1{font-weight: normal}
form{
margin-left:8px;
padding:16px 16px 40px 16px;
font-weight:normal;
border-radius:5px;
background:#fff;
border:1px solid #e5e5e5;
-moz-box-shadow:rgba(200,200,200,1) 0 4px 18px;
-webkit-box-shadow:rgba(200,200,200,1) 0 4px 18px;
-khtml-box-shadow:rgba(200,200,200,1) 0 4px 18px;
box-shadow:rgba(200,200,200,1) 0 4px 18px;}
.crtbtn_gr{
	border:1px outset #ccc;
	padding:5px 2px 4px;
	color:#fff;
	min-width: 100px;
	text-align: center;
	cursor:pointer;
	background:#729e01;
	background:-webkit-gradient(linear, left top, left bottom,from(#a3d030),to(#729e01));
	background:-moz-linear-gradient(top,#a3d030,#729e01);
	background:-o-linear-gradient(top,#a3d030,#729e01);
	background:linear-gradient(top,#a3d030,#729e01);
	-moz-border-radius:7px; -webkit-border-radius:7px;
}


.crtbtn_gr:hover{
	color:#000;
	background:#ddd;
	background:-webkit-gradient(linear, left top, left bottom,from(#fff),to(#ddd));
	background:-moz-linear-gradient(top,#fff,#ddd);
	background:-o-linear-gradient(top,#fff,#ddd);
	background:linear-gradient(top,#fff,#ddd);
}
#login form p{
margin-bottom:0;
}
label{
color:#777777;
font-size:14px;
}
form .submit,.alignright{
float:right;
}
form p{
margin-bottom:24px;
}
#login{
width:320px;
margin:7em auto;
}
#login_error,.message{
margin:0 0 16px 8px;
border-width:1px;
border-style:solid;
padding:12px;
-moz-border-radius:3px;
-khtml-border-radius:3px;
-webkit-border-radius:3px;
border-radius:3px;
}
#pass,#email,#username,#cpass{
font-size:20px;
width:97%;
padding:3px;
margin-top:2px;
margin-right:6px;
margin-bottom:16px;
border:1px solid #e5e5e5;
background:#fbfbfb;
}
#pass:focus,#email:focus,#username:focus,#cpass:focus{
border:1px solid #cccccc;
background:#dfdfdf;
}
input{
color:#555;
}
.clear{
clear:both;
}

</style>
</head>
<body class="login">

<div id="login"><h1>Universal Admin - Install</h1>
<br/>
<form name="installform" id="installform" action="install.php" method="post">
	<p>Please submit the following information for the first admin user account:</p>
	<?php
	if($error)
		echo '<br/><p style="color: red;">'.$error.'</p><br/>';
	if($success)
		echo '<br/><p style="color: green;">'.$success.'</p><br/>';
	?>
	<p>
		<label>Email<br>
		<input name="email" id="email" class="input" size="20" type="text" value="<?php echo ((isset($_POST['email'])) ? $_POST['email'] : '');?>" ></label>
	</p>
	<p>
		<label>Username<br>
		<input name="username" id="username" class="input" size="20" type="text" value="<?php echo ((isset($_POST['username'])) ? $_POST['username'] : '');?>" ></label>
	</p>
	<p>
		<label>Password<br>
		<input name="pass" id="pass" class="input" size="20" type="password" value="<?php echo ((isset($_POST['pass'])) ? $_POST['pass'] : '');?>" ></label>
	</p>
	<p>
		<label>Confirm Password<br>
		<input name="cpass" id="cpass" class="input" size="20" type="password" value="<?php echo ((isset($_POST['cpass'])) ? $_POST['cpass'] : '');?>" ></label>
	</p>
	<p class="submit">
		<input name="submit" id="submit" class="crtbtn_gr" value="Submit" type="submit">
	</p>
</form>
<br/>
</div>

</body></html>
<?php $admin->disconnect();?>