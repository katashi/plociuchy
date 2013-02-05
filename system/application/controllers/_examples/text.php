<?php
class Text extends Hub {

	function Text($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null, $page = null) {
        $this->assign_template_titlecall($template, $title_call);
        //$this->ci->session->unset_userdata('paginate_text');
        //$this->ci->session->unset_userdata('filter_text');
        // paginate
        $paginate = $this->paginate_init($template, TEXT_LIMIT, $this->load_all_ui_count(true));
        if (isset($page)) { $this->paginate_page_current_set($template, $page); } else { $page = $this->paginate_page_current_get($template); }
        $paginate_number = $this->paginate_from($this->paginate_get($template));
        $paginate = $this->paginate_get($template);
        $this->ci->smarty->assign('paginate', $paginate);
        $this->ci->smarty->assign('paginate_from', $paginate_number['from']);
        $this->ci->smarty->assign('paginate_to', $paginate_number['to']);
        $this->ci->smarty->assign('paginate_controller', $template);
        // text load
        $condition = array();
        $condition['limit'] = TEXT_LIMIT;
        $condition['page'] = $paginate['page_current'];
        $text = $this->load_all_ui(serialize($condition), true);
        $text = $this->rate_compute($text, 2);
        $text_count = $this->load_all_ui_count(true);
        // assign
        $this->ci->smarty->assign('text', $text);
        $this->ci->smarty->assign('text_count', $text_count);
        $this->ci->smarty->assign('filter_text', $this->ci->session->userdata('filter_text'));
        $this->ci->smarty->assign('genre', $this->genre_get_all(2));
        $this->smarty_display($template);
    }
    function display_redirect($template = null, $title_call = null, $variable = null) {
        $result = unserialize($variable);
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', $result['operation']);
        //
        $this->display('text');
    }
    function display_player($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->display_increment('kreomaniak_text', $id);
        $result = $this->load_ui($id);
        $result['data']['text'] = str_replace('
', '<br>', $result['data']['text']);
        $this->ci->smarty->assign('text', $result['data']);
        $this->ci->smarty->assign('user', $result['user']);
        $this->ci->smarty->assign('comment', $this->comment_load_all($id, 2));
        $this->ci->smarty->assign('rate', $this->rate_get($id, 2));
        $this->ci->smarty->assign('rate_enabled', $this->rate_enabled($id, 2));
        $this->smarty_display($template.'/player');
    }
    function display_add($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $genre = $this->genre_get_all(2);
        $this->ci->smarty->assign('genre', $genre);
        $this->smarty_display($template.'/add');
    }

    // load
    function load_all_ui($condition = null, $filtrate = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/load_all_ui/'.$condition;
        $data = isset($filtrate) ? $this->filter_get() : null;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_ui_count($filtrate = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/load_all_ui_count';
        $data = isset($filtrate) ? $this->filter_get() : null;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_ui($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/load_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }

    // add
    function add($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:text/add_ui';
        $data = $_POST;
        $data['genre'] = $this->genre_get($_POST['id_genre']);
        $result = $this->api_call($url, $data, true);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'text_add');
        // reduce points
        $this->point_reduce(1, 'text_add');
        // display
        $variable = array();
        $variable['success'] = $result['success'];
        $variable['code'] = $result['code'];
        $variable['operation'] = 'text_add';
        $variable = serialize($variable);
        $this->smarty_redirect_variables('text/display_redirect', 'text', $variable);
        //$text = new Text($this->ci);
        //$text->display('text');
    }

    // filter
    function filter_get() {
        $filter = $this->ci->session->userdata('filter_text');
        if ($filter == '') {
            $this->filter_set_default();
            $filter = $this->ci->session->userdata('filter_text');
        }
        return $filter;
    }
    function filter_set($template = null, $title_call = null) {
        // lets prepare filter data
        $text_filter1 = isset($_POST['text_filter1']) ? $_POST['text_filter1'] : null;
        $text_filter2 = isset($_POST['text_filter2']) ? $_POST['text_filter2'] : null;
        $text_filter3 = isset($_POST['text_filter3']) ? $_POST['text_filter3'] : null;
        // setup values
        $filter_text = array();
        $filter_text['filter1'] = (int)$text_filter1;
        $filter_text['filter2'] = (int)$text_filter2;
        $filter_text['filter3'] = (int)$text_filter3;
        $this->ci->session->set_userdata('filter_text', $filter_text);
        // display template
        $this->smarty_redirect($template, $title_call);
    }
    function filter_set_default() {
        $filter_text = array();
        $filter_text['filter1'] = 0;
        $filter_text['filter2'] = 0;
        $filter_text['filter3'] = 0;
        $this->ci->session->set_userdata('filter_text', $filter_text);
    }
    function filter_delete() {
        $this->ci->session->unset_userdata('filter_text');
    }

}