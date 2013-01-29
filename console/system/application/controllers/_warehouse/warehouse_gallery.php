<?php
if (!defined('BASEPATH')) die;

class Warehouse_Gallery extends Main {
	
	function Warehouse_Gallery($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('warehouse_gallery_model');
	}
	
	//
	// display
	//
	function display($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('warehouse_gallery/warehouse_gallery.html');
	}
	function display_add($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('warehouse_gallery/warehouse_gallery_add.html');
	}
	function display_edit($id) {
		$this->ci->smarty->assign('id', $id);
		$this->ci->smarty->display('warehouse_gallery/warehouse_gallery_edit.html');
	}
	
	//
	// load
	//
	function load_all($id_tree) {
		echo '{"total":'.json_encode($this->warehouse_gallery_model->load_all_count($id_tree)).', "data":'.json_encode($this->warehouse_gallery_model->load_all($id_tree)).'}';
	}
    function load_content($id) {
        echo '{"total":'.json_encode($this->warehouse_gallery_model->load_content_count($id)).', "data":'.json_encode($this->warehouse_gallery_model->load_content($id)).'}';
    }
	function load($id) {
		echo '{"success": true, "data":'.json_encode($this->warehouse_gallery_model->load($id)).'}';
	}
	
	//
	// add
	//
	function add($id_tree) {
		$result = $this->warehouse_gallery_model->add($id_tree);
		echo $result;
	}
	
	//
	// edit
	//
	function edit($id) {
		$result = $this->warehouse_gallery_model->edit($id);
		echo $result;
	}
	
	//
	// delete
	//
	function delete($id) {
		$result = $this->warehouse_gallery_model->delete($id);
		echo 'grid';
	}
    
    //
	// content bind
	//
	function content_bind() {
		$result = $this->warehouse_gallery_model->content_bind();
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
        $result = $this->warehouse_gallery_model->content_delete($id);
		echo 'grid';
    }
	
}