<?php
class Language {

	var $language	= array();
	Var $idiom = '';

	function Language($trans_page = '', $path = ''){
		if(!isset($_SESSION['sadmin_idiom']) OR ($_SESSION['sadmin_idiom'] == '')){
			$this->idiom = 'english';
		} else {
			$this->idiom = $_SESSION['sadmin_idiom'];
		}
		
		if(file_exists($path.'language/'.$this->idiom.'/common.php')){
			include($path.'language/'.$this->idiom.'/common.php');
			if($common_trans){
				$this->language = $common_trans;
			}
		}
		if($trans_page != ''){
			if(file_exists($path.'language/'.$this->idiom.'/'.$trans_page.'.php')){
				include($path.'language/'.$this->idiom.'/'.$trans_page.'.php');
				if($translation){
					$this->language = array_merge($this->language, $translation);
				}
			}
		}
	}
	
	function line($line = ''){
		if(($line == '') OR (!isset($this->language[$line]))){
			return 'nothing';
		} else {
			return $this->language[$line];
		}
	}

}
?>