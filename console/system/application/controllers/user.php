<?php
if (!defined('BASEPATH')) die;

class User extends Main {

	function User($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
        if (!isset($this->config_db_table_user)) { $this->config_db_table_user = 'user'; }
        //
        $this->ci->load->model('main_model');
		$this->ci->load->model('user_model');
	}

    // load
    function load_all() {
        $result = $this->client_model->load_all();
        echo '{"total":'.json_encode($result['total']).', "data":'.json_encode($result['data']).'}';
	}
    function load($field = 'id', $value = null) {
		echo '{"success": true, "data":'.json_encode($this->user_model->load($field, $value)).'}';
	}

	// opt-in
	function add_opt_in($record) {
		if (!isset($record['user'])) { die(); }
        // lets define data 
        $this->ci->smarty->assign('path_template', SITE_URL.'/templates/email/'.CONFIGURATION);
        $this->ci->smarty->assign('path_media', SITE_URL.'/templates/email/'.CONFIGURATION);
        $this->ci->smarty->assign('path_site', SITE_URL);
        $this->ci->smarty->assign('user', $record['user']);
        $this->ci->smarty->assign('name', $record['name']);
        $this->ci->smarty->assign('surname', $record['surname']);
        $this->ci->smarty->assign('password_hash', $record['password_hash']);
        $message = $this->ci->smarty->fetch('../../../../templates/email/'.CONFIGURATION.'/client_optin.html');
        //
        $config['protocol'] = EMAIL_PROTOCOL;
        $config['mailtype'] = EMAIL_MODE;
		$config['charset'] = EMAIL_ENCODING;
        //
        $this->ci->email->initialize($config);
        $this->ci->email->subject($this->config_email_subject_account_confirm);
        $this->ci->email->from($this->config_email_from1, $this->config_email_from2);
        $this->ci->email->to($record['user']);
        $this->ci->email->message($message);
        $this->ci->email->send();
	}

	// opt-in confirm
	function add_opt_in_confirm($password_hash = null) {
        if (!isset($password_hash)) { die(); }
        // select user
		$this->ci->db->where('password_hash', $password_hash);
		$query = $this->ci->db->get('motonacja_client');
		$record = $query->row_array();
        // lets define data 
        $this->ci->smarty->assign('path_template', SITE_URL.'/templates/email/'.CONFIGURATION);
        $this->ci->smarty->assign('path_media', SITE_URL.'/templates/email/'.CONFIGURATION);
        $this->ci->smarty->assign('path_site', SITE_URL);
        $this->ci->smarty->assign('user', $record['user']);
        $this->ci->smarty->assign('name', $record['name']);
        $this->ci->smarty->assign('surname', $record['surname']);
        $message = $this->ci->smarty->fetch('../../../../templates/email/'.CONFIGURATION.'/client_optin_confirm.html');
        //
        $config['protocol'] = EMAIL_PROTOCOL;
        $config['mailtype'] = EMAIL_MODE;
		$config['charset'] = EMAIL_ENCODING;
        //
        $this->ci->email->initialize($config);
        $this->ci->email->subject('Motonacja.com - Twoja rejestracja została potwierdzona');
        $this->ci->email->from('motonacja@motonacja.com', 'Motonacja.com');
        $this->ci->email->to($record['user']);
        $this->ci->email->message($message);
        $this->ci->email->send();
	}

    // password reset
    function password_reset($user = null) {
        if (!isset($user)) { echo '{"success": false}'; die; }
        // first we need user data
        $record = $this->user_model->load('user', $user);
        // logic: to send email you need a record and any password in a field
        // otherwise - result would be false
        if (isset($record) && isset($record['password'])) {
            // clear password field
            $this->user_model->password_clear($record['password_hash']);
            // template is on the start
            $this->ci->smarty->assign('path_template', SITE_URL.'/templates/email/'.CONFIGURATION);
            $this->ci->smarty->assign('path_media', SITE_URL.'/templates/email/'.CONFIGURATION);
            $this->ci->smarty->assign('path_site', SITE_URL);
            $this->ci->smarty->assign('user', $record['user']);
            $this->ci->smarty->assign('name', $record['name']);
            $this->ci->smarty->assign('surname', $record['surname']);
            $this->ci->smarty->assign('password_hash', $record['password_hash']);
            $message = $this->ci->smarty->fetch('../../../../templates/email/'.CONFIGURATION.'/client_password_reset.html');
            // now, email config
            $config['protocol'] = EMAIL_PROTOCOL;
            $config['mailtype'] = EMAIL_MODE;
    		$config['charset'] = EMAIL_ENCODING;
            // hit it! send it!
            $this->ci->email->initialize($config);
            $this->ci->email->subject($this->config_email_subject_password_reset);
            $this->ci->email->from($this->config_email_from1, $this->config_email_from2);
            $this->ci->email->to($record['user']);
            $this->ci->email->message($message);
            $this->ci->email->send();
            //
            echo '{"success": true}';
        } else {
            echo '{"success": false}';
        }
    }

    // password reset confirm
    function password_reset_confirm($password_hash = null) {
        if (!isset($password_hash)) { echo '{"success": false}'; die; }
        // first we need user data
        $record = $this->user_model->load('password_hash', $password_hash);
        // logic: to send email with new password you need a record and empty (null) password field
        // otherwise - result would be false
        if (isset($record) && !isset($record['password'])) { 
            // lets generate new password
            $password = $this->password_generate();
            // cipher new password with sha1
            $password_sha1 = sha1($password);
            // lets update user record
            $this->user_model->password_update($password_hash, $password_sha1);
            // template prepare
            $this->ci->smarty->assign('path_template', SITE_URL.'/templates/email/'.CONFIGURATION);
            $this->ci->smarty->assign('path_media', SITE_URL.'/templates/email/'.CONFIGURATION);
            $this->ci->smarty->assign('path_site', SITE_URL);
            $this->ci->smarty->assign('user', $record['user']);
            $this->ci->smarty->assign('name', $record['name']);
            $this->ci->smarty->assign('surname', $record['surname']);
            $this->ci->smarty->assign('password', $password);
            $message = $this->ci->smarty->fetch('../../../../templates/email/'.CONFIGURATION.'/client_password_reset_confirm.html');
            // now, email config
            $config['protocol'] = EMAIL_PROTOCOL;
            $config['mailtype'] = EMAIL_MODE;
      		$config['charset'] = EMAIL_ENCODING;
            // hit it! send it!
            $this->ci->email->initialize($config);
            $this->ci->email->subject($this->config_email_subject_password_reset);
            $this->ci->email->from($this->config_email_from1, $this->config_email_from2);
            $this->ci->email->to($record['user']);
            $this->ci->email->message($message);
            $this->ci->email->send();
            //
            echo '{"success": true}';
        } else {
            echo '{"success": false}';
        }
    }

    // password generate
    function password_generate() {
        $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ1234567890";
        $validCharNumber = strlen($validCharacters);
        $password = '';
        for ($i = 0; $i < 5; $i++) {
            $index = mt_rand(0, $validCharNumber-1);
            $password .= $validCharacters[$index];
        }
        return $password;
    }

    // user exist
    function user_exist($user = null) {
        if (!isset($user)) { die; }
        $success = $this->user_model->user_exist($user);
        echo '{"success": '.$success.'}';
    }

    // user activation
    function user_activation($password_hash = null) {
        $success = $this->user_model->user_activation($password_hash);
        if ($success) {
            $this->add_opt_in_confirm($password_hash);
        }
        echo '{"success": '.$success.'}';
    }

    // login
    function login() {
        $result = $this->user_model->login();
        if ($result['status']) {
            echo '{"status": '. $result['status'] .', "client": '. json_encode($result['client']) .'}';
        } else {
            echo '{"status": '. $result['status'] .', "error": '. $result['error'] .'}';
        }
    }

}