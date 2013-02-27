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
        echo '{"total":'.json_encode($this->product_model->load_all_count()).', "data":'.json_encode($this->product_model->load_all()).'}';
    }
    function load_all_user($id = null) {
        echo '{"total":'.json_encode($this->product_model->load_all_user_count($id)).', "data":'.json_encode($this->product_model->load_all_user($id)).'}';
    }
    function load($id = null) {
        echo '{"success": 1, "data":'.json_encode($this->product_model->load($id)).'}';
    }
    function load_promote() {
        echo '{"success": 1, "data":'.json_encode($this->product_model->load_promote()).'}';
    }

    function load_all_product($id , $where = 'id', $page = null , $limit = 10){
        if($page >= 1){
            $_REQUEST['start'] = ($page-1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":'.json_encode($this->product_model->load_all_product_count($id,$where)).', "data":'.json_encode($this->product_model->load_all_product($id,$where)).'}';
    }

    function load_all_product_search_ui($query=null, $page = null , $limit = 10){
        if(isset($query)){
            $_REQUEST['query'] = urldecode($query);//unserialize(urldecode($query));
        }
        if($page >= 1){
            $_REQUEST['start'] = ($page-1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":'.json_encode($this->product_model->load_all_product_count()).', "data":'.json_encode($this->product_model->load_all_product()).'}';
    }

    function load_all_user_product_ui($id_user, $page = null , $limit = 10){
        if($page >= 1){
            $_REQUEST['start'] = ($page-1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":'.json_encode($this->product_model->load_all_user_products_count($id_user,'id_partner')).', "data":'.json_encode($this->product_model->load_all_user_products($id_user,'id_partner')).'}';
    }

    // add
    function add() {
        $result = $this->product_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null) {
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
        echo 'grid';
    }

    function add_product_ui() {
        $_POST['active'] = '0';
        $result = $this->product_model->add_ui();
        echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '"}';
    }
    function edit_product_ui($id_product) {
        $_POST['active'] = '0';
        $result = $this->product_model->edit_ui($id_product);
        echo '{"success": ' . $result['success'] . ', "code": "' . $result['code'] . '"}';
    }
}