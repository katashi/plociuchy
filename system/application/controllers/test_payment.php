<?php
class Test_Payment extends Hub {

	function Test_Payment($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        // add reservation
        // add payment
        $url = CONSOLE_URL.'/plociuchy:payment_p24/add_ui';
        $result = $this->api_call($url, $data);
        // assign variables to form
        $this->ci->smarty->assign('payu_posid', PLATNOSCI_POSID);
        $this->ci->smarty->assign('payu_1md5', PLATNOSCI_1MD5);
        $this->ci->smarty->assign('payu_2md5', PLATNOSCI_2MD5);
        $this->ci->smarty->assign('payu_posauthkey', PLATNOSCI_POSAUTHKEY);
        $this->ci->smarty->assign('payu_id_order', $result['id']);
        $this->ci->smarty->assign('payu_id_session', $this->ci->session->userdata['session_id']);
        $this->ci->smarty->assign('payu_ip', $this->ci->session->userdata['ip_address']);
        $this->ci->smarty->assign('payu_amount_gr', $amount_gr);
        $this->ci->smarty->assign('payu_amount_zl', $amount_zl);
        $this->ci->smarty->assign('user', $this->payment_load_user_ui());
        $this->ci->smarty->assign('tab_title', 'Płatności');
        $this->ci->smarty->assign('tab', 'payment_add_cc');
        // display
        $this->smarty_display($template);
    }

}