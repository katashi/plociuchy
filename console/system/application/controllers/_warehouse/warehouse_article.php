<?php
if (!defined('BASEPATH')) die;

class Warehouse_Article extends Main {
	
	function Warehouse_Article($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('_warehouse/warehouse_article_model');
	}
	
	//
	// display
	//
	function display($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('_warehouse/warehouse_article.html');
	}
	function display_add($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('_warehouse/warehouse_article_add.html');
	}
	function display_edit($id) {
		$this->ci->smarty->assign('id', $id);
		$this->ci->smarty->display('_warehouse/warehouse_article_edit.html');
	}
	
	//
	// load
	//
	function load_all($id_tree) {
		echo '{"total":'.json_encode($this->warehouse_article_model->load_all_count($id_tree)).', "data":'.json_encode($this->warehouse_article_model->load_all($id_tree)).'}';
	}
	function load($id) {
		echo '{"success": true, "data":'.json_encode($this->warehouse_article_model->load($id)).'}';
	}
	
	//
	// add
	//
	function add($id_tree) {
		$result = $this->warehouse_article_model->add($id_tree);
		echo $result;
	}
	
	//
	// edit
	//
	function edit($id) {
		$result = $this->warehouse_article_model->edit($id);
		echo $result;
	}
	
	//
	// delete
	//
	function delete($id) {
		$result = $this->warehouse_article_model->delete($id);
		echo 'grid';
	}
	
}