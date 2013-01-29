<?php
if (!defined('BASEPATH')) die;

class Media_Video extends Main {

	function Media_Video($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->ci->load->model('_media/media_video_model');
	}
	
	//
	// display
	//
	function display($id_tree) {
		$this->ci->smarty->assign('id', $id_tree);
		$this->ci->smarty->display('_media/media_video.html');
	}
	
	//
	// load all
	//
	function load_all($id_tree, $sort='') {
		echo '{"total":'.json_encode($this->media_video_model->load_all_count($id_tree)).', "data":'.json_encode($this->media_video_model->load_all($id_tree, $sort)).'}';
	}
	function load() {
		
	}
	
	//
	// add
	//
	function add($id_tree) {
		$success = $this->media_video_model->add($id_tree);
		echo $success;
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
		$success = $this->media_video_model->delete($id);
		echo 'grid';
	}
	
}