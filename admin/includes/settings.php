<?php
/*
| -------------------------------------------------------------------
| UNIVERSAL PHP ADMIN SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	$config['hostname'] 			The hostname of your database server.
|	$config['username'] 			The username used to connect to the database
|	$config['password'] 			The password used to connect to the database
|	$config['database'] 			The name of the database you want to connect to
|	$config['table_prefix'] 		The prefix which will be added to the table names that Universal PHP Admin needs. 
|									If you change this make sure that you also change the table 
|									names in the database schema
|
|	$config['universal_admin_url'] 	URL to your Universal PHP Admin root WITH a trailing slash
|
|	$config['show_errors'] 			Set this to true when debugging and to false when you go live
|
|	$config['webmaster_email'] 		Email to be set in the outgiong emails
|	$config['webmaster_name']		Name to use in the outgiong emails
|
*/

$config['hostname'] = "localhost";
$config['username'] = "root";
$config['password'] = "";
$config['database'] = "drugstore";
$config['table_prefix'] = "admin_";
$config['universal_admin_url'] = "http://localhost/medical-drugstore/admin";
$config['show_errors'] = true;

$config['webmaster_email'] = 'hamishebahar.1989@gmail.com';
$config['webmaster_name'] = 'Hamishebahar';

$config['language'] = 'english';

/* End of file config.php */
?>