<?php
class Product_Reservation_Model extends Main_Model
{

    function Product_Reservation_Model()
    {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_product_reservation';
    }

    // filter check
    function filter_check() {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $this->db->like('id', $_REQUEST['query']);
            $this->db->or_like('id_user', $_REQUEST['query']);
            $this->db->or_like('id_partner', $_REQUEST['query']);
            $this->db->or_like('id_product', $_REQUEST['query']);
            $this->db->or_like('id_payment_p24_user', $_REQUEST['query']);
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

    function load($id,$where = 'id')
    {
        $this->db->where($where, $id);
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
        $record['date_last_modified'] = date("Y-m-d H:i:s");
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

    // active
    function active_set($id, $state)
    {
        $this->db->where('id', $id);
        $this->db->set('active', $state);
        $this->db->update($this->table_name);
        return '{"success": true}';
    }

    function load_all_user_product_reservation($id_user , $status = null ){
        $this->db->where('id_user', $id_user);
        switch ($status){
            case '0': // aktywne
                $this->db->where('status', '0');
                $this->db->where('active', '1');
                break;
            case '1': //historia
                //$this->db->where('status', '1');
                $this->db->where('active', '0');
                break;
            default:
                break;
        }
        $query = $this->db->get($this->table_name);
        $record = $query->result_array();
        return $record;
    }
    function load_all_user_product_reservation_count($id_user , $status = null ){
        $this->db->where('id_user', $id_user);
        switch ($status){
            case '0': // aktywne
                $this->db->where('status', '0');
                $this->db->where('active', '1');
                break;
            case '1': //historia
                //$this->db->where('status', '1');
                $this->db->where('active', '0');
                break;
            default:
                break;
        }
        $query = $this->db->get($this->table_name);

        return $this->db->count_all_results();
    }

}

?>