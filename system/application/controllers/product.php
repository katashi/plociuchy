<?php
class Product extends Hub {

	function Product($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load all article
        $result = $this->load_all_title_call($title_call);
        var_dump($result);
        var_dump($title_call);
//        $limit = 10;
//        $req = explode(',',$title_call);
//        $current_page = (isset($req['1']) ? $req['1'] : 1 );
//        $all_pages = 0;
//        if($result['total'] >= $limit){
//            $all_pages = ceil($result['total']/$limit);
//        }
//
//        $this->ci->smarty->assign('category_total', $result['total']);
//        $this->ci->smarty->assign('category', $result['data']);
//        $this->ci->smarty->assign('all_pages',$all_pages);
//        $this->ci->smarty->assign('current_page',$current_page);
        // display
        $this->smarty_display($template);
    }

    function display_product_single($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load article
        echo 'widok dla productu '.$id;
        $result = $this->load($id);
        $this->ci->smarty->assign('products_total', 1);
        $this->ci->smarty->assign('products', $result['data']);
        // display
        $this->smarty_display($template);
    }

    function display_category_single($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load article
        echo 'widok produktw dla ketegorii '.$id;
        $result = $this->load($id);
        $this->ci->smarty->assign('products_total', 1);
        $this->ci->smarty->assign('products', $result['data']);
        // display
        $this->smarty_display($template);
    }

    function display_vendor_single($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load article
        echo 'widok produktw dla marki'.$id;
        $result = $this->load($id);
        $this->ci->smarty->assign('products_total', 1);
        $this->ci->smarty->assign('products', $result['data']);
        // display
        $this->smarty_display($template);
    }

    // load
    function load_all_title_call($title_call = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_all_title_call_paginate/'.$title_call;
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