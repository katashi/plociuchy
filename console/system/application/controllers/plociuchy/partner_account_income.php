<?php
if (!defined('BASEPATH')) die;

class Partner_Account_Income extends Main {

    function Partner_Account_Income($_ci = '') {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/partner_account_income_model');
    }

    // display
    function display() {
        $this->ci->smarty->display('plociuchy/partner.html');
    }
    function display_add() {
        $this->ci->smarty->display('plociuchy/partner_add.html');
    }
    function display_edit($id = null) {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/partner_edit.html');
    }

    // load
    function load_all() {
        echo '{"total":' . json_encode($this->partner_account_income_model->load_all_count()) . ', "data":' . json_encode($this->partner_account_income_model->load_all()) . '}';
    }
    function load_all_user($id = null) {
        echo '{"total":' . json_encode($this->partner_account_income_model->load_all_partner_count($id)) . ', "data":' . json_encode($this->partner_account_income_model->load_all_partner($id)) . '}';
    }
    function load($id = null,$where ='id') {
        echo '{"success": 1, "data":' . json_encode($this->partner_account_income_model->load($id,$where)) . '}';
    }

    // add
    function add() {
        $result = $this->partner_account_income_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null) {
        $result = $this->partner_account_income_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null) {
        $result = $this->partner_account_income_model->delete($id);
        echo '{"success":' . $result . '}';
    }

    // active
    function active_set($id = null, $state = false) {
        $result = $this->partner_account_income_model->active_set($id, $state);
        echo 'grid';
    }


    function get_last_income_points($id_partner){
        echo '{"success":"1","data":' . json_encode($this->partner_account_income_model->get_last_income_points($id_partner)) . '}';
    }

    function get_outcome_income_all($id_partner){
        echo '{"success":"1","data":' . json_encode($this->partner_account_income_model->get_outcome_income_all($id_partner)) . '}';
    }

}