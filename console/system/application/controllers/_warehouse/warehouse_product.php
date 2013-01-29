<?php
if (!defined('BASEPATH')) die;

class Warehouse_Product extends Main {
	
	function Warehouse_Product($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('warehouse_product_model');
	}
	
	//
	// display
	//
	function display($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('warehouse_product/warehouse_product.html');
	}
	function display_add($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('warehouse_product/warehouse_product_add.html');
	}
	function display_edit($id) {
		$this->ci->smarty->assign('id', $id);
		$this->ci->smarty->display('warehouse_product/warehouse_product_edit.html');
	}
	
	//
	// load
	//
	function load_all($id_tree) {
		echo '{"total":'.json_encode($this->warehouse_product_model->load_all_count($id_tree)).', "data":'.json_encode($this->warehouse_product_model->load_all($id_tree)).'}';
	}
	function load($id) {
		echo '{"success": true, "data":'.json_encode($this->warehouse_product_model->load($id)).'}';
	}
	
	//
	// add
	//
	function add($id_tree) {
		$result = $this->warehouse_product_model->add($id_tree);
		echo $result;
	}
	
	//
	// edit
	//
	function edit($id) {
		$result = $this->warehouse_product_model->edit($id);
		echo $result;
	}
	
	//
	// delete
	//
	function delete($id) {
		$result = $this->warehouse_product_model->delete($id);
		echo 'grid';
	}
    
    //
	// active set
	//
	function active_set($id, $state) {
		$result = $this->warehouse_product_model->active_set($id, $state);
		echo 'grid';
	}
	
}