<?php
class Partner_Account_Income_Model extends Main_Model {

    function Partner_Account_Income_Model() {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_partner_account_income';
    }
    // load
    function load_all_count() {
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }
    function load_field($field, $value) {
        $this->db->where($field, $value);
        $query = $this->db->get($this->table_name);
        if ($this->db->affected_rows() > 0) {
            $record = $query->row_array();
            return $record;
        }
    }
    function load_all() {
        $this->limit_check();
        $this->sort_check();
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }
    function load_all_partner_count($id = null) {
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }
    function load_all_partner($id) {
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
        $record['date_added'] = date("Y-m-d H:i:s");
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

    function get_last_income_points($id_partner){
        $this->db->where('id_partner', $id_partner);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get($this->table_name);
        $record = $query->row_array();
        return $record;
    }

    function get_outcome_income_all($id_partner){

        $query = $this->db->query(
        '(SELECT id_partner, point , point_available , null as id_product , null as koszt_wystawienia, date_added
            FROM pc_partner_account_income WHERE id_partner = '.$id_partner.')
        Union All
        (SELECT id_partner, null , null , id_product, unit_cost, date_added
            FROM pc_partner_account_outcome WHERE id_partner = '.$id_partner.')
        ORDER BY date_added'
        );
        $record = $query->result_array();
        return $record;
    }
}

?>