<?php
class Newsletter_Configuration_Model extends Model {
		
	function Newsletter_Configuration_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}
		
	//
	// load
	//	
	function load() {
		$query = $this->db->get('newsletter_configuration');
		$record = $query->row_array();
        return $record;
	}
	
	//
	// add
	//
	function add() {
	}
	
	//
	// edit
	//
	function edit()	{
	   $record = $_POST;
	   $this->db->update('newsletter_configuration', $record);
	   return '{success:true}';
	}
	
	//
	// delete
	//
	function delete($id) {
	}
		
}
?>