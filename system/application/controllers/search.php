<?php
class Search extends Hub {

    function Search($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null, $query = null) {
        $this->assign_template_titlecall($template, $title_call);
        $limit = 3;
        $current_page = (isset($_REQUEST['page'])? $_REQUEST['page'] : 1);
        $all_pages = 0;
        $query = trim($_REQUEST['search']);
        $result = $this->load_all_search_products($query , $current_page , $limit);
        if($result['total'] >= $limit){
            $all_pages = ceil($result['total']/$limit);
        }
        $this->ci->smarty->assign('title', $query);
        $this->ci->smarty->assign('search_route_name','wyszukaj?search='.$query.'&page=');
        $this->ci->smarty->assign('products_total', $result['total']);
        $this->ci->smarty->assign('products', $result['data']);
        $this->ci->smarty->assign('all_pages',$all_pages);
        $this->ci->smarty->assign('current_page',$current_page);

        // display
        $template = 'product';
        $this->smarty_display($template);
    }

    function load_all_search_products($query = null, $page = 1 , $limit = 10) {

        $query = urlencode($query);//urlencode(serialize($query));
        $url = CONSOLE_URL . '/plociuchy:product/load_all_product_search_ui/' . $query . ',' . $page . ',' .$limit;
        $result = $this->api_call($url);
        return $result;
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