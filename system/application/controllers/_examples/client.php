<?php

class Client extends Hub {

    function Client($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // login
    function login($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/login';
        $data = $_POST;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            // in main controller
            $this->session_create_user($result['client']);
            // assign numbers for first login
            $this->user['id'] = $result['client']['id'];
            $this->ci->smarty->assign('account_header_element_count', $this->account_header_load_element_count());
        }
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'login');
        //$this->smarty_redirect('home');
        // display
        $home = new Home($this->ci);
        $home->display('home');
    }
    function logout($template = null, $title_call = null) {
        $this->session_destroy_user();
        $this->ci->smarty->assign('result', 1);
        $this->ci->smarty->assign('code', 'ok');
        $this->ci->smarty->assign('operation', 'logout');
        // display
        $home = new Home($this->ci);
        $home->display('home');
    }

    // last registered
    function client_last_registered() {
        $url = CONSOLE_URL.'/kreomaniak:client/load_last_registered_ui/';
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }

}