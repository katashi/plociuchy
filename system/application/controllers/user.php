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
        }
        $this->smarty_display($template);
    }

    function display_login($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);

        //sprawdzmy czy user zalogowany jesli tak przekierowanie na główną
        if (isset($this->ci->session->userdata['user_authorised'])) {
            $this->ci->smarty->assign('logged', 'true');
        } else {
            if (isset($_POST['login_submit'])) {
                unset($_POST['login_submit']);
                $url = CONSOLE_URL . '/plociuchy:user/login_ui';
                $data = $_POST;
                $result = $this->api_call($url, $data);
                if ($result['code'] == 'ok' ) {
                    $record = $result['client'];
                    $user = array(
                        'user_authorised' => true,
                        'user_id' => $record['id'],
                        'user_name' => $record['name'],
                        'user_surname' => $record['surname'],
                        'user_user' => $record['user'],
//                        'user_logged' => now(),
                    );
                    $this->ci->session->set_userdata($user);
                }
            }
        }
        $this->smarty_display($template);
    }

    function logout_user($template = null, $title_call = null) {
        $this->ci->session->unset_userdata('user_authorised');
        $this->ci->session->unset_userdata('user_id');
        $this->ci->session->unset_userdata('user_name');
        $this->ci->session->unset_userdata('user_surname');
        // force to logout swap view after first good attempt
        $this->ci->smarty->assign('user_status', 0);
    }

    //rejestracja
    function display_registration($template = null, $title_call = null, $val = null) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['registration_submit'])) {
            $url = CONSOLE_URL . '/plociuchy:user/add_ui';
            $data = $_POST;
            $result = $this->api_call($url, $data);
            if ($result['code'] == 'user_exist') {
                $result['code'] = 'Użytkownik istnieje';
            }
            var_dump($result);
            $this->ci->smarty->assign('result', $result['success']);
            $this->ci->smarty->assign('code', $result['code']);
            $this->ci->smarty->assign('operation', 'user_add');

            //if ok set confirm template
            if ($result['success'] == 1) {
                $template = 'user_registration_success';
            }
        }
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