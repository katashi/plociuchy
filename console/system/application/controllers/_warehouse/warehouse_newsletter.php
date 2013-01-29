<?php
if (!defined('BASEPATH')) die;

class Warehouse_Newsletter extends Main {
	
	function Warehouse_Newsletter($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('warehouse_newsletter_model');
	}
	
	//
	// display
	//
	function display($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('warehouse_newsletter/warehouse_newsletter.html');
	}
	function display_add($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('warehouse_newsletter/warehouse_newsletter_add.html');
	}
	function display_edit($id) {
		$this->ci->smarty->assign('id', $id);
		$this->ci->smarty->display('warehouse_newsletter/warehouse_newsletter_edit.html');
	}
	
	//
	// load
	//
	function load_all($id_tree) {
		echo '{"total":'.json_encode($this->warehouse_newsletter_model->load_all_count($id_tree)).', "data":'.json_encode($this->warehouse_newsletter_model->load_all($id_tree)).'}';
	}
	function load_content($id) {
        echo '{"total":'.json_encode($this->warehouse_newsletter_model->load_content_count($id)).', "data":'.json_encode($this->warehouse_newsletter_model->load_content($id)).'}';
    }
    function load($id) {
		echo '{"success": true, "data":'.json_encode($this->warehouse_newsletter_model->load($id)).'}';
	}
    
	
	//
	// add
	//
	function add($id_tree) {
		$result = $this->warehouse_newsletter_model->add($id_tree);
		echo $result;
	}
	
	//
	// edit
	//
	function edit($id) {
		$result = $this->warehouse_newsletter_model->edit($id);
		echo $result;
	}
	
	//
	// delete
	//
	function delete($id) {
		$result = $this->warehouse_newsletter_model->delete($id);
		echo 'grid';
	}
    
    //
	// content bind
	//
	function content_bind() {
		$result = $this->warehouse_newsletter_model->content_bind();
		echo $result;
	}
	
	//
	// content unbind
	//
	function content_unbind() {
		
	}
	
	//
	// content order change
	//
	function content_order_change() {
		
	}
    
    //
    // content delete
    //
    function content_delete($id) {
        $result = $this->warehouse_newsletter_model->content_delete($id);
		echo 'grid';
    }
	
}