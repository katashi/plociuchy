<?php
class Test_Payment extends Hub
{

    function Test_Payment($_ci)
    {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null)
    {
        $this->assign_template_titlecall($template, $title_call);

        // add payment
        $data['id_user'] = '1';
        $data['id_session'] = $this->ci->session->userdata['session_id'];
        $data['p24_kwota'] = 10.30;
        $data['p24_klient'] = '';
        $data['p24_adres'] = '';
        $data['p24_kod'] = '';
        $data['p24_miasto'] = '';
        $data['p24_kraj'] = '';
        $data['p24_email'] = '';
        //Add user payment
        $url = CONSOLE_URL . '/plociuchy:payment_p24_user/add_user_ui';
        $result = $this->api_call($url, $data);
        //
        $data = array();
        $data['id_user'] = 1;
        $data['id_partner'] = 1;
        $data['id_product'] = 1;
        $data['id_payment_p24_user'] = 1;
        $data['status'] = 0;
        $data['reject'] = 0;
        $data['active'] = 1;
        $data['date_from'] = date("Y-m-d H:i:s");
        $data['date_to'] = date("Y-m-d H:i:s");
        //Add reservation
        $url = CONSOLE_URL . '/plociuchy:payment_p24_user/add_reservation_ui';
        $this->api_call($url, $data);
        // assign variables to form
        $this->ci->smarty->assign('p24_id_sprzedawcy', $result['data']['p24_id_sprzedawcy']);
        $this->ci->smarty->assign('p24_session_id', $result['data']['p24_session_id']);
        $this->ci->smarty->assign('p24_kwota', $result['data']['p24_kwota']);
        $this->ci->smarty->assign('p24_crc', $result['data']['p24_crc']);
        // display
        $this->smarty_display($template);
    }

}