<?php
class User_Panel extends Hub {

    function User_Panel($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
        //
        //auth
        if (!isset($this->ci->session->userdata['user_authorised']) || $this->ci->session->userdata['user_authorised'] != true){
            //logowanie
          $login = new User($this->ci);
          $login->display_login('user_login');
          die();
        }

    }

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);

        switch ($template) {
            case 'user_panel_front':
                $this->display_front($template, $title_call);
                exit;
            case 'user_panel_data':
                $this->display_data($template, $title_call);
                exit;
            case 'user_panel_orders':
                $this->display_orders($template, $title_call);
                exit;
            case 'user_panel_change_password':
                $this->display_change_password($template, $title_call);
                exit;
            default;
                $template = 'user_panel_front';
                break;
        }
        $this->smarty_display($template);
    }

    public function display_front($template, $title_call) {
        $template = 'user_panel_front';
        $this->add_message_ok('Wybierz jedną z opcji w menu');
        $this->smarty_display($template);
    }

    public function display_data($template, $title_call) {
        $template = 'user_panel_data';
        if (isset($_POST['submit'])) {
            unset($_POST['submit']);

            $data = $_POST;
            $url = CONSOLE_URL . '/plociuchy:user/edit/' . $this->ci->session->userdata['user_id'];
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

    public function display_orders($template, $title_call) {
        //var
        $template = 'user_panel_orders';
        $active_reservation = array();

        //get user active reservation
        $status = 1; // active
        $url = CONSOLE_URL . '/plociuchy:product_reservation/load_all_user_product_reservation/' . $this->ci->session->userdata['user_id'].','.$status;
        $result = $this->api_call($url);
        if($result['data']){
            $active_reservation = $result['data'];
            foreach ($active_reservation as $key => $val){
                $active_reservation[$key]['product'] = $this->load_product($val['id_product']);
            }
            foreach ($active_reservation as $key => $val){
                $active_reservation[$key]['partner'] = $this->load_partner($val['id_partner']);
            }
        }
        //get history active reservation
        $status = 0; // history
        $url = CONSOLE_URL . '/plociuchy:product_reservation/load_all_user_product_reservation/' . $this->ci->session->userdata['user_id'].','.$status;
        $result = $this->api_call($url);
        if($result['data']){
            $history_reservation = $result['data'];
            foreach ($history_reservation as $key => $val){
                $history_reservation[$key]['product'] = $this->load_product($val['id_product']);
            }
            foreach ($history_reservation as $key => $val){
                $history_reservation[$key]['partner'] = $this->load_partner($val['id_partner']);
            }

        }
        //smarty
        $this->ci->smarty->assign('active_reservations', $active_reservation);
        $this->ci->smarty->assign('history_reservations', $history_reservation);

        $this->smarty_display($template);
    }

    public function display_change_password($template, $title_call) {
        $template = 'user_panel_change_password';
//        $this->add_message_ok('widok zmiany hasla');
        if (isset($_POST['submit'])) {
            $url = CONSOLE_URL . '/plociuchy:user/match_password/' . $this->ci->session->userdata['user_id'] . ',' . $_POST['password'];
            $result = $this->api_call($url);
            if ($result['code'] == 'match_ok'){
                if($_POST['new_password'] == $_POST['password']){
                    $this->add_message_error('Stare i nowe hasło jest identyczne.');
                }else if($_POST['new_password'] == $_POST['new_password2']){
                    $data = array();
                    $data['password'] = $_POST['new_password'];
                    $data['user'] = $this->ci->session->userdata['user_user'];
                    $url = CONSOLE_URL . '/plociuchy:user/password_reset_user_ui/';
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

    public function get_default_form_values() {
        $id_user = $this->ci->session->userdata['user_id'];
        //load values to form
        $url = CONSOLE_URL . '/plociuchy:user/load/' . $this->ci->session->userdata['user_id'];
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