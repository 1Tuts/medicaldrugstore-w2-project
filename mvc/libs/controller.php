<?php
require_once 'libs/moduls.php';
require_once 'libs/view.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if($page == 'price1'){

	db_connect();

	$drugs_array = db_getrows('drugs','id,name,price,sicks');

	$drugs_html['name'] = gen_drugs_price($drugs_array,'name');
	$drugs_html['price'] = gen_drugs_price($drugs_array,'price');
	$drugs_html['sicks'] = gen_drugs_price($drugs_array,'sicks');

	db_close();

}


if(!file_exists("template/$page.php")) $page='404';

@include "template/$page.php";