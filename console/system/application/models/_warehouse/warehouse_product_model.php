<?php
class Warehouse_Product_Model extends Model {
		
	function Warehouse_Product_Model() {
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
	function load_all_count($id) {
		$this->db->where('id_tree', $id);
		$this->db->from('warehouse_product_relations');
		return $this->db->count_all_results();
	}
	function load_all($id_tree) {
		$this->limit_check();
		$this->db->where('id_tree', $id_tree);
		$this->db->order_by('position');
		$this->db->order_by('id');
		$query = $this->db->get('warehouse_product_relations');
		$result = $query->result();
		$record = array();
		foreach ($result as $item) {
			$values['tree'] = 'warehouse_product';
			$values['relations_id'] = $item->id;
			$values['relations_id_element'] = $item->id_element;
			$query2 = $this->db->query("select * from warehouse_product where id=".$item->id_element);
			foreach ($query2->result() as $item2) {
				$values['id'] = $item2->id;
				$values['title'] = $item2->title;
				$values['header'] = $item2->header;
				$values['price'] = $item2->price;
                $values['image1'] = $item2->image1;
                $values['image2'] = $item2->image2;
                $values['image3'] = $item2->image3;
                $values['date_created'] = $item2->date_created;
				$values['date_last_modified'] = $item2->date_last_modified;
                $values['active'] = $item2->active;
			}
			$record[] = $values;
		}
		return $record;
	}
	function load($id) {
		$this->db->where('id', $id);
		$query = $this->db->get('warehouse_product');
		$record = $query->row_array();
		return $record;	
	}
	
	//
	// add
	//
	function add($id_tree) {
		$record = $_POST;
		$record['date_created'] = date("Y-m-d");
		$record['date_last_modified'] = date("Y-m-d");
		$this->db->insert('warehouse_product', $record); 
		$id_element = $this->db->insert_id();
		//
		$record = array();
		$this->db->select_max('position');
		$this->db->where('id_tree', $id_tree);
		$query = $this->db->get('warehouse_product_relations');
		$result = $query->row_array();
		$position = $result['position'] + 1;
		$record['id_tree'] = $id_tree;
		$record['id_element'] = $id_element;
		$record['position'] = $position;
		$this->db->insert('warehouse_product_relations', $record);
		return '{"success": true}';
	}
	
	//
	// edit
	//
	function edit($id) {
		$record = $_POST;
		$record['date_last_modified'] = date("Y-m-d");
		$this->db->where('id', $id);
		$this->db->update('warehouse_product', $record); 
	}
	
	//
	// delete
	//
	function delete($id) {
		$this->db->where('id_element', $id);
		$query = $this->db->delete('warehouse_product_relations');
		$this->db->where('id', $id);
		$query = $this->db->delete('warehouse_product');
		return '{"success": true}';	
	}
    
    //
	// active set
	//
	function active_set($id, $state) {
		$this->db->where('id', $id);
		$this->db->set('active', $state);
		$this->db->update('warehouse_product'); 
		return '{"success": true}';
	}
	
}
?>