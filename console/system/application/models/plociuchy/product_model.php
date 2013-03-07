<?php
class Product_Model extends Main_Model {

    function Product_Model() {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_product';
    }

    // filter check
    function filter_check() {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $this->db->like('text1', $_REQUEST['query']);
            $this->db->or_like('text2', $_REQUEST['query']);
            $this->db->or_like('text3', $_REQUEST['query']);
        }
    }
    // filter check
    function filter_check_serach() {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $this->db->like('concat(text1,text2,text3) ', $_REQUEST['query']);
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

    function load($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table_name);
        $record = $query->row_array();
        return $record;
    }

    function load_promote() {
        $this->db->where('promote', 1);
        $query = $this->db->get($this->table_name);
        $record = $query->row_array();
        return $record;
    }

    function load_all_product_count($id = null, $where = 'id') {
        $this->limit_check();
        $this->filter_check();
        if (isset($id)) {
            $this->db->where($where, $id);
        }
        $this->db->from($this->table_name);
        return $record = $this->db->count_all_results();
    }

    function load_all_active_product($id = null, $where = 'id') {
        $this->limit_check();
        $this->filter_check_serach();
        if (isset($id)) {
            $this->db->where($where, $id);
        }
        $this->db->where('active_to >=', date("Y-m-d H:i:s"));
        $this->db->where('reject =', 1 );
        $this->db->where('active =', 1 );
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record = $query->result_array();
    }
    function load_all_active_product_count($id = null, $where = 'id') {
        $this->limit_check();
        $this->filter_check_serach();
        if (isset($id)) {
            $this->db->where($where, $id);
        }
        $this->db->where('active_to >=', date("Y-m-d H:i:s"));
        $this->db->where('reject =', 1 );
        $this->db->where('active =', 1);
        $this->db->from($this->table_name);
        return $record = $this->db->count_all_results();
    }

    function load_all_product($id = null, $where = 'id') {
        $this->limit_check();
        $this->filter_check();
        if (isset($id)) {
            $this->db->where($where, $id);
        }
        $query = $this->db->get($this->table_name);
        return $record = $query->result_array();
    }

    function add() {
        $record = $_POST;
        $record['date_added'] = date("Y-m-d H:i:s");
        $this->db->insert($this->table_name, $record);
        return $this->db->insert_id();
    }

    // edit
    function edit($id) {
        $record = $_POST;
        $record['date_last_modified'] = date("Y-m-d H:i:s");
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

    // active
    function active_set($id, $state) {
        $this->db->where('id', $id);
        $this->db->set('active', $state);
        $this->db->update($this->table_name);
        return '{"success": true}';
    }

    // active
    function reject_set($id, $state) {
        $this->db->where('id', $id);
        $this->db->set('reject', $state);
        $this->db->set('active', ($state == -1 ? 0 : 1)) ;
        $this->db->update($this->table_name);
        return '{"success": true}';
    }

    function load_all_user_products_count($id = null) {
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }

    function load_all_user_products($id, $where='id') {
        $current_date = date("Y-m-d H:i:s");
        $this->limit_check();
        $this->db->where($where, $id);
        if (isset($_REQUEST['rejected']) && $_REQUEST['rejected'] == -1) {
            $this->db->where('reject =', -1 );
        }else{
            $this->db->where('reject >=', 0 );
        }
        
        if (isset($_REQUEST['active_to'])) {
            $this->db->where('active_to >', $current_date);
        }
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }

    function add_ui() {
        //check does the product exists in database
        $this->db->like('text1', $_POST['text1']);
        $query = $this->db->get($this->table_name);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'product_exist';
        } else {
            $_POST['date_added'] = date("Y-m-d H:i:s");
            $_POST['date_last_modified'] = date("Y-m-d H:i:s");
            // insert new product
            $this->db->insert($this->table_name, $_POST);
            // result
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'ok';
            $result['inserted_id'] =  $this->db->insert_id();
        }
        return $result;
    }
    function edit_ui($id_product) {
        //check does the product exists in database
        $this->db->where('text1', $_POST['text1']);
        $this->db->where('id !=', $id_product, false);
        $query = $this->db->get($this->table_name);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'product_exist';
        } else {
            $_POST['date_added'] = date("Y-m-d H:i:s");
            $_POST['date_last_modified'] = date("Y-m-d H:i:s");
            // insert new product
            $this->db->where('id', $id_product);
            $this->db->update($this->table_name, $_POST);
            // result
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'ok';
        }
        return $result;
    }

    function load_all_promotion_products_count(){
        $this->db->where('promote', '1');
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }

    function load_all_promotion_products(){
        $this->db->where('promote', '1');
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }

}

?>