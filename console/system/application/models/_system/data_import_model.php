<?php
class Data_Import_Model extends Model {
		
	function Data_Import_Model() {
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
    function load_all_database_table() {
        $table = $this->db->list_tables();
        $record = array();
        foreach($table as $entry) {
            $record[]['table'] = $entry;
        }
        return $record;
    }
    
    //
	// import
	//
	function import($table, $record) {
        // lets prepare header
        $header = explode(';', $record[0]);
        // remove \r from last cell
        $c1 = count($header)-1;
        $header[$c1] = str_replace("\r",'',$header[$c1]);
        // first we need to remove first line
		array_splice($record, 0, 1);
        // well, its hard delete everything from selected table
        $query = $this->db->query('delete from '.$table);
        // lets start with sql base
        $sql = 'insert into '.$table.' (';
        // now add headers from $header
        $step = 0;
        foreach ($header as $entry) { if ($step != 0) { $sql .= ','; } $sql .= $entry; $step++; }
        $sql .= ') values (';
        // lets iterate through all records
        foreach ($record as $entry) {
            // first of part of an query is already done
            $sql_insert = $sql;
            // lets explode record in to pieces
            $entry_element = explode(';', $entry);
            // remove \r from last cell
            $c2 = count($entry_element)-1;
            $entry_element[$c2] = str_replace("\r",'',$entry_element[$c2]);
            // now we need to go through header length
            for ($x=0; $x<=$c1; $x++) {
                if ($x != 0) { $sql_insert .= ','; }
                if (!isset($entry_element[$x])) { $value = ''; } else { $value = $entry_element[$x]; }
                $sql_insert .= '"'.$value.'"';
            }
            $sql_insert .= ')';
            $this->db->query($sql_insert);
        } 
		return '{"success": true}';
    }
	
	//
	// add
	//
	/*function add() {
		$record['name'] = $_REQUEST['name'];
		$record['address'] = $_REQUEST['address'];
		$record['city'] = $_REQUEST['city'];
		$record['district'] = $_REQUEST['district'];
		$record['email'] = $_REQUEST['email'];
		$record['phone'] = $_REQUEST['phone'];
		$this->db->insert('data_import', $record); 
		return '{"success": true}';
	}
    function add_email() {
        if (isset($_REQUEST['email'])) {
            $record['email'] = $_REQUEST['email'];
            $this->db->insert('data_import', $record); 
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
		$query = $this->db->delete('data_import');
		return '{"success": true}';
	}
    function delete_email($email) {
        $this->db->where('email', $email);
		$query = $this->db->delete('data_import');
		return '{"success": true}';
    }
	
	//
	// delete all
	//
	function delete_all() {
		$query = $this->db->query('delete from data_import');
		$query = $this->db->query('delete from data_import_group_relations');
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
		$key = array_search('email', $data); if (!isset($key)) { return '{success:false}'; }
		// lets get now info about groups column (does it exist?)
		$key_group = array_search('group', $data); if (!isset($key_group)) { $key_group = false; }
		// first we need to remove first line
		array_splice($record, 0, 1);
		// now if email fails clear record to removal (automatically look in proper column)
		$count = 0;
		foreach ($record as $entry) {
			$data = explode(';', $entry);
            if (count($data) > 1) {
    			$email = $data[4];
                if (isset($email)) {
        			// check for email correct
            		//if (!preg_match("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$^", $email)) { $record[$count] = ''; }
            		//if (!strstr($email, '@')) { $record[$count] = ''; }
            		//if (!strstr($email, '.')) { $record[$count] = ''; }
           			$count++;
                }
            }
		}
		// now look for an empty records
		$count = 0; foreach ($record as $entry) { if ($entry == '') { array_splice($record, $count, 1); } else { $count++; } }
		// now look for duplicates, first lets make a array mark as dirty
		$count = 0; $dirty = array(); foreach ($record as $entry) { $dirty[$count] = explode(';', $entry); $count++; }
		// now lets make a clean copy
		$clean = $dirty; $usedEmails = array();
		for( $i=0; $i<count($dirty); $i++ ) { if (in_array( $dirty[$i][4], $usedEmails)) { unset($clean[$i]); } else { $usedEmails[] = $dirty[$i][4]; } }
		// lets get emails in db
		$email = $this->load_all_email();
		// lets compare emails in db to new ones to import
		$record = $clean;
		foreach ($record as $entry) {
			$data = array();
			$data['name'] = $entry[0];
			$data['address'] = $entry[1];
			$data['city'] = $entry[2];
			$data['district'] = $entry[3];
			$data['email'] = $entry[4];
			$data['phone'] = $entry[5];
			if(!in_array($entry[$key], $email)) {
				$this->db->insert('data_import', $data);
				// if key_group exists lets put it into relation
				if ($entry[$key_group]) { 
				    $id_user = $this->db->insert_id();
					$data_group = array();
					$data_group['id_user'] = $id_user;
					$data_group['group'] = $entry[$key_group];
                    $this->user_group_relations_insert($data_group);
				}
                // update report
                $report['imported']++;
			} else {
				// here we need to determine id of an entry
				$this->db->where('data_import.email', $entry[$key]);
				$query = $this->db->get('data_import');
				$user = $query->row_array();
				if ($entry[$key_group]) { 
					$data_group = array();
                    $data_group['id_user'] = $user['id'];
					$data_group['group'] = $entry[$key_group];
                    $this->user_group_relations_update($data_group);
				}
				// now lets update record
				$this->db->where('email', $entry[$key]);
				$this->db->update('data_import', $data);
                // update report
                $report['updated']++;
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
            $this->db->where('data_import_group.name', $entry);
            $query = $this->db->get('data_import_group');
            $nug = $query->row_array();
            if ($this->db->affected_rows() > 0) {
                $group_id[] = $nug['id'];
            }
        }
        $data_group = array();
        $data_group['id_user'] = $data['id_user'];
        $data_group['group'] = implode(',',$group_id);
        // insert to database
        $this->db->insert('data_import_group_relations', $data_group);
    }
    function user_group_relations_update($data) {
        // swap string to array
        $group_name = explode(',', $data['group']);
        $group_id = array();
        // now we need to swap name to id 
        foreach($group_name as $entry) {
            $this->db->select('id');
            $this->db->where('data_import_group.name', $entry);
            $query = $this->db->get('data_import_group');
            $nug = $query->row_array();
            if ($this->db->affected_rows() > 0) {
                $group_id[] = $nug['id'];
            }
        }
        $data_group = array();
        $data_group['id_user'] = $data['id_user'];
        $data_group['group'] = implode(',',$group_id);
        //
        $this->db->where('id_user', $data['id_user']);
        $this->db->update('data_import_group_relations', $data_group);
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
				$this->db->insert('data_import', $data);
			} else {
				$this->db->where('email', $entry);
				$this->db->update('data_import', $data);
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
				$this->db->insert('data_import', $data);
			} else {
				$this->db->where('email', $entry);
				$this->db->update('data_import', $data);
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
        $query = $this->db->get('data_import');
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
            $this->db->update('data_import', $record);
        }
    }*/
	
}
?>