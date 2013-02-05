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

        $last_added = $this->last_added();
        $this->ci->smarty->assign('last_added', $last_added['data']);

        $article = new Article($this->ci);
        $article = $article->load_last_ui(45);
        $this->ci->smarty->assign('article', $article);

        $client = new Client($this->ci);
        $client = $client->client_last_registered();
        $this->ci->smarty->assign('client', $client);

        $song_top = new Song($this->ci);
        $song_top = $song_top->load_top_ui();
        $song_top = $this->rate_compute($song_top, 0);
        $song_top = $this->rate_sort($song_top, 0);
        $this->ci->smarty->assign('song_top', $song_top);

        $song = new Song($this->ci);
        $condition = array();
        $condition['limit'] = 10;
        $condition['order'] = 'date_added';
        $song = $song->load_all_ui(serialize($condition));
        $song = $this->rate_compute($song, 0);
        $this->ci->smarty->assign('song', $song);

        $arrange = new Arrange($this->ci);
        $condition = array();
        $condition['limit'] = 10;
        $condition['order'] = 'date_added';
        $arrange = $arrange->load_all_ui(serialize($condition));
        $arrange = $this->rate_compute($arrange, 1);
        $this->ci->smarty->assign('arrange', $arrange);

        $text = new Text($this->ci);
        $condition = array();
        $condition['limit'] = 10;
        $condition['order'] = 'date_added';
        $text = $text->load_all_ui(serialize($condition));
        $text = $this->rate_compute($text, 2);
        $this->ci->smarty->assign('text', $text);

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

}