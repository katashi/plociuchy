<?php
class Product_Reservation_Comment_Model extends Main_Model
{

    function Product_Reservation_Comment_Model()
    {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_product_reservation_comment';
    }

    // filter check
    function filter_check() {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $this->db->like('id_reservation', $_REQUEST['query']);
            $this->db->or_like('text1', $_REQUEST['query']);
            $this->db->or_like('text2', $_REQUEST['query']);
        }
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
        $this->filter_check();
        $this->sort_check();
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
        $record['date_added'] = date("Y-m-d H:i:s");
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