<?php
class Newsletter_Distribution_Model extends Model {
		
	function Newsletter_Distribution_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}
    
    //
    // composition
    //
    function newsletter_compose($id) {
		$this->db->select('id,title,header,text,image,date_created');
		$this->db->where('id', $id);
		$query = $this->db->get('warehouse_newsletter');
		$record = $query->row_array();
		return $record;
	}
	function newsletter_compose_content($id_newsletter) {
		$this->db->order_by('position');
		$this->db->where('id_newsletter', $id_newsletter);
		$query = $this->db->get('warehouse_newsletter_content_relations');
		$record = $query->result_array();
		return $record;
	}
	function newsletter_compose_article($id) {
		$record = array();
		$this->db->select('id,title,header,text,tag,image,date_created');
		$this->db->where('id', $id);
		$query = $this->db->get('warehouse_article');
		$record = $query->row_array();
		return $record;		
	}
    
    //
    // recipient
	//
    function recipient_get($data) {
        $this->db->select('newsletter_user.id, newsletter_user.name, newsletter_user.email');
        $this->db->order_by('newsletter_user.email');
        $this->db->where('newsletter_user.active', 1);
        // if there is a group selected
        if (isset($data['user_group']) && $data['user_group'] != '' && $data['user_group'] != '0') {
            $this->db->where('newsletter_user.id = newsletter_user_group_relations.id_user');
            $this->db->like('newsletter_user_group_relations.group', $data['user_group']);
            $query = $this->db->get('newsletter_user, newsletter_user_group_relations');
            $record = $query->result_array();
        } else { 
            $query = $this->db->get('newsletter_user');
    		$record = $query->result_array();
        }
		return $record;
    }
    
    //
    // spool
    //
	function spool_add($data) {
		$this->db->insert('newsletter_spool', $data);
        return '{"success": true}';
	}
    
    function spool_get() {
        $this->db->limit(1);
		$query = $this->db->get('newsletter_spool');
		$record = $query->row_array();
		//
		if (isset($record['id'])) {
			$this->db->where('id', $record['id']);
			$query = $this->db->delete('newsletter_spool');
		}
		return $record;
    }
    
}
?>