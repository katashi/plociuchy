<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * AMF CI Library
 *
 * Put the 'AMF' folder (unpacked in 'Libraries')
 * 	define("PRODUCTION_SERVER", false);
 */
class MySession
{
  public $CI;
  
  public function __construct()
  {
    $this->ci = get_instance();
    $userData = $this->ci->session->all_userdata();
	if(isset($userData['data'])) {
		$this->ci->smarty->assign('mySession', $userData['data']);
		$this->ci->mySession = $userData['data'];
	}
  }
}
