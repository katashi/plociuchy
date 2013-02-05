<?php
class Fb extends Hub {

	function Fb($_ci) {
		parent::Controller();
        //
        $this->ci = $_ci;
        //
        $this->init();
	}

    // init
    function init() {
        // include sdk
        require_once './services/facebook/facebook.php';
        // init facebook
        $this->facebook = new Facebook(array(
            'appId'  => FB_APP1_APPID,
            'secret' => FB_APP1_SECRET,
            'cookie' => true,
            'oauth' => true
        ));
    }

    // access token
    function access_token() {
        $access_token = $this->facebook->getAccessToken();
        return $access_token;
    }

    // user id
    function user_id() {
        $id = $this->facebook->getUser();
        return $id;
    }

    // user picture
    function user_picture() {
        $user_id = $this->user_id();
        $picture = 'http://graph.facebook.com/'. $user_id .'/picture';
        return $picture;
    }

    // user details
    function user_detail() {
        return $this->facebook->api('/me','GET');
    }

    // generate login url
    function generate_login_url($params) {
        return $this->facebook->getLoginUrl($params);
    }

    // generate logout url
    function generate_logout_url($params) {
         return $this->facebook->getLogoutUrl($params);
    }

    // restart after facebook logout
    function restart(){
        $this->ci->load->helper('cookie');
        delete_cookie("ci_session");
        delete_cookie("PHPSESSID");
        header("Location: ".SITE_URL);
    }

}