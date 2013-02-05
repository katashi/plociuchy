<?php
class Password_Reset extends Hub {

	function Password_Reset($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // index
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->smarty_display($template);
    }

    // reset
    function password_reset_ui($template = null, $title_call = null) {
        if (isset($_POST['user'])) {
            $url = CONSOLE_URL.'/kreomaniak:client/password_reset_ui/'.$_POST['user'];
            $result = $this->api_call($url);
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'password_reset');
        } else {
            $this->ci->smarty->assign('result', 0);
            $this->ci->smarty->assign('code', 'user_missing');
            $this->ci->smarty->assign('operation', 'password_reset');
        }
        // display
        $home = new Home($this->ci);
        $home->display('home');
    }
    function password_reset_confirm($template = null, $title_call = null, $password_hash = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/password_reset_confirm_ui/'.$password_hash;
        $result = $this->api_call($url);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'password_reset');
        // display
        $home = new Home($this->ci);
        $home->display('home');
    }

}