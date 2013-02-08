<?php
if (!defined('BASEPATH')) die;

class User extends Main
{

    function User($_ci = '')
    {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/user_model');
    }

    // display
    function display()
    {
        $this->ci->smarty->display('plociuchy/user.html');
    }

    function display_add()
    {
        $this->ci->smarty->display('plociuchy/user_add.html');
    }

    function display_edit($id = null)
    {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/user_edit.html');
    }

    // load
    function load_all()
    {
        echo '{"total":' . json_encode($this->user_model->load_all_count()) . ', "data":' . json_encode($this->user_model->load_all()) . '}';
    }

    function load_all_user($id = null)
    {
        echo '{"total":' . json_encode($this->user_model->load_all_user_count($id)) . ', "data":' . json_encode($this->user_model->load_all_user($id)) . '}';
    }

    function load($id = null)
    {
        echo '{"success": 1, "data":' . json_encode($this->user_model->load($id)) . '}';
    }

    // add
    function add()
    {
        $result = $this->user_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null)
    {
        $result = $this->user_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null)
    {
        $result = $this->user_model->delete($id);
        echo '{"success":' . $result . '}';
    }

    // active
    function active_set($id = null, $state = false)
    {
        $result = $this->user_model->active_set($id, $state);
        echo 'grid';
    }

}