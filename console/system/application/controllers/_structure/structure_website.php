<?php
if (!defined('BASEPATH')) die;

class Structure_Website extends Main {
	
	function Structure_Website($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('_structure/structure_website_model');
	}

	// display
	function display($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('_structure/structure_website.html');
	}
	function display_add($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('_structure/structure_website_add.html');
	}
	function display_edit($id) {
		$this->ci->smarty->assign('id', $id);
		$this->ci->smarty->display('_structure/structure_website_edit.html');
	}

    // load
    function load_all_count($id_tree = null) {
        echo '{"total":'.json_encode($this->structure_website_model->load_all_count($id_tree)).'}';
    }
    function load_all($id_tree = null, $paginate = '') {
        if ($paginate) {
            $array = unserialize(urldecode($paginate));
            if (isset($array['start'])) { $_REQUEST['start'] = $array['start']; } else { $_REQUEST['start'] = 0; }
            if (isset($array['limit'])) { $_REQUEST['limit'] = $array['limit']; } else { $_REQUEST['limit'] = 10; }
        }
        echo '{"total":'.json_encode($this->structure_website_model->load_all_count($id_tree)).', "data":'.json_encode($this->structure_website_model->load_all($id_tree)).'}';
    }
    function load_all_title_call($title_call, $paginate='') {
        if ($paginate) {
            $array = unserialize(urldecode($paginate));
            if (isset($array['start'])) { $_REQUEST['start'] = $array['start']; } else { $_REQUEST['start'] = 0; }
            if (isset($array['limit'])) { $_REQUEST['limit'] = $array['limit']; } else { $_REQUEST['limit'] = 10; }
        }
        echo '{"total":'.json_encode($this->structure_website_model->load_all_title_call_count($title_call)).', "data":'.json_encode($this->structure_website_model->load_all_title_call($title_call)).'}';
    }
    function load_filter($id_tree = null, $mode = 'full', $limit = 1) {
        echo '{"success": true, "data":'.json_encode($this->structure_website_model->load_filter($id_tree, $mode, $limit)).'}';
    }
    function load($tree = null, $id = null) {
        $required_model = $tree.'_model';
        $this->ci->load->model('_warehouse/'.$required_model);
        echo '{"success": true, "data":'.json_encode($this->$required_model->load($id)).'}';
    }

    // load ui
    function load_controller($controller = null) {
        if (!$controller) { die(); }
        echo '{"data":'.json_encode($this->structure_website_model->load_controller($controller)).'}';
    }
    function load_template($template = null) {
        if (!$template) { die(); }
        echo '{"data":'.json_encode($this->structure_website_model->load_template($template)).'}';
    }
    function load_node($field = null, $value = null) {
        // loading node in structure_website_tree based on $field dependency
        if (!$field) { die(); }
        echo '{"data":'.json_encode($this->structure_website_model->load_node($field, $value)).'}';
    }

    // add
    function add($id_tree) {
        $result = $this->structure_website_model->add($id_tree);
        echo $result;
    }

    // edit
    function edit($id) {
        $result = $this->structure_website_model->edit($id);
        echo $result;
    }

    // delete
    function delete($id) {
        $result = $this->structure_website_model->delete($id);
        echo 'grid';
    }

    // search article
    function search_article($word = null) {
        echo '{"data":'.json_encode($this->structure_website_model->search_article($word)).'}';
    }

}