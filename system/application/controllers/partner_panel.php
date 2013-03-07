<?php
class Partner_Panel extends Hub {

    function Partner_Panel($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
        //auth
        if (!isset($this->ci->session->userdata['partner_authorised']) || $this->ci->session->userdata['partner_authorised'] != true) {
            //logowanie
            $login = new Partner($this->ci);
            $login->display_login('partner_login');
            die();
        }
    }

    // display default
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        $template = 'partner_panel_front';
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
            $data['shipment'] = (isset($_POST['shipment']) ? 0 : 1);
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
            if ($result['code'] == 'match_ok') {
                if ($_POST['new_password'] == $_POST['password']) {
                    $this->add_message_error('Stare i nowe hasło jest identyczne.');
                } else if ($_POST['new_password'] == $_POST['new_password2']) {
                    $data = array();
                    $data['password'] = $_POST['new_password'];
                    $data['user'] = $this->ci->session->userdata['partner_user'];
                    $url = CONSOLE_URL . '/plociuchy:partner/password_reset_partner_ui/';
                    $result = $this->api_call($url, $data);
                    if ($result['success'] == true) {
                        $this->add_message_ok('Hasło zostało zmienione');
                    } else {
                        $this->add_message_error('Wystąpił błąd podaczas aktualizacji hasła');
                    }
                }
            } else {
                $this->add_message_error('"Stare hasło" jest nieporawidłowe.');
            }
        }
        $this->smarty_display($template);
    }

    //moje produkty
    public function display_add_product($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['submit_product'])) {
            unset($_POST['submit_product']);

            if(isset($_POST['vendor_propo']) && $_POST['vendor_propo'] != ''){
                $partner = $this->load_partner($this->ci->session->userdata['partner_id']);
                // lets define data
                $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
                $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
                $this->ci->smarty->assign('path_site', SITE_URL);

                $this->ci->smarty->assign('partner',  $partner);
                $this->ci->smarty->assign('propozycja', $_POST['vendor_propo']);

                $message = $this->ci->smarty->fetch('../../../templates/email/' . CONFIGURATION . '/partner_propo.html');
                //
                $config['protocol'] = 'sendmail';
                $config['mailtype'] = 'html';
                $config['charset'] = 'utf-8';
                //wysylamuy maila na maila admina
                $partner2 = $this->load_partner(1);
                $this->ci->email->initialize($config);
                $this->ci->email->subject('Plociuchy - Propozycja Producenta');
                $this->ci->email->from('rejestracja@plociuchy.pl');
                $this->ci->email->to($partner2['user']);
                $this->ci->email->message($message);
                $this->ci->email->send();

            }
            unset($partner2);
            unset($partner);
            unset($_POST['vendor_propo']);

            $count_days = $_POST['add_month'];
            unset($_POST['add_month']);
            $_POST['id_partner'] = $this->ci->session->userdata['partner_id'];
            $_POST['active_from'] = date("Y-m-d H:i:s");
            $_POST['active_to'] = date("Y-m-d H:i:s", strtotime($_POST['active_from'] . " +" . $count_days . "month"));
            // wtym miejscu dodaje obrazki
            $result = $this->add_product();
            if ($result['success'] == 1) {
                $this->add_message_error('Produkt został wysłany do zatwierdzenia przez administratora Plo-cichy.pl');
            } else {
                $this->add_message_error($result['code']);
            }
            $template = 'partner_panel_front';
        } else {
            $address = false;
            $partner = $this->load_partner($this->ci->session->userdata['partner_id']);
            $partner_points = $this->get_last_income_points($this->ci->session->userdata['partner_id']);
            $categorys = $this->load_all_category();
            $vendors = $this->load_all_vendor();
            $payment_cost = $this->get_payment_options();
            //Before add any product check address is ok
            if ( $partner['address'] == '' || $partner['zip'] == '' || $partner['city'] == '' ) {
                //walidacja adresu
                $address = true;
//                $user_panel = new Partner_Panel($this->ci);
//                $user_panel->display_data('user_panel_data',null);
//                die();
            }
            if($partner['shipment']== 1){
                if ( $partner['shipment_address'] == '' || $partner['shipment_zip'] == '' || $partner['shipment_city'] == '' ) {
                    //walidacja adresu
                    $address = true;
//                    $user_panel = new User_Panel($this->ci);
//                    $user_panel->display_data('user_panel_data',null);
//                    die();
                }
            }

            $this->ci->smarty->assign('check_address', $address);
            $this->ci->smarty->assign('partner', $partner);
            $this->ci->smarty->assign('payment_cost', $payment_cost);
            $this->ci->smarty->assign('partner_points', $partner_points);
            $this->ci->smarty->assign('categorys', $categorys);
            $this->ci->smarty->assign('vendors', $vendors);
            $template = 'partner_panel_product_add';
        }
        $this->smarty_display($template);
    }

    public function display_products($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['delete_prod'])) {
            $url = CONSOLE_URL . '/plociuchy:product/delete/' . $_POST['delete_prod'];
            $result = $this->api_call($url);

            $this->add_message_ok('Produkt został usunięty');
        }
        //pobieramy wszytskie produkty
        $products = $this->load_all_active_products();
        if (empty($products)) {
            $this->add_message_ok('Aktualnie nie posiadasz żadnych produktów. Wystaw produkt.');
        } else {
            foreach ($products as $key => $val) {
                $products[$key]['category'] = $this->load_category($val['id_category']);
                $products[$key]['left_days'] = $this->count_Days($val['active_to']);
                //sprawdzamy date oraz czy jest aktywny
                if ($val['active_to'] <= date("Y-m-d H:i:s") || $val['active'] == 0 || $val['reject'] <= 0) {
                    $products[$key]['displayed'] = false;
                } else {
                    $products[$key]['displayed'] = true;
                }
            }
        }
        $this->ci->smarty->assign('title_page', 'Spis Produktów');
        $this->ci->smarty->assign('user_products', $products);
        $template = 'partner_panel_products_list';
        $this->smarty_display($template);
    }

    public function display_products_edit($template, $title_call, $id_product) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['submit_product'])) {
            unset($_POST['submit_product']);
            unset($_POST['uploaded_file']);
            unset($_POST['vendor_propo']);
            $result = $this->edit_product($id_product);
            if ($result['success'] == 1) {
                $this->add_message_ok('Produkt został zapisany.');
            } else {
                $this->add_message_error($result['code']);
            }
        }
        $partner = $this->load_partner($this->ci->session->userdata['partner_id']);
        //$partner_points = $this->get_last_income_points($this->ci->session->userdata['partner_id']);
        $categorys = $this->load_all_category();
        $vendors = $this->load_all_vendor();
        //pobranie pol z bazi przypisanie do smartow
        $this->get_product_default_form_values($id_product);

        //$payment_cost = $this->get_payment_options();
        //$this->ci->smarty->assign('payment_cost', $payment_cost);
        //$this->ci->smarty->assign('partner_points', $partner_points);
        $this->ci->smarty->assign('partner', $partner);
        $this->ci->smarty->assign('categorys', $categorys);
        $this->ci->smarty->assign('vendors', $vendors);
        $template = 'partner_panel_product_edit';
        $this->smarty_display($template);
    }

    public function display_rejected_products($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['delete_prod'])) {
            $url = CONSOLE_URL . '/plociuchy:product/delete/' . $_POST['delete_prod'];
            $result = $this->api_call($url);

            $this->add_message_ok('Produkt został usunięty');
        }
        //pobieramy wszytskie produkty
        $products = $this->load_all_rejected_products();
        if (empty($products)) {
            $this->add_message_ok('Spis jest pusty co oznacza, że żaden z Twoich produktów nie został odrzucony.');
        } else {
            foreach ($products as $key => $val) {
                $products[$key]['category'] = $this->load_category($val['id_category']);
                $products[$key]['left_days'] = $this->count_Days($val['active_to']);
                $products[$key]['rejected_reson'] = $this->load_product_rejected_comment($val['id']);
            }
        }
        $this->ci->smarty->assign('title_page', 'Spis Odrzuconych Produktów');
        $this->ci->smarty->assign('user_products', $products);
        $template = 'partner_panel_products_rejected_list';
        $this->smarty_display($template);
    }

    public function display_reservations($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['change_status'])) {
            $this->change_partner_reservations_status($_POST['rented'], 2);
            $this->add_message_ok('Rezerwacja została przeniesiona do zakładki <a href="' . SITE_URL . '/partner-panel-rezerwacje-aktualne">"Aktualnie wypożyczane"</a><br/>');
        }
        //pobieramy wszytskie rezerwacje status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
        $reservations = $this->load_all_partner_reservations($id_status = 1);

        if (empty($reservations)) {
            $this->add_message_ok('Aktualnie nie posiadasz żadnych rezerwacji.');
        } else {
            if (isset($_POST['change_status']) == false){
                $this->add_message_ok('Spis przedstawia aktualne zapłacone rezerwacje które nie zostały jeszcze wysłane. <br/>Kliknij "wysłane" aby zmienić status produktu.');
            }
            foreach ($reservations as $key => $val) {
                $reservations[$key]['product'] = $this->load_product($val['id_product']);
                $reservations[$key]['user'] = $this->load_user($val['id_user']);
            }
        }
        $this->ci->smarty->assign('title_page', 'Spis Rezerwacji');
        $this->ci->smarty->assign('reservations', $reservations);
        $template = 'partner_panel_reservations_1';
        $this->smarty_display($template);
    }

    public function display_reservations_actual($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        if (isset($_POST['change_status'])) {
            $this->change_partner_reservations_status($_POST['rent_end'], 3);
            $this->add_message_ok('Rezerwacja została zakończona i przeniesiona do zakładki <a href="' . SITE_URL . '/partner-panel-historia-rezerwacji">"Historia rezerwacji".</a><br/>');
        }
        //pobieramy wszytskie rezerwacje status:1) aktualne, oplacone - status:1) czekajace na wyslanie;
        $reservations = $this->load_all_partner_reservations($id_status = 2);
        if (empty($reservations)) {
            $this->add_message_ok('Nie posiadasz aktualne żadnych rezerwacji do wysłania.');
        } else {
            if (isset($_POST['change_status']) == false){
                $this->add_message_ok('Spis przedstawia aktualne wypożyczone produkty które zostały wysłane do wypożyczających.<br/>
                                        Kliknij w opcje "odbiór" gdy odbierzesz ponownie swój produkt. Trafi on wówczas do historii rezerwacji.');
            }
            foreach ($reservations as $key => $val) {
                $reservations[$key]['product'] = $this->load_product($val['id_product']);
                $reservations[$key]['user'] = $this->load_user($val['id_user']);
            }
        }
        $this->ci->smarty->assign('title_page', 'Aktualnie wypożyczane produkty');
        $this->ci->smarty->assign('reservations', $reservations);
        $template = 'partner_panel_reservations_2';
        $this->smarty_display($template);
    }

    public function display_reservation_history($template, $title_call) {
        $this->assign_template_titlecall($template, $title_call);
        //pobieramy wszytskie rezerwacje status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
        $reservations = $this->load_all_partner_reservations($id_status = 3);
        if (empty($reservations)) {
            $this->add_message_ok('Brak historii rezerwacji.');
        } else {
            foreach ($reservations as $key => $val) {
                $reservations[$key]['product'] = $this->load_product($val['id_product']);
                $reservations[$key]['user'] = $this->load_user($val['id_user']);
            }
        }
        $this->ci->smarty->assign('title_page', 'Historia rezerwacji');
        $this->ci->smarty->assign('reservations', $reservations);
        $template = 'partner_panel_reservations_3';
        $this->smarty_display($template);
    }

    //płatności
    public function display_payment_balance($template, $title_call) {
        $account_income = true;
        //pobieramy ilosc ptk
        $available_points = $this->get_last_income_points($this->ci->session->userdata['partner_id']);
        if (empty($available_points)) {
            $this->add_message_ok('Saldo bieżące: 0 kredytów');
            $this->add_message_ok('<br>Nieodnotowano jeszcze zadnego doładowania twojego konta');
        } else {
            if ($available_points['point_available'] == 0) {
                $this->add_message_error('Saldo bieżące: ' . $available_points['point_available'] . ' kredytów. <br/> Doładuj Konto.');
            } else {
                $this->add_message_ok('Saldo bieżące: ' . $available_points['point_available'] . ' kredytów');
            }
        }
        //dodawanie do konta jest tylko mozliwe gdy stan jest 0
        if (isset($available_points['point_available']) && $available_points['point_available'] != 0) {
            $account_income = false;
        }
        $template = 'partner_panel_balance';
        $this->ci->smarty->assign('account_income', $account_income);
        $this->smarty_display($template);
    }

    public function display_payment_balance_add($template, $title_call) {
        $account_points = $this->get_last_income_points($this->ci->session->userdata['partner_id']);
        if (isset($account_points['point_available']) && $account_points['point_available'] != 0) {
            $partner = new Partner_Panel($this->ci);
            $partner->display_payment_balance('partner_panel_balance', null);
            die();
        }
        //proces dodawania
        $opt = null;
        if (isset($_POST['cart_income_3'])) {
            $title = 'Pakiet 3';
            $point = 120;
            $unit_cost = 4;
            $opt = 3;
            unset($_POST['cart_income_3']);
        } elseif (isset($_POST['cart_income_2'])) {
            $title = 'Pakiet 2';
            $point = 60;
            $unit_cost = 6;
            $opt = 2;
            unset($_POST['cart_income_2']);
        } elseif (isset($_POST['cart_income_1'])) {
            $title = 'Pakiet 1';
            $point = 24;
            $unit_cost = 8;
            $opt = 1;
            unset($_POST['cart_income_1']);
        }
        //na razie ptk = zl
        $price = $point;

        //jesli mamy jaką opcje wybrał kient. dodajemy  płatność i wyświetlamy form.
        if (isset($opt)) {
            //Add payment payment
            $data = array();
            $partner = $this->load_partner($this->ci->session->userdata['partner_id']);
            $data['id_partner'] = $this->ci->session->userdata['partner_id'];
            $data['id_session'] = $this->ci->session->userdata['session_id'];
            $data['p24_kwota'] = $price;
            $data['p24_klient'] = $partner['name'] . ' ' . $partner['surname'];
            $data['p24_adres'] = $partner['address'];
            $data['p24_kod'] = $partner['zip'];
            $data['p24_miasto'] = $partner['city'];
            $data['p24_kraj'] = $partner['country'];
            $data['p24_email'] = $partner['user'];
            foreach ($data as $key => $val) {
                $this->ci->smarty->assign($key, $val);
            }
            $url = CONSOLE_URL . '/plociuchy:payment_p24_partner/add_partner_ui';
            $result = $this->api_call($url, $data);
            //dodajemy pola do forma

            $this->ci->smarty->assign('opt', $opt);
            $this->ci->smarty->assign('p24_id_sprzedawcy', $result['data']['p24_id_sprzedawcy']);
            $this->ci->smarty->assign('p24_session_id', $result['data']['p24_session_id']);
            $this->ci->smarty->assign('p24_kwota', $result['data']['p24_kwota']);
            $this->ci->smarty->assign('p24_crc', $result['data']['p24_crc']);
            //teraz juz tylko czekamy na odpowiedz z platnosci 24
        }

        $template = 'partner_panel_balance_payment24';
        $this->ci->smarty->assign('title', $title);
        $this->ci->smarty->assign('price', $price);
        $this->add_message_ok('Dokonaj płatności aby dokonać doładowania.');

        $this->smarty_display($template);
    }

    public function display_payment_balance_add_ok($template, $title_call, $opt) {
        $data = $_POST;
        //Add user payment
        $url = CONSOLE_URL . '/plociuchy:payment_p24_partner/payment_p24_ok_ui/' . $opt;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 'true') {
            $this->add_message_ok('Dziekujemy za wpłatę. Twoje doładowanie konta została przeprowadzona prawidłowo.<br/>');
        } else {
            $this->add_message_error('Wystąpił błąd podaczas wpłaty.Proszę spróbować jeszcze raz.');
        }
        $template = 'partner_panel_balance';
        $this->display_payment_balance($template, $title_call);
    }

    public function display_payment_balance_add_error($template, $title_call) {
        $data = $_POST;
        //Add user payment
        $url = CONSOLE_URL . '/plociuchy:payment_p24_partner/payment_p24_error_ui';
        $result = $this->api_call($url, $data);
        $this->add_message_error('Wystąpił błąd podaczas wpłaty.Proszę spróbować jeszcze raz.');
        $template = 'partner_panel_balance';
        $this->smarty_display($template);
    }

    public function display_payment_history($template, $title_call) {
        $account_history = null;
        $url = CONSOLE_URL . '/plociuchy:partner_account_income/get_outcome_income_all/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url);
        if (isset($result['data'])) {
            $account_history = $result['data'];
            $saldo = 0;
            foreach ($account_history as $key => $history) {
                if ($history['point'] != null) {
                    $account_history[$key]['type'] = 'income';
                    $account_history[$key]['point_available'] = $history['point'];
                    $saldo = $account_history[$key]['point'];
                } else {
                    $account_history[$key]['type'] = 'outcome';
                    //dostepne ptk to poprzedni stan z income minus aktualny  koszt wysastawienia
                    $saldo = $saldo - $history['koszt_wystawienia'];
                    $account_history[$key]['point_available'] = $saldo;
                    //doadnie info o przedmiocie
                    $account_history[$key]['product'] = $this->load_product($history['id_product']);
                }
            }
        }
        if (empty($account_history)) {
            $this->add_message_ok('Brak historii platnosci');
        }
        $this->ci->smarty->assign('account_history', array_reverse($account_history));
        $template = 'partner_panel_payment_history';
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

    public function get_product_default_form_values($id_product) {
        //load values to form
        $url = CONSOLE_URL . '/plociuchy:product/load/' . $id_product;
        $result = $this->api_call($url);
        if ($result['success']) {
            foreach ($result['data'] as $field => $val) {
                $this->ci->smarty->assign($field, (isset($_POST[$field]) ? $_POST[$field] : $val));
            }
        }
    }

    public function count_Days($active_to) {
        $startTimeStamp = strtotime(date("m.d.y"));
        $endTimeStamp = strtotime($active_to);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 86400; // 86400 seconds in one day\
        $numberDays = intval($numberDays);
        if ($startTimeStamp > $endTimeStamp) {
            return '-' . $numberDays;
        }
        return $numberDays;
    }

    public function load_product($id_product) {
        $url = CONSOLE_URL . '/plociuchy:product/load/' . $id_product;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function add_product() {

        $data = $_POST;
        $data['active'] = 0;
        $url = CONSOLE_URL . '/plociuchy:product/add_product_ui';
        $result = $this->api_call($url, $data);
        if ($result['code'] == 'product_exist') {
            $result['code'] = 'Product o podanej nazwie istnieje';
        } else {
            //jesli dodano produkt dodajemy zdjecia
            $url = CONSOLE_URL . '/plociuchy:product/add_images_ui';
            $data = $_POST;
            $result2 = $this->api_call($url, $data, true); //- tutaj TRUE ma zasadnicze znaczenie, bowiem oznacza to ze dziala $_FILES.
            $images = array();
            if (!empty($result2['images'])) {
                foreach ($result2['images'] as $key => $val) {
                    $ims = $key + 1;
                    $images[$key] = $val;
                }
            }
            $data = $images;
            $url = CONSOLE_URL . '/plociuchy:product/edit/'.$result['inserted_id'];
            $result3 = $this->api_call($url, $data);
        }

//        $this->ci->smarty->assign('result', $result['success']);
//        $this->ci->smarty->assign('code', $result['code']);
//        $this->ci->smarty->assign('operation', 'user_add');
        return $result;
    }

    public function edit_product($id_product) {
        $data = $_POST;
        $url = CONSOLE_URL . '/plociuchy:product/edit_product_ui/' . $id_product;
        $result = $this->api_call($url, $data);
        if ($result['code'] == 'product_exist') {
            $result['code'] = 'Product o podanej nazwie istnieje.';
        } else {
            //jesli dodano produkt dodajemy zdjecia
            $url = CONSOLE_URL . '/plociuchy:product/add_images_ui';
            $data = $_POST;
            $result2 = $this->api_call($url, $data, true); //- tutaj TRUE ma zasadnicze znaczenie, bowiem oznacza to ze dziala $_FILES.
            $images = array();
            if (!empty($result2['images'])) {
                foreach ($result2['images'] as $key => $val) {
                    $ims = $key + 1;
                    $images[$key] = $val;
                }
            }
            $data = array();
            $data = $images;
            $url = CONSOLE_URL . '/plociuchy:product/edit/'.$id_product;
            $result3 = $this->api_call($url, $data);
        }

//        $this->ci->smarty->assign('result', $result['success']);
//        $this->ci->smarty->assign('code', $result['code']);
//        $this->ci->smarty->assign('operation', 'user_add');
        return $result;
    }

    public function load_all_active_products() {
        $data = array();
        //$data['active_to'] = true;
        $data['rejected'] = 0;
        $url = CONSOLE_URL . '/plociuchy:product/load_all_user_product_ui/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url, $data);
        return $result['data'];
    }

    public function load_all_rejected_products() {
        $data = array();
        $data['rejected'] = -1;
        $url = CONSOLE_URL . '/plociuchy:product/load_all_user_product_ui/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url, $data);
        return $result['data'];
    }

    public function load_partner($id_partner) {
        $url = CONSOLE_URL . '/plociuchy:partner/load/' . $id_partner;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_category($id_category) {
        $url = CONSOLE_URL . '/plociuchy:product_dict_category/load/' . $id_category;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_all_category() {
        $url = CONSOLE_URL . '/plociuchy:product_dict_category/load_all/';
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_all_vendor() {
        $url = CONSOLE_URL . '/plociuchy:product_dict_vendor/load_all';
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_user($id_user) {
        $url = CONSOLE_URL . '/plociuchy:user/load/' . $id_user;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function load_product_rejected_comment($id_product) {
        $url = CONSOLE_URL . '/plociuchy:product_comment/load/' . $id_product . ',id_product';
        $result = $this->api_call($url);
        return $result['data'];
    }

    //pobranie ptk partnera
    public function get_last_income_points($id_partner) {
        $url = CONSOLE_URL . '/plociuchy:partner_account_income/get_last_income_points/' . $this->ci->session->userdata['partner_id'];
        $result = $this->api_call($url);
        return $result['data'];
    }

    /*
      status:0)  = nowa rezerwacja
      status:1) aktualne, oplacone - status:1) czekajace na wyslanie ;
      status:2) aktualnie produkty w obiegu, oplacone i wyslane
      status:3) zakonczone rezerwacje
    */
    public function load_all_partner_reservations($status = 1) {
        $url = CONSOLE_URL . '/plociuchy:product_reservation/load_all_partner_products_reservation/' . $this->ci->session->userdata['partner_id'] . ',' . $status;
        $result = $this->api_call($url);
        return $result['data'];
    }

    public function get_payment_options() {
        $account_points = $this->get_last_income_points($this->ci->session->userdata['partner_id']);
        if (!empty($account_points)) {
            $return = array('1' => $account_points['unit_cost'],
                '2' => $account_points['unit_cost'] * 2,
                '3' => $account_points['unit_cost'] * 3,
            );
            return $return;
        }
        return false;
    }

    public function change_partner_reservations_status($id_reservation, $status) {
        //zabezpieczenie czy modyfikowana rezerwacja nalerzy do usera
        $url = CONSOLE_URL . '/plociuchy:product_reservation/load/' . $id_reservation;
        $result = $this->api_call($url);
        if ($result['data']['id_partner'] != $this->ci->session->userdata['partner_id']) {
            return 'data_error_owner';
        }
        $data = array();
        $data['status'] = $status;
        $url = CONSOLE_URL . '/plociuchy:product_reservation/edit/' . $id_reservation;
        $result = $this->api_call($url, $data);
        return 1;
    }
}