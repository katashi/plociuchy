<?php
class Product_Dict_Category_Model extends Main_Model {

    function Product_Dict_Category_Model() {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_product_dict_category';
    }

    // filter check
    function filter_check() {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $this->db->like('title', $_REQUEST['query']);
        }
    }

    // load
    function load_all_count() {
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }
    function load_all() {
        $this->limit_check();
        $this->filter_check();
        $this->sort_check();
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }
    function load_all_letters() {
        $this->limit_check();
        $this->filter_check();
        $this->sort_check();
        $this->db->select(" * , SUBSTRING(`title`,1,1) as letter" , false);
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }

    function load_all_user_count($id = null) {
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }
    function load_all_user($id) {
        $this->limit_check();
        $this->db->where('id', $id);
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }
    function load($id,$where = 'id') {
        $this->db->where($where, $id);
        $query = $this->db->get($this->table_name);
        $record = $query->row_array();
        return $record;
    }

    // add
    function add() {
        $record = $_POST;
        $this->db->insert($this->table_name, $record);
        return 1;
    }

    // edit
    function edit($id) {
        $record = $_POST;
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $record);
        return 1;
    }

    // delete
    function delete($id = null) {
        $this->db->where('id', $id);
        $this->db->delete($this->table_name);
        return 1;
    }

    function load_all_no_dodatki() {
        $this->limit_check();
        $this->filter_check();
        $this->sort_check();
        $this->db->where_not_in('id',array(1,2,3));
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }
    function load_all_dodatki() {
        $this->limit_check();
        $this->filter_check();
        $this->sort_check();
        $this->db->where_in('id',array(1,2,3));
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }

    function load_all_no_dodatki_count() {
        $this->limit_check();
        $this->filter_check();
        $this->sort_check();
        $this->db->where_not_in('id',array(1,2,3));
        $this->db->get($this->table_name);
        $record = $this->db->count_all_results();
        return $record;
    }
    function load_all_dodatki_count() {
        $this->limit_check();
        $this->filter_check();
        $this->sort_check();
        $this->db->where_in('id',array(1,2,3));
        $this->db->get($this->table_name);
        return $this->db->count_all_results();
    }
}
?>