<?php
class Article extends Hub {

	function Article($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load all article
        $result = $this->load_all_title_call($title_call);
        $this->ci->smarty->assign('article_total', $result['total']);
        $this->ci->smarty->assign('article', $result['data']);
        // display
        $this->smarty_display($template);
    }
    function display_single($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load article
        $result = $this->load($id);
        $this->ci->smarty->assign('article_total', 1);
        $this->ci->smarty->assign('article', $result['data']);
        // display
        $this->smarty_display($template);
    }

    // load
    function load_all_title_call($title_call = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_all_title_call/'.$title_call;
        $result = $this->api_call($url);
        return $result;
    }
    function load($id = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load/warehouse_article,'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }











    /*function load_last_ui($id_node = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_last/'.$id_node;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_ui($id_node = null, $id = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load/warehouse_article,'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }*/

}