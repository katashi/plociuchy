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

    // load
    function load_all() {
        echo '{"total":'.json_encode($this->product_comment_model->load_all_count()).', "data":'.json_encode($this->product_comment_model->load_all()).'}';
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