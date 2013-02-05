<?php
class Register extends Hub {

	function Register($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // index
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->smarty_display($template);
    }

    // add
    function add($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/add_ui';
        $data = $_POST;
        $result = $this->api_call($url, $data);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'user_add');
        // display
        $home = new Home($this->ci);
        $home->display('home');
    }
    function add_facebook($template = null, $title_call = null) {
        if (isset($this->ci->me)) {
            // ok, user authorised an access via an application
            // now we need to check sensitive issue - update or add new user
            $url = CONSOLE_URL.'/kreomaniak:client/add_facebook_ui_check';
            $data = array();
            $data['user'] = $this->ci->me['email'];
            $result = $this->api_call($url, $data);
            if ($result['success'] == 1) {
                if ($result['code'] == 'user_exist') {
                    // well, user exist, lets update only crucial fb data
                    $record = array();
                    $record['facebook_auth'] = 1;
                    $record['facebook_id'] = $this->ci->me['id'];
                    $record['facebook_access_token'] = $this->ci->fb_access_token();
                    $record['facebook_avatar'] = $this->ci->fb_user_picture();
                    $record['date_last_modified'] = date("Y-m-d H:i:s");
                    //
                    $url = CONSOLE_URL.'/kreomaniak:client/add_facebook_ui_update/'. $data['user'];
                    $data = $record;
                    $result = $this->api_call($url, $data);
                }
                if ($result['code'] == 'user_does_not_exist') {
                    // here we put new record to client db
                    $record = array();
                    $record['facebook_auth'] = 1;
                    $record['facebook_id'] = $this->ci->me['id'];
                    $record['facebook_access_token'] = $this->ci->fb_access_token();
                    $record['facebook_avatar'] = $this->ci->fb_user_picture();
                    //$record['nick'] = $this->ci->me['first_name'].' '.$this->ci->me['last_name'];
                    $record['user'] = $this->ci->me['email'];
                    $record['name'] = $this->ci->me['first_name'];
                    $record['surname'] = $this->ci->me['last_name'];
                    $record['checkbox_marketing'] = 1;
                    $record['checkbox_newsletter'] = 1;
                    $record['active'] = 1;
                    $record['date_added'] = date("Y-m-d H:i:s");
                    $record['date_activated'] = date("Y-m-d H:i:s");
                    $record['date_last_modified'] = date("Y-m-d H:i:s");
                    //
                    $url = CONSOLE_URL.'/kreomaniak:client/add_facebook_ui_add';
                    $data = $record;
                    $result = $this->api_call($url, $data);
                }
            }
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'user_add_facebook');
        } else {
            // nothing here :)
        }
        // create session - in main controller!
        if (isset($this->ci->me)) {
            $this->user = $this->user_get('user', $this->ci->me['email']);
            $this->session_create_facebook($this->user);
            // operation depends on effect
            if ($result['code'] == 'facebook_add') {
                $this->ci->smarty->assign('nick_missing', 1);
                $this->ci->smarty->assign('user_nick', '-');
                $account = new Account($this->ci);
                $account->display('account');
            }
            if ($result['code'] == 'facebook_update') {
                $this->ci->smarty->assign('account_header_element_count', $this->account_header_load_element_count());
                $this->ci->smarty->assign('point', $this->point_get($this->user['id']));
                //
                $home = new Home($this->ci);
                $home->display('home');
            }
        }
    }

    // activate
    function activate($template = null, $title_call = null, $password_hash) {
        $url = CONSOLE_URL . '/kreomaniak:client/activate/'.$password_hash;
        $result = $this->api_call($url);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'activate');
        //
        $home = new Home($this->ci);
        $home->display('home');
    }

}