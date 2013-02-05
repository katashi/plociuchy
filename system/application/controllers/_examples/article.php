<?php
class Article extends Hub {

	function Article($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null, $id_node = null) {
        $this->assign_template_titlecall($template, $title_call);
        $result = $this->load_all_ui($id_node);
        // if one only is attached display full, otherwise display list
        if ($result['total'] > 1) {
            $this->ci->smarty->assign('article_count', $result['total']);
            $this->ci->smarty->assign('article', $result['data']);
            $ext = 'list';
        } else {
            $this->ci->smarty->assign('article_count', 1);
            $this->ci->smarty->assign('article', $result['data'][0]);
            $ext = 'full';
        }
        $this->smarty_display($template.'/'.$ext);
    }
    function display_full($template = null, $title_call = null, $id_node = null, $id = null) {
        // id - id element in "warehouse" ( not in "structure" )
        $this->assign_template_titlecall($template, $title_call);
        $result = $this->load_ui($id_node, $id);
        $this->ci->smarty->assign('article', $result['data']);
        $this->smarty_display($template.'/full');
    }

    // load
    function load_all_ui($id_node = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_all/'.$id_node;
        $result = $this->api_call($url);
        return $result;
    }
    function load_last_ui($id_node = null) {
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
    }

}