<?php
class Partner_Model extends Main_Model {

    function Partner_Model() {
        // Call the Model constructor
        parent::Model();
        //
        if (isset($this->ci)) {
            $this->db = $this->ci->db;
        }
        //
        $this->table_name = 'pc_partner';
    }

    // filter check
    function filter_check() {
        if (isset($_REQUEST['query']) && $_REQUEST['query'] != '') {
            $this->db->like('nick', $_REQUEST['query']);
            $this->db->or_like('user', $_REQUEST['query']);
            $this->db->or_like('name', $_REQUEST['query']);
            $this->db->or_like('surname', $_REQUEST['query']);
        }
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
        $this->filter_check();
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
        $this->db->set('date_activated', date("Y-m-d H:i:s"));
        $this->db->update($this->table_name);
        return '{"success": true}';
    }

    function add_ui() {
        // check for missed user
        if (!isset($_POST['user'])) {
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'user_missing';
            return $result;
        }
        // remove second password from post
        unset($_POST['password2']);
        unset($_POST['checkbox_agree']);
        unset($_POST['submit1']);
        unset($_POST['submit2']);
        unset($_POST['registration_submit']);

        // check does the user exists in database (email)
        $this->db->where('user', $_POST['user']);
        $query = $this->db->get($this->table_name);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            // user (email) exists - registration impossible
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'user_exist';
        } else {
            // define element for tree table ( user is related to directories to organise content )
            $_REQUEST['title'] = $_POST['user'];
            // create directories in a tree structure
//            $this->tree_model->add_save('media_image', MEDIA_IMAGE_CLIENT_ID);
//            $mi_id = $this->tree_model->db->insert_id();
//            $this->tree_model->add_save('media_video', MEDIA_VIDEO_CLIENT_ID);
//            $mv_id = $this->tree_model->db->insert_id();
//            $this->tree_model->add_save('media_file', MEDIA_FILE_CLIENT_ID);
//            $mf_id = $this->tree_model->db->insert_id();
//            $_POST['id_media_image'] = $mi_id;
//            $_POST['id_media_video'] = $mv_id;
//            $_POST['id_media_file'] = $mf_id;
            // insert user
            // generate password
            $_POST['password'] = isset($_POST['password']) ? sha1($_POST['password']) : sha1('default');
            $_POST['password_hash'] = md5(date("Y-m-d H:i:s").$_POST['user']);
            // generate date related stuff
            $_POST['date_added'] = date("Y-m-d H:i:s");
            $_POST['date_last_modified'] = date("Y-m-d H:i:s");
            //$_POST['point'] = 15;
            // insert new user
            $this->db->insert($this->table_name, $_POST);
            // result
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'ok';
        }
        return $result;
    }

    function activate($password_hash) {
        $this->db->where('password_hash', $password_hash);
        $query = $this->db->get($this->table_name);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            $record = array();
            $record['active'] = 1;
            $record['date_activated'] = date("Y-m-d H:i:s");
            $this->db->where('password_hash', $password_hash);
            $this->db->update($this->table_name, $record);
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'ok';
        } else {
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'hash_not_found';
        }
        return $result;
    }

    function login() {
        $result = array();
        if (!isset($_POST['user']) || !isset($_POST['password'])) {
            $result['success'] = 0; $result['code'] = 'no_data'; return $result;
        }
        $this->db->where('user', $_POST['user']);
        $this->db->where('password', sha1($_POST['password']));
        $query = $this->db->get($this->table_name);
        $record = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            //if ($record['suspend'] == 1) { $result['success'] = 0; $result['code'] = 'suspended'; }else
            if ($record['active'] == 0) { $result['success'] = 0; $result['code'] = 'unactive'; }
            else { $result['success'] = 1; $result['client'] = $record; $result['code'] = 'ok'; }
        } else {
            $result['success'] = 0; $result['code'] = 'not_found'; return $result;
        }
        return $result;
    }

    function user_get($field, $value) {
        $this->db->where($field, $value);
        $query = $this->db->get($this->table_name);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'ok';
            $result['user'] = $user;
        } else {
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'user_not_found';
        }
        return $result;
    }

    // password reset
    function password_reset_ui() {
        $record = array();
        $record['password'] = sha1($_POST['password']);
        $record['password_hash'] = md5(date("Y-m-d H:i:s").$_POST['user']);
        // generate date related stuff
        $_POST['date_last_modified'] = date("Y-m-d H:i:s");
        // update
        $this->db->where('user', $_POST['user']);
        $this->db->update($this->table_name, $record);
        // result
        $result = array();
        $result['success'] = 1;
        $result['code'] = 'ok';
        return $result;
    }
    function password_clear($password_hash) {
        $record = array();
        $record['password'] = null;
        $this->db->where('password_hash', $password_hash);
        $this->db->update($this->table_name, $record);
        // result
        $result = array();
        $result['success'] = 1;
        $result['code'] = 'ok';
        return $result;
    }
    function password_update($password_hash, $password) {
        $record = array();
        $record['password'] = $password;
        $this->db->where('password_hash', $password_hash);
        $this->db->update($this->table_name, $record);
        // result
        $result = array();
        $result['success'] = 1;
        $result['code'] = 'ok';
        return $result;
    }

    function match_password($id_user,$password){
        $passwords = sha1($password);
        $this->db->where('password', $passwords);
        $this->db->where('id', $id_user);
        $query = $this->db->get($this->table_name);
        $user = $query->row_array();
        if ($this->db->affected_rows() > 0) {
            $result = array();
            $result['success'] = 1;
            $result['code'] = 'match_ok';
        } else {
            $result = array();
            $result['success'] = 0;
            $result['code'] = 'match_error';
        }
        return $result;
    }


}

?>