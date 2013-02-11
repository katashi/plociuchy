<?php
class Home extends Hub {

	function Home($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        echo $template.','.$title_call;
        // article
        $article = new Article($this->ci);
        $article = $article->load_all_title_call($title_call);
        $this->ci->smarty->assign('article_total', $article['total']);
        $this->ci->smarty->assign('article', $article['data']);
        // category
        $this->ci->smarty->assign('category', $this->category_load_all());
        // vendor
        $this->ci->smarty->assign('vendor', $this->vendor_load_all());
        // display
        $this->smarty_display($template);
    }
    function display_redirect($template = null, $title_call = null, $variable = null) {
        $result = unserialize($variable);
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', $result['operation']);
        //
        $this->display('home');
    }

    // category
    function category_load_all($field = null, $value = null) {
        $url = CONSOLE_URL.'/plociuchy:product_dict_category/load_all';
        $result = $this->api_call($url);
        if ($result['total'] > 0) {
            return $result['data'];
        } else {
            return 0;
        }
    }
    // vendor
    function vendor_load_all($field = null, $value = null) {
        $url = CONSOLE_URL.'/plociuchy:product_dict_vendor/load_all';
        $result = $this->api_call($url);
        if ($result['total'] > 0) {
            return $result['data'];
        } else {
            return 0;
        }
    }

}