<?php
class Media_Video_Model extends Model {
		
	function Media_Video_Model() {
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
		$this->db->from('media_video_relations');
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
		$query = $this->db->get('media_video_relations');
		$result = $query->result();
		$record = array();
		foreach ($result as $item) {
			$values['tree'] = 'media_video';
			$values['relations_id'] = $item->id;
			$values['relations_id_element'] = $item->id_element;
			$query2 = $this->db->query("select * from media_video where id=".$item->id_element);
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
            if (file_exists($this->path.'/../media/video/'. $values['element_file_name'])) {
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
            //
            // check for proper place of storage
            //
            if (MEDIA_VIDEO_TRANSCODE == 1) {
                //
                // storage is used only when transcoding
                // otherwise file will stay on home server
                //
                if (STORAGE_ACTIVE && STORAGE_VIDEO) {
                if (STORAGE_TYPE == 'S3') {
                    $this->move_to_s3($this->upload_data);
                } 
                //
                // else move to original image location
                //
                } else {
                    $this->move($this->upload_data);
                }
                //
                // lets transcode
                //
                $transcode_data = $this->transcode($this->upload_data);
                // insert information into db basing on transcoded content
                $this->add_save($id_tree, $transcode_data);
            } else {
                //
                // normally, without transcoding we just move a file
                //
                $this->move($this->upload_data);
                $this->add_save($id_tree, $this->upload_data);
            }
			// remove video from tmp
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
		$query = $this->db->get('media_video');
		$result = $query->row();
		@unlink($this->path.'/../media/video/'.$result->video_name);
		// delete db entries
		$this->db->where('id_element', $id);
		$query = $this->db->delete('media_video_relations');
		$this->db->where('id', $id);
		$query = $this->db->delete('media_video');
		return '{"success": true}';
	}
	
	//
	// move
	//
	function move(&$upload_data, $path_tmp='./tmp/', $video_name=NULL) {
		if(!file_exists($this->path.'/../media/video/')) { mkdir($this->path.'/../media/video/'); chmod($this->path."/../media/video/", 0777);  }
		if(!empty($video_name)){
			copy($path_tmp.'/'.$video_name, $this->path.'/../media/video/'. $upload_data['video_name']);
			$upload_data['video_path'] = 'media/video/';
		} else {
			copy($path_tmp.$upload_data['file_name'], $this->path.'/../media/video/'.$upload_data['file_name']);
			$upload_data['file_path'] = 'media/video/';
		}
		chmod($this->path.'/../media/video/'.$upload_data['file_name'], 0777);
	}
    
    //
    // move to s3
    //
    function move_to_s3(&$upload_data, $path = './tmp/', $file_name = NULL) {
        // include s3 sdk
        require_once './services/s3/sdk.class.php';
        // s3 init
		$s3 = new AmazonS3();
        //
        // Upload a partial file
        $response = $s3->create_object('media_video', $upload_data['file_name'], array(
            'fileUpload' => $path.$upload_data['file_name'],
            'acl' => AmazonS3::ACL_PUBLIC
        ));
    }	
	
	//
	// insert informations about video into db
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
		$this->db->insert('media_video', $record); 
		$id_media = $this->db->insert_id();
		//select max in order column for id_tree
		$this->add_save_relation($id_tree, $id_media);
	}
	
	//
	// insert informations about relations video to tree
	//
	function add_save_relation($id_tree, $id_element){
		$record = array();
		$this->db->select_max('position');
		$this->db->where('id_tree', $id_tree);
		$query = $this->db->get('media_video_relations');
		$result = $query->row_array();
		$position = $result['position'] + 1;
		//record insert
		$record['id_tree'] = $id_tree;
		$record['id_element'] = $id_element;
		$record['position'] = $position;
		$this->db->insert('media_video_relations', $record);
	}
    
    //
    // transcode! ( using ffmpeg )
    //
	function transcode(&$upload_data, $path_tmp='./tmp/') {
        $movie_source = $path_tmp.$upload_data['file_name'];
        $movie_target = $this->path.'/../media/video/'. $upload_data['raw_name'] .'.flv';
        //
        $ffmpegPath = "/usr/bin/ffmpeg";
        $ffmpegObj = new ffmpeg_movie($movie_source);
        //
        $srcWidth = $this->makeMultipleTwo($ffmpegObj->getFrameWidth());
        $srcHeight = $this->makeMultipleTwo($ffmpegObj->getFrameHeight());
        $srcFPS = $ffmpegObj->getFrameRate();
        $srcAB = intval($ffmpegObj->getAudioBitRate()/1000);  
        $srcAR = $ffmpegObj->getAudioSampleRate(); 
        //
        exec($ffmpegPath . " -i " . $movie_source . " -an -f flv -s vga -b 700k " . $movie_target, $result);
        //       
        $record = array();
        $record['title'] = $upload_data['raw_name'] .'.flv';
		$record['file_name'] = $upload_data['raw_name'] .'.flv';
		$record['file_type'] = 'video/x-flv';
		$record['file_path'] = $upload_data['file_path'];
		$record['full_path'] = $upload_data['full_path'];
		$record['orig_name'] = $upload_data['raw_name'] .'.flv';
		$record['file_ext'] = '.flv';
		$record['file_size'] = 0;
        $record['result'] = $result;  
        return $record;    
	}
    
    function makeMultipleTwo($value) {
        $sType = gettype($value/2);
        if($sType == "integer") {
            return $value;
        } else {
            return ($value-1);
        }
    }
    
}
?>