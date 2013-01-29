<?php
if (!defined('BASEPATH')) die;

class Data_Import extends Main {

	function Data_Import($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('data_import_model');
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('data/import/import.html');
	}

	//
	// load all
	//
	function load_all() {
	}	
	
	//
	// add
	//
	function add() {
	}
	
	//
	// edit
	//
	function edit() {	
	}

	//
	// delete
	//
	function delete() {
	}

	//
	// delete all
	//
	function delete_all() {
	}

    //
    // lets load name of db tables
    //
    function load_all_database_table() {
        echo '{"total": false, "data":'.json_encode($this->data_import_model->load_all_database_table()).'}';
    }
	
    //
	// import
	//
	function import() {
	    // security check for table existence
        if (!isset($_REQUEST['table'])) { die; }
		//file handling - lets open csv file
		$tmp_name = $_FILES['data']['tmp_name'];
		$file_size = $_FILES['data']['size'];
		$fp = fopen($tmp_name, 'r');
		$content = fread($fp, $file_size);
		$content = addslashes($content);
		fclose($fp);
        //lets assign selected table
        $table = $_REQUEST['table'];
        //lets define array
		$record = explode("\n", $content);
        // go for import
        $this->data_import_model->import($table, $record);
        // echo
		echo '{"success": true}';
	}

}