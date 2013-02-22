<?php
class Partner_Panel extends Hub {

    function Partner_Panel($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
        //
        //auth
        if (!isset($this->ci->session->userdata['partner_authorised']) || $this->ci->session->userdata['partner_authorised'] != true){
            //logowanie
          $login = new Partner($this->ci);
          $login->display_login('partner_login');
          die();
        }

    }

    // display default
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->smarty_display($template);
    }

    public function display_front($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('Wybierz jedną z opcji w menu');
        $this->smarty_display($template);
    }
    public function display_data($template, $title_call) {
        $template = 'partner_panel_data';
        if (isset($_POST['submit'])) {
            unset($_POST['submit']);

            $data = $_POST;
            $url = CONSOLE_URL . '/plociuchy:partner/edit/' . $this->ci->session->userdata['partner_id'];
            $result = $this->api_call($url, $data);

            if ($result['success']) {
                $this->add_message_ok('Dane zostały zapisane.');
            } else {
                $this->add_message_error('Wystąpiłbłąd podczas zapisu.');
            }
        }

        $this->get_default_form_values();

        $this->smarty_display($template);
    }
    public function display_change_password($template, $title_call) {
        $template = 'partner_panel_change_password';
//        $this->add_message_ok('widok zmiany hasla');
        if (isset($_POST['submit'])) {
            $url = CONSOLE_URL . '/plociuchy:partner/match_password/' . $this->ci->session->userdata['partner_id'] . ',' . $_POST['password'];
            $result = $this->api_call($url);
            if ($result['code'] == 'match_ok'){
                if($_POST['new_password'] == $_POST['password']){
                    $this->add_message_error('Stare i nowe hasło jest identyczne.');
                }else if($_POST['new_password'] == $_POST['new_password2']){
                    $data = array();
                    $data['password'] = $_POST['new_password'];
                    $data['user'] = $this->ci->session->userdata['partner_user'];
                    print_R($data);
                    echo $url = CONSOLE_URL . '/plociuchy:partner/password_reset_partner_ui/';
                    $result = $this->api_call($url,$data);
                    if($result['success'] == true){
                        $this->add_message_ok('Hasło zostało zmienione');
                    }else{
                        $this->add_message_error('Wystąpił błąd podaczas aktualizacji hasła');
                    }
                }
            }else{
                $this->add_message_error('"Stare hasło" jest nieporawidłowe.');
            }
        }
        $this->smarty_display($template);
    }
    public function display_add_product($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);

        $template = 'partner_panel_front';
        $this->add_message_ok('widok dodawania produktu');
        $this->smarty_display($template);
    }
    public function display_products($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);

        $template = 'partner_panel_front';
        $this->add_message_ok('widok produktów');
        $this->smarty_display($template);
    }
    public function display_rejected_products($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);

        $template = 'partner_panel_front';
        $this->add_message_ok('widok odrzuconych produktow');
        $this->smarty_display($template);
    }
    public function display_reservations($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok(' rezerwacje');
        $this->smarty_display($template);
    }
    public function display_reservations_actual($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('aktualne rezerwacje');
        $this->smarty_display($template);
    }
    public function display_reservation_history($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('Wybierz jedną z opcji w menu');
        $this->smarty_display($template);
    }
    public function display_pauyment_balance($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('historia rezerwacji');
        $this->smarty_display($template);
    }
    public function display_payment_balance($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('platnosci bilans');
        $this->smarty_display($template);
    }
    public function display_payment_history($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('historia platnosci');
        $this->smarty_display($template);
    }



    public function get_default_form_values() {
        $id_partner = $this->ci->session->userdata['partner_id'];
        //load values to form
        $url = CONSOLE_URL . '/plociuchy:partner/load/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url);

        if ($result['success']) {
            foreach ($result['data'] as $field => $val) {
                $this->ci->smarty->assign($field, (isset($_POST[$field]) ? $_POST[$field] : $val));
            }
        }
    }

    public function load_product($id_product){
        $url = CONSOLE_URL . '/plociuchy:product/load/' . $id_product;
        $result = $this->api_call($url);
        return $result['data'];
    }
    public function load_partner($id_partner){
        $url = CONSOLE_URL . '/plociuchy:partner/load/' . $id_partner;
        $result = $this->api_call($url);
        return $result['data'];
    }
}