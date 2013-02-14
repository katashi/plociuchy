<?php
if (!defined('BASEPATH')) die;

class Product_Dict_Category extends Main {

    function Product_Dict_Category($_ci = '') {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/product_dict_category_model');
    }

    // display
    function display() {
        $this->ci->smarty->display('plociuchy/product_dict_category.html');
    }
    function display_add() {
        $this->ci->smarty->display('plociuchy/product_dict_category_add.html');
    }
    function display_edit($id = null) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/product_dict_category_edit.html');
    }

    // load
    function load_all() {
        echo '{"total":'.json_encode($this->product_dict_category_model->load_all_count()).', "data":'.json_encode($this->product_dict_category_model->load_all()).'}';
    }
    function load_all_letters() {
        echo '{"total":'.json_encode($this->product_dict_category_model->load_all_count()).', "data":'.json_encode($this->product_dict_category_model->load_all_letters()).'}';
    }
    function load_all_user($id = null) {
        echo '{"total":'.json_encode($this->product_dict_category_model->load_all_user_count($id)).', "data":'.json_encode($this->product_dict_category_model->load_all_user($id)).'}';
    }
    function load($id = null) {
        echo '{"success": 1, "data":'.json_encode($this->product_dict_category_model->load($id)).'}';
    }

    // add
    function add() {
        $result = $this->product_dict_category_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null) {
        $result = $this->product_dict_category_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null) {
        $result = $this->product_dict_category_model->delete($id);
        echo '{"success":' . $result . '}';
    }

}