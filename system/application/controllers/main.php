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
		ini_set('display_errors', false);
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__) . '/../../../error_log.txt');
        //
        $this->include_controller('hub');
        $this->include_controller('home');
        /*$this->include_controller('article');
        $this->include_controller('client');
        $this->include_controller('fb');
        $this->include_controller('song');
        $this->include_controller('arrange');
        $this->include_controller('text');
        $this->include_controller('account');
        // fb check
        $this->fb_init();
        $this->fb_login_status = $this->fb_login_status($this->fb_user_id());
        // user check
        $this->user_status = $this->user_status();
        // last comments
        $this->smarty->assign('comment_last', $this->comment_load_last());
        // count elements for account header
        if (($this->fb_login_status || $this->user_status) && isset($this->session->userdata['user_authorised'])) {
            $this->smarty->assign('account_header_element_count', $this->account_header_load_element_count());
            $this->smarty->assign('point', $this->point_get($this->session->userdata['user_id']));
        }*/
	}

	// index
	function index() {
        header('location: index.php/main/run/home');
	}

	// run
	function run() {
		// we need to segment uri to gain some control
		$this->uri2 = array_slice($this->uri->segment_array(), 2);
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

    /*
    // fb
    function fb_init() {
        $this->fb = new Fb($this);
    }
    function fb_access_token() {
        return $this->fb->access_token();
    }
    function fb_user_id() {
        return $this->fb->user_id();
    }
    function fb_user_picture() {
        return $this->fb->user_picture();
    }
    function fb_login_status($id) {
        if (isset($id) && $id != 0) {
            $this->fb_generate_logout_url();
            $this->me = $this->fb->user_detail();
            $this->user = $this->user_get('user', $this->me['email']);
            $this->smarty->assign('fb_status', 1);
            $this->smarty->assign('user_id', $this->user['id']);
            $this->smarty->assign('user_id_media_file', $this->user['id_media_file']);
            $this->smarty->assign('user_id_media_image', $this->user['id_media_image']);
            $this->smarty->assign('user_id_media_video', $this->user['id_media_video']);
            //$this->smarty->assign('user_nick', $this->me['name']);
            $this->smarty->assign('user_nick', $this->user['nick']);
            $this->smarty->assign('user_user', $this->me['email']);
            $this->smarty->assign('user_image', $this->fb_user_picture());
            $this->smarty->assign('me', $this->me);
            if ($this->user['nick'] == '') {
                $this->smarty->assign('nick_missing', 1);
                $this->smarty->assign('user_nick', '-');
                $this->smarty->assign('operation', 'nick_missing');
                $this->smarty->assign('result', 0);
                $this->nick_missing = true;
            }
            return 1;
        } else {
            $this->fb_generate_login_url();
            $this->smarty->assign('fb_status', 0);
            return 0;
        }
    }
    function fb_generate_login_url() {
        $params = array(
            'scope' => 'offline_access, email',
            'redirect_uri' => SITE_URL.'/index.php/main/run/register/add_facebook'
        );
        $this->fb_login_url = $this->fb->generate_login_url($params);
        $this->smarty->assign('fb_login_url', $this->fb_login_url);
    }
    function fb_generate_logout_url() {
        $params = array(
            'next' => SITE_URL . '/index.php/main/run/fb/restart'
        );
        $this->fb_logout_url = $this->fb->generate_logout_url($params);
        $this->smarty->assign('fb_logout_url', $this->fb_logout_url);
    }

    // user
    function user_status() {
        if (isset($this->session->userdata['user_mode']) && $this->session->userdata['user_mode'] == 'account') {
            $this->user = $this->user_get('user', $this->session->userdata['user_user']);
            $this->smarty->assign('user_status', 1);
            $this->smarty->assign('user_id', $this->session->userdata['user_id']);
            $this->smarty->assign('user_id_media_file', $this->session->userdata['user_id_media_file']);
            $this->smarty->assign('user_id_media_image', $this->session->userdata['user_id_media_image']);
            $this->smarty->assign('user_id_media_video', $this->session->userdata['user_id_media_video']);
            $this->smarty->assign('user_nick', $this->session->userdata['user_nick']);
            $this->smarty->assign('user_user', $this->session->userdata['user_user']);
            $this->smarty->assign('user_image', $this->session->userdata['user_image']);
            return 1;
        } else {
            $this->smarty->assign('user_status', 0);
            return 0;
        }
    }

    // session
    function session_create_facebook($record) {
        $this->load->helper('date');
        $user = array(
            'user_authorised' => true,
            'user_id' => $record['id'],
            'user_id_media_file' => $record['id_media_file'],
            'user_id_media_image' => $record['id_media_image'],
            'user_id_media_video' => $record['id_media_video'],
            'user_nick' => $record['nick'],
            'user_user' => $record['user'],
            'user_logged' => now(),
            'user_image' => $record['facebook_avatar'],
            'user_mode' => 'facebook'
        );
        $this->ci->session->set_userdata($user);
        // force to login swap view after first good attempt
        $this->ci->smarty->assign('fb_status', 1);
        $this->ci->smarty->assign('user_id', $this->ci->session->userdata['user_id']);
        $this->ci->smarty->assign('user_id_media_file', $this->ci->session->userdata['user_id_media_file']);
        $this->ci->smarty->assign('user_id_media_image', $this->ci->session->userdata['user_id_media_image']);
        $this->ci->smarty->assign('user_id_media_video', $this->ci->session->userdata['user_id_media_video']);
        $this->ci->smarty->assign('user_nick', $this->ci->session->userdata['user_nick']);
        $this->ci->smarty->assign('user_user', $this->ci->session->userdata['user_user']);
        $this->ci->smarty->assign('user_image', $this->ci->session->userdata['user_image']);
    }
    function session_create_user($record) {
        $this->load->helper('date');
        $user = array(
            'user_authorised' => true,
            'user_id' => $record['id'],
            'user_id_media_file' => $record['id_media_file'],
            'user_id_media_image' => $record['id_media_image'],
            'user_id_media_video' => $record['id_media_video'],
            'user_nick' => $record['nick'],
            'user_user' => $record['user'],
            'user_logged' => now(),
            'user_image' => $record['image'],
            'user_mode' => 'account'
        );
        $this->ci->session->set_userdata($user);
        // force to login swap view after first good attempt
        $this->ci->smarty->assign('user_status', 1);
        $this->ci->smarty->assign('user_id', $this->ci->session->userdata['user_id']);
        $this->ci->smarty->assign('user_id_media_file', $this->ci->session->userdata['user_id_media_file']);
        $this->ci->smarty->assign('user_id_media_image', $this->ci->session->userdata['user_id_media_image']);
        $this->ci->smarty->assign('user_id_media_video', $this->ci->session->userdata['user_id_media_video']);
        $this->ci->smarty->assign('user_nick', $this->ci->session->userdata['user_nick']);
        $this->ci->smarty->assign('user_user', $this->ci->session->userdata['user_user']);
        $this->ci->smarty->assign('user_image', $this->ci->session->userdata['user_image']);
        $this->ci->smarty->assign('point', $this->point_get($this->ci->session->userdata['user_id']));
    }
    function session_destroy_user() {
        $this->ci->session->unset_userdata('user_authorised');
        $this->ci->session->unset_userdata('user_id');
        $this->ci->session->unset_userdata('user_nick');
        $this->ci->session->unset_userdata('user_user');
        $this->ci->session->unset_userdata('user_logged');
        $this->ci->session->unset_userdata('user_image');
        $this->ci->session->unset_userdata('user_mode');
        $this->ci->session->unset_userdata('briefcase');
        // force to logout swap view after first good attempt
        $this->ci->smarty->assign('user_status', 0);
    }

    // user
    function user_get($field = null, $value = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/user_get/'. $field .','. $value;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['user'];
        } else {
            return 0;
        }
    }

    // increment
    function display_increment($table = null, $id = null) {
        // cookie to prevent increment illegal increase
        $dic_name = $table.'_'.$id.'_increment';
        $dic = get_cookie($dic_name);
        if ($dic) {
            $increment = false;
        } else {
            $increment = true;
            set_cookie($dic_name, true, 8);
        }
        // increment
        if ($increment) {
            $url = CONSOLE_URL.'/kreomaniak:hub/display_increment/'. $table .','. $id;
            $result = $this->api_call($url);
            if ($result['success'] == 1) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    // genre
    function genre_get_all($type = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/genre_get_all/'. $type;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['genre'];
        } else {
            return 0;
        }
    }
    function genre_get($id_genre = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/genre_get/'. $id_genre;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['genre'];
        } else {
            return 0;
        }
    }

    // rate
    function rate_compute($data = null, $type = null) {
        foreach($data as $key => $value) {
            $rate = $this->rate_get($value['id'], $type);
            $data[$key]['rate_total'] = $rate['total'];
            $data[$key]['rate_computed'] = $rate['rate_computed'];
        }
        return $data;
    }
    function rate_compute_single($data = null, $type = null) {
        $rate = $this->rate_get($data['id'], $type);
        $data['rate_total'] = $rate['total'];
        $data['rate_computed'] = $rate['rate_computed'];
        return $data;
    }
    function rate_sort($data = null) {
        $this->rate_sort_loop($data, 'rate_computed');
        if (isset($data[0])) {
            return $data[0];
        } else {
            return false;
        }
    }
    function rate_sort_loop(&$arr, $col, $dir = SORT_DESC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }
    function rate_enabled($id = null, $type = null) {
        $id_user = isset($this->ci->session->userdata['user_id']) ? $this->ci->session->userdata['user_id'] : 0;
        if ($id_user == 0) {
            return 0;
        } else {
            // cookie to prevent increment illegal voting
            $rc_name = $id_user.'_'.$id.'_'.$type.'_rate';
            $rc = get_cookie($rc_name);
            if ($rc) {
                $filter = false;
                return 0;
            } else {
                $filter = true;
            }
            // additionally, check and compare data in db
            if ($filter) {
                $url = CONSOLE_URL.'/kreomaniak:hub/rate_enabled/'. $id_user .','. $id .','. $type;
                $result = $this->api_call($url);
                return $result;
            }
        }
    }
    function rate_get($id = null, $type = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/rate_get/'. $id .','. $type;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            $rate = $result['rate'];
            $total = count($rate);
            //
            $rate_total = 0;
            foreach ($rate as $entry) {
                $rate_total += $entry['rate'];
            }
            if ($rate_total > 0) {
                $rate_computed = round($rate_total / $total, 2);
                $rate_computed_round = round($rate_total / $total, 0);
            } else {
                $rate_computed = 0;
            }
            //
            $rate_final = array();
            $rate_final['total'] = $total;
            $rate_final['rate_computed'] = $rate_computed;
            $rate_final['rate_computed_round'] = $rate_computed_round;
            return $rate_final;
        } else {
            $rate_final = array();
            $rate_final['total'] = 0;
            $rate_final['rate_computed'] = 0;
            $rate_final['rate_computed_round'] = 0;
            return $rate_final;
        }
    }
    function rate_set($template = null, $title_call = null, $id_user = null, $id = null, $type = null) {
        $enabled = $this->rate_enabled($id, $type);
        if ($enabled) {
            // cookie add
            $rc_name = $id_user.'_'.$id.'_'.$type.'_rate';
            set_cookie($rc_name, true, 21600);
            // database add
            $url = CONSOLE_URL.'/kreomaniak:hub/rate_set/'. $id_user .','. $id .','. $type;
            $data = $_POST;
            $result = $this->api_call($url, $data);
            if ($result['success'] == 1) {
                // nothing here temporarily
            }
        }
        // redirect
        if ($type == 0) {
            $this->smarty_redirect('song/display_player/'.$id);
        }
        if ($type == 1) {
            $this->smarty_redirect('arrange/display_player/'.$id);
        }
        if ($type == 2) {
            $this->smarty_redirect('text/display_player/'.$id);
        }
    }

    // comment
    function comment_load_all($id_element = null, $type = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/comment_load_all_ui/'. $id_element .','. $type;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        } else {
            return 0;
        }
    }
    function comment_load_last() {
        $url = CONSOLE_URL.'/kreomaniak:hub/comment_load_last_ui';
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        } else {
            return 0;
        }
    }
    function comment_add($template = null, $title_call = null, $id_user = null, $id_element = null, $type = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/comment_add_ui/'. $id_user. ','. $id_element .','. $type;
        $data = $_POST;
        $this->api_call($url, $data);
        // redirect to last visible element
        switch ($type) {
            case 0: $type_name = 'song'; break;
            case 1: $type_name = 'arrange'; break;
            case 2: $type_name = 'text'; break;
        }
        header('location: '.APP_URL.'/run/'.$type_name.'/display_player/'.$id_element);
    }
    function comment_delete($template = null, $title_call = null, $id = null, $id_element = null, $type = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/comment_delete_ui/'. $id;
        $data = $_POST;
        $this->api_call($url, $data);
        // redirect to last visible element
        switch ($type) {
            case 0: $type_name = 'song'; break;
            case 1: $type_name = 'arrange'; break;
            case 2: $type_name = 'text'; break;
        }
        header('location: '.APP_URL.'/run/'.$type_name.'/display_player/'.$id_element);
    }

    // account
    function account_header_load_element_count(){
        $url = CONSOLE_URL.'/kreomaniak:hub/account_header_load_element_count_ui/'.$this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        } else {
            return 0;
        }
    }
    function account_header_load_element_profile_count($id = null){
        $url = CONSOLE_URL.'/kreomaniak:hub/account_header_load_element_count_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        } else {
            return 0;
        }
    }

    // search
    function search($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/search_ui';
        $data = $_POST;
        $result = $this->api_call($url, $data);
        //$this->ci->smarty->assign('result', $result['success']);
        //$this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('search', $result['data']);
        //$this->ci->smarty->assign('operation', 'search_result');
        $this->smarty_display('search');
    }

    // paginate
    function paginate_init($type = null, $limit = 0, $total = 1) {
        $paginate = $this->ci->session->userdata('paginate_'.$type);
        //if ($paginate != null && $paginate != '') {
            //return $paginate;
        //} else {
            $data = array();
            $data['limit'] = $limit;
            $data['total'] = $total;
            $data['page_current'] = 1;
            $data['page_total'] = ceil($total / $limit);
            $this->ci->session->set_userdata('paginate_'.$type, $data);
            return $data;
        //}
    }
    function paginate_from($paginate = null) {
        $paginate_number = array();
        //
        $from = $paginate['page_current'] - 2;
        $to = $paginate['page_current'] + 3;
        //
        if ($from <= 1) {
            $from = 1;
            $to = 6;
            if ($to > $paginate['page_total']) {
                $to = $paginate['page_total'] + 1;
            }
        }
        if ($to > $paginate['page_total']) {
            $from = $paginate['page_total'] - 4;
            if ($from < 1) {
                $from = 1;
            }
            $to = $paginate['page_total'] + 1;
        }
        //
        $paginate_number['from'] = $from;
        $paginate_number['to'] = $to;
        //
        return $paginate_number;
    }
    function paginate_get($type = null) {
        $paginate = $this->ci->session->userdata('paginate_'.$type);
        return $paginate;
    }
    function paginate_page_current_set($type = null, $page = 0) {
        $paginate = $this->ci->session->userdata('paginate_'.$type);
        $paginate['page_current'] = $page;
        $this->ci->session->set_userdata('paginate_'.$type, $paginate);
    }
    function paginate_page_current_get($type = null) {
        $paginate = $this->ci->session->userdata('paginate_'.$type);
        return $paginate['page_current'];
    }

    // point
    function point_get($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/point_get_ui/'. $id;
        $result = $this->api_call($url);
        return $result['point'];
    }
    function point_reduce($point = null, $operation = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/point_reduce_ui/'. $this->ci->session->userdata['user_id'] .','. $point .','. $operation;
        $result = $this->api_call($url);
        return $result;
    }

    // last added elements ( song, arrange, text )
    function last_added() {
        $url = CONSOLE_URL.'/kreomaniak:hub/last_added_ui/';
        $result = $this->api_call($url);
        // its seems that we need to do some special things
        // because of client request
        // ok, so first we need to iterate throuh result
        foreach($result['data'] as $key => $entry) {
            if ($entry['source'] == 'kreomaniak_song') {
                $result['data'][$key] = $this->rate_compute_single($entry, 0);
            }
            if ($entry['source'] == 'kreomaniak_arrange') {
                $result['data'][$key] = $this->rate_compute_single($entry, 1);
            }
            if ($entry['source'] == 'kreomaniak_text') {
                $result['data'][$key] = $this->rate_compute_single($entry, 2);
            }
        }
        return $result;
    }
    function last_added_date($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:hub/last_added_date_ui/'.$id;
        $result = $this->api_call($url);
        return $result;
    }

    // smarty redirect
    function smarty_redirect_variables($template = null, $title_call = null, $variable = null) {
        $path = 'Location: '. APP_URL .'/run/'.$template.'/'.$variable;
        header($path);
    }*/

}