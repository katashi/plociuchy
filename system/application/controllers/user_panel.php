<?php
class User_Panel extends Hub {

    function User_panel($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
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
        }
        //$this->smarty_display($template);
    }

    public function display_front($template, $title_call) {
        $this->add_message_ok('Wybierz jedną z opcji w menu');
        $this->smarty_display($template);
    }

    public function display_data($template, $title_call) {

        if (isset($_POST['submit'])) {
            unset($_POST['submit']);

            $data = $_POST;
            $url = CONSOLE_URL . '/plociuchy:user/edit/' . $this->ci->session->userdata['user_id'];
            $result = $this->api_call($url,$data);

            if($result['success']){
                $this->add_message_ok('Dane zostały zapisane.');
            }else{
                $this->add_message_error('Wystąpiłbłąd podczas zapisu.');
            }
        }

        $this->get_default_form_values();

        $this->smarty_display($template);
    }

    public function display_orders($template, $title_call) {
        $this->add_message_ok('widok zamowień');

        $url = CONSOLE_URL . '/plociuchy:/edit/' . $this->ci->session->userdata['user_id'];
        $result = $this->api_call($url);


        $this->smarty_display($template);
    }

    public function display_change_password($template, $title_call) {
//        $this->add_message_ok('widok zmiany hasla');

        if(isset($_POST['submit'])){
            echo $url = CONSOLE_URL . '/plociuchy:user/match_password/' . $this->ci->session->userdata['user_id'].','.$_POST['password'];
            $result = $this->api_call($url);

            var_dump($result);

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

}