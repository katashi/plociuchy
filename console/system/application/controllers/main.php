<?php
class Main extends Controller {
	
	var $controllers;

	function Main() {
		parent::Controller();
		// some defaults
		$this->db->query("SET NAMES 'utf8'");
		$this->smarty->assign('base_url', $this->config->site_url().'/');	
		$this->phpVersion = substr(phpversion(),0,1);
		$this->controllers = array();
		// remote ini set
		ini_set('display_errors', true);
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__) . '/../../../error_log.txt');
		// check for auth every time ( controller include to the mainframe )
		$this->include_controller('_access/administrator');
        // autoload controller for katashi
        $this->include_autoload_controller_katashi();
        // load config
        $this->config->load('configuration/'.CONFIGURATION);
        // autoload controller ( for katashi and for custom config )
        $this->include_autoload_controller();
	}
	
	//
	// preliminary launch
	//
	function index() {
        // administrator base init plus template setup due to auth process
        $this->administrator = new Administrator($this);
        $this->administrator_authorised = $this->administrator->session_verify();
        $this->template = ($this->administrator_authorised) ? 'main' : 'login';
        // prepare js injection
        $jsvar0 = $this->js_prepare_var('base_ui', CONFIGURATION);
        $this->smarty->assign('base_ui', $jsvar0);
        $js0 = $this->js_prepare_file('javascript/configuration/configuration.js');
        $this->smarty->assign('js0', $js0);
        $js1 = $this->js_prepare_directory('javascript/component');
        $this->smarty->assign('js1', $js1);
        $js2 = $this->js_prepare_directory('javascript/module/center');
        $this->smarty->assign('js2', $js2);
        $js3 = $this->js_prepare_directory('javascript/module/east');
        $this->smarty->assign('js3', $js3);
        $js4 = $this->js_prepare_directory('javascript/module/north');
        $this->smarty->assign('js4', $js4);
        $js5 = $this->js_prepare_directory('javascript/module/south');
        $this->smarty->assign('js5', $js5);
        $js6 = $this->js_prepare_directory('javascript/module/west');
        $this->smarty->assign('js6', $js6);
		// display
        $this->smarty->display($this->template.'.html');
	}
	function index_check() {
        $this->administrator = new Administrator($this);
		echo $this->administrator->authorise();
	}
	
	//
	// run
	// 
	function run() {
		// we need to segment uri to gain some control
		$this->uri = array_slice($this->uri->segment_array(), 2);		
		if (isset($this->uri[0])) { $command_sequence = $this->uri[0]; } else { $command_sequence = ''; }
        // ok, because we need to put controllers in different directories to get general control
        // we put : between DIR:MODULE(controller)
        if (stristr($command_sequence, ':')) {
            $tmp = explode(':', $command_sequence);
            $command_module_directory = $tmp[0];
            $command_module = $tmp[1];
        } else {
            $command_module_directory = '';
            $command_module = $command_sequence;
        }
        // lets go to methods/arguments
		if (isset($this->uri[1])) {	$command_method = $this->uri[1]; } else { $command_method = 'display'; }
		if (isset($this->uri[2])) { $command_arguments = explode(",", $this->uri[2]); } else { $command_arguments = array(); }
		// now we will create new class instance including desired controller ( which will include required model )
		if ($command_module) { $$command_module = $this->run_factory($command_module_directory, $command_module); }
		if ($command_method) { call_user_func_array(array($$command_module, $command_method), $command_arguments); }
	}
	
	//
	// run factory
	//
	function run_factory($directory='', $module='') {
        if ($directory != '') { $directory .= '/'; }
		$this->include_controller($directory.$module);
		$classname = new $module($this);
        return $classname;
	}
	
	//
	// welcome screen
	//
	function welcome() {
        $this->smarty->display('welcome.html');
	}
	
	//
	// include controller
	//
	function include_controller($controller) {
		$this->included = false;
		foreach ($this->controllers as $value) {
			if ($controller == $value) { $this->included = true; }
		}
		if (!$this->included) {
			array_push($this->controllers, $controller);
			include($controller.'.php');
		}
	}
    
    //
    // include autoload controller
    //
    function include_autoload_controller_katashi() {
        if ($this->config->item('autoload_controller_katashi') != false) {
            foreach($this->config->item('autoload_controller_katashi') as $controller) {
                $this->include_controller($controller);
            }
        }
    }
    function include_autoload_controller() {
        if ($this->config->item('autoload_controller') != false) {
            foreach($this->config->item('autoload_controller') as $controller) {
                $this->include_controller($controller);
            }
        }
    }
	
	//
	// converting php arrays to json
	//
	function php2js($a=false) {
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a)) {
			if (is_float($a)) {
				// Always use "." for floats.
				$a = str_replace(",", ".", strval($a));
			}
			// All scalars are converted to strings to avoid indeterminism.
			// PHP's "1" and 1 are equal for all PHP operators, but
			// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
			// we should get the same result in the JS frontend (string).
			// Character replacements for JSON.
			static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
			array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
			if (key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList) {
			foreach ($a as $v) $result[] = $this->php2js($v);
			return '[' . join(',', $result) . ']';
		} else {
			foreach ($a as $k => $v) $result[] = $this->php2js($k).':'.$this->php2js($v);
			return '{' . join(',', $result) . '}';
		}
	}

    //
    // js injection
    //
    function js_prepare_var($var_name, $var_value) {
        $include = "var ".$var_name." = '".$var_value."';\n";
        return $include;
    }
    function js_prepare_file($file) {
        $include = "<script type=\"text/javascript\" src=\"".$file."\"></script>\n";
        return $include;
    }
    function js_prepare_directory($path) {
        $files = scandir($path);
        $files = array_slice($files, 2);
        $include = '';
        foreach($files as $file) {
            $include .= "<script type=\"text/javascript\" src=\"".$path."/".$file."\"></script>\n";
        }
        return $include;
    }

	//
    // display
    //
    function display() {
        $this->ci->smarty->display(CONFIGURATION.'/'.$this->name.'.html');
    }
    function display_add() {
        $this->ci->smarty->display(CONFIGURATION.'/'.$this->name.'_add.html');
    }
    function display_edit($id) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display(CONFIGURATION.'/'.$this->name.'_edit.html');
    }

}