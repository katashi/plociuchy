<?php
if (!defined('BASEPATH')) die;

class Product_Comment extends Main {

    function Product_Comment($_ci = '') {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/product_comment_model');
    }

    // display
    function display() {
        $this->ci->smarty->display('plociuchy/product_comment.html');
    }
    function display_add() {
        $this->ci->smarty->display('plociuchy/product_comment_add.html');
    }
    function display_edit($id = null) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/product_comment_edit.html');
    }
    function display_reject($id = null,$id_partner = null) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->assign('id_partner', $id_partner);
        $this->ci->smarty->display('plociuchy/product_comment_reject.html');
    }

    // load
    function load_all() {
        echo '{"total":' . json_encode($this->product_comment_model->load_all_count()) . ', "data":' . json_encode($this->product_comment_model->load_all()) . '}';
    }
    function load_all_user($id = null) {
        echo '{"total":' . json_encode($this->product_comment_model->load_all_user_count($id)) . ', "data":' . json_encode($this->product_comment_model->load_all_user($id)) . '}';
    }
    function load($id = null,$where = 'id') {
        echo '{"success": 1, "data":' . json_encode($this->product_comment_model->load($id,$where)) . '}';
    }
    function load_promote() {
        echo '{"success": 1, "data":' . json_encode($this->product_comment_model->load_promote()) . '}';
    }
    function load_all_comments($id , $where = 'id', $page = null , $limit = 10){
        if($page >= 1){
            $_REQUEST['start'] = ($page-1) * $limit;
            $_REQUEST['limit'] = $limit;
        }
        echo '{"total":'.json_encode($this->product_comment_model->load_all_product_comments_count($id,$where)).', "data":'.json_encode($this->product_comment_model->load_all_product_comments($id,$where)).'}';
    }


    // add
    function add() {
        $result = $this->product_comment_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null) {
        $result = $this->product_comment_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null) {
        $result = $this->product_comment_model->delete($id);
        echo '{"success":' . $result . '}';
    }

}