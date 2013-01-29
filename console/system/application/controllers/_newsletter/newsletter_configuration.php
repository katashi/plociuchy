<?php
if (!defined('BASEPATH')) die;

class Newsletter_Configuration extends Main {

	function Newsletter_Configuration($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('newsletter_configuration_model');
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('newsletter/newsletter_configuration.html');
	}
	
	//
	// load all
	//
	function load() {
        echo '{"success": true, "data":'.json_encode($this->newsletter_configuration_model->load()).'}';
	}
    function load_cron() {
        return $this->newsletter_configuration_model->load();
    }
	
	//
	// add
	//
	function add() {
	}
	
	//
	// edit
	//
	function edit() {	
        $result = $this->newsletter_configuration_model->edit();
        echo $result;
	}
	
	//
	// delete
	//
	function delete() {	
	}
	
}