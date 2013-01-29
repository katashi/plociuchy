<?php
class Newsletter_User_Model extends Model {
		
	function Newsletter_User_Model() {
		// Call the Model constructor
		parent::Model();
		//
		if (isset($this->ci)) { $this->db = $this->ci->db; }
	}
	
	//
	// limit check
	//
	function limit_check() {
		if (isset($_REQUEST['start']) && isset($_REQUEST['limit'])) { 
			$this->start = $_REQUEST['start'];
			$this->limit = $_REQUEST['limit'];
			$this->db->limit($this->limit, $this->start);
		}
	}
	
	//
	// load
	//
	function load_all_count() {
		$this->db->from('newsletter_user');
		return $this->db->count_all_results();
	}
	function load_all() {
		$this->limit_check();
		$this->db->order_by('id');
		$query = $this->db->get('newsletter_user');
		$record = $query->result_array();
		return $record;	
	}
 	function load_all_count_filter($id) {
		$this->db->from('newsletter_user');
		return $this->db->count_all_results();
	}
	function load_all_filter($id) {
		// its more complicated than normal get
		// so, first we need to get all users and connect them with their user_group_relations
		$this->limit_check();
		$this->db->order_by('newsletter_user.id');
		$this->db->where('newsletter_user_group_relations.id_user = newsletter_user.id');
		$query = $this->db->get('newsletter_user, newsletter_user_group_relations');
		$user = $query->result_array();
		// right now we have to compare user and remove everyone who doesnt fit
		$record = array();
		foreach ($user as $entry) {
			$found = false;
			$group = explode(',', $entry['group']);
			foreach ($group as $element) {
				if ($element == intval($id)) { 
					$found = true;
					//echo 'found...';
				}
			}
			if ($found) {
				$record[] = $entry;
				$found = false;
			}
		}
		return $record;
	}
	function load_all_id_email() {
		$this->db->select('id,email');
		$this->db->order_by('email');
		$query = $this->db->get('newsletter_user');
		$result = $query->result();
		$record = array();
		foreach ($result as $item) {
            $entry = array();
			$entry['id'] = $item->id;
            $entry['email'] = str_replace (" ", "", $item->email);
            if (isset($entry['email']) && $entry['email'] != '') {
                $record[] = $entry;
            }
		}
		return $record;
	}
	function load() {	
	}
	
	//
	// add
	//
	function add() {
		$record['name'] = $_REQUEST['name'];
		$record['address'] = $_REQUEST['address'];
		$record['city'] = $_REQUEST['city'];
		$record['district'] = $_REQUEST['district'];
		$record['email'] = $_REQUEST['email'];
		$record['phone'] = $_REQUEST['phone'];
		$this->db->insert('newsletter_user', $record); 
		return '{"success": true}';
	}
    function add_email() {
        if (isset($_REQUEST['email'])) {
            $record['email'] = $_REQUEST['email'];
            $this->db->insert('newsletter_user', $record); 
            return '{"success": true}';
        } else {
            return '{"success": false}';
        }
    }
	
	//
	// edit
	//
	function edit() {	
	}
	
	//
	// delete
	//
	function delete($id) {
		$this->db->where('id', $id);
		$query = $this->db->delete('newsletter_user');
		return '{"success": true}';
	}
    function delete_email($email) {
        $this->db->where('email', $email);
		$query = $this->db->delete('newsletter_user');
		return '{"success": true}';
    }
	
	//
	// delete all
	//
	function delete_all() {
		$query = $this->db->query('delete from newsletter_user');
		$query = $this->db->query('delete from newsletter_user_group_relations');
		return '{success:true}';
	}
	
	//
	// import csv
	//
	function import_csv($record) {
        // import result var
        $report = array();
        $report['imported'] = 0;
        $report['updated'] = 0;
        // check for email column (does it exist?)
		$entry = $record[0];
		$data = explode(';', $entry);
        // remove \r from last cell
        $c1 = count($data)-1;
        $data[$c1] = str_replace("\r",'',$data[$c1]);
        // lets check for email column
		$key = array_search('email', $data); if (!isset($key)) { return '{"success": false}'; }
		// lets get now info about groups column (does it exist?)
		$key_group = array_search('group', $data); if (!isset($key_group)) { $key_group = false; }
		// first we need to remove first line
		array_splice($record, 0, 1);
		// now if email fails clear record to removal (automatically look in proper column)
		$count = 0;
		foreach ($record as $entry) {
			$data = explode(';', $entry);
            if (count($data) > 1) {
    			$email = isset($data[$key]) ? $data[$key] : '';
                if (isset($email) && $email != '') {
        			// check for email correct
            		//if (!preg_match("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$^", $email)) { $record[$count] = ''; }
            		if (!strstr($email, '@')) { $record[$count] = ''; } //echo 'email fail @ - '.$email.'<br>'; }
            		if (!strstr($email, '.')) { $record[$count] = ''; } //echo 'email fail . - '.$email.'<br>'; }
                    if ($email == '') { $record[$count] = ''; } //echo 'email fail empty<br>'; }
           			$count++;
                }
            }
		}
        //echo 'original count ='.count($record).'<br>';
		// now look for an empty records
		$count = 0; foreach ($record as $entry) { if ($entry == '') { array_splice($record, $count, 1); } else { $count++; } }
		//echo 'after empty count ='.count($record).'<br>';
        // now look for duplicates, first lets make a array mark as dirty
		$count = 0; $dirty = array(); foreach ($record as $entry) { $dirty[$count] = explode(';', $entry); $count++; }
        //echo 'dirty count ='.count($dirty).'<br>';
		// now lets make a clean copy
		$clean = $dirty; $usedEmails = array();
		for($i=0; $i<count($dirty); $i++) { 
		    if (isset($dirty[$i][4]) && $dirty[$i][4] != '') {
                if (in_array( $dirty[$i][4], $usedEmails)) { 
                    unset($clean[$i]); 
                } else { 
                    $usedEmails[] = $dirty[$i][4]; 
                } 
            }
        }
        //echo 'clean copy count ='.count($clean).'<br>';
		// lets get emails in db
		$user_id_email = $this->load_all_id_email();
        // lets swap values into parts
        $user_id = array();
        $user_email = array();
        foreach($user_id_email as $entry) {
            $user_id[] = $entry['id'];
            $user_email[] = $entry['email'];
        }
        //echo 'emails in database = '.count($user_email).'<br>';
		// lets compare emails in db to new ones to import
		$record = $clean;
		foreach ($record as $entry) {
		    // remove \r from last cell
            $c2 = count($entry)-1;
            $entry[$c2] = str_replace("\r",'',$entry[$c2]);
		    // lets define data
            $data = array();
    		$data['name'] = isset($entry[0]) ? $entry[0] : '';
    		$data['address'] = isset($entry[1]) ? $entry[1] : '';
   			$data['city'] = isset($entry[2]) ? $entry[2] : '';
   			$data['district'] = isset($entry[3]) ? str_replace (" ", "", $entry[3]) : '';
   			$data['email'] = isset($entry[4]) ? str_replace (" ", "", $entry[4]) : '';
   			$data['phone'] = isset($entry[5]) ? str_replace (" ", "", $entry[5]) : '';
            //
            if(isset($entry[$key]) && $entry[$key] != '') {
    			if(!in_array(str_replace(" ", "", $entry[$key]), $user_email)) {
                    $this->db->insert('newsletter_user', $data);
    				// if key_group exists lets put it into relation
    				if (isset($entry[$key_group]) && $entry[$key_group] != '') { 
    				    $id_user = $this->db->insert_id();
    					$data_group = array();
    					$data_group['id_user'] = $id_user;
    					$data_group['group'] = $entry[$key_group];
                        $this->user_group_relations_insert($data_group);
    				}
                    // update report
                    $report['imported']++;
    			} else {
                    /* TEMPORARY SWITCHED OFF
    				// here we need to determine id of an entry
    				//$this->db->where('newsletter_user.email', $entry[$key]);
    				//$query = $this->db->get('newsletter_user');
    				//$user = $query->row_array();
    				if ($entry[$key_group]) { 
    					$data_group = array();
                        $data_group['id_user'] = $user_id[$step];
    					$data_group['group'] = $entry[$key_group];
                        $this->user_group_relations_update($data_group);
    				}
    				// now lets update record
    				$this->db->where('email', $entry[$key]);
    				$this->db->update('newsletter_user', $data);
                    // update report
                    $report['updated']++;*/
    			}
            }
		}
        return $report;
	}
    
    //
    // user_group_relations
    //
    function user_group_relations_insert($data) {
        // swap string to array
        $group_name = explode(',', $data['group']);
        $group_id = array();
        // now we need to swap name to id 
        foreach($group_name as $entry) {
            $this->db->select('id');
            $this->db->where('newsletter_user_group.name', $entry);
            $query = $this->db->get('newsletter_user_group');
            $nug = $query->row_array();
            if ($this->db->affected_rows() > 0) {
                $group_id[] = $nug['id'];
            }
        }
        if (isset($group_id) && count($group_id)>0) {
            $data_group = array();
            $data_group['id_user'] = $data['id_user'];
            $data_group['group'] = implode(',',$group_id);
            // insert to database
            $this->db->insert('newsletter_user_group_relations', $data_group);
        }
    }
    function user_group_relations_update($data) {
        // swap string to array
        $group_name = explode(',', $data['group']);
        $group_id = array();
        // now we need to swap name to id 
        foreach($group_name as $entry) {
            $this->db->select('id');
            $this->db->where('newsletter_user_group.name', $entry);
            $query = $this->db->get('newsletter_user_group');
            $nug = $query->row_array();
            if ($this->db->affected_rows() > 0) {
                $group_id[] = $nug['id'];
            }
        }
        if (isset($group_id) && count($group_id)>0) {
            $data_group = array();
            $data_group['id_user'] = $data['id_user'];
            $data_group['group'] = implode(',',$group_id);
            //
            $this->db->where('id_user', $data['id_user']);
            $this->db->update('newsletter_user_group_relations', $data_group);
        }
    }
	
	//
	// import txt
	//
	function import_txt($record) {
		// if email fails clear record to removal
		$count = 0;
		foreach ($record as $entry) {
			// check for email correct
			if (!preg_match("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$^", $entry)) { $record[$count] = ''; }
			if (!strstr($entry, '@')) { $record[$count] = ''; }
			if (!strstr($entry, '.')) { $record[$count] = ''; }
			$count++;
		}
		// now look for an empty records
		$count = 0; foreach ($record as $entry) { if ($entry == '') { array_splice($record, $count, 1); } else { $count++; } }
		// lets remove duplicates
		$dirty = $record;
		$clean = array_unique($dirty);
		// lets get emails in db
		$email = $this->load_all_email();
		// lets compare emails in db to new ones to import
		$record = $clean;
		foreach ($record as $entry) {
			$data = array();
			$data['name'] = '';
			$data['address'] = '';
			$data['city'] = '';
			$data['district'] = '';
			$data['email'] = $entry;
			$data['phone'] = '';
			if(!in_array($entry, $email)) {
				$this->db->insert('newsletter_user', $data);
			} else {
				$this->db->where('email', $entry);
				$this->db->update('newsletter_user', $data);
			}
		}
		return '{"success": true}';
	}
	
	//
	// import textarea
	//
	function import_textarea($record) {
		// if email fails clear record to removal
		$count = 0;
		foreach ($record as $entry) {
			// check for email correct
			if (!preg_match("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$^", $entry)) { $record[$count] = ''; }
			if (!strstr($entry, '@')) { $record[$count] = ''; }
			if (!strstr($entry, '.')) { $record[$count] = ''; }
			$count++;
		}
		// now look for an empty records
		$count = 0; foreach ($record as $entry) { if ($entry == '') { array_splice($record, $count, 1); } else { $count++; } }
		// lets remove duplicates
		$dirty = $record;
		$clean = array_unique($dirty);
		// lets get emails in db
		$email = $this->load_all_email();
		// lets compare emails in db to new ones to import
		$record = $clean;
		foreach ($record as $entry) {
			$data = array();
			$data['name'] = '';
			$data['address'] = '';
			$data['city'] = '';
			$data['district'] = '';
			$data['email'] = $entry;
			$data['phone'] = '';
			if(!in_array($entry, $email)) {
				$this->db->insert('newsletter_user', $data);
			} else {
				$this->db->where('email', $entry);
				$this->db->update('newsletter_user', $data);
			}
		}
		return '{"success": true}';
	}

	//
	// export csv
	//
	function export_csv() {
		
	}
    
    //
    // email verification functions
	//
    function verify_email_recipient_get() {
        $this->db->limit(25);
        $this->db->select('email');
        $this->db->where('status', 0);
        $this->db->where('active', 1);
        $this->db->where('email != ""');
        $query = $this->db->get('newsletter_user');
        $record = $query->result_array();
  		return $record;
    }
    function verify_email_status_update($result) {
        foreach($result as $entry) {
            $record = array();
            $record['status'] = $entry['code'];
            switch($entry['code']) {
                case 220: $record['status_description'] = 'Usługa SMTP Aktywna'; $record['active'] = 1; break;
                case 250: $record['status_description'] = 'Adres poprawny'; $record['active'] = 1; break;
                case 251: $record['status_description'] = 'Adres poprawny (relay)'; $record['active'] = 1; break;
                case 252: $record['status_description'] = 'Adres niezweryfikowany (zaakceptowany)'; $record['active'] = 1; break;
                case 421: $record['status_description'] = 'Usługa niedostępna (błąd MTA)'; $record['active'] = 0; break;
                case 450: $record['status_description'] = 'Adres niepoprawny (odrzucony)'; $record['active'] = 0; break;
                case 451: $record['status_description'] = 'Niezweryfikowano (błąd MTA)'; $record['active'] = 1; break;
                case 452: $record['status_description'] = 'Niezweryfikowano (błąd MTA)'; $record['active'] = 1; break;
                default: $record['status_description'] = 'Adres niepoprawny (odrzucony)'; $record['active'] = 0; break;
            }
            $this->db->where('email', $entry['email']);
            $this->db->update('newsletter_user', $record);
        }
    }
    
    //
    // lets verify does the user exist
    //
    function user_exist($email) {
        if (!isset($email)) { die; }
        $this->db->where('email', $email);
		$query = $this->db->get('newsletter_user');
		$user = $query->row_array();
        if ($this->db->affected_rows() == 0) {
       	    return '{"success": false}';
        } else {
            return '{"success": true}';
        }
    }
	
}
?>