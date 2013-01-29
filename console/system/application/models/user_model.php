<?php
class User_Model extends Main_Model {
	
	function User_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}

	// limit check
	function limit_check() {
		if (isset($_REQUEST['start']) && isset($_REQUEST['limit'])) { 
			$this->start = $_REQUEST['start'];
			$this->limit = $_REQUEST['limit'];
			$this->db->limit($this->limit, $this->start);
		}
	}

    // load
	function load_all() {
        $this->limit_check();
        $this->db->order_by('id');
        $query = $this->ci->db->get('doit4me_client');
        $record = $query->result_array();
        $record_total = $this->db->affected_rows();
        $result = array('total' => $record_total, 'data' => $record);
        return $result;
	}
    function load($field, $value) {
        $this->db->where($field, $value);
		$query = $this->db->get($this->config_db_table_user);
        if ($this->db->affected_rows() > 0) {
            $record = $query->row_array();
            return $record; 
        }
    }

    // add
    function add() {
        $this->db->insert($this->config_db_table_user, $_POST);
        return 1;
    }

    // edit
    function edit($id) {
        $this->db->where('id', $id);
        $this->db->update($this->config_db_table_user, $_POST);
        return 1;
    }

    // delete
    function delete($id) {
        $this->db->where('id', $id);
        $query = $this->db->delete($this->config_db_table_user);
        return 1;
    }

    // password clear
    function password_clear($password_hash) {
        $record = array();
        $record['password'] = null;
        $this->db->where('password_hash', $password_hash);
		$this->db->update($this->config_db_table_user, $record);
        return 1;
    }

    // password update
    function password_update($password_hash, $password) {
        $record = array();
        $record['password'] = $password;
        $this->db->where('password_hash', $password_hash);
		$this->db->update($this->config_db_table_user, $record);
        return 1;
    }

    // user exist
    function user_exist($user = false) {
        $this->db->where('user', $user);
        $query = $this->db->get($this->config_db_table_user);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    // user activation
    function user_activation($password_hash) {
        if (!isset($password_hash)) { die; }
        $this->db->where('password_hash', $password_hash);
        $query = $this->db->get($this->config_db_table_user);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            $record = array();
            $record['active'] = 1;
            $this->db->where('password_hash', $password_hash);
            $this->db->update($this->config_db_table_user, $record);
            return 1;
        } else {
            return 0;
        }
    }

    // login
    function login() {
        if (!isset($_POST['user']) || !isset($_POST['password'])) {
            $result['status'] = 0; $result['error'] = 4; return $result;
        }
        $this->db->where('user', $_POST['user']);
        $this->db->where('password', sha1($_POST['password']));
        $query = $this->db->get($this->config_db_table_user);
        $record = $query->row_array();
        $result = array();
        if ($this->db->affected_rows() > 0) {
            if ($record['suspend'] == 1) { $result['status'] = 0; $result['error'] = 1; return $result; }
            else if ($record['active'] == 0) { $result['status'] = 0; $result['error'] = 2; return $result; }
            else { $result['status'] = 1; $result['client'] = $record; return $result; }
        } else {
            $result['status'] = 0; $result['error'] = 3; return $result;
        }
    }

}
?>