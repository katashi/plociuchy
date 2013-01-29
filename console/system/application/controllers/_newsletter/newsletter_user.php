<?php
if (!defined('BASEPATH')) die;

class Newsletter_User extends Main {

	function Newsletter_User($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('newsletter_user_model');
        //
        include_once('newsletter_configuration.php');
		$this->nc = new Newsletter_Configuration($this->ci);
        $this->configuration = $this->nc->load_cron();
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('newsletter/newsletter_user.html');
	}
	function display_add() {
		$this->ci->smarty->display('newsletter/newsletter_user_add.html');
	}
	function display_import() {
		$this->ci->smarty->display('newsletter/newsletter_user_import.html');
	}
    function display_verify() {
		$this->ci->smarty->display('newsletter/newsletter_user_verify.html');
	}
	
	//
	// load all
	//
	function load_all() {
		if (!isset($_REQUEST['id']) || ($_REQUEST['id'] == '0')) {
			echo '{"total":'.json_encode($this->newsletter_user_model->load_all_count()).', "data":'.json_encode($this->newsletter_user_model->load_all()).'}';
		} else {
			$id = $_REQUEST['id'];
			echo '{"total":'.json_encode($this->newsletter_user_model->load_all_count_filter($id)).', "data":'.json_encode($this->newsletter_user_model->load_all_filter($id)).'}';
		}
	}	
	
	//
	// add
	//
	function add() {
	   $result = $this->newsletter_user_model->add();
       echo $result;
	}
    function add_email() {
        // email assign
        $email = $_POST['email'];
        // first we need to verify does user exist in db already
        $user_exist = json_decode($this->newsletter_user_model->user_exist($email), true);
        // now lets register or not :)
        if ($user_exist['success'] == false) {
            $result = $this->newsletter_user_model->add_email();
            echo $result;
        } else {
            echo '{"success": false}';
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
		$result = $this->newsletter_user_model->delete($id);
		echo 'grid';	
	}
    function delete_email($email) {
        $result = $this->newsletter_user_model->delete_email($email);
		echo 'grid';
    }
	
	//
	// delete all
	//
	function delete_all() {
		$result = $this->newsletter_user_model->delete_all();
		echo 'grid';	
	}
	
	//
	// import csv
	//
	function import_csv() {
		//file handling - lets open csv file
		$tmp_name = $_FILES['data']['tmp_name'];
		$file_size = $_FILES['data']['size'];
		$fp = fopen($tmp_name, 'r');
		$content = fread($fp, $file_size);
		$content = addslashes($content);
		fclose($fp);
        //lets define array
		$record = explode("\n", $content);
        // if there is more record then 1000 - lets divide section on different files
        $record_quantity = count($record);
        if ($record_quantity > 500) {
            // check for directory
            if(!file_exists('./cron/newsletter_user_import/')) { mkdir('./cron/newsletter_user_import/'); chmod("./cron/newsletter_user_import/", 0777); }
            $header = $record[0]."\n";
            $loop = ceil($record_quantity/500);
            $date = date("Y-m-d");
            // lets create different files to import
            for($x=0; $x<$loop; $x++) {
                $filename = $date.'_'.$x.'.csv';
                $fp = fopen('./cron/newsletter_user_import/'.$filename, 'w');
                fwrite($fp, $header);
                $start = $x * 500 + 1;
                $end = $x * 500 + 500;
                for($y=$start; $y<$end; $y++) {
                    if (isset($record[$y])) {
                        fwrite($fp, $record[$y]."\n");
                    }
                }
            }
        } else {
            $report = $this->newsletter_user_model->import_csv($record);
            $this->import_csv_report($report);
        }
		echo '{"success": true}';
	}
	
    //
    // import csv cron
    //
    function import_csv_cron() {
        // first we need to check does a directory exists in cron/newsletter_user_import 
        $dir = './cron/newsletter_user_import/';
        if(file_exists($dir)) {
            $dir_content = scandir($dir);
            array_shift($dir_content);
            array_shift($dir_content);
            if (count($dir_content) > 0) {
                // lets take first file
                $fp = fopen($dir.$dir_content[0], 'r');
        		$content = fread($fp, filesize($dir.$dir_content[0]));
        		$content = addslashes($content);
        		fclose($fp);
                // lets define array
        		$record = explode("\n", $content);
                // import
                $result = $this->newsletter_user_model->import_csv($record);
                $this->import_csv_report($result);
                // unlink
                @unlink($dir.$dir_content[0]);
            }
        }  
    }
    
    //
    // import csv report
    //
    function import_csv_report($report) {
        // we need init administrator for grabbing emails
        include_once('administrator.php');
        $this->a = new Administrator($this->ci);
        $this->administrator = $this->a->load_all_object();
        // lets load email library
		$config['protocol'] = 'mail';
        $this->ci->email->initialize($config);
        foreach($this->administrator as $administrator) {
            if (isset($administrator['email']) && $administrator['email'] != '') {
                // ok, lets send test email
                $this->ci->email->subject('Raport - Import adresów e-mail');
        		$this->ci->email->from('biuro@ad-ministry.com', 'Katashi');
        		$this->ci->email->to($administrator['email']);
        		$this->ci->email->message('Zaimportowano adresów - '.$report['imported'].', Zaktualizowano adresów - '.$report['updated']);
                $this->ci->email->send();
            }
        }        
    }
    
	//
	// import txt
	//
	function import_txt() {
		//file handling
		$tmp_name = $_FILES['data']['tmp_name'];
		$file_size = $_FILES['data']['size'];
		$fp = fopen($tmp_name, 'r');
		$content = fread($fp, $file_size);
		$content = addslashes($content);
		fclose($fp);
		//lets define array
		$record = explode(";", $content);
		//lets add to newsletter_user table
		$success = $this->newsletter_user_model->import_txt($record);
		echo $success;
	}
	
	//
	// import textarea
	//
	function import_textarea() {
		//lets define array
		$record = explode(";", $_REQUEST['data']);
		//lets add to newsletter_user table
		$success = $this->newsletter_user_model->import_textarea($record);
		echo $success;
	}
	
	//
	// export csv
	//
	function export_csv() {
		$emails = $this->newsletter_user_model->load_all();
		$date = date("Y-m-d");
		// lets prepare file
		$fp = fopen("media/files/newsletter_user_".CONFIGURATION."_".$date.".csv", "w");
        $data = 'name;address;city;district;email;phone;group;adnotation';
        fwrite($fp, $data."\n");
	    foreach ($emails as $entry) {
			$data = implode(";", $entry);
			fwrite($fp, $data."\n");
	    }
		fclose($fp);
		echo '{"success": true}';
	}
    
    //
    // verify
    //
    function verify_email() {
        // first we have to grab some emails to check
        $result = $this->newsletter_user_model->verify_email_recipient_get();
        if (isset($result) && count($result)) {
            // lets swap to one array
            foreach($result as $entry) { $email[] = $entry['email']; }
            // an optional sender  
            $sender = $this->configuration['from_email'];
            $from_domain = $this->configuration['server_domain'];
            $nameservers = array($from_domain);
            // do the validation 
            $result = $this->ci->smtp->validate($email, $sender, $from_domain, $nameservers);  
            // now we have to put information to database
            $this->newsletter_user_model->verify_email_status_update($result);
    		echo '{"success": true}';
        } else {
            echo '{"success": false}';
        }
    }
    
}