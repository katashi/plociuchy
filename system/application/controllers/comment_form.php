<?php
class Comment_Form extends Hub {

    function Comment_Form($_ci) {
        parent::Controller();
        //
        $this->ci = $_ci;
    }

    // display
    function display($template = null, $title_call = null) {
        $this->assign_template_titlecall($template, $title_call);
        // display
        $this->smarty_display($template);
    }

    function display_form($template = null, $title_call = null, $id_product=null) {
        if (isset($_POST['submit'])) {
            //dodawnie komentarza
            $id_product = $_POST['id_product'];
            $data =  array();
            $data['id_product'] = $_POST['id_product'];
            $data['nick'] = $_POST['name'];
            $data['email'] = $_POST['email'];
            $data['text1'] = $_POST['message'];
            $data['nick'] = $_POST['name'];
            //$data['id_user'] = '';
            $data['active'] = 0;
            $url = CONSOLE_URL.'/plociuchy:product_comment_user/add';
            $result = $this->api_call($url,$data);
            $this->add_message_ok('Komentarz został przekazany do moderacji');
        }
        if(!$id_product){
            die('Brak info o produkcie');
        }
        $this->ci->smarty->assign('id_product',$id_product);
        $this->smarty_display('extension/comment_form');
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