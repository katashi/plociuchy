<?php
class User extends Hub {

    function User($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);

        switch ($template) {
            case 'user_registration':
                $this->display_registration($template, $title_call);
                exit;
            case 'user_login';
                $this->display_login($template, $title_call);
                exit;
            case 'user_password_reminder';
                $this->display_password_reset($template, $title_call);
                exit;
            case 'user_logout';
                $this->display_logout($template, $title_call);
                exit;
        }
        //$this->smarty_display($template);
    }

    function display_login($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);

        //sprawdzmy czy user zalogowany jesli tak przekierowanie na główną
        if (isset($this->ci->session->userdata['user_authorised'])) {
            $this->add_message_ok('Użytkownik jest już zalogowany.');
            // lub redirect
        }elseif(isset($_POST['cart_summary'])){
            $this->add_message_error('Zaloguj się aby potwierdzić rezerwację.');
        }else{
            if (isset($_POST['login_submit'])) {
                unset($_POST['login_submit']);
                $url = CONSOLE_URL . '/plociuchy:user/login_ui';
                $data = $_POST;
                $result = $this->api_call($url, $data);
                if ($result['code'] == 'ok') {
                    $record = $result['client'];
                    $user = array(
                        'user_authorised' => true,
                        'user_id' => $record['id'],
                        'user_name' => $record['name'],
                        'user_surname' => $record['surname'],
                        'user_user' => $record['user'],
                        //'user_logged' => now(),
                    );
                    $this->ci->session->set_userdata($user);
                    //display message success
                    $this->add_message_ok('Użytkownik zalogowany.');
                }else{
                    if($result['code'] == 'unactive'){
                    $this->add_message_error('Przepraszamy konto jest nieaktywne lub zostało zablokowane.');
                    }else{
                    $this->add_message_error('Nazwa użytkownika lub hasło są nieprawidłowe.');
                    }
                }
            }
        }
        $this->smarty_display($template);
    }

    function display_logout($template = null, $title_call = null) {
        $this->ci->session->unset_userdata('user_authorised');
        $this->ci->session->unset_userdata('user_id');
        $this->ci->session->unset_userdata('user_name');
        $this->ci->session->unset_userdata('user_surname');
        // force to logout swap view after first good attempt
        $this->ci->smarty->assign('user_status', 0);
        // display message logout
        $this->add_message_ok('Użytkownik został poprawnie wylogowany');

        //display
        $template = 'user_logout';
        $this->smarty_display($template);
    }

    //rejestracja
    function display_registration($template = null, $title_call = null, $val = null) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['registration_submit'])) {
            $url = CONSOLE_URL . '/plociuchy:user/add_ui';
            $data = $_POST;
            $result = $this->api_call($url, $data);
            if ($result['code'] == 'user_exist') {
                $result['code'] = 'Użytkownik o podanym adresie e-mail jest już zarejestrowany w bazie.';
            }
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'user_add');
            $this->add_message_error($result['code']);
            //if ok set confirm template
            if ($result['success'] == 1) {
                $template = 'user_registration_success';
            }
        }
        $this->smarty_display($template);
    }

    // reset
    function display_password_reset($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['user'])) {
            $url = CONSOLE_URL . '/plociuchy:user/password_reset_ui/' . $_POST['user'];
            $result = $this->api_call($url);
            if($result['code'] == 'user_missing') {
                $this->ci->smarty->assign('result', 0);
                $this->ci->smarty->assign('code', 'user_missing');
                $this->ci->smarty->assign('operation', 'password_reset');
                $this->add_message_error('Nieznaleziono podanego adresu email.');
            }else{
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'password_reset');
            $this->add_message_ok('Wysłano wiadomość z linkiem resetującym hasło. Sprawdź pocztę.');
            }
        }
        //display
        $this->smarty_display($template);
//        $home = new Home($this->ci);
//        $home->display('home');
    }

    function password_reset_confirm($template = null, $title_call = null, $password_hash = null) {
        $url = CONSOLE_URL . '/plociuchy:user/password_reset_confirm_ui/' . $password_hash;
        $result = $this->api_call($url);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'password_reset');
        //if ok
        if ($result['code'] == 'password_changed') {
            $this->add_message_ok('Hasło zostało zresetowane.<br/> Nowe hasło zostało wysłane na twój adres email.');
            $this->ci->smarty->assign('reset_ok', true);
        }
        // display
        $template = 'user_login';

        $this->smarty_display($template);
    }

    //weryfikacja usera
    function display_verification($template = null, $title_call = null, $password_hash = null) {
        $this->assign_template_titlecall($template, $title_call);
        $url = CONSOLE_URL . '/plociuchy:user/activate/' . $password_hash;
        $result = $this->api_call($url);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'activate');
        $template = 'user_veryfication';
        $this->smarty_display($template);
    }


}