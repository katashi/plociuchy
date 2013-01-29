<?php
class Warehouse_Product_Category_Model extends Model {
		
	function Warehouse_Product_Category_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}
	
	function limit_check() {
		if (isset($_REQUEST['start']) && isset($_REQUEST['limit'])) { 
			$this->start = $_REQUEST['start'];
			$this->limit = $_REQUEST['limit'];
			$this->db->limit($this->limit, $this->start);
		}
	}
	
	//
	// load
	//
	function load_all_count() {
		$this->db->from('warehouse_product_category');
		return $this->db->count_all_results();
	}
	function load_all() {
		$this->limit_check();
		$this->db->order_by('id');
		$query = $this->db->get('warehouse_product_category');
		$record = $query->result_array();
		return $record;	
	}
	function load($id) {
		$this->db->where('id', $id);
		$query = $this->db->get('warehouse_product_category');
		$record = $query->row_array();
		return $record;	
	}
	
	//
	// add
	//
	function add() {
		$record = $_POST;
		$this->db->insert('warehouse_product_category', $record); 
		return '{"success": true}';
	}
	
	//
	// edit
	//
	function edit($id) {
		$record = $_POST;
		$this->db->where('id', $id);
		$this->db->update('warehouse_product_category', $record); 
	}
	
	//
	// delete
	//
	function delete($id) {
		$this->db->where('id', $id);
		$query = $this->db->delete('warehouse_product_category');
		return '{"success": true}';	
	}
    
    //
	// active set
	//
	function active_set($id, $state) {
		$this->db->where('id', $id);
		$this->db->set('active', $state);
		$this->db->update('warehouse_product_category'); 
		return '{"success": true}';
	}
	
}
?>