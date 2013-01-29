<?php
if (!defined('BASEPATH')) die;

class Warehouse_Product_Category extends Main {
	
	function Warehouse_Product_Category($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('warehouse_product_category_model');
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('warehouse_product/warehouse_product_category.html');
	}
	function display_add() {
		$this->ci->smarty->display('warehouse_product/warehouse_product_category_add.html');
	}
	function display_edit($id) {
		$this->ci->smarty->assign('id', $id);
		$this->ci->smarty->display('warehouse_product/warehouse_product_category_edit.html');
	}
	
	//
	// load
	//
	function load_all() {
		echo '{"total":'.json_encode($this->warehouse_product_category_model->load_all_count()).', "data":'.json_encode($this->warehouse_product_category_model->load_all()).'}';
	}
	function load($id) {
		echo '{"success": true, "data":'.json_encode($this->warehouse_product_category_model->load($id)).'}';
	}
	
	//
	// add
	//
	function add() {
		$result = $this->warehouse_product_category_model->add();
		echo $result;
	}
	
	//
	// edit
	//
	function edit($id) {
		$result = $this->warehouse_product_category_model->edit($id);
		echo $result;
	}
	
	//
	// delete
	//
	function delete($id) {
		$result = $this->warehouse_product_category_model->delete($id);
		echo 'grid';
	}
    
    //
	// active set
	//
	function active_set($id, $state) {
		$result = $this->warehouse_product_category_model->active_set($id, $state);
		echo 'grid';
	}
	
}