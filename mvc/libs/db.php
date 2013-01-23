<?php

$db = array(
	'con' => '',
	'name'   => 'dbtest',
	'server' => 'localhost',
	'user'   => 'root',
	'pass'   => ''

);

function db_err(){
	$err_num = mysql_errno();
	$err_msg = mysql_error();
	die("Error $err_num: $err_msg");
}

function db_connect(){
	global $db;
	if($db['con']) return;
	$db['con'] = @mysql_connect($db['server'],$db['user'],$db['pass']) or db_err();
	@mysql_select_db($db['name'],$db['con']) or db_err();
}

function db_query($q){
	global $db;
	$res = @mysql_query($q,$db['con']) or db_err();
	return $res;
}

function db_close(){
	global $db;
	@mysql_close($db['con']);
}

function db_getrows($table, $sellect = '*', $where = true){
	$res = db_query("SELECT $sellect FROM $table WHERE $where");
	while($row=mysql_fetch_array($res,MYSQL_ASSOC)){
		$rows[] = $row; // append to last
	}
	return $rows;
}
