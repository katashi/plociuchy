<?php
if (!defined('BASEPATH')) die;

class Product extends Main
{

    function Product($_ci = '')
    {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/partner_model');
    }

    // display
    function display()
    {
        $this->ci->smarty->display('plociuchy/partner.html');
    }

    function display_add()
    {
        $this->ci->smarty->display('plociuchy/partner_add.html');
    }

    function display_edit($id = null)
    {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/partner_edit.html');
    }

    // load
    function load_all()
    {
        echo '{"total":' . json_encode($this->partner_model->load_all_count()) . ', "data":' . json_encode($this->product_model->load_all()) . '}';
    }

    function load_all_user($id = null)
    {
        echo '{"total":' . json_encode($this->partner_model->load_all_user_count($id)) . ', "data":' . json_encode($this->product_model->load_all_user($id)) . '}';
    }

    function load($id = null)
    {
        echo '{"success": 1, "data":' . json_encode($this->partner_model->load($id)) . '}';
    }

    // add
    function add()
    {
        $result = $this->partner_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null)
    {
        $result = $this->partner_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null)
    {
        $result = $this->partner_model->delete($id);
        echo '{"success":' . $result . '}';
    }

    // active
    function active_set($id = null, $state = false)
    {
        $result = $this->partner_model->active_set($id, $state);
        echo 'grid';
    }

}