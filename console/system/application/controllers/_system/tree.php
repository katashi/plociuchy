<?php
if (!defined('BASEPATH')) die;

class Tree extends Main {

	function Tree($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('_system/tree_model');
	}
    
    //
    // tree create complete
    //
   	function tree_create_complete($table, $parent='0', $level='0') {
   	    echo json_encode($this->tree_model->tree_create_complete($table, $parent, $level));
    }
		
	//
	// tree create
	//
	function tree_create($table, $type='') {
		switch ($type) {
			case 'reorder':
				echo $this->tree_model->reorder($table);
				break;
			case 'reparent':
				echo $this->tree_model->reparent($table);
				break;
			case 'reorder_element':
				echo $this->tree_model->reorder_element($table);
				break;
			case 'reparent_element':
				echo $this->tree_model->reparent_element($table);
				break;
			case 'assign':
				echo $this->tree_model->assign($table);
				break;
			default:
				echo json_encode($this->tree_model->tree_create(0, $table));
				break;
		}
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('');
	}
	
	//
	// add
	//
	function add($table, $flag='') {
	    if ($flag == 1) { $extension = '_'.$table; } else { $extension = ''; }
		$this->ci->smarty->assign('table', $table);
		$this->ci->smarty->display('_system/tree_add'.$extension.'.html');
	}
	
	//
	// add save
	//
	function add_save($table, $pid) {
		$success = $this->tree_model->add_save($table, $pid);	
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
	function delete() {
	}
	
}