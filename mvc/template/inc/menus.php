<?php
	//$menus = "Menu 1 name,menu1-url; Menu 2 name,menu2-url";
	$menus = "صفحه اصلی,home; درباره ما,aboutus; اخبارپزشکی,news; داروها,price1; محصولات بهداشتی,price2; سفارش دارو,order; بیمه های طرف قرارداد,garantee; گالری,gallery; ارتباط با ما,contactus";

	$menus = explode('; ', $menus);
	echo '<ul>';
	foreach ($menus as $menu) {
		$menu = explode(',', $menu);
		echo "<li><a href='./?page=$menu[1]'>$menu[0]</a></li>";
	}
	echo '</ul>';
?>