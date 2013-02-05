<?php
class Product_Dict_Verndor_Model extends Main_Model
{

    function Product_Dict_Vendor_Model()
    {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_product_dict_vendor';
    }

    // load
    function load_all_count()
    {
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }

    function load_all()
    {
        $this->limit_check();
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }

    function load_all_user_count($id = null)
    {
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }

    function load_all_user($id)
    {
        $this->limit_check();
        $this->db->where('id', $id);
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }

    function load($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table_name);
        $record = $query->row_array();
        return $record;
    }

    function add()
    {
        $record = $_POST;
        $this->db->insert($this->table_name, $record);
        return 1;
    }

    // edit
    function edit($id)
    {
        $record = $_POST;
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $record);
        return 1;
    }

    // delete
    function delete($id = null)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table_name);
        return 1;
    }

}

?>