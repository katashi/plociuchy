<?php
class Hub extends Main {

	function Hub($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // smarty
    function smarty_display($template = null, $title_call = null) {
        //Display user info
        $this->display_user_data();
        //Display messages
        $this->ci->smarty->assign('messages',$this->display_messages());
        //display
        $this->ci->smarty->display($template.'.html');
    }
    function smarty_redirect($template = null, $title_call = null) {
        $path = 'Location: '. APP_URL .'/run/'.$template;
        header($path);
    }
    function smarty_redirect_variables($template = null, $title_call = null, $variable = null) {
        $path = 'Location: '. APP_URL .'/run/'.$template.'/'.$variable;
        header($path);
    }

    // assign basic values
    function assign_template_titlecall($template = null, $title_call = null, $authorisation_required = false) {
        // those values references to values passed from main controller
        $this->template = $template;
        $this->title_call = $title_call;
        //
        $this->ci->smarty->assign('template', $this->template);
        $this->ci->smarty->assign('title_call', $this->title_call);
        //
        if ($authorisation_required) {
            if (!isset($this->ci->session->userdata['user_authorised'])) {
                $variable = array();
                $variable['success'] = 0;
                $variable['code'] = 'unauthorised';
                $variable['operation'] = 'redirect';
                $variable = serialize($variable);
                $this->smarty_redirect_variables('home/display_redirect', 'home', $variable);
            }
        }
    }


    public function display_user_data(){
        if (isset($this->ci->session->userdata['user_authorised'])){
            $this->ci->smarty->assign('user_authorised',$this->ci->session->userdata['user_authorised']);
            $this->ci->smarty->assign('user_id',$this->ci->session->userdata['user_id']);
            $this->ci->smarty->assign('user_name',$this->ci->session->userdata['user_name']);
            $this->ci->smarty->assign('user_surname',$this->ci->session->userdata['user_surname']);
        }
    }

}