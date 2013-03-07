<?php
if (!defined('BASEPATH')) die;

class Product extends Main {

    function Product($_ci = '') {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/product_model');
        $this->load->model('plociuchy/product_comment_model');
        $this->load->model('plociuchy/partner_model');
        $this->load->model('plociuchy/partner_account_income_model');
        $this->load->model('plociuchy/partner_account_outcome_model');
        $this->load->model('_media/media_image_model');
    }

    // display
    function display() {
        $this->ci->smarty->display('plociuchy/product.html');
    }

    function display_add() {
        $this->ci->smarty->display('plociuchy/product_add.html');
    }

    function display_edit($id = null) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/product_edit.html');
    }

    // load
    function load_all() {
        echo '{"total":' . json_encode($this->product_model->load_all_count()) . ', "data":' . json_encode($this->product_model->load_all()) . '}';
    }

    function load_all_user($id = null) {
        echo '{"total":' . json_encode($this->product_model->load_all_user_count($id)) . ', "data":' . json_encode($this->product_model->load_all_user($id)) . '}';
    }

    function load($id = null) {
        echo '{"success": 1, "data":' . json_encode($this->product_model->load($id)) . '}';
    }

    function load_promote() {
        echo '{"success": 1, "data":' . json_encode($this->product_model->load_promote()) . '}';
    }

    function load_all_product($id, $where = 'id', $page = null, $limit = 10) {
        if ($page >= 1) {
            $_REQUEST['start'] = ($page - 1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":' . json_encode($this->product_model->load_all_product_count($id, $where)) . ', "data":' . json_encode($this->product_model->load_all_product($id, $where)) . '}';
    }

    function load_all_active_product($id, $where = 'id', $page = null, $limit = 10) {
        if ($page >= 1) {
            $_REQUEST['start'] = ($page - 1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":' . json_encode($this->product_model->load_all_active_product_count($id, $where)) . ', "data":' . json_encode($this->product_model->load_all_active_product($id, $where)) . '}';
    }

    function load_all_product_search_ui($query = null, $page = null, $limit = 10) {
        if (isset($query)) {
            $_REQUEST['query'] = urldecode($query); //unserialize(urldecode($query));
        }
        if ($page >= 1) {
            $_REQUEST['start'] = ($page - 1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":' . json_encode($this->product_model->load_all_active_product_count()) . ', "data":' . json_encode($this->product_model->load_all_active_product()) . '}';
    }

    function load_all_user_product_ui($id_user, $page = null, $limit = 10) {
        if ($page >= 1) {
            $_REQUEST['start'] = ($page - 1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":' . json_encode($this->product_model->load_all_user_products_count($id_user, 'id_partner')) . ', "data":' . json_encode($this->product_model->load_all_user_products($id_user, 'id_partner')) . '}';
    }

    // add
    function add() {
        $result = $this->product_model->add();
        if (isset($_FILES['userfile']['name']) && $_FILES['userfile']['name'] != '') {
            //let's use media file model
            $result = $this->media_image_model->add($partner['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $_POST['image1'] = $s->upload_data->file_name;
            }
        }
        // now we will arrange avatar if exists
        if (isset($_FILES['userfile2']['name']) && $_FILES['userfile2']['name'] != '') {
            // let's use media file model
            $result = $this->media_image_model->add($partner['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $_POST['image2'] = $s->upload_data->file_name;
            }
        }
        if (isset($_FILES['userfile3']['name']) && $_FILES['userfile3']['name'] != '') {
            // let's use media file model
            $result = $this->media_image_model->add($partner['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $_POST['image3'] = $s->upload_data->file_name;
            }
        }
        $result = $this->product_model->edit($result);
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null) {
        //add images
        // ok, here we will add file ( mp3 )
        //load user_id_media_image
        $partner = $this->partner_model->load($_POST['id_partner']);
        if (isset($_FILES['userfile']['name']) && $_FILES['userfile']['name'] != '') {
            //let's use media file model
            $result = $this->media_image_model->add($partner['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $_POST['image1'] = $s->upload_data->file_name;
            }
        }
        // now we will arrange avatar if exists
        if (isset($_FILES['userfile2']['name']) && $_FILES['userfile2']['name'] != '') {
            // let's use media file model
            $result = $this->media_image_model->add($partner['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $_POST['image2'] = $s->upload_data->file_name;
            }
        }
        if (isset($_FILES['userfile3']['name']) && $_FILES['userfile3']['name'] != '') {
            // let's use media file model
            $result = $this->media_image_model->add($partner['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $_POST['image3'] = $s->upload_data->file_name;
            }
        }
        $result = $this->product_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null) {
        $result = $this->product_model->delete($id);
        echo '{"success":' . $result . '}';
    }

    // active
    function active_set($id = null, $state = false) {
        $result = $this->product_model->active_set($id, $state);
        echo 'grid';
    }

    // reject
    function reject_set($id = null, $state = false) {
        $result = $this->product_model->reject_set($id, $state);
        //send email to user about status
        $product = $this->product_model->load($id);
        $data = array();
        $data['partner'] = $this->partner_model->load($product['id_partner']);
        $data['product'] = $product;
        $data['reject'] = false;
        //zatwierdzony do wystawienia
        if ($state == 1) {
            $data['reason_reject'] = false;
        } else {
            $reject_comment = $this->product_comment_model->load($product['id'], 'id_product');
            $data['reject'] = true;
            $data['reason_reject'] = $reject_comment;
        }
        //send email
        $this->send_email_to_user($data);
        //count how many days take product
        if ($state == 1) {
            $opt = $this->cout_days($product['active_from'], $product['active_to']);
            //accout outcome
            $income = $this->partner_account_income_model->get_last_income_points($product['id_partner']);
            $income_unitcost = $income['unit_cost'] * $opt; //cena jednostkowa * ilość miesięcy
            $_POST = array();
            $_POST['id_partner'] = $product['id_partner'];
            $_POST['id_product'] = $product['id'];
            $_POST['unit_cost'] = $income_unitcost;
            $this->partner_account_outcome_model->add();
            //modyfikujemy aktualne ptk
            $_POST = array();
            $_POST['point_available'] = $income['point_available'] - $income_unitcost;
            $modify_points = $this->partner_account_income_model->edit($income['id']);
        }
        echo 'grid';
    }

    public function cout_days($date_from, $date_to) {
        //count days
        $cdate_from = strtotime($date_from);
        $cdate_to = strtotime($date_to);
        $timeDiff = abs($cdate_from - $cdate_to);
        $numberDays = $timeDiff / (60 * 60 * 24 * 30); //86400;
        $numberDays = intval($numberDays);
        return $numberDays;
    }

    function add_product_ui() {
        $_POST['active'] = '0'; //add images
        unset($_POST['user_id_media_image']);
        $result = $this->product_model->add_ui();
        echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '", "inserted_id": "' . $result['inserted_id'] . '"}';
    }

    function edit_product_ui($id_product) {
        unset($_POST['user_id_media_image']);
        $result = $this->product_model->edit_ui($id_product);
        echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '"}';
    }

    function send_email_to_user($data) {
        // lets define data
        $this->ci->smarty->assign('path_template', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_media', SITE_URL . '/templates/email/' . CONFIGURATION);
        $this->ci->smarty->assign('path_site', SITE_URL);
        $this->ci->smarty->assign('product', $data['product']);
        $this->ci->smarty->assign('partner', $data['partner']);
        $this->ci->smarty->assign('reason_reject', $data['reason_reject']);

        if ($data['reject'] == true) {
            $message = $this->ci->smarty->fetch('../../../../templates/email/' . CONFIGURATION . '/partner_product_reject.html');
        } else {
            $message = $this->ci->smarty->fetch('../../../../templates/email/' . CONFIGURATION . '/partner_product_confirm.html');
        }
        //
        $config['protocol'] = EMAIL_PROTOCOL;
        $config['mailtype'] = EMAIL_MODE;
        $config['charset'] = EMAIL_ENCODING;
        //
        $this->ci->email->initialize($config);
        $this->ci->email->subject('Plociuchy - Informacje odnośnie produktu');
        $this->ci->email->from('rejestracja@plociuchy.pl');
        $this->ci->email->to($data['partner']['user']);
        $this->ci->email->message($message);
        $this->ci->email->send();
    }

    public function add_images_ui() {
        $result['success'] = '0';
        $result['code'] = 'false';
        $img = array();
        // ok, here we will add file ( mp3 )
        if (isset($_POST['userfile_name']) && $_POST['userfile_name'] != '') {
            $_POST['userfile_name'] = $_POST['userfile_name'];
            $_POST['userfile_type'] = $_POST['userfile_type'];
            $_FILES['userfile'] = $_FILES['userfile'];
            //let's use media file model
            $result = $this->media_image_model->add($_POST['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $img['image1'] = $s->upload_data->file_name;
            }
        }
        // now we will arrange avatar if exists
        if (isset($_POST['userfile2_name']) && $_POST['userfile2_name'] != '') {
            $_POST['userfile_name'] = $_POST['userfile2_name'];
            $_POST['userfile_type'] = $_POST['userfile2_type'];
            $_FILES['userfile'] = $_FILES['userfile2'];
            // let's use media file model
            $result = $this->media_image_model->add($_POST['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $img['image2'] = $s->upload_data->file_name;
            }
        }
        if (isset($_POST['userfile3_name']) && $_POST['userfile3_name'] != '') {
            $_POST['userfile_name'] = $_POST['userfile3_name'];
            $_POST['userfile_type'] = $_POST['userfile3_type'];
            $_FILES['userfile'] = $_FILES['userfile3'];
            // let's use media file model
            $result = $this->media_image_model->add($_POST['user_id_media_image']);
            if ($result['success'] == true) {
                // after adding a file we need to pass that file id number
                //$img['image2'] = $result['upload_data']['file_name'];//old version
                $s = json_decode($result);
                $img['image3'] = $s->upload_data->file_name;
            }
        }
        // adding record
        //$result = $this->arrange_model->add_ui();
        echo '{"success": "true", "code": "ok" , "images": ' . json_encode($img) . '}';
        // echo '{"success": '. $result['success'] .', "code": "'.$result['code'].'"}';
    }

    function load_all_promotion_products() {
        echo '{"total":' . json_encode($this->product_model->load_all_promotion_products_count()) . ', "data":' . json_encode($this->product_model->load_all_promotion_products()) . '}';
    }

}