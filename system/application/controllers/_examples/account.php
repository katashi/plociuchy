<?php
class Account extends Hub {

	function Account($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null) {
        // default - we can change it easily
        $this->display_account($template, $title_call);
    }
    function display_song($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('song', $this->load_all_song_user_ui(serialize($condition)));
        $this->ci->smarty->assign('account_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Piosenki');
        $this->ci->smarty->assign('tab', 'song');
        $this->smarty_display($template);
    }
    function display_song_profile($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('song', $this->load_all_song_user_profile_ui(serialize($condition), $id));
        $this->ci->smarty->assign('account', $this->load_account_profile_ui($id));
        $this->ci->smarty->assign('account_profile_id', $id);
        $this->ci->smarty->assign('account_mode', 0);
        $this->ci->smarty->assign('tab_title', 'Piosenki');
        $this->ci->smarty->assign('tab', 'song');
        $this->smarty_display('account_profile');
    }
    function display_song_edit($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        /*$condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('text', $this->load_all_text_user_ui(serialize($condition)));
        $this->ci->smarty->assign('account_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Teksty');
        $this->ci->smarty->assign('tab', 'text');*/
        echo '>>>'.$template;
        $this->smarty_display($template);
    }
    function display_arrange($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('arrange', $this->load_all_arrange_user_ui(serialize($condition)));
        $this->ci->smarty->assign('account_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Aranże');
        $this->ci->smarty->assign('tab', 'arrange');
        $this->smarty_display($template);
    }
    function display_arrange_profile($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('arrange', $this->load_all_arrange_user_profile_ui(serialize($condition), $id));
        $this->ci->smarty->assign('account', $this->load_account_profile_ui($id));
        $this->ci->smarty->assign('account_profile_id', $id);
        $this->ci->smarty->assign('account_mode', 0);
        $this->ci->smarty->assign('tab_title', 'Aranże');
        $this->ci->smarty->assign('tab', 'arrange');
        $this->smarty_display('account_profile');
    }
    function display_arrange_edit($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        /*$condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('text', $this->load_all_text_user_ui(serialize($condition)));
        $this->ci->smarty->assign('account_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Teksty');
        $this->ci->smarty->assign('tab', 'text');*/
        echo '>>>'.$template;
        $this->smarty_display($template);
    }
    function display_text($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('text', $this->load_all_text_user_ui(serialize($condition)));
        $this->ci->smarty->assign('account_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Teksty');
        $this->ci->smarty->assign('tab', 'text');
        $this->smarty_display($template);
    }
    function display_text_profile($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('text', $this->load_all_text_user_profile_ui(serialize($condition), $id));
        $this->ci->smarty->assign('account', $this->load_account_profile_ui($id));
        $this->ci->smarty->assign('account_profile_id', $id);
        $this->ci->smarty->assign('account_mode', 0);
        $this->ci->smarty->assign('tab_title', 'Teksty');
        $this->ci->smarty->assign('tab', 'text');
        $this->smarty_display('account_profile');
    }
    function display_text_edit($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        /*$condition = array();
        $condition['limit'] = 100;
        $this->ci->smarty->assign('text', $this->load_all_text_user_ui(serialize($condition)));
        $this->ci->smarty->assign('account_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Teksty');
        $this->ci->smarty->assign('tab', 'text');*/
        echo '>>>'.$template;
        $this->smarty_display($template);
    }
    function display_account($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $this->ci->smarty->assign('account', $this->load_account_ui());
        $this->ci->smarty->assign('tab_title', 'Moje Dane');
        $this->ci->smarty->assign('tab', 'account');
        $this->smarty_display($template);
    }
    function display_account_profile($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->ci->smarty->assign('account', $this->load_account_profile_ui($id));
        $this->ci->smarty->assign('account_profile_detail', $this->load_account_detail_profile_ui($id));
        $this->ci->smarty->assign('account_profile_element_count', $this->account_header_load_element_profile_count($id));
        $this->ci->smarty->assign('account_profile_id', $id);
        $this->ci->smarty->assign('tab_title', 'Profil');
        $this->ci->smarty->assign('tab', 'account_profile');
        $this->smarty_display('account_profile');
    }
    function display_payment($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $this->ci->smarty->assign('payment_operation', $this->payment_load_all_user_ui());
        $this->ci->smarty->assign('point_operation', $this->point_load_all_user_ui());
        $this->ci->smarty->assign('tab_title', 'Konto');
        $this->ci->smarty->assign('tab', 'payment');
        $this->smarty_display($template);
    }
    function display_payment_add_cc($template = null, $title_call = null, $price = 18) {
        $this->assign_template_titlecall($template, $title_call, true);
        $amount_gr = $price * 100;
        $amount_zl = $price;
        //
        $data = array();
        $data['id_session'] = $this->ci->session->userdata['session_id'];
        $data['id_user'] = $this->ci->session->userdata['user_id'];
        $data['amount'] = $amount_zl;
        $data['pay_mode'] = 0;
        $data['desc1'] = '';
        $data['desc2'] = '';
        $data['ip'] = $this->ci->session->userdata['ip_address'];
        $data['status'] = 0;
        $data['date_added'] = date("Y-m-d H:i:s");
        $data['date_last_modified'] = date("Y-m-d H:i:s");
        $data['error'] = 0;
        //
        $url = CONSOLE_URL.'/kreomaniak:payment/add_ui';
        $result = $this->api_call($url, $data);
        //
        $this->ci->smarty->assign('payu_posid', PLATNOSCI_POSID);
        $this->ci->smarty->assign('payu_1md5', PLATNOSCI_1MD5);
        $this->ci->smarty->assign('payu_2md5', PLATNOSCI_2MD5);
        $this->ci->smarty->assign('payu_posauthkey', PLATNOSCI_POSAUTHKEY);
        $this->ci->smarty->assign('payu_id_order', $result['id']);
        $this->ci->smarty->assign('payu_id_session', $this->ci->session->userdata['session_id']);
        $this->ci->smarty->assign('payu_ip', $this->ci->session->userdata['ip_address']);
        $this->ci->smarty->assign('payu_amount_gr', $amount_gr);
        $this->ci->smarty->assign('payu_amount_zl', $amount_zl);
        $this->ci->smarty->assign('user', $this->payment_load_user_ui());
        $this->ci->smarty->assign('tab_title', 'Płatności');
        $this->ci->smarty->assign('tab', 'payment_add_cc');
        $this->smarty_display($template);
    }
    function display_favorite($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $result = $this->load_all_favorite_ui();
        $this->ci->smarty->assign('favorite', $result);
        $this->ci->smarty->assign('favorite_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Ulubione');
        $this->ci->smarty->assign('tab', 'favorite');
        $this->smarty_display($template);
    }
    function display_briefcase($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $result = $this->load_all_briefcase_ui();
        $this->ci->smarty->assign('briefcase', $result);
        $this->ci->smarty->assign('briefcase_mode', 1);
        $this->ci->smarty->assign('tab_title', 'Kreownia');
        $this->ci->smarty->assign('tab', 'briefcase');
        $this->smarty_display($template);
    }

    // account
    function load_account_ui() {
        $this->user = $this->user_get('user', $this->ci->session->userdata['user_user']);
        $url = CONSOLE_URL.'/kreomaniak:client/load_ui/'. $this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_account_profile_ui($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/load_ui/'. $id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_account_detail_profile_ui($id = null) {
        $data = array();
        // first, we need info about last added element owned by specific user
        $url = CONSOLE_URL.'/kreomaniak:hub/last_added_date_ui/'. $id;
        $result = $this->api_call($url);
        $data['last_added_date'] = $result['data'];
        return $data;
    }
    function edit_account_ui($template = null, $title_call = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:client/edit_ui/'. $id;
        $data = $_POST;
        $result = $this->api_call($url, $data, true);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'account_update');
        // update avatar data session is attached
        if ($result['upload_data']) {
            $this->ci->session->set_userdata('user_image', $result['upload_data']['file_name']);
            $this->ci->smarty->assign('user_image', $this->ci->session->userdata['user_image']);
        }
        //
        if (isset($data['nick'])) {
            $this->ci->smarty->clear_assign('nick_missing');
        } else {
            $this->ci->smarty->assign('nick_missing', 1);
        }
        //
        if ($result['success'] == 0 && $result['code'] == 'nick_exists') {
            $this->ci->smarty->assign('nick_missing', 1);
        }
        $this->display_account($template, $title_call);
    }

    // song
    function load_all_song_user_ui($condition = null) {
        $this->user = $this->user_get('user', $this->ci->session->userdata['user_user']);
        $url = CONSOLE_URL.'/kreomaniak:song/load_all_user_ui/'.$condition.','.$this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_song_user_profile_ui($condition = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/load_all_user_ui/'.$condition.','.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function delete_song_ui($template = null, $title_call = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:song/delete_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            // nothing here temporarily
        }
        $this->display_song($template, $title_call);
    }

    // arrange
    function load_all_arrange_user_ui($condition = null) {
        $this->user = $this->user_get('user', $this->ci->session->userdata['user_user']);
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_all_user_ui/'.$condition.','.$this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_arrange_user_profile_ui($condition = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_all_user_ui/'.$condition.','.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function delete_arrange_ui($template = null, $title_call = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/delete_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            // nothing here temporarily
        }
        $this->display_arrange($template, $title_call);
    }

    // text
    function load_all_text_user_ui($condition = null) {
        $this->user = $this->user_get('user', $this->ci->session->userdata['user_user']);
        $url = CONSOLE_URL.'/kreomaniak:text/load_all_user_ui/'.$condition.','.$this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_text_user_profile_ui($condition = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/load_all_user_ui/'.$condition.','.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function delete_text_ui($template = null, $title_call = null, $id = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/delete_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            // nothing here temporarily
        }
        $this->display_text($template, $title_call);
    }

    // payment
    function payment_load_all_user_ui() {
        $url = CONSOLE_URL.'/kreomaniak:payment/load_all_user_ui/'. $this->ci->session->userdata['user_id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function payment_load_user_ui() {
        $url = CONSOLE_URL.'/kreomaniak:client/load_ui/'. $this->ci->session->userdata['user_id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function payment_status_positive($template = null, $title_call = null, $id_trans = null, $id_session = null, $id_order = null) {
        $data = array();
        $data['id_trans'] = $id_trans;
        $data['id_session'] = $id_session;
        $data['id_order'] = $id_order;
        //
        $url = CONSOLE_URL.'/kreomaniak:payment/edit_status_positive_ui';
        $result = $this->api_call($url, $data);
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'payment_add_positive');
        $this->display_payment($template, $title_call);
    }
    function payment_status_negative($template = null, $title_call = null, $id_session = null, $id_order = null, $error = null) {
        $data = array();
        $data['id_trans'] = 0;
        $data['id_session'] = $id_session;
        $data['id_order'] = $id_order;
        $data['error'] = $error;
        $url = CONSOLE_URL.'/kreomaniak:payment/edit_status_negative_ui';
        $result = $this->api_call($url, $data);
        //
        $this->ci->smarty->assign('result', 0);
        $this->ci->smarty->assign('code', $error);
        $this->ci->smarty->assign('operation', 'payment_add_negative');
        $this->display_payment($template, $title_call);
    }
    function payment_status_update($data = null) {
        $session_id = $_POST['session_id'];
        $ts = time();
        $sig = md5(PLATNOSCI_POSID.$session_id.$ts.PLATNOSCI_1MD5);
        //
        $parameters = "pos_id=".PLATNOSCI_POSID."&session_id=".$session_id."&ts=".$ts."&sig=".$sig;
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.platnosci.pl/paygw/ISO/Payment/get");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $xml = curl_exec($ch);
        curl_close($ch);
        //
        $xmlstr = new SimpleXMLElement($xml);
        //
        $data = array();
        $data['id_trans'] = $xmlstr->trans->id;
        $data['id_session'] = $xmlstr->trans->session_id;
        $data['pay_type'] = $xmlstr->trans->pay_type;
        $data['status'] = $xmlstr->trans->status;
        $url = CONSOLE_URL.'/kreomaniak:payment/edit_status_update_ui';
        $result = $this->api_call($url, $data);
        // in case of 99 we need add update point in client account
        if ($data['status'] == 99) {
            $data2 = array();
            $data2['amount'] = $xmlstr->trans->amount;
            $data2['id'] = $xmlstr->trans->order_id;
            $data2['id_trans'] = $xmlstr->trans->id;
            $data2['pay_type'] = $xmlstr->trans->pay_type;
            $url = CONSOLE_URL.'/kreomaniak:client/edit_point_ui';
            $result = $this->api_call($url, $data2);
        }
        //
        $this->ci->email->subject('Kreomaniak - Platnosc');
        $this->ci->email->from('kontakt@plo-ciuchy.pl','Plo-ciuchy.pl');
        $this->ci->email->to('biuro@katashi.pl');
        $m = $session_id.'/'.$ts.'/'.$sig.'/'.$xml;
        $this->ci->email->message($m);
        $this->ci->email->send();
    }
    function payment_report() {
        $this->payment_status_update($_POST);
        echo 'OK';
    }

    // point
    function point_load_all_user_ui() {
        $url = CONSOLE_URL.'/kreomaniak:client/point_load_all_user_ui/'. $this->ci->session->userdata['user_id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }

    // favorite
    function load_all_favorite_ui() {
        // first, lets retrieve favorite
        $this->user = $this->user_get('user', $this->ci->session->userdata['user_user']);
        $url = CONSOLE_URL.'/kreomaniak:hub/load_all_favorite_ui/'.$this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            // lets define favorite
            $favorite = $result['data'];
            // prepare variables for display
            $song = array();
            $arrange = array();
            $text = array();
            // now lets roll through elements and load data from console
            if ($favorite != '') {
                foreach($favorite as $key => $value) {
                    if ($value['type'] == 0) {
                        $url = CONSOLE_URL.'/kreomaniak:song/load_ui/'.$value['id_element'];
                        $result = $this->api_call($url);
                        if ($result['success'] == 1) {
                            $song[] = $result['data'];
                        }
                    }
                    if ($value['type'] == 1) {
                        $url = CONSOLE_URL.'/kreomaniak:arrange/load_ui/'.$value['id_element'];
                        $result = $this->api_call($url);
                        if ($result['success'] == 1) {
                            $arrange[] = $result['data'];
                        }
                    }
                    if ($value['type'] == 2) {
                        $url = CONSOLE_URL.'/kreomaniak:text/load_ui/'.$value['id_element'];
                        $result = $this->api_call($url);
                        if ($result['success'] == 1) {
                            $text[] = $result['data'];
                        }
                    }
                }
            }
            $result = array();
            $result['success'] = 1;
            $result['song'] = $song;
            $result['arrange'] = $arrange;
            $result['text'] = $text;
        } else {
            $result = array();
            $result['success'] = 0;
        }
        return $result;
    }
    function load_all_favorite_recorder_ui($template = null, $title_call = null, $type = null) {
        // first, lets retrieve favorite
        $this->user = $this->user_get('user', $this->ci->session->userdata['user_user']);
        $url = CONSOLE_URL.'/kreomaniak:hub/load_all_favorite_ui/'.$this->user['id'];
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            // lets define favorite
            $favorite = $result['data'];
            // prepare variables for display
            $song = array();
            $arrange = array();
            $text = array();
            // now lets roll through elements and load data from console
            if ($favorite != '') {
                foreach($favorite as $key => $value) {
                    if ($value['type'] == 0) {
                        $url = CONSOLE_URL.'/kreomaniak:song/load_ui/'.$value['id_element'];
                        $result = $this->api_call($url);
                        if ($result['success'] == 1) {
                            $song[] = $result['data'];
                        }
                    }
                    if ($value['type'] == 1) {
                        $url = CONSOLE_URL.'/kreomaniak:arrange/load_ui/'.$value['id_element'];
                        $result = $this->api_call($url);
                        if ($result['success'] == 1) {
                            $arrange[] = $result['data'];
                        }
                    }
                    if ($value['type'] == 2) {
                        $url = CONSOLE_URL.'/kreomaniak:text/load_ui/'.$value['id_element'];
                        $result = $this->api_call($url);
                        if ($result['success'] == 1) {
                            $text[] = $result['data'];
                        }
                    }
                }
            }
            $result = array();
            $result['success'] = 1;
            $result['song'] = $song;
            $result['arrange'] = $arrange;
            $result['text'] = $text;
            if ($type == 0) {
                $content = $song;
            }
            if ($type == 1) {
                $content = $arrange;
            }
            if ($type == 2) {
                $content = $text;
            }
            echo '{"success": 1, "data":'.json_encode($content).'}';
        } else {
            $result = array();
            $result['success'] = 0;
        }

    }
    function add_favorite_ui($template = null, $title_call = null, $id_user = null, $id_element = null, $type = null) {
        // new element definition
        $entry = array();
        $entry['id_user'] = $id_user;
        $entry['id_element'] = $id_element;
        $entry['type'] = $type;
        $entry['date_added'] = date("Y-m-d H:i:s");
        // add
        $url = CONSOLE_URL.'/kreomaniak:hub/add_favorite_ui';
        $data = $entry;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            // nothing here temporarily
        }
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'favorite_add');
        //
        if ($type == 0) {
            $song = new Song($this->ci);
            $song->display_player('song', 'song', $id_element);
        }
        if ($type == 1) {
            $song = new Arrange($this->ci);
            $song->display_player('arrange', 'arrange', $id_element);
        }
        if ($type == 2) {
            $song = new Text($this->ci);
            $song->display_player('text', 'text', $id_element);
        }
    }
    function delete_favorite_ui($template = null, $title_call = null, $id_user = null, $id_element = null, $type = null) {
        // new element definition
        $entry = array();
        $entry['id_user'] = $id_user;
        $entry['id_element'] = $id_element;
        $entry['type'] = $type;
        // remove favorite
        $url = CONSOLE_URL.'/kreomaniak:hub/delete_favorite_ui';
        $data = $entry;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            // nothing here temporarily
        }
        $this->display_favorite($template, $title_call);
    }

    // briefcase
    function load_all_briefcase_ui() {
        // first, lets retrieve briefcase
        $briefcase = $this->ci->session->userdata('briefcase');
        // prepare variables for display
        $song = array();
        $arrange = array();
        $text = array();
        // now lets roll through elements and load data from console
        if ($briefcase != '') {
            foreach($briefcase as $key => $value) {
                if ($value['type'] == 0) {
                    $url = CONSOLE_URL.'/kreomaniak:song/load_ui/'.$value['id_element'];
                    $result = $this->api_call($url);
                    if ($result['success'] == 1) {
                        $song[] = $result['data'];
                    }
                }
                if ($value['type'] == 1) {
                    $url = CONSOLE_URL.'/kreomaniak:arrange/load_ui/'.$value['id_element'];
                    $result = $this->api_call($url);
                    if ($result['success'] == 1) {
                        $arrange[] = $result['data'];
                    }
                }
                if ($value['type'] == 2) {
                    $url = CONSOLE_URL.'/kreomaniak:text/load_ui/'.$value['id_element'];
                    $result = $this->api_call($url);
                    if ($result['success'] == 1) {
                        $text[] = $result['data'];
                    }
                }
            }
        }
        $result = array();
        $result['success'] = 1;
        $result['song'] = $song;
        $result['arrange'] = $arrange;
        $result['text'] = $text;
        return $result;
    }
    function load_all_briefcase_recorder_ui($template = null, $title_call = null, $type = null) {
        // first, lets retrieve briefcase
        $briefcase = $this->ci->session->userdata('briefcase');
        // prepare variables for display
        $song = array();
        $arrange = array();
        $text = array();
        // now lets roll through elements and load data from console
        if ($briefcase != '') {
            foreach($briefcase as $key => $value) {
                if ($value['type'] == 0) {
                    $url = CONSOLE_URL.'/kreomaniak:song/load_ui/'.$value['id_element'];
                    $result = $this->api_call($url);
                    if ($result['success'] == 1) {
                        $song[] = $result['data'];
                    }
                }
                if ($value['type'] == 1) {
                    $url = CONSOLE_URL.'/kreomaniak:arrange/load_ui/'.$value['id_element'];
                    $result = $this->api_call($url);
                    if ($result['success'] == 1) {
                        $arrange[] = $result['data'];
                    }
                }
                if ($value['type'] == 2) {
                    $url = CONSOLE_URL.'/kreomaniak:text/load_ui/'.$value['id_element'];
                    $result = $this->api_call($url);
                    if ($result['success'] == 1) {
                        $text[] = $result['data'];
                    }
                }
            }
        }
        $result = array();
        $result['success'] = 1;
        $result['song'] = $song;
        $result['arrange'] = $arrange;
        $result['text'] = $text;
        if ($type == 0) {
            $content = $song;
        }
        if ($type == 1) {
            $content = $arrange;
        }
        if ($type == 2) {
            $content = $text;
        }
        echo '{"success": 1, "data":'.json_encode($content).'}';
    }
    function add_briefcase_ui($template = null, $title_call = null, $id_element = null, $type = null) {
        // ok, lets roll on session
        // new element definition
        $entry = array();
        $entry['id_element'] = $id_element;
        $entry['type'] = $type;
        $entry['date_added'] = date("Y-m-d H:i:s");
        // first we need to check does the briefcase exists
        if ($this->ci->session->userdata('briefcase')) {
            // first, lets retrieve briefcase
            $briefcase = $this->ci->session->userdata('briefcase');
            // now lets compare does element already is put into briefcase
            foreach($briefcase as $key => $value) {
                if ($value['id_element'] == $id_element && $value['type'] == $type) {
                    $found = true;
                    $result = array();
                    $result['success'] = 0;
                    $result['code'] = 'already_exist';
                }
            }
            // if doesnt exist add new element
            if (!isset($found)) {
                $briefcase[] = $entry;
                $this->ci->session->set_userdata('briefcase', $briefcase);
                $result = array();
                $result['success'] = 1;
                $result['code'] = 'ok';
            }
        } else {
            // new element to briefcase and create briefcase as prior
            $briefcase = array();
            $briefcase[] = $entry;
            $this->ci->session->set_userdata('briefcase', $briefcase);
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'ok';
        }
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'briefcase_add');
        //
        if ($type == 0) {
            $song = new Song($this->ci);
            $song->display_player('song', 'song', $id_element);
        }
        if ($type == 1) {
            $song = new Arrange($this->ci);
            $song->display_player('arrange', 'arrange', $id_element);
        }
        if ($type == 2) {
            $song = new Text($this->ci);
            $song->display_player('text', 'text', $id_element);
        }
    }
    function delete_briefcase_ui($template = null, $title_call = null, $id_element = null, $type = null) {
        // first we need to check does the briefcase exists
        if ($this->ci->session->userdata('briefcase')) {
            // first, lets retrieve briefcase
            $briefcase = $this->ci->session->userdata('briefcase');
            // if element exists remove it
            foreach($briefcase as $key => $value) {
                if ($value['id_element'] == $id_element && $value['type'] == $type) {
                    array_splice($briefcase, $key, 1);
                }
            }
            $this->ci->session->set_userdata('briefcase', $briefcase);
            // if briefcase is empty simply remove it
            if (count($briefcase) >= 0) {
                $this->ci->session->unset_userdata('briefcase');
            }
        }
        $this->display_briefcase($template, $title_call);
    }

}