<?php
class Tree_Model extends Model {
	
	function Tree_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}
    
    //
	// tree create (complete - all nodes)
	//
	function tree_create_complete($table, $parent, $level) {
        if (!isset($table)) { die(); }
        $tree = $this->tree_create_complete_children($table, $parent, $level);
        return $tree;
	}
    
    //
    // tree create nodes (complete)
    //
    function tree_create_complete_children($table, $parent, $level) {
		$this->db->where('pid', $parent);
		$this->db->orderby('position');
		$query = $this->db->get($table.'_tree');
        $record = $query->result_array();
        $nodes = array();
		foreach ($record as $item) {
			$path = array();
			$path['id']	= $item['id'];
			$path['level'] = $level;
			$path['title'] = $item['title'];
            if (isset($item['title_call'])) { $path['title_call'] = $item['title_call']; }
            $path['children'] = $this->tree_create_complete_children($table, $item['id'], $level+1);
			//
			$nodes[] = $path;
		}
        return $nodes;
    }
	
	//
	// tree create (node by node)
	//
	function tree_create($_node, $table) {
		if (isset($_REQUEST['level'])) { $level = $_REQUEST['level']; } else { $level = '0'; }
		if (isset($_REQUEST['node']) && $_REQUEST['node'] >= 0) { $node = $_REQUEST['node']; } else { $node = $_node; }
		//
		if ($level <= 0) {
			$path['id']	= '0';
			$path['id_element']	= '0';
			$path['level'] = $level;
			$path['text'] = 'start';
			$path['genre'] = 'tree';
			$path['tree'] = $table;
			$path['children'] = $this->tree_create_children($node, $level+1, $table);
			$tree[] = $path;
			return $tree;
		} else {
			$tree = $this->tree_create_children($node, $level, $table);
			return $tree;
		}
	}
	
	//
	// create tree - recursive through elements
	//
	function tree_create_children($parent, $level, $table) {
		$this->db->where('pid', $parent);
		$this->db->orderby('position');
		$query = $this->db->get($table.'_tree');
		$result = $query->result();
		$nodes = array();
		foreach ($result as $item) {
			$path = array();
			$path['genre'] = 'tree';
			$path['id']	= $item->id;
			$path['id_element']	= $item->id;
			$path['level'] = $level;
			$path['text'] = $item->title .' (id='.$item->id.')';
			$path['type'] = $table;
			$path['tree'] = $table;
			//
			$nodes[] = $path;
		}
		//
		//
        //
        if (isset($_REQUEST['node'])) { $id_tree = $_REQUEST['node']; } else { $id_tree = 0; }
		//
		// get from media_file
		//
		if ($table == 'media_file') {
			$query = $this->db->query("SELECT
			`media_file_relations`.`id` as `mri`,
			`media_file`.`id`,
			`media_file`.`title`,
			`media_file`.`file_name`,
			`media_file`.`file_ext`,
            `media_file`.`orig_name`
			FROM
			`media_file_relations`
			Left Join `media_file` ON `media_file`.`id` = `media_file_relations`.`id_element`
			WHERE
			`media_file_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`media_file_relations`.`position`");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = substr($item->file_ext, 1);
				$record['file_name'] = $item->file_name;
				$record['file_ext'] = $item->file_ext;
                $record['orig_name'] = $item->orig_name;
				$nodes[] = $record;
			}
		}
		//
		// get from media_image
		//
		if ($table == 'media_image') {
			$query = $this->db->query("SELECT
			`media_image_relations`.`id` as `mri`,
			`media_image`.`id`,
			`media_image`.`title`,
			`media_image`.`file_name`,
			`media_image`.`image_width`,
			`media_image`.`image_height`,
			`media_image`.`is_image`,
			`media_image`.`file_ext`,
            `media_image`.`orig_name`
			FROM
			`media_image_relations`
			Left Join `media_image` ON `media_image`.`id` = `media_image_relations`.`id_element`
			WHERE
			`media_image_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`media_image_relations`.`position`");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = substr($item->file_ext, 1);
				$record['file_name'] = $item->file_name;
				$record['file_ext'] = $item->file_ext;
                $record['orig_name'] = $item->orig_name;
				$record['image_width'] = $item->image_width;
				$record['image_height'] = $item->image_height;
				$record['is_image'] = $item->is_image;
				$nodes[] = $record;
			}
		}
		//
		// get from media_video
		//
		if ($table == 'media_video') {
			$query = $this->db->query("SELECT
			`media_video_relations`.`id` as `mri`,
			`media_video`.`id`,
			`media_video`.`title`,
			`media_video`.`file_name`,
			`media_video`.`file_ext`,
            `media_video`.`orig_name`
			FROM
			`media_video_relations`
			Left Join `media_video` ON `media_video`.`id` = `media_video_relations`.`id_element`
			WHERE
			`media_video_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`media_video_relations`.`position`");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = substr($item->file_ext, 1);
				$record['file_name'] = $item->file_name;
				$record['file_ext'] = $item->file_ext;
                $record['orig_name'] = $item->orig_name;
				$nodes[] = $record;
			}
		}
		//
		// get from structure website
		//
		if ($table == 'structure_website') {
			$query = $this->db->query("select * from ".$table."_relations where id_tree=".$id_tree." order by ".$table."_relations.position");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->id;
				$query2 = $this->db->query("select * from ".$item->type." where id=".$item->id_element);
				foreach ($query2->result() as $item2) {
					$record['tree'] = $table;
					$record['id'] = 'e'.$item->id;
					$record['id_element'] = $item2->id;
					$record['text'] = $item2->title .' (id='.$item->id.')';
					$record['type'] = $item->type;
					$record['genre'] = 'element';
					$record['leaf'] = true;
					$record['iconCls'] = $item->type;
					$nodes[] = $record;
				}
			}
		}
		//
		// get from warehouse article
		//
		if ($table == 'warehouse_article') {
			$query = $this->db->query("SELECT
			`warehouse_article_relations`.`id` as `mri`,
			`warehouse_article`.`id`,
			`warehouse_article`.`title`
			FROM
			`warehouse_article_relations`
			Left Join `warehouse_article` ON `warehouse_article`.`id` = `warehouse_article_relations`.`id_element`
			WHERE
			`warehouse_article_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`warehouse_article_relations`.`position`");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = 'page';
				$nodes[] = $record;
			}
		}
        //
		// get from warehouse gallery
		//
		if ($table == 'warehouse_gallery') {
			$query = $this->db->query("SELECT
			`warehouse_gallery_relations`.`id` as `mri`,
			`warehouse_gallery`.`id`,
			`warehouse_gallery`.`title`
			FROM
			`warehouse_gallery_relations`
			Left Join `warehouse_gallery` ON `warehouse_gallery`.`id` = `warehouse_gallery_relations`.`id_element`
			WHERE
			`warehouse_gallery_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`warehouse_gallery_relations`.`position`");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = 'page';
				$nodes[] = $record;
			}
		}
        //
		// get from warehouse newsletter
		//
		if ($table == 'warehouse_newsletter') {
			$query = $this->db->query("SELECT
			`warehouse_newsletter_relations`.`id` as `mri`,
			`warehouse_newsletter`.`id`,
			`warehouse_newsletter`.`title`
			FROM
			`warehouse_newsletter_relations`
			Left Join `warehouse_newsletter` ON `warehouse_newsletter`.`id` = `warehouse_newsletter_relations`.`id_element`
			WHERE
			`warehouse_newsletter_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`warehouse_newsletter_relations`.`position` ASC");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = 'page';
				$nodes[] = $record;
			}
		}
        //
		// get from warehouse newsletter
		//
		if ($table == 'warehouse_product') {
			$query = $this->db->query("SELECT
			`warehouse_product_relations`.`id` as `mri`,
			`warehouse_product`.`id`,
			`warehouse_product`.`title`
			FROM
			`warehouse_product_relations`
			Left Join `warehouse_product` ON `warehouse_product`.`id` = `warehouse_product_relations`.`id_element`
			WHERE
			`warehouse_product_relations`.`id_tree` =  '".$id_tree."'
			ORDER BY
			`warehouse_product_relations`.`position` ASC");
			foreach ($query->result() as $item) {
				$record = array();
				$record['id_relations'] = $item->mri;
				$record['tree'] = $table;
				$record['id'] = 'e'.$item->id;
				$record['id_element'] = $item->id;
				$record['text'] = $item->title .' (id='.$item->id.')';
				$record['type'] = $table;
				$record['genre'] = 'element';
				$record['leaf'] = true;
				$record['iconCls'] = 'page';
				$nodes[] = $record;
			}
		}
		return $nodes;
	}
	
	//
	// reorder tree node
	//
	function reorder($table) {
		// delta is the difference in position (1 = next node, -1 = previous node)
		$delta = $_REQUEST['delta'];
		$node = $_REQUEST['node'];
		$table .= '_tree';
		if ($delta > 0) {
			$this->move_down($table, $node, abs($delta));
		} elseif ($delta < 0) {
			$this->move_up($table, $node, abs($delta));
		}
		return '1';
	}
	
	//
	// reparent tree node
	//
	function reparent($table){
		$node = $_REQUEST['node'];
		$parent = $_REQUEST['parent'];
		$position = $_REQUEST['position'];
		$table .= '_tree';
		$this->move_down($table, $node, 0);
		$count = $this->child_count($table, $parent);
		$sql = "update ".$table." set pid=".$parent.", ".$table.".position=".($count+1)." where id=".$node;
		$this->db->query($sql);
		if ($position == 0){
			$this->move_up($table, $node, 0);
		} else {
			$delta = $count-$position;
			if ($delta > 0){
				$this->move_up($table, $node, $delta);
			}
		}
		return '1';
	}

	//
	// move up
	//
	function move_up($table, $id, $delta) {
		$sql = "select * from ".$table." where id=".$id;
		$query = $this->db->query($sql);
		$item = $query->row();
		if ($delta == 0) {
			$sql="update ".$table." set ".$table.".position=".$table.".position+1 where pid=".$item->pid." && ".$table.".position<=".$item->position;
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=1 where id=$id";
			$this->db->query($sql);
		} else {
			$sql="update ".$table." set ".$table.".position=".$table.".position+1 where pid=".$item->pid." && ".$table.".position>=".($item->position-$delta)." && ".$table.".position<=".$item->position;
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=".($item->position-$delta)." where id=".$id;
			$this->db->query($sql);
		}
	}
	
	//
	// move down
	//
	function move_down($table, $id, $delta) {
		$sql = "select * from ".$table." where id=".$id;
		$query = $this->db->query($sql);
		$item = $query->row();
		if ($delta == 0) {
			$sql="update ".$table." set ".$table.".position=".$table.".position-1 where pid=".$item->pid." && ".$table.".position>".$item->position;
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=1 where id=".$id;
			$this->db->query($sql);
		} else {
			$sql="update ".$table." set ".$table.".position=".$table.".position-1 where pid=".$item->pid." && ".$table.".position>=".$item->position." and ".$table.".position<=".($item->position + $delta);
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=".($item->position + $delta)." where id=".$id;
			$this->db->query($sql);
		}
	}
	
	//
	// child count
	//
	function child_count($table, $parent) {
		$sql = "select count(*) as count from ".$table." where pid=".$parent;
		$query = $this->ci->db->query($sql);
		$result = $query->row();
		return $result->count;
	}
	
	//
	// reorder element
	//
	function reorder_element($table) {
		// delta is the difference in position (1 = next node, -1 = previous node)
		$node = $_REQUEST['node'];
		$delta = $_REQUEST['delta'];
		if ($delta > 0) {
			$this->move_down_element($table, $node, abs($delta));
		} elseif ($delta < 0) {
			$this->move_up_element($table, $node, abs($delta));
		}
		return '1';
	}
	
	//
	// reparent element
	//
	function reparent_element($table){
		$node = $_REQUEST['node'];
		$node_structure = $_REQUEST['node_structure'];
		$parent = $_REQUEST['parent'];
		$position = $_REQUEST['position'];
		$this->move_down_element($table, $node, 0);
		$count = $this->child_count_element($table, $parent);
		$sql = "update ".$table."_relations set id_tree=".$parent.", position=".($count+1)." where id=".$node_structure;
		$this->db->query($sql);
		if ($position == 0){
			$this->move_up_element($table, $node, 0);
		} else {
			$delta = $count-$position;
			if ($delta > 0){
				$this->move_up_element($table, $node, $delta);
			}
		}
		return '1';
	}
	
	//
	// move up element
	//
	function move_up_element($table, $id, $delta) {
		$table .= '_relations';
		$sql = "select * from ".$table." where id_element=".$id;
		$query = $this->db->query($sql);
		$item = $query->row();
		if ($delta == 0) {
			$sql="update ".$table." set ".$table.".position=".$table.".position+1 where id_tree=".$item->id_tree." && ".$table.".position<=".$item->position;
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=1 where id=$id";
			$this->db->query($sql);
		} else {
			$sql="update ".$table." set ".$table.".position=".$table.".position+1 where id_tree=".$item->id_tree." && ".$table.".position>=".($item->position-$delta)." && ".$table.".position<=".$item->position;
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=".($item->position-$delta)." where id_element=".$id;
			$this->db->query($sql);
		}
	}
	
	//
	// move down element
	//
	function move_down_element($table, $id, $delta) {
		$table .= '_relations';
		$sql = "select * from ".$table." where id_element=".$id;
		$query = $this->db->query($sql);
		$item = $query->row();
		if ($delta == 0) {
			$sql="update ".$table." set ".$table.".position=".$table.".position-1 where id_tree=".$item->id_tree." && ".$table.".position>".$item->position;
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=1 where id=".$id;
			$this->db->query($sql);
		} else {
			$sql="update ".$table." set ".$table.".position=".$table.".position-1 where id_tree=".$item->id_tree." && ".$table.".position>=".$item->position." and ".$table.".position<=".($item->position + $delta);
			$this->db->query($sql);
			$sql="update ".$table." set ".$table.".position=".($item->position + $delta)." where id_element=".$id;
			$this->db->query($sql);
		}
	}
	
	//
	// child element count
	//
	function child_count_element($table, $parent) {
		$sql = "select count(*) as count from ".$table."_relations where id_tree=".$parent;
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->count;
	}
    
    //
    // assign ( for an assigment between trees and panels )
    //
    function assign($table) {
        $table .= '_relations';
        // first we need to define next position
        $this->db->select_max('position');
		$this->db->where('id_tree', $_POST['id_tree']);
		$query = $this->db->get($table);
		$record = $query->row_array();
        $position = $record['position'] + 1;
        // now, lets finally put it together
        $record = array();
		$record['id_tree'] = $_POST['id_tree'];
		$record['id_element'] = $_POST['id_element'];
        if ($table == 'structure_website_relations') { $record['type'] = $_POST['type']; } // its an exception, but good one! :)
		$record['position'] = $position;
		$this->ci->db->insert($table, $record);
        return '{success:true}';
    }
	
	//
	// add save
	//
	function add_save($table, $pid) {
		//select max in order column
		$this->db->select_max('position');
		$this->db->where('pid', $pid);
		$query = $this->db->get($table.'_tree');
		$result = $query->result();
		foreach ($result as $item) { $record['position'] = $item->position + 1; }
		//
		$record['pid'] = $pid;
		$record['title'] = $_REQUEST['title'];
        //
        // if we deal with structure_website
        //
        if ($table == 'structure_website') {
            $record['title_call'] = $_REQUEST['title_call'];
            $record['controller'] = $_REQUEST['controller'];
            $record['template'] = $_REQUEST['template'];
            $record['meta_title'] = $_REQUEST['meta_title'];
            $record['meta_description'] = $_REQUEST['meta_description'];
            $record['meta_keywords'] = $_REQUEST['meta_keywords'];
        }
        //
		$success = $this->db->insert($table.'_tree', $record);
		return '{success:true}';
	}

}
?>