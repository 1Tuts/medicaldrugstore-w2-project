<?php
class Validation{
	private $the_value;
	private $the_field;
	private $validation_functions = array();
	
	function __construct() 
	{
		//$this->validation_functions[] = array('table'=>'test_polls', 'field' => 'question', 'function'=>'numeric_value');
	}
	
	
	
	
	
	private function numeric_value()
	{
		$field_name = $this->the_field;
		
		$results = array();
		$results['error_message'] = " The value provided for $field_name needs to be numeric.";
		$results['valid'] = true;
		
		$str = trim($this->the_value);
		$holder_array = str_split($str);
		$holder_str = '';
		foreach($holder_array as $holder)
		{
			if (!preg_match("/[0-9]/", $holder))
			{
				$results['valid'] = false;
			}
		}	
		return $results;
	}
	
	public function run_validation($table, $field, $field_display_name, $value)
	{
		$table = $this->clean($table);
		$field = $this->clean($field);
		$this->the_field = $this->clean($field_display_name);
		$this->the_value = $this->clean($value);
		$error = '';
		foreach($this->validation_functions as $validation_function)
		{
			if(($validation_function['table'] == $table) AND ($validation_function['field'] == $field))
			{
				$function = $validation_function['function'];
				$validate = $this->$function();
				if(!$validate['valid'])
					$error .= $validate['error_message'];
			}
		}
		return $error;
	}
	
	public function clean($str) 
	{
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
}
?>