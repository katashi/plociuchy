<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require('system/smarty/Smarty.class.php');

class CI_Smarty extends Smarty {
	
	function CI_Smarty() {
		//$smarty = new Smarty;
		$this->compile_check = true;
		$this->debugging = false;
		$this->template_dir = 'system/application/templates/';
		$this->compile_dir  = 'system/application/templates_c/';
		$this->config_dir   = 'system/application/config/';
	}

}
?>