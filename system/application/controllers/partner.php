<?php
class Partner extends Hub {

    function Partner($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null) {

        //$this->smarty_display($template);
    }

    function display_login($template = null, $title_call = null) {
        $template = 'partner_login';
        $this->assign_template_titlecall($template, $title_call);

        //sprawdzmy czy partner zalogowany jesli tak przekierowanie na główną
        if (isset($this->ci->session->userdata['partner_authorised'])) {
            $this->add_message_ok('Partner jest juz Zalogowany.');
            // lub redirect
        }elseif(isset($_POST['cart_summary'])){
            $this->add_message_error('Zaloguj się jako zwykły użytkownik aby potwierdzić rezerwację.');
        }else{
            if (isset($_POST['login_submit'])) {
                unset($_POST['login_submit']);
                $url = CONSOLE_URL . '/plociuchy:partner/login_ui';
                $data = $_POST;
                $result = $this->api_call($url, $data);
                if ($result['code'] == 'ok') {
                    $record = $result['client'];
                    $partner = array(
                        'partner_authorised' => true,
                        'partner_id' => $record['id'],
                        'partner_name' => $record['name'],
                        'partner_surname' => $record['surname'],
                        'partner_user' => $record['user'],
                        //'user_logged' => now(),
                    );
                    $this->ci->session->set_userdata($partner);
                    //display message success
                    $this->add_message_ok('Partner Zalogowany.');
                }else{
                    $this->add_message_error('Nazwa partnera lub hasło są nieprawidłowe.');
                }
            }
        }

        $this->smarty_display($template);
    }

    function display_logout($template = null, $title_call = null) {
        $this->ci->session->unset_userdata('partner_authorised');
        $this->ci->session->unset_userdata('partner_id');
        $this->ci->session->unset_userdata('partner_name');
        $this->ci->session->unset_userdata('partner_surname');
        // force to logout swap view after first good attempt
        $this->ci->smarty->assign('partner_status', 0);
        // display message logout
        $this->add_message_ok('Partner został poprawnie wylogowany');
        //display
        $template = 'partner_logout';
        $this->smarty_display($template);
    }

    //rejestracja
    function display_registration($template = null, $title_call = null, $val = null) {
        $template = 'partner_registration';
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['registration_submit'])) {
            $url = CONSOLE_URL . '/plociuchy:partner/add_ui';
            $data = $_POST;
            $result = $this->api_call($url, $data);
            if ($result['code'] == 'partner_exist') {
                $result['code'] = 'Użytkownik istnieje';
            }
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'partner_add');

            //if ok set confirm template
            if ($result['success'] == 1) {
                $template = 'partner_registration_success';
            }
        }

        $this->smarty_display($template);
    }

    // reset
    function display_password_reset($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['user'])) {
            $url = CONSOLE_URL . '/plociuchy:partner/password_reset_ui/' . $_POST['user'];
            $result = $this->api_call($url);
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'password_reset');
            $this->add_message_ok('Wysłano wiadomość z linkiem resetującym hasło.Sprawdź pocze');
        } else {
            $this->ci->smarty->assign('result', 0);
            $this->ci->smarty->assign('code', 'partner_missing');
            $this->ci->smarty->assign('operation', 'password_reset');
            $this->add_message_error('Nieznaleziono podanego adresu email.');
        }
        //display
        $template = 'partner_password_reminder';
        $this->smarty_display($template);
//        $home = new Home($this->ci);
//        $home->display('home');
    }

    function password_reset_confirm($template = null, $title_call = null, $password_hash = null) {
        $url = CONSOLE_URL . '/plociuchy:partner/password_reset_confirm_ui/' . $password_hash;
        $result = $this->api_call($url);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'password_reset');
        //if ok
        if ($result['code'] == 'password_changed') {
            $this->add_message_ok('Hasło zostało zresetowane.<br/> Nowe Hasło zostało wysłane na twój adres email.');
            $this->ci->smarty->assign('reset_ok', true);
        }
        // display
        $template = 'partner_password_reminder';

        $this->smarty_display($template);
    }

    //weryfikacja partnera
    function display_verification($template = null, $title_call = null, $password_hash = null) {
        $this->assign_template_titlecall($template, $title_call);
        $url = CONSOLE_URL . '/plociuchy:partner/activate/' . $password_hash;
        $result = $this->api_call($url);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'activate');
        $template = 'partner_veryfication';
        $this->smarty_display($template);
    }


}