<?php
class Administrator_Model extends Model {
		
	function Administrator_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}

	// authorise
	function authorise() {
		$this->db->where('user', $_REQUEST['user']);
		$this->db->where('password', sha1($_REQUEST['password']));
		$query = $this->ci->db->get('administrator');
		$record = $query->row_array();
		if ($record) {
			$this->activity_update($record); 
			return $record; 
		} else { 
			return false; 
		}
	}
	function activity_update($record) {
		$update = array();
		$update['request_time'] = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : '0'; 
		$update['remote_addr'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0';
		$update['remote_host'] = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '0';
		$update['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$update['query_string'] = $_SERVER['QUERY_STRING'];
		$this->db->where('id', $record['id']);
		$this->db->update('administrator', $update);
	}

	// load
	function load_all_count() {
		$this->db->from('administrator');
		return $this->db->count_all_results();
	}
	function load_all() {
		$query = $this->db->get('administrator');
		$record = $query->result_array();
		return $record;	
	}
	function load($id) {
	    $this->db->select('id,user,email');
	    $this->db->where('id', $id);
		$query = $this->db->get('administrator');
		$record = $query->row_array();
		return $record;	
	}

	// add
	function add() {
		$record = array();
        $record['user'] = $_POST['user'];
        $record['password'] = sha1($_POST['password']);
        $record['email'] = $_POST['email'];
		$this->db->insert('administrator', $record); 
		return '{"success": true}';
	}

	// edit
	function edit($id) {
		$record = $_POST;
        $record['password'] = sha1($_POST['password']);
		$this->db->where('id', $id);
		$this->db->update('administrator', $record); 
        return '{"success": true}';
	}

	// delete
	function delete($id) {
		$this->db->where('id', $id);
		$query = $this->db->delete('administrator');
		return '{"success": true}';
	}
	
}
?>