<?php
if (!defined('BASEPATH')) die;

class Partner extends Main {

    function Partner($_ci = '') {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/partner_model');
        $this->load->model('_system/tree_model');
    }

    // display
    function display() {
        $this->ci->smarty->display('plociuchy/partner.html');
    }
    function display_add() {
        $this->ci->smarty->display('plociuchy/partner_add.html');
    }
    function display_edit($id = null) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/partner_edit.html');
    }

    // load
    function load_all() {
        echo '{"total":' . json_encode($this->partner_model->load_all_count()) . ', "data":' . json_encode($this->partner_model->load_all()) . '}';
    }
    function load_all_user($id = null) {
        echo '{"total":' . json_encode($this->partner_model->load_all_partner_count($id)) . ', "data":' . json_encode($this->partner_model->load_all_partner($id)) . '}';
    }
    function load($id = null) {
        echo '{"success": 1, "data":' . json_encode($this->partner_model->load($id)) . '}';
    }

    // add
    function add() {
        $result = $this->partner_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null) {
        $result = $this->partner_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null) {
        $result = $this->partner_model->delete($id);
        echo '{"success":' . $result . '}';
    }

    // active
    function active_set($id = null, $state = false) {
        $result = $this->partner_model->active_set($id, $state);
        echo 'grid';
    }

    function add_ui() {
        //add user
        $result = $this->partner_model->add_ui();
        // if success send optin email
        if ($result['success'] == 1) {
            $this->add_opt_in($_POST);
        }
        echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '"}';
    }

    //
    function activate($password_hash = null) {
        $result = $this->partner_model->activate($password_hash);
        // if hash fits send confirmation email
        if ($result['success'] == 1) {
            // $this->add_opt_in_confirm($password_hash);
        }
        echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '"}';
    }

    function login_ui() {
        $result = $this->partner_model->login();
        if ($result['success'] == 1) {
            echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '", "client": ' . json_encode($result['client']) . '}';
        } else {
            echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '"}';
        }
    }

    // opt wysyłanie maila
    function add_opt_in($record) {
        // lets define data
        $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_site', SITE_URL);
        $this->ci->smarty->assign('nick', $_POST['name'] . ' ' . $_POST['surname']);
        $this->ci->smarty->assign('password_hash', $record['password_hash']);
        $message = $this->ci->smarty->fetch('../../../../templates/email/' . CONFIGURATION . '/partner_optin.html');
        //
        $config['protocol'] = EMAIL_PROTOCOL;
        $config['mailtype'] = EMAIL_MODE;
        $config['charset'] = EMAIL_ENCODING;
        //
        $this->ci->email->initialize($config);
        $this->ci->email->subject('Plociuchy - Rejestracja Partnera');
        $this->ci->email->from('kontakt@plo-ciuchy.pl');
        $this->ci->email->to($_POST['user']);
        $this->ci->email->message($message);
        $this->ci->email->send();
    }

    //match passwords
    function match_password($id_user, $password) {
        $pass = $this->partner_model->match_password($id_user, $password);
        if ($pass) {
            echo '{"success": "' . $pass['success'] . '" , "code": "' . $pass['code'] . '"}';
        }
    }
    //reset hasla podanego przez usera
    function password_reset_partner_ui() {
        $reset = $this->partner_model->password_reset_ui();
        if ($reset['code'] == 'ok') {
            echo '{"success": 1, "code": "password_reset"}';
        } else {
            echo '{"success": 0, "code": "password_error"}';
        }
    }

    // password reset
    function password_reset_ui($user = null) {
        if (!isset($user)) {
            echo '{"success": false}';
            die;
        }
        // first we need user data
        $record = $this->partner_model->load_field('user', $user);
        //
        if (isset($record) && isset($record['password'])) {
            // clear password field
            //$this->partner_model->password_clear($record['password_hash']);
            // template is on the start
            $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
            $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
            $this->ci->smarty->assign('path_site', SITE_URL);
            $this->ci->smarty->assign('user', $record['user']);
            $this->ci->smarty->assign('name', $record['name']);
            $this->ci->smarty->assign('surname', $record['surname']);
            $this->ci->smarty->assign('password_hash', $record['password_hash']);
            $message = $this->ci->smarty->fetch('../../../../templates/email/' . CONFIGURATION . '/partner_password_reset.html');
            // now, email config
            $config['protocol'] = EMAIL_PROTOCOL;
            $config['mailtype'] = EMAIL_MODE;
            $config['charset'] = EMAIL_ENCODING;
            // hit it! send it!
            $this->ci->email->initialize($config);
            $this->ci->email->subject('plo-ciuchy.pl - Reset Hasła Partnera');
            $this->ci->email->from('kontakt@plo-ciuchy.pl');
            $this->ci->email->to($record['user']);
            $this->ci->email->message($message);
            $this->ci->email->send();
            //
            echo '{"success": 1, "code": "password_reset"}';
        } else {
            echo '{"success": 0, "code": "user_missing"}';
        }
    }

    function password_reset_confirm_ui($password_hash = null) {
        if (!isset($password_hash)) {
            echo '{"success": false}';
            die;
        }
        // first we need user data
        $record = $this->partner_model->load_field('password_hash', $password_hash);
        //
        if (isset($record) && isset($record['password'])) {
            // lets generate new password
            $password = $this->password_generate();
            // cipher new password with sha1
            $password_sha1 = sha1($password);
            // lets update user record
            $this->partner_model->password_update($password_hash, $password_sha1);
            // template prepare
            $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
            $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
            $this->ci->smarty->assign('path_site', SITE_URL);
            $this->ci->smarty->assign('user', $record['user']);
            $this->ci->smarty->assign('name', $record['name']);
            $this->ci->smarty->assign('surname', $record['surname']);
            $this->ci->smarty->assign('password', $password);
            $message = $this->ci->smarty->fetch('../../../../templates/email/' . CONFIGURATION . '/partner_password_reset_confirm.html');
            // now, email config
            $config['protocol'] = EMAIL_PROTOCOL;
            $config['mailtype'] = EMAIL_MODE;
            $config['charset'] = EMAIL_ENCODING;
            // hit it! send it!
            $this->ci->email->initialize($config);
            $this->ci->email->subject('Plo-ciuchy.pl - Reset Hasła Partnera');
            $this->ci->email->from('kontakt@plo-ciuchy.pl');
            $this->ci->email->to($record['user']);
            $this->ci->email->message($message);
            $this->ci->email->send();
            //
            echo '{"success": 1, "code": "password_changed"}';
        } else {
            echo '{"success": 0, "code": "user_missing"}';
        }
    }

    function password_generate() {
        $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ1234567890";
        $validCharNumber = strlen($validCharacters);
        $password = '';
        for ($i = 0; $i < 5; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $password .= $validCharacters[$index];
        }
        return $password;
    }


}