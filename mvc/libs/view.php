
<?php

function gen_drugs_price($drugs,$field){
	$html = '';
	foreach($drugs as $drug){
		$text = $drug[$field];
		if($field=='price') $text.=' ريال';
		$html .= "<li><a href='./?page=detail&id=$drug[id]'>$text</a></li>";
	}
	return $html;
}