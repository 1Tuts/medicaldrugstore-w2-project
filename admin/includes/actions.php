<?php
class Actions{
	private $myconn;
	private $hostname;
	private $username;
	private $password;
	private $database;
	private $current_table;
	private $current_table_primary_key;
	private $show_errors = false;
	private $action_functions = array();
	private $posted_ids = array();
	
	function __construct() 
	{
		//$this->action_functions[] = array('table'=>'all', 'function_name'=>'Something', 'display_message'=>'A test action');
	}
	
	
	
	
	
	private function delete()
	{
		$results = array();
		$results['success_message'] = 'The records were successfully deleted.';
		$results['error_message'] = 'There was an error. Please try again.';
		$table = $this->current_table;
		$primary_key = $this->current_table_primary_key;
		foreach($this->posted_ids as $id)
		{
			$qry = mysql_query("DELETE FROM $table WHERE $primary_key = $id");
		}
		$results['status'] = 'success';
		
		return $results;
	}
	
	
	public function run_function($table, $primary_key, $function)
	{
		$this->current_table = $this->clean($table);
		$this->current_table_primary_key = $this->clean($primary_key);
		$function = $this->clean($function);
		$this->get_posted_ids();
		return $this->$function();
	}
	public function get_defined_functions()
	{
		return $this->action_functions;
	}
	private function get_posted_ids()
	{
		foreach($_POST as $key => $value)
		{
			if(substr($key,0,6) == "check_")
				$this->posted_ids[] = intval($this->clean($value));
		}
		return true;
	}
	public function clean($str) 
	{
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	public function last_error(){
		return $this->the_last_error;
	}

}
?>