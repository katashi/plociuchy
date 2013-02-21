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

        //złąpanie dokumentu  welcome
        $welcome = $this->load_all_title_call('glowna-witamy');
        //załadowanie dokumentów jak zamowić
        $how_to_rent = $this->load_all_title_call('glowna-jak-wypozyczyc');
        //załadowanie dokumentów jak wystawiać
        $how_to_insert = $this->load_all_title_call('glowna-jak-wystawic');
        //załadowanie dokumentu glowna-ubrania
        $main_wear = $this->load_all_title_call('glowna-ubranie');
        // display
        $this->ci->smarty->assign('main_wear', $main_wear['data'][0]);
        $this->ci->smarty->assign('how_to_rent', $how_to_rent['data']);
        $this->ci->smarty->assign('how_to_insert', $how_to_insert['data']);
        $this->ci->smarty->assign('welcome', $welcome['data'][0] );
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

    function load_all_title_call($title_call = null) {
        $url = CONSOLE_URL.'/_structure:structure_website/load_all_title_call/'.$title_call;
        $result = $this->api_call($url);
        return $result;
    }
}