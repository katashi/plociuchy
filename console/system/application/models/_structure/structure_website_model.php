<?php
class Structure_Website_Model extends Model {
		
	function Structure_Website_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}

    // limit check
	function limit_check() {
		if (isset($_REQUEST['start']) && isset($_REQUEST['limit'])) { 
			$this->start = $_REQUEST['start'];
			$this->limit = $_REQUEST['limit'];
			$this->db->limit($this->limit, $this->start);
		}
	}

    // load
    function load_all_count($id) {
        $this->db->where('id_tree', $id);
        $this->db->from('structure_website_relations');
        return $this->db->count_all_results();
    }
    function load_all($id_tree) {
        $this->limit_check();
        $this->db->where('id_tree', $id_tree);
        $this->db->order_by('position');
        $this->db->order_by('id');
        $query = $this->db->get('structure_website_relations');
        $result = $query->result();
        $record = array();
        foreach ($result as $item) {
            $values['tree'] = $item->type;
            $values['relations_id'] = $item->id;
            $values['relations_id_element'] = $item->id_element;
            $query2 = $this->db->query("select * from ". $item->type ." where id=".$item->id_element);
            if ($item->type == 'warehouse_article') {
                foreach ($query2->result() as $item2) {
                    //$values['id'] = $item2->id;
                    $values['type'] = 'warehouse_article';
                    $values['title'] = $item2->title;
                    $values['header'] = $item2->header;
                    $values['text'] = $item2->text;
                    $values['tag'] = $item2->tag;
                    $values['image'] = $item2->image;
                    $values['active'] = $item2->active;
                    $values['date_created'] = $item2->date_created;
                    $values['date_last_modified'] = $item2->date_last_modified;
                    $values['meta_title'] = $item2->meta_title;
                    $values['meta_description'] = $item2->meta_description;
                    $values['meta_keywords'] = $item2->meta_keywords;
                }
            }
            $record[] = $values;
        }
        return $record;
    }
    function load_all_title_call_count($title_call) {
        // here is a little bit more complicated
        $this->db->select('id');
        $this->db->where('title_call', $title_call);
        $query = $this->db->get('structure_website_tree');
        $record = $query->row_array();
        // get count from structure_website_relations
        $this->db->where('id_tree', $record['id']);
        $this->db->from('structure_website_relations');
        return $this->db->count_all_results();
    }
    function load_all_title_call($title_call) {
        // here is a little bit more complicated
        $this->db->select('id');
        $this->db->where('title_call', $title_call);
        $query = $this->db->get('structure_website_tree');
        $record = $query->row_array();
        // lets grab data
        $this->limit_check();
        $this->db->where('id_tree', $record['id']);
        $this->db->order_by('position');
        $this->db->order_by('id');
        $query = $this->db->get('structure_website_relations');
        $result = $query->result();
        $record = array();
        foreach ($result as $item) {
            $values['tree'] = $item->type;
            $values['relations_id'] = $item->id;
            $values['relations_id_element'] = $item->id_element;
            $query2 = $this->db->query("select * from ". $item->type ." where id=".$item->id_element);
            if ($item->type == 'warehouse_article') {
                foreach ($query2->result() as $item2) {
                    $values['id'] = $item2->id;
                    $values['title'] = $item2->title;
                    $values['header'] = $item2->header;
                    $values['text'] = $item2->text;
                    $values['tag'] = $item2->tag;
                    $values['image'] = $item2->image;
                    $values['active'] = $item2->active;
                    $values['date_created'] = $item2->date_created;
                    $values['date_last_modified'] = $item2->date_last_modified;
                    $values['meta_title'] = $item2->meta_title;
                    $values['meta_description'] = $item2->meta_description;
                    $values['meta_keywords'] = $item2->meta_keywords;
                }
            }
            $record[] = $values;
        }
        return $record;
    }
    function load_filter($id_tree, $mode, $limit) {
        $this->db->limit($limit);
        $this->db->where('id_tree', $id_tree);
        $query = $this->db->get('structure_website_relations');
        $result = $query->result();
        $record = array();
        foreach ($result as $item) {
            $values['tree'] = $item->type;
            // mode interpretation ( complexity of query )
            switch ($mode) {
                case 'basic': $select = 'id,title,image,date_created'; break;
                case 'full':  $select = '*'; break;
            }
            $this->db->select($select);
            $this->db->where('id', $item->id_element);
            $query2 = $this->db->get($item->type);
            if ($item->type == 'warehouse_article') {
                foreach ($query2->result() as $item2) {
                    foreach ($item2 as $key => $entry) {
                        $values[$key] = $entry;
                    }
                    $values['type'] = $item->type;
                }
            }
            $record[] = $values;
        }
        return $record;
    }
    function load() {

    }

    // load ui
    function load_controller($title_call) {
        $this->db->select('controller');
        $this->db->where('title_call', $title_call);
        $query = $this->db->get('structure_website_tree');
        $record = $query->row_array();
        return $record;
    }
    function load_template($title_call) {
        $this->db->select('template');
        $this->db->where('title_call', $title_call);
        $query = $this->db->get('structure_website_tree');
        $record = $query->row_array();
        return $record;
    }
    function load_node($field, $value) {
        $this->db->where($field, $value);
        $query = $this->db->get('structure_website_tree');
        $record = $query->row_array();
        return $record;
    }

    // add
    function add($id_tree) {
        $record = $_POST;
        $record['date_created'] = date("Y-m-d");
        $record['date_last_modified'] = date("Y-m-d");
        $this->db->insert('structure_website', $record);
        $id_element = $this->db->insert_id();
        //
        $record = array();
        $this->db->select_max('position');
        $this->db->where('id_tree', $id_tree);
        $query = $this->db->get('structure_website_relations');
        $result = $query->row_array();
        $position = $result['position'] + 1;
        $record['id_tree'] = $id_tree;
        $record['id_element'] = $id_element;
        $record['position'] = $position;
        $this->db->insert('structure_website_relations', $record);
        return '{"success": true}';
    }

    // edit
    function edit($id) {
        $record = $_POST;
        $record['date_last_modified'] = date("Y-m-d");
        $this->db->where('id', $id);
        $this->db->update('structure_website', $record);
    }

    // delete
    function delete($id) {
        $this->db->where('id', $id);
        $query = $this->db->delete('structure_website_relations');
        return '{"success": true}';
    }

    // search
    function search_article($word) {
        $query = $this->db->query("select wa.id, wa.title, wa.header, wa.image, swr.id_tree, swr.type, swt.title as node_title, swt.title_call, swt.controller, swt.template from structure_website_relations swr, warehouse_article wa, structure_website_tree swt where swr.id_element=wa.id && swr.id_tree=swt.id && (wa.title like '%".$word."%' || wa.header like '%".$word."%' || wa.text like '%".$word."%') group by title");
        $record = $query->result_array();
        return $record;
    }

}
?>