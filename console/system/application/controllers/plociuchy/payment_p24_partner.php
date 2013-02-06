<?php
if (!defined('BASEPATH')) die;

class Payment_P24_Partner extends Main
{

    function Payment_P24_Partner($_ci = '')
    {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/payment_p24_partner_model');
    }

    // display
    function display()
    {
        $this->ci->smarty->display('plociuchy/payment_p24_partner.html');
    }

    function display_add()
    {
        $this->ci->smarty->display('plociuchy/payment_p24_partner_add.html');
    }

    function display_edit($id = null)
    {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/payment_p24_partner_edit.html');
    }

    // load
    function load_all()
    {
        echo '{"total":' . json_encode($this->payment_p24_partner_model->load_all_count()) . ', "data":' . json_encode($this->payment_p24_partner_model->load_all()) . '}';
    }

    function load_all_user($id = null)
    {
        echo '{"total":' . json_encode($this->payment_p24_partner_model->load_all_user_count($id)) . ', "data":' . json_encode($this->payment_p24_partner_model->load_all_user($id)) . '}';
    }

    function load($id = null)
    {
        echo '{"success": 1, "data":' . json_encode($this->payment_p24_partner_model->load($id)) . '}';
    }

    // add
    function add()
    {
        $result = $this->payment_p24_partner_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null)
    {
        $result = $this->payment_p24_partner_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null)
    {
        $result = $this->payment_p24_partner_model->delete($id);
        echo '{"success":' . $result . '}';
    }

}