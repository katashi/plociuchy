<?php
class Home extends Hub {

	function Home($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        // display
        $this->smarty_display($template);
    }
    function display_redirect($template = null, $title_call = null, $variable = null) {
        $result = unserialize($variable);
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', $result['operation']);
        //
        $this->display('home');
    }

}