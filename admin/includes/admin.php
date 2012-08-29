<?php
class Admin{
	private $myconn;
	private $hostname;
	private $username;
	private $password;
	private $database;
	private $tables_table;
	private $fields_table;
	private $permissions_table;
	private $roles_table;
	private $roles_permissions_table;
	private $users_table;
	private $base_url;
	private $the_last_error = '';
	private $show_errors = false;
	
	private $webmaster_email;
	private $webmaster_name;
	
	function __construct() 
	{
		include "settings.php";
		$this->hostname = $config['hostname'];
		$this->username = $config['username'];
		$this->password = $config['password'];
		$this->database = $config['database'];
		$this->tables_table = $config['table_prefix']."tables";
		$this->fields_table = $config['table_prefix']."fields";
		$this->permissions_table = $config['table_prefix']."permissions";
		$this->roles_table = $config['table_prefix']."roles";
		$this->roles_permissions_table = $config['table_prefix']."roles_permissions";
		$this->users_table = $config['table_prefix']."users";
		$this->base_url = $config['universal_admin_url'];
		$this->show_errors = $config['show_errors'];
		$this->webmaster_email = $config['webmaster_email'];
		$this->webmaster_name = $config['webmaster_name'];
		
		$_SESSION['sadmin_idiom'] = $config['language'];
		
		$this->connect();
	}
	
	
	
	public function webmaster_email(){
		return $this->webmaster_email;
	}
	
	public function webmaster_name(){
		return $this->webmaster_name;
	}
	
	public function get_base_url(){
		return $this->base_url;
	}
	
	public function connect()
	{
		$result = false;
		$this->myconn = mysql_connect($this->hostname,$this->username,$this->password);
		if($this->myconn){
			$seldb = mysql_select_db($this->database,$this->myconn);
			if($seldb){
				$result = true;
			}  else {
				if($this->show_errors)
					die(mysql_error());
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $result;
	}
	
	public function disconnect() 
	{
		return  mysql_close($this->myconn);
	}
	
	public function clean($str) 
	{
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	public function valid_file_extension($name, $allowed_extensions)
	{
		$allowed_extensions = explode('|', $allowed_extensions);
		$extension = strtolower($this->get_extension($name));
		if (in_array($extension, $allowed_extensions, TRUE))
		{
			return true;
		} else {
			return false;
		}
		
		return true;
	}
	
	private function get_extension($filename)
	{
		$x = explode('.', $filename);
		return end($x);
	}
	
	public function clean_file_name($filename)
	{
		$invalid = array("<!--","-->","'","<",">",'"','&','$','=',';','?','/',"%20","%22","%3c","%253c","%3e","%0e","%28","%29","%2528","%26","%24","%3f","%3b", "%3d");		
		$filename = str_replace($invalid, '', $filename);
		$filename = preg_replace("/\s+/", "_", $filename);
		return stripslashes($filename);
	}
	
	public function set_filename($path, $filename)
	{
		$file_ext = $this->get_extension($filename);
		if ( ! file_exists($path.$filename))
		{
			return $filename;
		}
		$new_filename = str_replace('.'.$file_ext, '', $filename);
		for ($i = 1; $i < 300; $i++)
		{			
			if ( ! file_exists($path.$new_filename.'_'.$i.'.'.$file_ext))
			{
				$new_filename .= '_'.$i.'.'.$file_ext;
				break;
			}
		}
		return $new_filename;
	}
	
	public function last_error()
	{
		return $this->the_last_error;
	}
	
	private function tableExists($table)
    {
        $tablesInDb = @mysql_query('SHOW TABLES FROM '.$this->database.' LIKE "'.$table.'"');
        if($tablesInDb){
            if(mysql_num_rows($tablesInDb)==1){
                return true;
            } else {
                return false;
            }
        }
    }
	
	private function fieldExists($table_id, $field)
	{
		$fields = mysql_list_fields($this->database, $this->get_table_name($table_id));
		$columns = mysql_num_fields($fields);       
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}      
		if (!in_array($field, $field_array)) {
			return false;
		} else {
			return true;
		}
	}
	
	public function fieldExists_public($tablename, $field)
	{
		$tablename = $this->clean($tablename);
		$field = $this->clean($field);
		
		$fields = mysql_list_fields($this->database, $this->get_table_name($tablename));
		$columns = mysql_num_fields($fields);       
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}      
		if (!in_array($field, $field_array)) {
			return false;
		} else {
			return true;
		}
	}
	
	private function get_table_name($id)
	{
		$results = '';
		$id = intval($this->clean($id));
		$table = $this->tables_table;
		$qry = mysql_query("SELECT * FROM $table WHERE id = $id");
		if($qry)
		{
			$row = mysql_fetch_array($qry);
			$results =  $row['name'];
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $results;
	}
	
	public function new_table($name, $display_name = '')
	{
		$return = 0;
		$name = $this->clean($name);
		$display_name = $this->clean($display_name);
		
		if($this->tableExists($name))
        {	
			$table = $this->tables_table;
		
			$qry = mysql_query("INSERT INTO $table (name, displayname) VALUES ('$name', '$display_name')");
			if($qry)
			{
				$table_id = mysql_insert_id();
				$this->create_table_permissions($name);
				
				$primary_key = mysql_query("SHOW index FROM $name WHERE Key_name = 'PRIMARY'");
				
				$fields = mysql_list_fields($this->database, $name);
				$columns = mysql_num_fields($fields);       
				for ($i = 0; $i < $columns; $i++) {
					$field_name = mysql_field_name($fields, $i);
					$meta = mysql_fetch_field($fields, $i);
					if($meta->primary_key == 1){
						$this->new_field($table_id,$field_name,'','pk','y','','','','','','','','','','','','');
					} else {
						$this->new_field($table_id,$field_name,'','ti','y','','','','','','','','','','','','');
					}
				}
				
				return $table_id;
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
			
		} else {
			$this->the_last_error = " The table '$name' does not exist.";
		}
		return $return;
	}
	
	private function create_table_permissions($name)
	{
		$do_all = $this->new_permission('Add, Change and Delete '.$name, 'add_change_delete_'.$name, 'n');
		$add = $this->new_permission('Add '.$name, 'add_'.$name, 'n');
		$change = $this->new_permission('Change '.$name, 'change_'.$name, 'n');
		$delete = $this->new_permission('Delete '.$name, 'delete_'.$name, 'n');
		return true;
	}
	
	public function new_field($table_id, 
								$name, 
								$display_name, 
								$input_type,
								$is_required,
								$string_rep,
								$default_state, 
								$value_when_not_checked, 
								$value_when_checked, 
								$select_options, 
								$place_to_store, 
								$allowed_extensions, 
								$datetime_save_format,
								$foreignkey_table,
								$display_on_change_list,
								$auto_save_timestamp_new,
								$auto_save_timestamp_update)
	{
		$return = 0;
		$table_id = intval($table_id);
		$name = $this->clean($name);
		$display_name = $this->clean($display_name);
		$input_type = $this->clean($input_type);
		$is_required = $this->clean($is_required);
		$string_rep = $this->clean($string_rep);
		$default_state = $this->clean($default_state);
		$value_when_not_checked = $this->clean($value_when_not_checked);
		$value_when_checked = $this->clean($value_when_checked);
		$select_options = $this->clean($select_options);
		$place_to_store = $this->clean($place_to_store);
		$allowed_extensions = $this->clean($allowed_extensions);
		$datetime_save_format = $this->clean($datetime_save_format);
		$foreignkey_table = intval($this->clean($foreignkey_table));
		$display_on_change_list = $this->clean($display_on_change_list);
		$auto_save_timestamp_new = $this->clean($auto_save_timestamp_new);
		$auto_save_timestamp_update = $this->clean($auto_save_timestamp_update);
		if($is_number){
			$is_number = 'y';
		} else {
			$is_number = 'n';
		}
		
		if($this->fieldExists($table_id, $name))
        {	
			$table = $this->fields_table;
		
			$qry = mysql_query("INSERT INTO $table (table_id, 
													name, 
													displayname, 
													input_type,
													is_required,
													string_rep,
													default_state,
													value_when_not_checked,
													value_when_checked,
													select_options,
													place_to_store,
													allowed_extensions,
													datetime_save_format,
													foreignkey_table,
													display_on_change_list,
													auto_save_timestamp_new,
													auto_save_timestamp_update
												) VALUES ($table_id, 
													'$name',
													'$display_name', 
													'$input_type',
													'$is_required',
													'$string_rep',
													'$default_state',
													'$value_when_not_checked',
													'$value_when_checked',
													'$select_options',
													'$place_to_store',
													'$allowed_extensions',
													'$datetime_save_format',
													$foreignkey_table,
													'$display_on_change_list',
													'$auto_save_timestamp_new',
													'$auto_save_timestamp_update')");
			if($qry)
			{
				return mysql_insert_id();
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
		} else {
			$this->the_last_error = " The field '$name' does not exist in the table ".$this->get_table_name($table_id).".";
		}
		return $return;
	}
	
	public function check_if_table_is_ready($id)
	{
		$fields = array();
		$results = false;
		$primary_key = false;
		$foreign_key = false;
		$foreign_table = false;
		$string_rep = false;
		$table = $this->fields_table;
		$qry = mysql_query("SELECT * FROM $table WHERE table_id = $id");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$fields[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		foreach($fields as $field)
		{
			if($field['input_type'] == 'pk')
				$primary_key = true;
		}
		foreach($fields as $field)
		{
			if($field['input_type'] == 'fk')
			{
				$foreign_key = true;
				if($field['foreignkey_table'])
				{
					$foreign_table = true;
				}
			}
		}
		foreach($fields as $field)
		{
			if($field['string_rep'] == 'y')
				$string_rep = true;
		}
		if($primary_key == true)
		{
			if($foreign_key == false)
				$results = $string_rep;
			if(($foreign_key == true) AND ($foreign_table == true))
				$results = $string_rep;
		}
		return $results;
	}
	
	public function get_all_tables()
	{
		$table = $this->tables_table;
		return $this->get_all_records($table);
	}
	
	public function get_all_ready_tables()
	{
		$table = $this->tables_table;
		$tables = $this->get_all_records($table);
		$return = array();
		foreach($tables as $t)
		{
			if($this->check_if_table_is_ready($t['id']))
				$return[] = $t;
		}
		return $return;
	}
	
	public function get_table($id)
	{
		$id = intval($id);
		$table = $this->tables_table;
		return $this->get_one_record($id, $table);
	}
	
	public function get_field($id)
	{
		$id = intval($id);
		$table = $this->fields_table;
		return $this->get_one_record($id, $table);
	}
	
	public function get_related_record($table, $id)
	{
		$id = intval($id);
		$table = $this->clean($table);
		return $this->get_one_record($id, $table);
	}
	
	public function get_table_records($table, $offset, $rows, $order = '')
	{
		$results = array();
		$table = $this->clean($table);
		$offset = intval($offset);
		$rows = intval($rows);
		$order_qry = '';
		if($order){
			$order = $this->clean($order);
			$order_qry = "ORDER BY $order";
		}
		$qry = mysql_query("SELECT * FROM $table $order_qry LIMIT $offset, $rows");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$results[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function count_table_records($table)
	{
		$results = 0;
		$table = $this->clean($table);
		$offset = intval($offset);
		$rows = intval($rows);
		$qry = mysql_query("SELECT * FROM $table");
		if($qry)
		{
			$results = mysql_num_rows($qry);
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function get_string_rep($table_id)
	{
		$table_id = intval($this->clean($table_id));
		$table = $this->fields_table;
		$results = '';
		$qry = mysql_query("SELECT * FROM $table WHERE table_id = $table_id AND string_rep = 'y'");
		if($qry)
		{
			$row = mysql_fetch_array($qry);
			$results =  $row['name'];
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function get_records_string_rep($id)
	{
		$id = intval($this->clean($id));
		$table = $this->get_table($id);
		$name = $table['name'];
		$string_rep_field = $this->get_string_rep($id);
		$results = array();
		$qry = mysql_query("SELECT * FROM $name");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$results[$i]['id'] =  $row['id'];
				$results[$i]['display'] =  $row[$string_rep_field];
			}
			$row = mysql_fetch_array($qry);
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function get_options($id)
	{
		$id = intval($this->clean($id));
		$table = $this->fields_table;
		$results = '';
		$option = array();
		$qry = mysql_query("SELECT * FROM $table WHERE id = $id");
		if($qry)
		{
			$row = mysql_fetch_array($qry);
			$results =  $row['select_options'];
			//$results =  "D:Down town;U:Up town";
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		$array_1 = array();
		$array_2 = array();
		$array_1 = explode(";", $results);
		$i = 0;
		foreach ($array_1 as $a1)
		{
			$array_2 = explode(":", $a1);
			$option[$i]['save'] = $array_2[0];
			if($array_2[1])
			{
				$option[$i]['display'] = $array_2[1];
			} else {
				$option[$i]['display'] = $array_2[0];
			}
			$i++;
		}
		return $option;
	}
	
	public function change_table($id, $name, $display_name = '')
	{
		$id = intval($this->clean($id));
		$return = false;
		$name = $this->clean($name);
		$display_name = $this->clean($display_name);
		
		if($this->tableExists($name))
        {	
			$table = $this->tables_table;
		
			$qry = mysql_query("UPDATE $table SET name = '$name', displayname = '$display_name' WHERE id = $id");
			if($qry)
			{
				return true;
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
			
		} else {
			$this->the_last_error = " The table '$name' does not exist.";
		}
		return $return;
	}
	
	public function get_record($table, $id, $primary_key)
	{
		$id = intval($id);
		$table = $this->clean($table);
		$primary_key = $this->clean($primary_key);
		$results = array();
		$id = intval($id);
		$qry = mysql_query("SELECT * FROM $table WHERE $primary_key = $id");
		if($qry)
		{
			$row = mysql_fetch_array($qry);
			$results =  $row;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	
	private function get_one_record($id, $table)
	{
		$results = array();
		$id = intval($id);
		$qry = mysql_query("SELECT * FROM $table WHERE id = $id");
		if($qry)
		{
			$row = mysql_fetch_array($qry);
			$results =  $row;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	private function get_all_records($table)
	{
		$results = array();
		$qry = mysql_query("SELECT * FROM $table");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$results[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function is_table_complete($id)
	{
		$fields = array();
		$results = '';
		$primary_key = false;
		$foreign_key = false;
		$foreign_table = false;
		$string_rep = false;
		$table = $this->fields_table;
		$qry = mysql_query("SELECT * FROM $table WHERE table_id = $id");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$fields[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		foreach($fields as $field)
		{
			if($field['input_type'] == 'pk')
				$primary_key = true;
		}
		foreach($fields as $field)
		{
			if($field['input_type'] == 'fk')
			{
				$foreign_key = true;
				if($field['foreignkey_table'])
				{
					$foreign_table = true;
				}
			}
		}
		foreach($fields as $field)
		{
			if($field['string_rep'] == 'y')
				$string_rep = true;
		}
		if($primary_key == false)
		{
			$results .= 'You have not defined the field that is the primary key for this table yet.';
		}
		if(($foreign_key == true) AND ($foreign_table != true))
		{
			$results .= ' You have not specified the table that the a foreign key is linked to.';
		}
		if($string_rep == false)
		{
			$results .= 'You have not defined the field that is the string representation for this table yet.';
		}
		return $results;
	}
	
	public function get_table_fields($id)
	{
		$id = intval($id);
		$fields = array();
		$table = $this->fields_table;
		$qry = mysql_query("SELECT * FROM $table WHERE table_id = $id");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$fields[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $fields;
	}
	
	public function change_field($id, 
								$table_id, 
								$name, 
								$display_name, 
								$input_type,
								$is_required,
								$string_rep,
								$default_state, 
								$value_when_not_checked, 
								$value_when_checked, 
								$select_options, 
								$place_to_store, 
								$allowed_extensions, 
								$datetime_save_format,
								$foreignkey_table,
								$display_on_change_list,
								$auto_save_timestamp_new,
								$auto_save_timestamp_update)
	{
		$return = false;
		$id = intval($id);
		$table_id = intval($table_id);
		$name = $this->clean($name);
		$display_name = $this->clean($display_name);
		$input_type = $this->clean($input_type);
		$is_required = $this->clean($is_required);
		$string_rep = $this->clean($string_rep);
		$default_state = $this->clean($default_state);
		$value_when_not_checked = $this->clean($value_when_not_checked);
		$value_when_checked = $this->clean($value_when_checked);
		$select_options = $this->clean($select_options);
		$place_to_store = $this->clean($place_to_store);
		$allowed_extensions = $this->clean($allowed_extensions);
		$datetime_save_format = $this->clean($datetime_save_format);
		$foreignkey_table = intval($this->clean($foreignkey_table));
		$display_on_change_list = $this->clean($display_on_change_list);
		$auto_save_timestamp_new = $this->clean($auto_save_timestamp_new);
		$auto_save_timestamp_update = $this->clean($auto_save_timestamp_update);;
		if($is_number){
			$is_number = 'y';
		} else {
			$is_number = 'n';
		}
		
		if($this->fieldExists($table_id, $name))
        {	
			$table = $this->fields_table;
		
			$qry = mysql_query("UPDATE $table SET table_id = $table_id, 
													name = '$name', 
													displayname = '$display_name', 
													input_type = '$input_type',
													is_required = '$is_required',
													string_rep = '$string_rep',
													default_state = '$default_state',
													value_when_not_checked = '$value_when_not_checked',
													value_when_checked = '$value_when_checked',
													select_options = '$select_options',
													place_to_store = '$place_to_store',
													allowed_extensions = '$allowed_extensions',
													datetime_save_format = '$datetime_save_format',
													foreignkey_table = $foreignkey_table,
													display_on_change_list = '$display_on_change_list',
													auto_save_timestamp_new = '$auto_save_timestamp_new',
													auto_save_timestamp_update = '$auto_save_timestamp_update' WHERE id = $id ");
			if($qry)
			{
				return true;
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
		} else {
			$this->the_last_error = " The field '$name' does not exist in the table ".$this->get_table_name($table_id).".";
		}
		return $return;
	}
	
	function validate_upload_path($upload_path)
	{
		if ($upload_path == '')
		{
			$this->set_error('upload_no_filepath');
			return FALSE;
		}
		
		if (function_exists('realpath') AND @realpath($upload_path) !== FALSE)
		{
			$upload_path = str_replace("\\", "/", realpath($upload_path));
		}

		if ( ! @is_dir($upload_path))
		{
			$this->set_error('upload_no_filepath');
			return FALSE;
		}

		if ( ! is_really_writable($upload_path))
		{
			$this->set_error('upload_not_writable');
			return FALSE;
		}

		$upload_path = preg_replace("/(.+?)\/*$/", "\\1/",  $upload_path);
		return TRUE;
	}
	public function convert_date_to_timestamp($datetime)
	{
		$datetime = explode(" ", $datetime);
		$date = $datetime[0];
		$time = $datetime[1];
		$date = explode("-", $date);
		$time = explode(":", $time);
		
		return  mktime($time[0], $time[1], $time[2], $date[1], $date[0], $date[2]);
	}
	
	public function save_new_record($table, $values)
	{
		if($values){
			$query = '';
			$k = '';
			$v = '';
			$table = $this->clean($table);
			$query .= "INSERT INTO $table (";
			$count = 1;
			$number_of_fields = count($values);
			foreach($values as $value)
			{
				
				$k .= $this->clean($value['key']);
				if($value['value'] == ''){
					$v .= "NULL";
				} else {
					if(is_string($value['value'])){
						$v .= "'".$this->clean($value['value'])."'";
					} else {
						$v .= $this->clean($value['value']);
					}
				}
				if($count < $number_of_fields)
				{
					$k .= ', ';
					$v .= ', ';
				}
				$count = $count + 1;
			}
		
			$query .= "$k) VALUES ($v)";
			$qry = mysql_query($query);
			if($qry)
			{
				return true;
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
		}
		
		return false;
		
	}
	
	public function update_existing_record($id, $table, $primary_key, $values)
	{
		if($values){
			$query = '';
			$k = '';
			$v = '';
			$id = intval($id);
			$table = $this->clean($table);
			$query .= "UPDATE $table SET ";
			$count = 1;
			$number_of_fields = count($values);
			foreach($values as $value)
			{
				if($value['value'] == ''){
					$query .= "".$this->clean($value['key'])." = NULL";
				} else {
					if(is_string($value['value'])){
						$query .= "".$this->clean($value['key'])." = '".$this->clean($value['value'])."'";
					} else {
						$query .= "".$this->clean($value['key'])." = ".$this->clean($value['value'])."";
					}
				}
				
				if($count < $number_of_fields)
				{
					$query .= ", ";
				}
				$count = $count + 1;
			}
		
			$query .= " WHERE ".$primary_key." = $id";
			$qry = mysql_query($query);
			if($qry)
			{
				return true;
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
		}
		
		return false;
		
	}
	
	public function delete_a_record($table, $id, $primary_key)
	{
		$id = intval($id);
		$table = $this->clean($table);
		$primary_key = $this->clean($primary_key);
		return mysql_query("DELETE FROM $table WHERE $primary_key = $id");
	}
	
	public function new_permission($name, $key, $editable)
	{
		$return = 0;
		$name = $this->clean($name);
		$key = $this->clean($key);
		
		$table = $this->permissions_table;
		$qry = mysql_query("INSERT INTO $table (name, permkey, editable) VALUES ('$name', '$key', '$editable')");
		if($qry)
		{
			return mysql_insert_id();
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function change_permission($id, $name, $key)
	{
		$return = false;
		$name = $this->clean($name);
		$key = $this->clean($key);
		$id = intval($id);
		
		$table = $this->permissions_table;
		$qry = mysql_query("UPDATE $table SET name = '$name', permkey = '$key' WHERE id = $id");
		if($qry)
		{
			return true;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function get_all_permission()
	{
		$table = $this->permissions_table;
		return $this->get_all_records($table);
	}
	
	public function get_permission($id)
	{
		$id = intval($id);
		$table = $this->permissions_table;
		return $this->get_one_record($id, $table);
	}
	
	public function new_role($name, $home, $editable)
	{
		$return = 0;
		$name = $this->clean($name);
		$editable = $this->clean($editable);
		$home = $this->clean($home);
		
		$table = $this->roles_table;
		$qry = mysql_query("INSERT INTO $table (name, homepage, editable) VALUES ('$name', '$homepage', '$editable')");
		if($qry)
		{
			return mysql_insert_id();
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function change_role($id, $name, $home = '')
	{
		$return = false;
		$id = intval($id);
		$name = $this->clean($name);
		$home = $this->clean($home);
		
		$table = $this->roles_table;
		$qry = mysql_query("UPDATE $table SET name = '$name', homepage = '$home' WHERE id = $id");
		if($qry)
		{
			return true;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function get_all_role()
	{
		$table = $this->roles_table;
		return $this->get_all_records($table);
	}
	
	public function get_role($id)
	{
		$id = intval($id);
		$table = $this->roles_table;
		return $this->get_one_record($id, $table);
	}
	
	public function new_role_perm($role_id, $perm_id)
	{
		$return = 0;
		$role_id = intval($role_id);
		$perm_id = intval($perm_id);
		
		$table = $this->roles_permissions_table;
		$qry = mysql_query("INSERT INTO $table (role_id, permission_id) VALUES ($role_id, $perm_id)");
		if($qry)
		{
			return mysql_insert_id();
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function get_role_permissions($id)
	{
		$id = intval($id);
		$table = $this->roles_permissions_table;
		$results = array();
		$qry = mysql_query("SELECT * FROM $table WHERE role_id = $id");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$results[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function add_or_update_role_perm($role_id, $perm_id)
	{
		$role_id = intval($role_id);
		$perm_id = intval($perm_id);
		$table = $this->roles_permissions_table;
		$qry = mysql_query("SELECT * FROM $table WHERE role_id = $role_id AND permission_id = $perm_id");
		$result_count = mysql_num_rows($qry);
		if ($result_count > 0)
		{
			return true;
		} else {
			return mysql_query("INSERT INTO $table (role_id, permission_id) VALUE ($role_id ,$perm_id)");
		}
	}
	
	public function delete_role_perm($role_id, $perm_id)
	{
		$role_id = intval($role_id);
		$perm_id = intval($perm_id);
		$table = $this->roles_permissions_table;
		return mysql_query("DELETE FROM $table WHERE role_id = $role_id AND permission_id = $perm_id");
	}
	
	public function is_username_available($username)
	{
		$username = $this->clean($username);
		$table = $this->users_table;
		$qry = mysql_query("SELECT * FROM $table WHERE username = '$username'");
		$result_count = mysql_num_rows($qry);
		if ($result_count > 0)
		{
			return false;
		} else {
			return true;
		}
	}
	
	public function is_email_available($email)
	{
		$email = $this->clean($email);
		$table = $this->users_table;
		$qry = mysql_query("SELECT * FROM $table WHERE email = '$email'");
		$result_count = mysql_num_rows($qry);
		if ($result_count > 0)
		{
			return false;
		} else {
			return true;
		}
	}
	
	public function new_user($fname, $lname, $username, $email, $password, $role)
	{
		$return = 0;
		$fname = $this->clean($fname);
		$lname = $this->clean($lname);
		$username = $this->clean($username);
		$email = $this->clean($email);
		$password = $this->encode_password($this->clean($password));
		$role = intval($this->clean($role));
		$now = time();
		
		$table = $this->users_table;
		$qry = mysql_query("INSERT INTO $table (fname, lname, username, email, password, role_id, dateadded) VALUES ('$fname', '$lname', '$username', '$email', '$password', $role, $now)");
		if($qry)
		{
			return mysql_insert_id();
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function change_user($id, $fname, $lname, $email, $role)
	{
		$return = false;
		$id = intval($id);
		$fname = $this->clean($fname);
		$lname = $this->clean($lname);
		$role = intval($this->clean($role));
		$email = $this->clean($email);
		
		$table = $this->users_table;
		$qry = mysql_query("UPDATE $table SET fname = '$fname', lname = '$lname', email = '$email', role_id = $role WHERE id = $id");
		if($qry)
		{
			return true;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function get_all_user()
	{
		$table = $this->users_table;
		return $this->get_all_records($table);
	}
	
	public function get_user($id)
	{
		$id = intval($id);
		$table = $this->users_table;
		return $this->get_one_record($id, $table);
	}
	
	public function delete_auth_record($item, $id)
	{
		$id = intval($id);
		switch ($item) {
			case 'user':
				$table = $this->users_table;
				break;
			case 'role':
				$table = $this->roles_table;
				break;
			case 'permission':
				$table = $this->permissions_table;
				break;
		}
		return mysql_query("DELETE FROM $table WHERE id = $id");
	}
	
	public function get_auth_paged_records($item, $offset, $rows)
	{
		$results = array();
		switch ($item) {
			case 'user':
				$table = $this->users_table;
				break;
			case 'role':
				$table = $this->roles_table;
				break;
			case 'permission':
				$table = $this->permissions_table;
				break;
		}
		$offset = intval($offset);
		$rows = intval($rows);
		$qry = mysql_query("SELECT * FROM $table LIMIT $offset, $rows");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			for($i = 0; $i < $result_count; $i++){
				$row = mysql_fetch_array($qry);
				$results[] =  $row;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function count_auth_table_records($item)
	{
		$results = 0;
		switch ($item) {
			case 'user':
				$table = $this->users_table;
				break;
			case 'role':
				$table = $this->roles_table;
				break;
			case 'permission':
				$table = $this->permissions_table;
				break;
		}
		$offset = intval($offset);
		$rows = intval($rows);
		$qry = mysql_query("SELECT * FROM $table");
		if($qry)
		{
			$results = mysql_num_rows($qry);
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	public function is_it_changable($item, $id)
	{
		$id = intval($id);
		switch ($item) {
			case 'user':
				return true;
				break;
			case 'role':
				$table = $this->roles_table;
				break;
			case 'permission':
				$table = $this->permissions_table;
				break;
		}
		
		$results = array();
		$qry = mysql_query("SELECT * FROM $table WHERE id = $id");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			if ($result_count == 0)
			{
				return true;
			}
			$row = mysql_fetch_array($qry);
			$results =  $row;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		if($results['editable'] == 'y')
		{
			return true;
		} else {
			return false;
		}
	}
	
	public function does_user_have_access($page_access_key, $region = '')
	{
		if($this->user_has_permission('can_do_everything_everywhere'))
			return true;
		if($region == 'config'){
			if($this->user_has_permission('can_do_everything_config'))
				return true;
		}
		if($region == 'auth'){
			if($this->user_has_permission('can_do_everything_auth'))
				return true;
		}
		if($region == 'tables'){
			if($this->user_has_permission('can_do_everything_tables'))
				return true;
		}
		if (substr($page_access_key,0,9) == "add_page_")
		{
			$add_perm_key = "add_".str_replace("add_page_","",$page_access_key);
			$do_all = "add_change_delete_".str_replace("add_page_","",$page_access_key);
			if($this->user_has_permission($add_perm_key) OR $this->user_has_permission($do_all))
				return true;	
		}
		if (substr($page_access_key,0,12) == "change_page_")
		{
			$change_perm_key = "change_".str_replace("change_page_","",$page_access_key);
			$delete_perm_key = "delete_".str_replace("change_page_","",$page_access_key);
			$do_all = "add_change_delete_".str_replace("change_page_","",$page_access_key);
			if($this->user_has_permission($change_perm_key) OR $this->user_has_permission($delete_perm_key) OR $this->user_has_permission($do_all))
				return true;
			
		}
		if (substr($page_access_key,0,12) == "delete_page_")
		{
			$delete_perm_key = "delete_".str_replace("delete_page_","",$page_access_key);
			$do_all = "add_change_delete_".str_replace("delete_page_","",$page_access_key);
			if($this->user_has_permission($delete_perm_key) OR $this->user_has_permission($do_all))
				return true;
		}
	}
	
	public function user_has_permission($add_perm_key)
	{
		$role = intval($_SESSION['sadmin_user_role']);
		$permissions = $this->get_role_permissions($role);
		foreach($permissions as $p)
		{
			if($this->get_perm_key($p['permission_id']) == $add_perm_key)
				return true;
		}
		return false;
	}
	
	public function user_has_role($role_name)
	{
		$id = intval($_SESSION['sadmin_user_role']);
		$role_details = $this->get_role($id);
		if($role_details['name'] == $role_name){
			return true;
		} else {
			return false;
		}
	}
	
	private function get_perm_key($id)
	{
		$results = '';
		$id = intval($id);
		$table = $this->permissions_table;
		$qry = mysql_query("SELECT * FROM $table WHERE id = $id");
		if($qry)
		{
			$row = mysql_fetch_array($qry);
			$results =  $row['permkey'];
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $results;
	}
	
	
	
	public function generate_passwword($len = 10)
	{
		$pool = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#.-_,$%&+=@^*!';
		$str = '';
		for ($i = 0; $i < $len; $i++)
		{
			$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		return $str;
	}
	
	private function encode_password($password)
	{
		$salt = 'hl09523K0H@NA+PHP_7hE-SW!FtFZl8pdwwa84';
		$_pass = str_split($password);
		foreach ($_pass as $_hashpass)
		{
			$salt .= md5($_hashpass);
		}
		return md5($salt);
	}
	
	public function logout()
	{
		unset($_SESSION['sadmin_logged_in']);
		unset($_SESSION['sadmin_user_id']);
		unset($_SESSION['sadmin_user_role']);
		unset($_SESSION['sadmin_user_email']);
		unset($_SESSION['sadmin_user_home']);
		return true;
	}
	
	public function update_last_login_time($id)
	{
		$return = false;
		$id = intval($id);
		$table = $this->users_table;
		$now = time();
		$qry = mysql_query("UPDATE $table SET lastlogin = $now WHERE id = $id");
		if($qry)
		{
			return true;
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		
		return $return;
	}
	
	public function login($username, $password)
	{
		$table = $this->users_table;
		$username = $this->clean($username);
		$password = $this->encode_password($this->clean($password));
		$qry = mysql_query("SELECT * FROM $table WHERE username = '$username' AND password = '$password'");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			if($result_count > 0)
			{
				$row = mysql_fetch_array($qry);
				$_SESSION['sadmin_logged_in'] = true;
				$_SESSION['sadmin_user_id'] = $row['id'];
				$_SESSION['sadmin_user_email'] = $row['email'];
				$_SESSION['sadmin_user_role'] = $row['role_id'];
				$role_details = $this->get_role($row['role_id']);
				if($role_details['homepage'])
					$_SESSION['sadmin_user_home'] = $role_details['homepage'];
				$this->update_last_login_time($row['id']);
				return true;
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return false;
	}
	
	public function forgot_password($login)
	{
		$return = array();
		$table = $this->users_table;
		$id = intval($login);
		$login = $this->clean($login);
		$password = $this->generate_passwword();
		$encrypt = $this->encode_password($password);
		$qry = mysql_query("SELECT * FROM $table WHERE username = '$login' OR email = '$login'");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			if($result_count > 0)
			{
				$row = mysql_fetch_array($qry);
				$id = $row['id'];
				$qry_2 = mysql_query("UPDATE $table SET password = '$encrypt' WHERE id = $id");
				if($qry_2)
				{
					$return['email'] = $row['email'];
					$return['username'] = $row['username'];
					$return['password'] = $password;
					return $return;
				} else {
					if($this->show_errors)
						die(mysql_error());
				}
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return $return;
	}
	
	public function change_password($id, $oldpassword, $newpassword)
	{
		$table = $this->users_table;
		$id = intval($id);
		$oldpassword = $this->encode_password($this->clean($oldpassword));
		$newpassword = $this->encode_password($this->clean($newpassword));
		$qry = mysql_query("SELECT * FROM $table WHERE id = $id");
		if($qry)
		{
			$result_count = mysql_num_rows($qry);
			if($result_count > 0)
			{
				
				$row = mysql_fetch_array($qry);
				if($row['password'] == $oldpassword){
					$qry_2 = mysql_query("UPDATE $table SET password = '$newpassword' WHERE id = $id");
					if($qry_2)
					{
						return true;
					} else {
						if($this->show_errors)
							die(mysql_error());
					}
				}
			}
		} else {
			if($this->show_errors)
				die(mysql_error());
		}
		return false;
	}
	
	public function delete_config_field($id)
	{
		$id = intval($id);
		$table = $this->fields_table;
		return mysql_query("DELETE FROM $table WHERE id = $id");
	}
	public function delete_config_table($id)
	{
		$id = intval($id);
		$table = $this->tables_table;;
		return mysql_query("DELETE FROM $table WHERE id = $id");
	}
	
	public function delete_table_permissions($table_id)
	{
		$name = $this->get_table_name($table_id);
		$do_all = 'add_change_delete_'.$name;
		$add = 'add_'.$name;
		$change = 'change_'.$name;
		$delete = 'delete_'.$name;
		
		$table = $this->permissions_table;
		$qry = mysql_query("DELETE FROM $table WHERE permkey = '$do_all'");
		$qry = mysql_query("DELETE FROM $table WHERE permkey = '$add'");
		$qry = mysql_query("DELETE FROM $table WHERE permkey = '$change'");
		$qry = mysql_query("DELETE FROM $table WHERE permkey = '$delete'");
		
		return true;
	}
	
	public function delete_table_fields($table_id)
	{
		$table_id = intval($table_id);
		$table = $this->fields_table;
		return mysql_query("DELETE FROM $table WHERE table_id = $table_id");
	}
	
	public function search_table($table, $columns = array(), $query = '', $options = array()){

		$terms = $this->search_split_terms($query);
		$terms_db = $this->search_db_escape_terms($terms);
		
		$sql_query = array();
		foreach($terms_db as $key=>$value){
			$column_list = $columns;
			$keywords = $this->clean($value);
			$sql = array();
			for($i = 0; $i < count($column_list); $i++){
				$sql[] = '' . $column_list[$i] . ' RLIKE "' . $keywords . '"';
			}
			$sql_query = array_merge($sql_query, $sql);
			
		}
		$sql_query = implode(' OR ', $sql_query);

		$results = array();
		$qry = mysql_query('SELECT ' . $options['columns'] . ' FROM ' . $table . ' WHERE ' . $sql_query . ' ' . $options['extra_sql']);
			if($qry)
			{
				$result_count = mysql_num_rows($qry);
				for($i = 0; $i < $result_count; $i++){
					$row = mysql_fetch_array($qry);
					$results[] =  $row;
				}
			} else {
				if($this->show_errors)
					die(mysql_error());
			}
		return $results;
	}
	
	// START - Search Helper Function
	private function search_split_terms($terms){

		$terms = preg_replace("/\"(.*?)\"/e", "$this->search_transform_term('\$1')", $terms);
		$terms = preg_split("/\s+|,/", $terms);

		$out = array();

		foreach($terms as $term){

			$term = preg_replace("/\{WHITESPACE-([0-9]+)\}/e", "chr(\$1)", $term);
			$term = preg_replace("/\{COMMA\}/", ",", $term);

			$out[] = $term;
		}

		return $out;
	}
	private function search_transform_term($term){
		$term = preg_replace("/(\s)/e", "'{WHITESPACE-'.ord('\$1').'}'", $term);
		$term = preg_replace("/,/", "{COMMA}", $term);
		return $term;
	}
	private function search_escape_rlike($string){
		return preg_replace("/([.\[\]*^\$])/", '\\\$1', $string);
	}
	private function search_db_escape_terms($terms){
		$out = array();
		foreach($terms as $term){
			$out[] = '[[:<:]]'.AddSlashes($this->search_escape_rlike($term)).'[[:>:]]';
		}
		return $out;
	}
	
}
?>