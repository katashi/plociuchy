<?php
class Main extends Controller {
	
	var $controllers;

	function Main() {
		parent::Controller();
		//
		$this->smarty->assign('site_url', SITE_URL.'/');
        $this->smarty->assign('app_url', APP_URL.'/');
        $this->smarty->assign('media_url', MEDIA_URL.'/');
        $this->phpVersion = substr(phpversion(),0,1);
		$this->controllers = array();
		//
		ini_set('display_errors', true);
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__) . '/../../../error_log.txt');
        //
        $this->include_controller('hub');
        $this->include_controller('home');
        $this->include_controller('article');
        // category
        $this->smarty->assign('category', $this->category_load_all());
        // vendor
        $this->smarty->assign('vendor', $this->vendor_load_all());

	}

	// index
	function index() {
        $this->uri->rsegments = array('main','run','home');
        $this->run();
	}

	// run
	function run() {
		// we need to segment uri to gain some control
		$this->uri2 = array_slice($this->uri->rsegment_array(), 2);
		if (isset($this->uri2[0])) { $command_sequence = $this->uri2[0]; } else { $command_sequence = ''; }
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
        // title call definition
        $title_call = $command_module;
        // load name of the template file
        $template = $this->load_template($command_module);
        if (!isset($template)) { $template = $command_module; }
        // load name of the controller file
        $controller = $this->load_controller($command_module);
        if (isset($controller)) { $command_module = $controller; }
        // redefine some values, lets go to methods/arguments
		if (isset($this->uri2[1])) { $command_method = $this->uri2[1]; } else { $command_method = 'display'; }
        if (isset($this->uri2[2])) { $command_arguments = explode(",", $this->uri2[2]); } else { $command_arguments = array(); }
        // correction of command arguments - we will put in front name of a title_call and template from menu
        array_splice($command_arguments, 0, 0, $title_call);
        array_splice($command_arguments, 0, 0, $template);
		// now we will create new class instance including desired controller ( which will include required model )
		if ($command_module) { $$command_module = $this->run_factory($command_module_directory, $command_module); }
		if ($command_method) { call_user_func_array(array($$command_module, $command_method), $command_arguments); }
	}

    // load
    function load_controller($command_module = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_controller/'.$command_module;
        $result = $this->api_call($url);
        if (isset($result['data']['controller'])) {
            return $result['data']['controller'];
        } else {
            return null;
        }
    }
    function load_template($template = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_template/'.$template;
        $result = $this->api_call($url);
        if (isset($result['data']['template'])) {
            return $result['data']['template'];
        } else {
            return null;
        }
    }

	// run factory
	function run_factory($directory='', $module='') {
        if ($directory != '') { $directory .= '/'; }
		$this->include_controller($directory.$module);
		$classname = new $module($this);
        return $classname;
	}

	// include controller
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
    function include_autoload_controller() {
        if ($this->config->item('autoload_controller') != false) {
            foreach($this->config->item('autoload_controller') as $controller) {
                $this->include_controller($controller);
            }
        }
    }

    // api call
    function api_call($url='', $data='', $upload='') {
        require_once 'console/system/application/config/configuration/'.CONFIGURATION.'.php';
        //
        if ($upload) {
            // first file
            if (isset($_FILES["userfile"]["tmp_name"]) && $_FILES["userfile"]["tmp_name"] != '') {
                $tmp_name = $_FILES["userfile"]["tmp_name"];
                $name = $_FILES["userfile"]["name"];
                $type = $_FILES["userfile"]["type"];
                $data['userfile'] = "@".$tmp_name;
                $data['userfile_name'] = $name;
                $data['userfile_type'] = $type;
            }
            // second file ( exception )
            if (isset($_FILES["userfile2"]["tmp_name"]) && $_FILES["userfile2"]["tmp_name"] != '') {
                $tmp_name2 = $_FILES["userfile2"]["tmp_name"];
                $name2 = $_FILES["userfile2"]["name"];
                $type2 = $_FILES["userfile2"]["type"];
                $data['userfile2'] = "@".$tmp_name2;
                $data['userfile2_name'] = $name2;
                $data['userfile2_type'] = $type2;
            }
        }
        $call = curl_init($url);
        curl_setopt($call, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($call, CURLOPT_TIMEOUT, 2);
        curl_setopt($call, CURLOPT_USERAGENT, 'katashi-1.0');
        curl_setopt($call, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($call, CURLOPT_FAILONERROR, true);
        //curl_setopt($call, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($call, CURLOPT_HEADER, 0);
        if (isset($data)) {
            curl_setopt($call, CURLOPT_POST, true);
            curl_setopt($call, CURLOPT_POSTFIELDS, $data);
        }
        $result_json = curl_exec($call);
        $result_object = json_decode($result_json, true);
        $info = curl_getinfo($call);
        curl_close($call);

        // debug
        if (API_DEBUG == 1) {
            //var_dump($info);
            echo $url.'<br>';
            var_dump($data);
            echo 'Result OBJECT<br>';
            var_dump($result_object);
            echo 'Result JSON<br>';
            var_dump($result_json);
            echo '<hr>';
        }
        // return
        if (API_DATAFORMAT == 'OBJECT') {
            return $result_object;
        }
        if (API_DATAFORMAT == 'JSON') {
            return $result_json;
        }
    }

	// converting php arrays to json
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

    // category
    function category_load_all($field = null, $value = null) {
        $url = CONSOLE_URL.'/plociuchy:product_dict_category/load_all';
        $result = $this->api_call($url);
        if ($result['total'] > 0) {
            return $result['data'];
        } else {
            return 0;
        }
    }

    // vendor
    function vendor_load_all($field = null, $value = null) {
        $url = CONSOLE_URL.'/plociuchy:product_dict_vendor/load_all';
        $result = $this->api_call($url);
        if ($result['total'] > 0) {
            return $result['data'];
        } else {
            return 0;
        }
    }

}