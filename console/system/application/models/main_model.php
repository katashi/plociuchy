<?php
class Main_Model extends Model {
		
	function Main_Model() {
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

}
?>