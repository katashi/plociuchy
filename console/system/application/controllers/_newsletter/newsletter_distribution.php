<?php
if (!defined('BASEPATH')) die;

class Newsletter_Distribution extends Main {

	function Newsletter_Distribution($_ci='') {
		parent::Controller();
		//
		$this->ci = $_ci;
		//
		$this->load->model('newsletter_distribution_model');
        //
        include_once('newsletter_configuration.php');
		$this->nc = new Newsletter_Configuration($this->ci);
        $this->configuration = $this->nc->load_cron();
	}
	
	//
	// display
	//
	function display() {
		$this->ci->smarty->display('newsletter/newsletter_distribution.html');
	}
    function display_preview($newsletter_id, $template, $text_contact_attach, $unsubscribe_attach) {
        $message = $this->preview($newsletter_id, $template, $text_contact_attach, $unsubscribe_attach);
        $this->ci->smarty->assign('message', $message);
        $this->ci->smarty->display('newsletter/newsletter_preview.html');
    }
    
    //
    // load
    //
    function load_all_template() {
        $dir = './../templates/newsletter/newsletter_'.CONFIGURATION;
        if(!file_exists($dir)) { mkdir($dir); chmod($dir, 0777); mkdir($dir.'/default'); chmod($dir.'/default', 0777);  }
        $content = scandir($dir);
        array_shift($content);
        array_shift($content);
        $data = array();
        foreach($content as $entry) { $data[]['template'] = $entry; }
        echo '{"success": true, "data":'.json_encode($data).'}';
    }
    
    //
    // send
    //
    function send($mode) {
        // first assign elements from a form to $data variable
        $data = $_POST;
        $data['mode'] = $mode;
        // if we deal with mode test here it is
        if ($mode == 'test') {
            $this->send_execute_test($data);
        }
        // if we deal with mode mass - well.. here it is
        if ($mode == 'mass') {
            $this->send_execute_mass($data);
        }
    }
    
    function send_execute_test($data) {
		$config['protocol'] = 'sendmail';
        $config['useragent'] = $data['x-mailer'];
		$config['mailtype'] = $data['format'];
		$config['charset'] = $data['encoding'];
        $this->ci->email->initialize($config);
        // here we get newsletter and connected content
        $newsletter_content = $this->message_setup_part1($data);
        // lets correct values from checkboxes
        $data['text_contact_attach'] = isset($data['text_contact_attach']) ? 1 : 0;
        $data['unsubscribe_attach'] = isset($data['unsubscribe_attach']) ? 1 : 0;
        // ok, lets send test email
        $recipient = explode(';', $data['recipient_test']);
		foreach ($recipient as $email) {
            $newsletter = $this->message_setup_part2($newsletter_content, $email, $data['template'], $data['text_contact_attach'], $data['unsubscribe_attach']);
           	$this->ci->email->subject($data['subject']);
			$this->ci->email->from($data['from_email'], $data['from_name']);
			$this->ci->email->to($email);
			$this->ci->email->message($newsletter);
			$this->ci->email->send();
            //echo $this->ci->email->print_debugger();
		}
        echo '{"success": true}';
    }
    
    function send_execute_mass($data) {
        // we need users as recipients
        $recipient = $this->newsletter_distribution_model->recipient_get($data);
        $recipient_total = count($recipient);
		$counter = 0;
		//
		$email_sequence = array();
		$email_section = array();
		// lets divide emails
		for($x=0; $x<$recipient_total; $x++) {
            array_push($email_section, $recipient[$x]);
            $counter++;
            if ($counter >= $data['packet'] || $x >= $recipient_total - 1) {
                $counter = 0;
                array_push($email_sequence, $email_section);
                $email_section = array();
            } 
        }
		// lets push it into db to spool... send via cron for a while
		for($x=0; $x<count($email_sequence); $x++) {
            $data_spool = array();
            $data_spool['newsletter_id'] = $data['newsletter_id'];
            // lets iterate through sequence
            $user_id = array();
            $user_name = array();
            $user_email = array();
            for($y=0; $y<count($email_sequence[$x]); $y++) {
                if (isset($email_sequence[$x][$y]['id'])) { $id = $email_sequence[$x][$y]['id']; } else { $id = '0'; }
                if (isset($email_sequence[$x][$y]['name'])) { $name = $email_sequence[$x][$y]['name']; } else { $name = 'Nieznajomy'; }
                if (isset($email_sequence[$x][$y]['email'])) { $email = $email_sequence[$x][$y]['email']; } else { $email = '0'; }
                array_push($user_id, $id); 
                array_push($user_name, $name); 
                array_push($user_email, $email); 
            }
            $data_spool['user_id'] = implode(";", $user_id);
			$data_spool['user_name'] = implode(";", $user_name);
            $data_spool['user_email'] = implode(";", $user_email);
            $data_spool['template'] = $data['template'];
            // lets correct checkbox values
            $data_spool['text_contact_attach'] = isset($data['text_contact_attach']) ? 1 : 0;
            $data_spool['unsubscribe_attach'] = isset($data['unsubscribe_attach']) ? 1 : 0;
            //
            $result = $this->newsletter_distribution_model->spool_add($data_spool);
		}
        echo '{"success": true}';
    }
    
    function send_execute_mass_cron() {
        // lets load email library
		$config['protocol'] = 'sendmail';
        $config['useragent'] = $this->configuration['x-mailer'];
		$config['mailtype'] = $this->configuration['format'];
		$config['charset'] = $this->configuration['encoding'];
        $this->ci->email->initialize($config);
        // we need users
        $data = $this->newsletter_distribution_model->spool_get(); 
        if ($data) {
            // here we get newsletter and connected content
            $newsletter_content = $this->message_setup_part1($data);
            // ok, lets send test email
            $user_id = explode(';', $data['user_id']);
    		$user_name = explode(';', $data['user_name']);
            $user_email = explode(';', $data['user_email']);
            //
            for ($x=0; $x<count($user_id); $x++) {
                $newsletter = $this->message_setup_part2($newsletter_content, $user_email[$x], $data['template'], $data['text_contact_attach'], $data['unsubscribe_attach']);
                $this->ci->email->subject($this->configuration['subject']);
    			$this->ci->email->from($this->configuration['from_email'], $this->configuration['from_name']);
    			$this->ci->email->to($user_email[$x]);
    			$this->ci->email->message($newsletter);
    			$this->ci->email->send();
                //echo $this->ci->email->print_debugger();
    		}
            echo '{"success": true}'; 
        } 
    }
    
    function preview($newsletter_id, $template, $text_contact_attach, $unsubscribe_attach) {
        // first assign elements from a form to $data variable
        $data = array();
        $data['newsletter_id'] = $newsletter_id;
        $data['template'] = $template;
        // lets correct values from checkboxes
        $text_contact_attach = ($text_contact_attach == 'true') ? 1 : 0;
        $unsubscribe_attach = ($unsubscribe_attach == 'true') ? 1 : 0;
        // here we get newsletter and connected content
        $newsletter_content = $this->message_setup_part1($data);
        // and now final layout
        return $this->message_setup_part2($newsletter_content, '', $template, $text_contact_attach, $unsubscribe_attach);
    }
    
    function message_setup_part1($data) {
		$newsletter_compose = $this->newsletter_distribution_model->newsletter_compose($data['newsletter_id']);
		$newsletter_compose_elements = $this->newsletter_distribution_model->newsletter_compose_content($data['newsletter_id']);
        $newsletter_compose_article = array();
		//lets roll through types
		foreach ($newsletter_compose_elements as $element) {
			foreach ($element as $key => $value) {
				//here we handle arts
				if ($key == 'type' && $value == 'warehouse_article') {
					$newsletter_compose_article[] = $this->newsletter_distribution_model->newsletter_compose_article($element['id_content']);
				}
			}
		}
		$message[] = $newsletter_compose;
		$message[] = $newsletter_compose_article;
		return $message;
	}
	
    function message_setup_part2($message, $email='', $template='default', $text_contact_attach, $unsubscribe_attach) {
        if (!isset($message[0]['title'])) { $message[0]['title'] = ''; }
        if (!isset($message[0]['text'])) { $message[0]['text'] = ''; }
        if (!isset($message[0]['date_created'])) { $message[0]['date_created'] = ''; }
        //
        $this->ci->smarty->assign('path_template', SITE_URL.'/templates/newsletter_'.CONFIGURATION.'/'.$template);
        $this->ci->smarty->assign('path_media', SITE_URL.'/media/image');
        $this->ci->smarty->assign('path_site', SITE_URL);
        $this->ci->smarty->assign('title', $message[0]['title']);
        $this->ci->smarty->assign('text', $message[0]['text']);
		$this->ci->smarty->assign('image', $message[0]['image']);
        $this->ci->smarty->assign('date_created', $message[0]['date_created']);
        $this->ci->smarty->assign('articles', $message[1]);
        $this->ci->smarty->assign('email', $email);
        $this->ci->smarty->assign('text_contact_attach', $text_contact_attach);
        $this->ci->smarty->assign('text_contact', $this->configuration['text_contact']);
        $this->ci->smarty->assign('unsubscribe_attach', $unsubscribe_attach);
		return $this->ci->smarty->fetch('../../../../templates/newsletter_'.CONFIGURATION.'/'.$template.'/index.html');
	}
    
}