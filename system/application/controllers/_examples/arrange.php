<?php
class Arrange extends Hub {

	function Arrange($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
	}

    // display
    function display($template = null, $title_call = null, $page = null) {
        $this->assign_template_titlecall($template, $title_call);
        //$this->ci->session->unset_userdata('paginate_arrange');
        //$this->ci->session->unset_userdata('filter_arrange');
        // paginate
        $paginate = $this->paginate_init($template, ARRANGE_LIMIT, $this->load_all_ui_count(true));
        if (isset($page)) { $this->paginate_page_current_set($template, $page); } else { $page = $this->paginate_page_current_get($template); }
        $paginate_number = $this->paginate_from($this->paginate_get($template));
        $paginate = $this->paginate_get($template);
        $this->ci->smarty->assign('paginate', $paginate);
        $this->ci->smarty->assign('paginate_from', $paginate_number['from']);
        $this->ci->smarty->assign('paginate_to', $paginate_number['to']);
        $this->ci->smarty->assign('paginate_controller', $template);
        // arrange load
        $condition = array();
        $condition['limit'] = ARRANGE_LIMIT;
        $condition['page'] = $paginate['page_current'];
        $arrange = $this->load_all_ui(serialize($condition), true);
        $arrange = $this->rate_compute($arrange, 1);
        $arrange_count = $this->load_all_ui_count(true);
        $this->ci->smarty->assign('arrange', $arrange);
        $this->ci->smarty->assign('arrange_count', $arrange_count);
        $this->ci->smarty->assign('filter_arrange', $this->ci->session->userdata('filter_arrange'));
        $this->ci->smarty->assign('genre', $this->genre_get_all(1));
        $this->smarty_display($template);
    }
    function display_redirect($template = null, $title_call = null, $variable = null) {
        $result = unserialize($variable);
        //
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', $result['operation']);
        //
        $this->display('arrange');
    }
    function display_player($template = null, $title_call = null, $id = null) {
        $this->assign_template_titlecall($template, $title_call);
        $this->display_increment('kreomaniak_arrange', $id);
        $result = $this->load_ui($id);
        $this->ci->smarty->assign('arrange', $result['data']);
        $this->ci->smarty->assign('user', $result['user']);
        $this->ci->smarty->assign('comment', $this->comment_load_all($id, 1));
        $this->ci->smarty->assign('rate', $this->rate_get($id, 1));
        $this->ci->smarty->assign('rate_enabled', $this->rate_enabled($id, 1));
        $this->smarty_display($template.'/player');
    }
    function display_add($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call, true);
        $genre = $this->genre_get_all(1);
        $this->ci->smarty->assign('genre', $genre);
        $this->smarty_display($template.'/add');
    }

    // load
    function load_all_ui($condition = null, $filtrate = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_all_ui/'.$condition;
        $data = isset($filtrate) ? $this->filter_get() : null;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_all_ui_count($filtrate = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_all_ui_count';
        $data = isset($filtrate) ? $this->filter_get() : null;
        $result = $this->api_call($url, $data);
        if ($result['success'] == 1) {
            return $result['data'];
        }
    }
    function load_ui($id = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/load_ui/'.$id;
        $result = $this->api_call($url);
        if ($result['success'] == 1) {
            return $result;
        }
    }

    // add
    function add($template = null, $title_call = null) {
        $url = CONSOLE_URL.'/kreomaniak:arrange/add_ui';
        $data = $_POST;
        $data['genre'] = $this->genre_get($_POST['id_genre']);
        $result = $this->api_call($url, $data, true);
        $this->ci->smarty->assign('result', $result['success']);
        $this->ci->smarty->assign('code', $result['code']);
        $this->ci->smarty->assign('operation', 'arrange_add');
        // reduce points
        $this->point_reduce(1, 'arrange_add');
        // display
        $variable = array();
        $variable['success'] = $result['success'];
        $variable['code'] = $result['code'];
        $variable['operation'] = 'arrange_add';
        $variable = serialize($variable);
        $this->smarty_redirect_variables('arrange/display_redirect', 'arrange', $variable);
        //$arrange = new Arrange($this->ci);
        //$arrange->display('arrange');
    }

    // filter
    function filter_get() {
        $filter = $this->ci->session->userdata('filter_arrange');
        if ($filter == '') {
            $this->filter_set_default();
            $filter = $this->ci->session->userdata('filter_arrange');
        }
        return $filter;
    }
    function filter_set($template = null, $title_call = null) {
        // lets prepare filter data
        $arrange_filter1 = isset($_POST['arrange_filter1']) ? $_POST['arrange_filter1'] : null;
        $arrange_filter2 = isset($_POST['arrange_filter2']) ? $_POST['arrange_filter2'] : null;
        $arrange_filter3 = isset($_POST['arrange_filter3']) ? $_POST['arrange_filter3'] : null;
        // setup values
        $filter_arrange = array();
        $filter_arrange['filter1'] = (int)$arrange_filter1;
        $filter_arrange['filter2'] = (int)$arrange_filter2;
        $filter_arrange['filter3'] = (int)$arrange_filter3;
        $this->ci->session->set_userdata('filter_arrange', $filter_arrange);
        // display template
        $this->smarty_redirect($template, $title_call);
    }
    function filter_set_default() {
        $filter_arrange = array();
        $filter_arrange['filter1'] = 0;
        $filter_arrange['filter2'] = 0;
        $filter_arrange['filter3'] = 0;
        $this->ci->session->set_userdata('filter_arrange', $filter_arrange);
    }
    function filter_delete() {
        $this->ci->session->unset_userdata('filter_text');
    }

}