<?php
if (!defined('BASEPATH')) die;

class Partner_Account_Outcome extends Main {

    function Partner_Account($_ci = '') {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/partner_account_outcome_model');
    }

    // display
    function display() {
        $this->ci->smarty->display('plociuchy/partner_account.html');
    }

    // load
    function load_all() {
        echo '{"total":' . json_encode($this->partner_account_outcome_model->load_all_count()) . ', "data":' . json_encode($this->partner_account_outcome_model->load_all()) . '}';
    }
    function load_all_partner($id = null) {
        echo '{"total":' . json_encode($this->partner_account_outcome_model->load_all_partner_account_count($id)) . ', "data":' . json_encode($this->partner_account_outcome_model->load_all_partner($id)) . '}';
    }

    // add
    function add() {
        $result = $this->partner_account_outcome_model->add();
        echo '{"success": ' . $result . '}';
    }

}