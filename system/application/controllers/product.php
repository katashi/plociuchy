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
        switch ($title_call) {
            case 'kategorie':
                $title = 'Kategorie';
                $link = 'kategoria';
                $result = $this->load_all_category();
                break;
            case 'marki':
                $title = 'Markę';
                $link = 'marka';
                $result = $this->load_all_vendor();
                break;
            default:
                echo '';
                break;
        }

        //tworzymy tabele liter
        $letters_array = array();
        foreach ($result['data'] as $data) {
            $letters_array[$data['letter']][$data['id']] = $data['title'];
        }
        $this->ci->smarty->assign('title', $title);
        $this->ci->smarty->assign('link', $link);
        $this->ci->smarty->assign('letters_array', $letters_array);
        // display
        $this->smarty_display($template);
    }

    function display_product_single($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        // load product
        $product = $this->load_product($id);

        // load product comments
        $product_comments = $this->load_all_product_comments_user($id);

        $this->ci->smarty->assign('product_view', true);
        $this->ci->smarty->assign('products');
        $this->ci->smarty->assign('product_comments', $product_comments['data']);
        $this->ci->smarty->assign('product', $product['data']);
        // display
        $this->smarty_display($template);
    }

    function display_category_single_list($template = null, $title_call = null, $id_category = null, $page = null) {
        $this->assign_template_titlecall($template, $title_call);
        //get name cat
        $cat = $this->load_category($id_category);
        $title = $cat['data']['title'];

        //Set limit
        $limit = 8;
        $current_page = (isset($page) ? $page : 1);
        $all_pages = 0;
        // load products
        $result = $this->load_all_products($id_category, 'id_category', $current_page, $limit);

        if ($result['total'] >= $limit) {
            $all_pages = ceil($result['total'] / $limit);
        }

        $this->ci->smarty->assign('id', $id_category);
        $this->ci->smarty->assign('title', $title);
        $this->ci->smarty->assign('route_name', 'kategoria');
        $this->ci->smarty->assign('products_total', $result['total']);
        $this->ci->smarty->assign('products', $result['data']);
        $this->ci->smarty->assign('all_pages', $all_pages);
        $this->ci->smarty->assign('current_page', $current_page);
        // display
        $this->smarty_display($template);
    }

    function display_vendor_single_list($template = null, $title_call = null, $id_vendor = null) {
        $this->assign_template_titlecall($template, $title_call);
        //set page title
        $ven = $this->load_vendor($id_vendor);
        $title = $ven['data']['title'];
        //Set limit
        $limit = 8;
        $current_page = (isset($page) ? $page : 1);
        $all_pages = 0;
        // load products
        $result = $this->load_all_products($id_vendor, 'id_cendor', $current_page, $limit);

        if ($result['total'] >= $limit) {
            $all_pages = ceil($result['total'] / $limit);
        }
        $this->ci->smarty->assign('id', $id_vendor);
        $this->ci->smarty->assign('title', $title);
        $this->ci->smarty->assign('route_name', 'marka');
        $this->ci->smarty->assign('products_total', $result['total']);
        $this->ci->smarty->assign('products', $result['data']);
        $this->ci->smarty->assign('all_pages', $all_pages);
        $this->ci->smarty->assign('current_page', $current_page);

        // display
        $this->smarty_display($template);
    }

    // load
    function load_all_title_call($title_call = null) {
        $url = CONSOLE_URL . '/_structure:structure_website/load_all_title_call_paginate/' . $title_call;
        $result = $this->api_call($url);
        return $result;
    }

    function load_product($id = null) {
        $url = CONSOLE_URL . '/plociuchy:product/load/' . $id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }

    function load_all_product_comments_user($id_product) {
        $url = CONSOLE_URL . '/plociuchy:product_comment_user/load_all_comments_ui/' . $id_product . ',id_product';
        return $result = $this->api_call($url);
    }

    function load_all_products($id = null, $where = 'id', $page = 1, $limit = 10) {
        $url = CONSOLE_URL . '/plociuchy:product/load_all_product/' . $id . ',' . $where . ',' . $page . ',' . $limit;
        $result = $this->api_call($url);
        return $result;
    }

    function load_category($id) {
        $url = CONSOLE_URL . '/plociuchy:product_dict_category/load/' . $id;
        $result = $this->api_call($url);
        return $result;
    }

    function load_vendor($id) {
        $url = CONSOLE_URL . '/plociuchy:product_dict_vendor/load/' . $id;
        $result = $this->api_call($url);
        return $result;
    }

    function load_all_category() {
        $data['sort'] = 'title';
        $data['dir'] = 'ASC';
        $url = CONSOLE_URL . '/plociuchy:product_dict_category/load_all_letters/';
        $result = $this->api_call($url, $data);
        return $result;
    }

    function load_all_vendor() {
        $data['sort'] = 'title';
        $data['dir'] = 'ASC';
        $url = CONSOLE_URL . '/plociuchy:product_dict_vendor/load_all_letters/';
        $result = $this->api_call($url, $data);
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