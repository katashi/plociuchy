<?php
class Newsletter_User_Group_Model extends Model {
		
	function Newsletter_User_Group_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}
	
	//
	// limit check
	//
	function limit_check() {
		if (isset($_REQUEST['start']) && isset($_REQUEST['limit'])) { 
			$this->start = $_REQUEST['start'];
			$this->limit = $_REQUEST['limit'];
			$this->db->limit($this->limit, $this->start);
		}
	}
	
	//
	// load
	//
	function load_all_count() {
		$this->db->from('newsletter_user_group');
		return $this->db->count_all_results();;
	}
	
	function load_all() {
		$this->limit_check();
		$this->db->order_by('id');
		$query = $this->ci->db->get('newsletter_user_group');
		$record_options = $query->result_array();
		$record_all[] = array('id'=>'0', 'name'=>'Wszystkie');
		$record = array_merge($record_all, $record_options);
		return $record;	
	}
	
	function load() {
		
	}
	
	//
	// add
	//
	function add() {
		$record['name'] = $_REQUEST['name'];
		$this->db->insert('newsletter_user_group', $record); 
		return '1';
	}
	
	//
	// edit
	//
	function edit() {
		
	}
	
	//
	// delete
	//
	function delete($id) {
		$this->db->where('id', $id);
		$query = $this->db->delete('newsletter_user_group');
		return '{success:true}';
	}
	
	//
	// delete all
	//
	function delete_all() {
		$query = $this->db->query('delete from newsletter_user_group');
		return '{success:true}';
	}
	
}
?>