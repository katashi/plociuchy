<?php
class Cart extends Hub {

    function Cart($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display_cart($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $date_from = '';
        $date_to = '';
        $products = array();
        $final_price = 0;
        $id_user = 0;
        //check date
        if ((isset($_POST['date_to']) && $_POST['date_to'] == '') || (isset($_POST['date_from']) && $_POST['date_from'] == '')) {
            $this->add_message_error('Nie wybrałeś daty rezerwacji.');
            $products = false;
        } else {
            // check is session cart is exist
            if (isset($this->ci->session->userdata['cart_data']) && $this->ci->session->userdata['cart_data'] != false) {
                $date_from = $this->ci->session->userdata['date_from'];
                $date_to = $this->ci->session->userdata['date_to'];
                $products = $this->ci->session->userdata['products'];
                if (isset($this->ci->session->userdata['user_authorised'])) {
                    $id_user = $this->ci->session->userdata['user_id'];
                }
            }
            //geting from post
            if (isset($_POST['add_to_cart'])) {
                $cart_data = array(
                    'cart_data' => true,
                    'date_from' => $_POST['date_from'],
                    'date_to' => $_POST['date_to'],
                    'products' => array($_POST['id_product'])
                );
                $this->ci->session->set_userdata($cart_data);
                //update values
                $date_from = $cart_data['date_from'];
                $date_to = $cart_data['date_to'];
                $products = $cart_data['products'];
            }
            if (isset($_POST['delete_cart'])) {
                $cart_data = array(
                    'cart_data' => false,
                    'date_from' => '',
                    'date_to' => '',
                    'products' => false
                );
                $this->add_message_ok('Produkt został usunięty z koszyka.');
                $this->ci->session->unset_userdata($cart_data);
                //update values
                $date_from = '';
                $date_to = '';
                $products = array();
            }
        }
        $cart_products = $products;
        if (!empty($products)) {
            $cart_products = array();
            //foreach($products as $product){
            $cart_products[] = $this->load_product($products[0]); //tylko 1 zamowienie na raz
            //}
            $numberDays = $this->cout_days($date_from, $date_to);
            $shipment_cost = $this->get_shipmentCost();
            foreach ($cart_products as $key => $product) {
                //price
                if ($numberDays == 3) {
                    $cart_products[$key]['end_price'] = $product['price1'];
                } else {
                    $cart_products[$key]['end_price'] = $product['price2'];
                }
                $final_price = $shipment_cost + $cart_products[$key]['end_price'];
                //vendor
                $cart_products[$key]['vendor'] = $this->load_vendor($product['id_vendor']);
            }
            $this->ci->smarty->assign('shipment_cost', $shipment_cost);
            $this->ci->smarty->assign('id_user', $id_user);
            $this->ci->smarty->assign('final_price', $final_price);
            $this->ci->smarty->assign('date_from', $date_from);
            $this->ci->smarty->assign('date_to', $date_to);
            $this->ci->smarty->assign('days', $numberDays);
        }
        $this->ci->smarty->assign('products', $cart_products);
        $this->smarty_display($template);
    }

    function display_cart_summary($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $id_product = '';
        $final_price = 0;
        if (!isset($this->ci->session->userdata['user_authorised']) || $this->ci->session->userdata['user_authorised'] != true) {
            //logowanie
            $login = new User($this->ci);
            $this->add_message_error('Zaloguj się aby dokonać rezerwacji');
            $login->display_login('user_login');
            die();
        }
        $user = $this->load_user($this->ci->session->userdata['user_id']);
        if ($user['address'] == '' || $user['zip'] == '' || $user['city'] == '') {
            //walidacja adresu
            $user_panel = new User_Panel($this->ci);
            $user_panel->display_data('user_panel_data', null);
            die();
        }
        if ($user['shipment'] == 1) {
            if ($user['shipment_address'] == '' || $user['shipment_zip'] == '' || $user['shipment_city'] == '') {
                //walidacja adresu
                $user_panel = new User_Panel($this->ci);
                $user_panel->display_data('user_panel_data', null);
                die();
            }
        }
        //geting from post
        if (isset($_POST['cart_summary'])) {
            //update values
            $id_user = $this->ci->session->userdata['user_id'];
            $date_from = $_POST['date_from'];
            $date_to = $_POST['date_to'];
            $id_product = $_POST['id_product'];
        }
        $cart_product = $this->load_product($id_product);
        $numberDays = $this->cout_days($date_from, $date_to);
        $shipment_cost = $this->get_shipmentCost();
        $reserved_days = $this->load_reserved_product_days($id_product);
        $collide = false;
        //check dates
        foreach ($reserved_days as $key => $day) {
            if (date('Y-m-d', strtotime($date_from)) >= date('Y-m-d', $day['real_date_from']) && date('Y-m-d', strtotime($date_from)) <= date('Y-m-d', $day['real_date_to'])) {
                $collide = true;
//                echo 'Data = ' . $date_from . ' Jest pomiedzy datami (' . date('Y-m-d', $day['real_date_from']) . '/' . date('Y-m-d', $day['real_date_to']) . ')';
            }
            if (date('Y-m-d', strtotime($date_to)) >= date('Y-m-d', $day['real_date_from']) && date('Y-m-d', strtotime($date_to)) <= date('Y-m-d', $day['real_date_to'])) {
                $collide = true;
//                echo 'Data = ' . $date_to . ' Jest pomiedzy datami (' . date('Y-m-d', $day['real_date_from']) . '/' . date('Y-m-d', $day['real_date_to']) . ')';
            }
        }
        if ($collide == false) {
            if ($numberDays == 3) {
                $cart_product['end_price'] = $cart_product['price1'];
            } else {
                $cart_product['end_price'] = $cart_product['price2'];
            }
            $final_price = $shipment_cost + $cart_product['end_price'];
            //vendor
            $cart_product['vendor'] = $this->load_vendor($cart_product['id_vendor']);

            $data = array();
            $data['final_price'] = $final_price;
            $data['date_from'] = $date_from;
            $data['date_to'] = $date_to;
            $data['product'] = $cart_product;

            //dodajemy logike platnosci
            $this->payment24($data);

            $this->ci->smarty->assign('final_price', $final_price);
            $this->ci->smarty->assign('date_from', $date_from);
            $this->ci->smarty->assign('date_to', $date_to);
            $this->ci->smarty->assign('days', $numberDays);

        }
        $this->ci->smarty->assign('product', $cart_product);
        $this->ci->smarty->assign('collide', $collide);
        $template = 'cart_summary';
        $this->smarty_display($template);
    }

    function display_platnosci24_ok($template = null, $title_call = null) {

        $data = $_POST;
        //Add user payment
        $url = CONSOLE_URL . '/plociuchy:payment_p24_user/payment_p24_ok_ui';
        $result = $this->api_call($url, $data);

        $datae = array();
        $array_prod = $this->ci->session->userdata['products'];
        $datae['product'] = $this->load_product($array_prod['0']);
        $user = $this->ci->session->userdata['user_id'];
        $datae['user'] = $this->load_user($user);
        $datae['partner'] = $this->load_partner($datae['product']['id_partner']);
        //wysyłanie maila do usera
        $this->send_user_email_confirm($datae);
        //wysyłanie maila do partnera
        $this->send_partner_email_confirm($datae);
        if ($result['success'] == 'true') {
            //delete product from cart
            $cart_data = array(
                'cart_data' => false,
                'date_from' => '',
                'date_to' => '',
                'products' => false
            );
            $this->ci->session->unset_userdata($cart_data);

            $this->add_message_ok('Dziekujemy za wpłatę. Twoja rezerwacja została przeprowadzona prawidłowo.');
        } else {
            $this->add_message_error('Wystąpił błąd podaczas wpłaty.Proszę spróbować jeszcze raz.');
        }
        $template = 'cart_payment24';
        $this->smarty_display($template);
    }

    function display_platnosci24_error($template = null, $title_call = null) {
        $data = $_POST;
        //Add user payment
        $url = CONSOLE_URL . '/plociuchy:payment_p24_user/payment_p24_error_ui';
        $result = $this->api_call($url, $data);
        $this->add_message_error('Wystąpił błąd podaczas wpłaty.Proszę spróbować jeszcze raz.');
        $template = 'cart_payment24';
        $this->smarty_display($template);
    }

    public function get_shipmentCost() {
        return 20;
    }

    public function send_user_email_confirm($data) {
        // lets define data
        $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_site', SITE_URL);

        $this->ci->smarty->assign('user', $data['user']);
        $this->ci->smarty->assign('partner', $data['partner']);

        $message = $this->ci->smarty->fetch('../../../templates/email/' . CONFIGURATION . '/cart_user_confirm.html');
        //
        $config['protocol'] = 'sendmail';
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        //
        $this->ci->email->initialize($config);
        $this->ci->email->subject('Plociuchy - Potwierdzenie zamówienia');
        $this->ci->email->from('kontakt@plo-ciuchy.pl');
        $this->ci->email->to($data['user']['user']);
        $this->ci->email->message($message);
        $this->ci->email->send();
    }

    public function send_partner_email_confirm($data) {
        // lets define data
        $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_site', SITE_URL);

        $this->ci->smarty->assign('user', $data['user']);
        $this->ci->smarty->assign('partner', $data['partner']);

        $message = $this->ci->smarty->fetch('../../../templates/email/' . CONFIGURATION . '/cart_partner_confirm.html');
        //
        $config['protocol'] = 'sendmail';
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        //
        $this->ci->email->initialize($config);
        $this->ci->email->subject('Plo-ciuchy.pl - Potwierdzenie zamówienia');
        $this->ci->email->from('kontakt@plo-ciuchy.pl');
        $this->ci->email->to($data['partner']['user']);
        $this->ci->email->message($message);
        $this->ci->email->send();
    }

    public function cout_days($date_from, $date_to) {
        //count days
        $cdate_from = strtotime($date_from);
        $cdate_to = strtotime($date_to);
        $timeDiff = abs($cdate_from - $cdate_to);
        $numberDays = $timeDiff / 86400;
        $numberDays = intval($numberDays) + 1;

        return $numberDays;
    }

    public function payment24($data_s) {
        // add reservation
        // add payment
        $user = $this->load_user($this->ci->session->userdata['user_id']);

        $data['id_user'] = $this->ci->session->userdata['user_id'];
        $data['id_session'] = $this->ci->session->userdata['session_id'];
        $data['p24_kwota'] = $data_s['final_price'];
        $data['p24_klient'] = $user['name'] . ' ' . $user['surname'];
        $data['p24_adres'] = $user['address'];
        $data['p24_kod'] = $user['zip'];
        $data['p24_miasto'] = $user['city'];
        $data['p24_kraj'] = $user['country'];
        $data['p24_email'] = $user['user'];

        foreach ($data as $key => $val) {
            $this->ci->smarty->assign($key, $val);
        }
        //Add user payment
        $url = CONSOLE_URL . '/plociuchy:payment_p24_user/add_user_ui';
        $result = $this->api_call($url, $data);
        $data = array();
        $data['id_user'] = $this->ci->session->userdata['user_id'];
        $data['id_partner'] = $data_s['product']['id_partner'];
        $data['id_product'] = $data_s['product']['id'];
        $data['id_payment_p24_user'] = $result['data']['id'];
        $data['status'] = 0;
        $data['reject'] = 0;
        $data['active'] = 0; //0-nowe 1-zatwierdzone
        $data['date_from'] = date("Y-m-d H:i:s", strtotime($data_s['date_from']));
        $data['date_to'] = date("Y-m-d H:i:s", strtotime($data_s['date_to']));
        //Add reservation
        $url = CONSOLE_URL . '/plociuchy:payment_p24_user/add_reservation_ui';
        $this->api_call($url, $data);

        // assign variables to form
        $this->ci->smarty->assign('user', $user);
        $this->ci->smarty->assign('p24_id_sprzedawcy', $result['data']['p24_id_sprzedawcy']);
        $this->ci->smarty->assign('p24_session_id', $result['data']['p24_session_id']);
        $this->ci->smarty->assign('p24_kwota', $result['data']['p24_kwota']);
        $this->ci->smarty->assign('p24_crc', $result['data']['p24_crc']);

    }

    public function load_product($id_product) {
        $url = CONSOLE_URL . '/plociuchy:product/load/' . $id_product;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_vendor($id_vendor) {
        $url = CONSOLE_URL . '/plociuchy:product_dict_vendor/load/' . $id_vendor;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_user($id_user) {
        $url = CONSOLE_URL . '/plociuchy:user/load/' . $id_user;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_partner($id_partner) {
        $url = CONSOLE_URL . '/plociuchy:partner/load/' . $id_partner;
        $result = $this->api_call($url);
        return $result['data'];
    }

    function load_reserved_product_days($id_product) {
        $url = CONSOLE_URL . '/plociuchy:product_reservation/load_reserved_product_days/' . $id_product;
        $result = $this->api_call($url);
        //add +/- 2 dni
        foreach ($result['data'] as $key => $val) {
            $result['data'][$key]['real_date_from'] = strtotime($val['date_from'] . '-2days');
            $result['data'][$key]['real_date_to'] = strtotime($val['date_to'] . '+2days');
        }
        return $result['data'];
    }

}