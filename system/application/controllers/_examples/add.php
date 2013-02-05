<?php
class Add extends Hub {

	function Add($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display main
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->ci->smarty->assign('point', $this->point_get($this->ci->session->userdata['user_id']));
        $this->smarty_display($template);
    }

}