<?php
if (!defined('BASEPATH')) die;

class Newsletter_User_Group extends Main {

	function Newsletter_User_Group($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('newsletter_user_group_model');
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('newsletter/newsletter_user_group.html');
	}
	function display_add() {
		$this->ci->smarty->display('newsletter/newsletter_user_group_add.html');
	}
	
	//
	// load all
	//
	function load_all() {
		echo '{"total":'.json_encode($this->newsletter_user_group_model->load_all_count()).', "data":'.json_encode($this->newsletter_user_group_model->load_all()).'}';
	}
	
	//
	// add
	//
	function add() {
		$result = $this->newsletter_user_group_model->add();
		return $result;
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
		$success = $this->newsletter_user_group_model->delete($id);
		echo 'grid';	
	}
	
	//
	// delete all
	//
	function delete_all() {
		$success = $this->newsletter_user_group_model->delete_all();
		echo 'grid';	
	}
	
}