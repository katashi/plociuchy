<?php
class Warehouse_Newsletter_Model extends Model {
		
	function Warehouse_Newsletter_Model() {
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
		$this->db->from('warehouse_newsletter_relations');
		return $this->db->count_all_results();
	}
	function load_all($id_tree) {
		$this->limit_check();
		$this->db->where('id_tree', $id_tree);
		$this->db->order_by('position');
		$this->db->order_by('id');
		$query = $this->db->get('warehouse_newsletter_relations');
		$result = $query->result();
		$record = array();
		foreach ($result as $item) {
			$values['tree'] = 'warehouse_newsletter';
			$values['relations_id'] = $item->id;
			$values['relations_id_element'] = $item->id_element;
			$query2 = $this->db->query("select * from warehouse_newsletter where id=".$item->id_element);
			foreach ($query2->result() as $item2) {
				$values['id'] = $item2->id;
				$values['title'] = $item2->title;
				$values['header'] = $item2->header;
				$values['date_created'] = $item2->date_created;
				$values['active'] = $item2->active;
			}
			$record[] = $values;
		}
		return $record;
	}
    function load_content_count($id) {
        $this->db->where('id_newsletter', $id);
		$this->db->from('warehouse_newsletter_content_relations');
		return $this->db->count_all_results();
    }
    function load_content($id) {
        $this->limit_check();
        $this->db->select('warehouse_newsletter_content_relations.id as id');
        $this->db->select('warehouse_newsletter_content_relations.id_content as id_content');
        $this->db->select('warehouse_newsletter_content_relations.type as type');
        $this->db->select('warehouse_newsletter_content_relations.position as position');
        $this->db->where('id_newsletter', $id);
        $this->db->order_by('position');
		$query = $this->db->get('warehouse_newsletter_content_relations');
        $result = $query->result();
		$record = array();
        foreach ($result as $item) {
            $values = array();
            $values['id'] = $item->id;
            $values['id_content'] = $item->id_content;
            $values['type'] = $item->type;
            $values['position'] = $item->position;
            if ($item->type == 'warehouse_article') {
                $query2 = $this->db->query("select * from warehouse_article where id=".$item->id_content);
    			foreach ($query2->result() as $item2) {
    				$values['article_id'] = $item2->id;
    				$values['article_title'] = $item2->title;
    				$values['article_header'] = $item2->header;
    				$values['article_date_created'] = $item2->date_created;
    			}
            }   
            $record[] = $values;
        }
        return $record;
    }
	function load($id) { 
		$this->db->where('id', $id);
		$query = $this->db->get('warehouse_newsletter');
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
		$this->db->insert('warehouse_newsletter', $record); 
		$id_element = $this->db->insert_id();
		//
		$record = array();
		$this->db->select_max('position');
		$this->db->where('id_tree', $id_tree);
		$query = $this->db->get('warehouse_newsletter_relations');
		$result = $query->row_array();
		$position = $result['position'] + 1;
		$record['id_tree'] = $id_tree;
		$record['id_element'] = $id_element;
		$record['position'] = $position;
		$this->db->insert('warehouse_newsletter_relations', $record);
		return '{"success": true}';
	}
	
	//
	// edit
	//
	function edit($id) {
		$record = $_POST;
		$record['date_last_modified'] = date("Y-m-d");
		$this->db->where('id', $id);
		$this->db->update('warehouse_newsletter', $record); 
	}
	
	//
	// delete
	//
	function delete($id) {
		$this->db->where('id_element', $id);
		$query = $this->db->delete('warehouse_newsletter_relations');
		$this->db->where('id', $id);
		$query = $this->db->delete('warehouse_newsletter');
		return '{"success": true}';	
	}
    
    //
	// content bind
	//
	function content_bind() {
	    // select max value from position
        $this->db->select_max('position');
		$query = $this->db->get('warehouse_newsletter_content_relations');
		$result = $query->row_array();
        $position_max = $result['position'] + 1;
        // content bind
		$record = $_POST;
        $record['position'] = $position_max;
		$this->db->insert('warehouse_newsletter_content_relations', $record);
        return '{"success": true}';
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
		$this->db->where('id', $id);
		$query = $this->db->delete('warehouse_newsletter_content_relations');
		return '{"success": true}';	
    }
	
}
?>