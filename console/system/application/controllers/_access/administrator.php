<?php
if (!defined('BASEPATH')) die;

class Administrator extends Main {

	function Administrator($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('_access/administrator_model');
        //
        $this->name = 'administrator';
	}

    // display
    function display() {
        $this->ci->smarty->display('_access/administrator.html');
    }
    function display_add() {
        $this->ci->smarty->display('_access/administrator_add.html');
    }
    function display_edit($id) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('_access/administrator_edit.html');
    }

	// load all
	function load_all() {
		echo '{"total":'.json_encode($this->administrator_model->load_all_count()).', "data":'.json_encode($this->administrator_model->load_all()).'}';
	}
    function load_all_object() {
        return $this->administrator_model->load_all();
    }
	function load($id) {
		echo '{"success": true, "data":'.json_encode($this->administrator_model->load($id)).'}';
	}

	// add
	function add() {
		$result = $this->administrator_model->add();
		echo $result;
	}

	// edit
	function edit($id) {
		$result = $this->administrator_model->edit($id);
		echo $result;
	}

	// delete
	function delete($id) {
	    $result = $this->administrator_model->delete($id);
		echo $result;
	}

	// session
	function session_create($record) {
		$this->ci->session->set_userdata('administrator_authorised', true);
        $this->ci->session->set_userdata('administrator_type', $record['type']);
        $this->ci->session->set_userdata('administrator_user', $record['user']);
        $this->ci->session->set_userdata('administrator_email', $record['email']);
	}
	function session_destroy() {
		$this->ci->session->unset_userdata('administrator_authorised');
        $this->ci->session->unset_userdata('administrator_type');
        $this->ci->session->unset_userdata('administrator_user');
        $this->ci->session->unset_userdata('administrator_email');
	}
	function session_verify() {
		$administrator_authorised = $this->ci->session->userdata('administrator_authorised');
		if ($administrator_authorised) { 
			return true;
		} else {
			return false;
		}
	}

	// authorisation
	function authorise() {
		$record = $this->administrator_model->authorise(); 
		if ($record) {
			$this->session_create($record);
			return '{"success": true}';
		} else {
			return '{"success": false}';
		}
	}

	// logout
	function logout() {
		$this->session_destroy();
		header("Location: ". SITE_URL .'/console');
	}
	
}