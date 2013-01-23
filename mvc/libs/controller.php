<?php
require_once 'libs/moduls.php';
require_once 'libs/view.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'first';

if($page == 'users'){

	db_connect();

	$users = db_getrows('users','fname,lname,email');

	$table = show_table($users);

	db_close();

}


if(!file_exists("template/$page.php")) $page='404';

@include "template/$page.php";
?>