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


    //moje produkty
    public function display_add_product($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);

        $template = 'partner_panel_front';
        $this->add_message_ok('widok dodawania produktu');
        $this->smarty_display($template);
    }

    public function display_products($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);

        //pobieramy wszytskie produkty
        $products = $this->load_all_active_products();
        if(empty($products)){
            $this->add_message_ok('Aktualnie nie posiadasz żadnych produktów. Wystaw produkt.');
        }else{

            foreach ($products as $key =>  $val){
                $products[$key]['category'] = $this->load_category($val['id_category']);
                $products[$key]['left_days'] = $this->count_Days($val['active_to']);
            }

        }
        $this->ci->smarty->assign('title_page','Spis Produktów');
        $this->ci->smarty->assign('user_products' , $products);
        $template = 'partner_panel_products_list';
        $this->smarty_display($template);
    }
    public function display_rejected_products($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        //pobieramy wszytskie produkty
        $products = $this->load_all_rejected_products();
        if(empty($products)){
            $this->add_message_ok('Spis jest pusty co oznacza, że żaden z Twoich produktów nie został odrzucony.');
        }else
        {
            foreach ($products as $key =>  $val){
                $products[$key]['category'] = $this->load_category($val['id_category']);
                $products[$key]['left_days'] = $this->count_Days($val['active_to']);
                $products[$key]['rejected_reson'] = $this->load_product_rejected_comment($val['id']);
            }
        }
        $this->ci->smarty->assign('title_page','Spis Odrzuconych Produktów');
        $this->ci->smarty->assign('user_products' , $products);
        $template = 'partner_panel_products_rejected_list';
        $this->smarty_display($template);
    }

    public function display_reservations($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        //pobieramy wszytskie rezerwacje status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
        $reservations= $this->load_all_partner_reservations($id_status = 1);

        if(empty($reservations)){
            $this->add_message_ok('Spis przedstawia aktualne zapłacone rezerwacje które nie zostały jeszcze wysłane. Kliknij "wysłane" aby zmienić status produktu.');
        }else{
            foreach ($reservations as $key => $val){
                $reservations[$key]['product'] = $this->load_product($val['id_product']);
                $reservations[$key]['user'] = $this->load_user($val['id_user']);
            }
        }
        $this->ci->smarty->assign('reservations' , $reservations);
        $template = 'partner_panel_reservations_1';
        $this->smarty_display($template);
    }
    public function display_reservations_actual($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        //pobieramy wszytskie rezerwacje status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
        $reservations= $this->load_all_partner_reservations($id_status = 2);

        if(empty($reservations)){
            $this->add_message_ok('Spis przedstawia aktualne zapłacone rezerwacje które nie zostały jeszcze wysłane. Kliknij "wysłane" aby zmienić status produktu.');
        }else{
            foreach ($reservations as $key => $val){
                $reservations[$key]['product'] = $this->load_product($val['id_product']);
                $reservations[$key]['user'] = $this->load_user($val['id_user']);
            }


        }
        $this->ci->smarty->assign('reservations' , $reservations);
        $template = 'partner_panel_reservations_2';
        $this->smarty_display($template);
    }
    public function display_reservation_history($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        //pobieramy wszytskie rezerwacje status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
        $reservations= $this->load_all_partner_reservations($id_status = 3);
        if(empty($reservations)){
            $this->add_message_ok('Spis przedstawia aktualne zapłacone rezerwacje które nie zostały jeszcze wysłane. Kliknij "wysłane" aby zmienić status produktu.');
        }else{
            foreach ($reservations as $key => $val){
                $reservations[$key]['product'] = $this->load_product($val['id_product']);
                $reservations[$key]['user'] = $this->load_user($val['id_user']);
            }
        }
        $this->ci->smarty->assign('reservations' , $reservations);
        $template = 'partner_panel_reservations_3';
        $this->smarty_display($template);
    }

    //płatności
    public function display_payment_balance($template, $title_call) {
        $template = 'partner_panel_balance';
        $this->add_message_ok('platnosci bilans');
        $this->smarty_display($template);
    }

    public function display_payment_balance_add($template, $title_call) {
        $opt = null;
        if(isset($_POST['cart_income_3'])){
            $opt = 3;
        }elseif(isset($_POST['cart_income_2'])){
            $opt = 2;
        }elseif(isset($_POST['cart_income_1'])){
            $opt = 1;
        }

        if(isset($opt)){

            $this->add_message_ok('Wyśwetlony zostanie formularz platnosci24<br/>');

        }

        $template = 'partner_panel_front';
        $this->add_message_ok('platnosci bilans dodawanie pakietu do konta '.$opt);
        $this->smarty_display($template);
    }

    public function display_payment_history($template, $title_call) {
        $template = 'partner_panel_front';
        $this->add_message_ok('historia platnosci');
        $this->smarty_display($template);
    }

    public function get_default_form_values() {
        //load values to form
        $url = CONSOLE_URL . '/plociuchy:partner/load/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url);
        if ($result['success']) {
            foreach ($result['data'] as $field => $val) {
                $this->ci->smarty->assign($field, (isset($_POST[$field]) ? $_POST[$field] : $val));
            }
        }
    }

    public function count_Days($active_to){
        $startTimeStamp = strtotime(date("m.d.y"));
        $endTimeStamp = strtotime($active_to);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;  // 86400 seconds in one day\
        $numberDays = intval($numberDays);
        if($startTimeStamp>$endTimeStamp){
            return '-'.$numberDays;
        }
        return $numberDays;
    }

    public function load_product($id_product){
        $url = CONSOLE_URL . '/plociuchy:product/load/' . $id_product;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_all_active_products(){
        $data = array();
        $data['active_to'] = true;
        $data['rejected'] = 0;
        $url = CONSOLE_URL . '/plociuchy:product/load_all_user_product_ui/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url,$data);
        return $result['data'];
    }

    public function load_all_rejected_products(){
        $data = array();
        $data['rejected'] = -1;
        $url = CONSOLE_URL . '/plociuchy:product/load_all_user_product_ui/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url,$data);
        return $result['data'];
    }
    public function load_partner($id_partner){
        $url = CONSOLE_URL . '/plociuchy:partner/load/' . $id_partner;
        $result = $this->api_call($url);
        return $result['data'];
    }
    public function load_category($id_category){
        $url = CONSOLE_URL . '/plociuchy:product_dict_category/load/' . $id_category;
        $result = $this->api_call($url);
        return $result['data'];
    }
    public function load_user($id_user){
        $url = CONSOLE_URL . '/plociuchy:user/load/' . $id_user;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_product_rejected_comment($id_product){
        $url = CONSOLE_URL . '/plociuchy:product_comment/load/' . $id_product .',id_product';
        $result = $this->api_call($url);
        return $result['data'];
    }

    /*
      status:0)  = nowa rezerwacja
      status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
      status:2) aktualnie produkty w obiegu, oplacone i wyslane
      status:3) zakonczone rezerwacje
    */
    public function load_all_partner_reservations($status = 1){
        $url = CONSOLE_URL . '/plociuchy:product_reservation/load_all_partner_products_reservation/' . $this->ci->session->userdata['partner_id'] .','.$status;
        $result = $this->api_call($url);
        return $result['data'];
    }

}