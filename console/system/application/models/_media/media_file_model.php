<?php
class Media_File_Model extends Model {
		
	function Media_File_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
        //
        $this->path = getcwd();
	}
	
	//
	// limit check
	//
	function limit_check() {
		if (isset($_REQUEST['start']) && isset($_REQUEST['limit'])) { 
			$this->start = $_REQUEST['start'];
			$this->limit = $_REQUEST['limit'];
			$this->db->limit($this->limit, $this->start);
		}
	}
	
	//
	// load all count
	//
	function load_all_count($id) {
		$this->db->where('id_tree', $id);
		$this->db->from('media_file_relations');
		return $this->db->count_all_results();
	}
	
	//
	// load all
	//
	function load_all($id_tree, $sort) {
		$this->limit_check();
		$this->db->where('id_tree', $id_tree);
		$this->db->order_by('position '.$sort);
		$this->db->order_by('id');
		$query = $this->db->get('media_file_relations');
		$result = $query->result();
		$record = array();
		foreach ($result as $item) {
			$values['tree'] = 'media_file';
			$values['relations_id'] = $item->id;
			$values['relations_id_element'] = $item->id_element;
			$query2 = $this->db->query("select * from media_file where id=".$item->id_element);
			foreach ($query2->result() as $item2) {
				$values['element_id'] = $item2->id;
				$values['element_title'] = $item2->title;
				$values['element_file_name'] = $item2->file_name;
				$values['element_file_type'] = $item2->file_type;
				$values['element_file_path'] = $item2->file_type;
				$values['element_full_path'] = $item2->full_path;
				$values['element_orig_name'] = $item2->orig_name;
				$values['element_file_ext'] = $item2->file_ext;
				$values['element_file_size'] = $item2->file_size;
				$values['element_date']	= $item2->date;
			}
            if (file_exists($this->path.'/../media/file/'. $values['element_file_name'])) {
                $record[] = $values;
            }
		}
		return $record;
	}
	
	//
	// load
	//
	function load() {
		
	}
	
	//
	// add
	//
	function add($id_tree) {
		// additional data related to curl
		if (isset($_POST['name'])) { $_FILES['userfile']['name'] = $_POST['name']; }
		if (isset($_POST['type'])) { $_FILES['userfile']['type'] = $_POST['type']; }
		// upload config definition
		$config_upload['upload_path'] = './tmp'; 
		// load library
		$this->load->library('upload', $config_upload);
		// upload
		if ($this->upload->do_upload()) {
			$this->upload_data = $this->upload->data();
			// move file
			$this->move($this->upload_data);
			// insert information into db
			$this->add_save($id_tree, $this->upload_data);
			// remove file from tmp
			@unlink('./tmp/'.$this->upload_data['file_name']);
			//
			return '{"success": true}';
		} else {
			return '{"success": false}';
		}
	}
	
	//
	// edit
	//
	function edit() {
		
	}
	
	//
	// delete
	//
	function delete($id) {
		// delete physical elements
		$this->db->where('id', $id);
		$query = $this->db->get('media_file');
		$result = $query->row();
		@unlink($this->path.'/../media/file/'.$result->file_name);
		// delete db entries
		$this->db->where('id_element', $id);
		$query = $this->db->delete('media_file_relations');
		$this->db->where('id', $id);
		$query = $this->db->delete('media_file');
		return '{"success": true}';
	}
	
	//
	// move
	//
	function move(&$upload_data, $path_tmp='./tmp/', $file_name=NULL) {
		if(!file_exists($this->path.'/../media/file/')) { mkdir($this->path.'/../media/file/'); chmod($this->path."/../media/file/", 0777);  }
		if(!empty($file_name)){
			copy($path_tmp.'/'.$file_name, $this->path.'/../media/file/'. $upload_data['file_name']);
			$upload_data['file_path'] = 'media/file/';
		} else {
			copy($path_tmp.$upload_data['file_name'], $this->path.'/../media/file/'.$upload_data['file_name']);
			$upload_data['file_path'] = 'media/file/';
		}
		chmod($this->path.'/../media/file/'.$upload_data['file_name'], 0777);
	}	
	
	//
	// insert informations about file into db
	//
	function add_save($id_tree, $data) {		
		//data insert
		$record['title'] = $data['orig_name'];
		$record['file_name'] = $data['file_name'];
		$record['file_type'] = $data['file_type'];
		$record['file_path'] = $data['file_path'];
		$record['full_path'] = $data['full_path'];
		$record['orig_name'] = $data['orig_name'];
		$record['file_ext'] = $data['file_ext'];
		$record['file_size'] = $data['file_size'];
		$record['date'] = date("Y-m-d H:i:s");
		//insert record to media
		$this->db->insert('media_file', $record); 
		$id_media = $this->db->insert_id();
		//select max in order column for id_tree
		$this->add_save_relation($id_tree, $id_media);
	}
	
	//
	// insert informations about relations file to tree
	//
	function add_save_relation($id_tree, $id_element){
		$record = array();
		$this->db->select_max('position');
		$this->db->where('id_tree', $id_tree);
		$query = $this->db->get('media_file_relations');
		$result = $query->row_array();
		$position = $result['position'] + 1;
		//record insert
		$record['id_tree'] = $id_tree;
		$record['id_element'] = $id_element;
		$record['position'] = $position;
		$this->db->insert('media_file_relations', $record);
	}
	
}
?>