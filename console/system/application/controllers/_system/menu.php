<?php
if (!defined('BASEPATH')) die;

class Menu extends Main {

	function Menu($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
	}
	
	//
	// menu
	//
	function get($menu) {
		$this->data = array('menu' => $this->config->item($menu));
		echo $this->ci->php2js($this->data);
	}

}