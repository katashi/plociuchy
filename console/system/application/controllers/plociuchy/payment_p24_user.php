<?php
if (!defined('BASEPATH')) die;

class Payment_P24_User extends Main
{

    public $p24_session_id;
    public $p24_id_sprzedawcy;
    public $p24_crc;

    function Payment_P24_User($_ci = '')
    {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        $this->load->model('plociuchy/payment_p24_user_model');
        $this->load->model('plociuchy/product_reservation_model');
        //
        $this->init();
    }

    /*
    * Fukcja init(). Ustawia podstawowe zmienne
    */
    public function init()
    {
        $this->p24_session_id = md5(time());
        $this->p24_id_sprzedawcy = '18592';
        $this->p24_crc = '51e016ba07cd389e';

    }

    // display
    function display()
    {
        $this->ci->smarty->display('plociuchy/payment_p24_user.html');
    }

    function display_add()
    {
        $this->ci->smarty->display('plociuchy/payment_p24_user_add.html');
    }

    function display_edit($id = null)
    {
        $this->ci->smarty->assign('id', $id);
        $this->ci->smarty->display('plociuchy/payment_p24_user_edit.html');
    }

    // load
    function load_all()
    {
        echo '{"total":' . json_encode($this->payment_p24_user_model->load_all_count()) . ', "data":' . json_encode($this->payment_p24_user_model->load_all()) . '}';
    }

    function load_all_user($id = null)
    {
        echo '{"total":' . json_encode($this->payment_p24_user_model->load_all_user_count($id)) . ', "data":' . json_encode($this->payment_p24_user_model->load_all_user($id)) . '}';
    }

    function load($id = null)
    {
        echo '{"success": 1, "data":' . json_encode($this->payment_p24_user_model->load($id)) . '}';
    }

    // add
    function add()
    {
        $result = $this->payment_p24_user_model->add();
        echo '{"success": ' . $result . '}';
    }

    // edit
    function edit($id = null)
    {
        $result = $this->payment_p24_user_model->edit($id);
        echo '{"success": ' . $result . '}';
    }

    // delete
    function delete($id = null)
    {
        $result = $this->payment_p24_user_model->delete($id);
        echo '{"success":' . $result . '}';
    }

    function add_user_ui()
    {
        $_POST['p24_session_id'] = $this->p24_session_id;
        $_POST['p24_id_sprzedawcy'] = $this->p24_id_sprzedawcy;
        $_POST['p24_kwota'] = number_format($_POST['p24_kwota'], 2, '.', '') * 100;
        $_POST['p24_crc'] = md5($this->p24_session_id . '|' . $this->p24_id_sprzedawcy . '|' . $_POST['p24_kwota'] . '|' . $this->p24_crc);
        //Dodajemy zamowienie do usera
        $inserted_id = $this->payment_p24_user_model->add();
        $data = $_POST;
        $data['id'] = $inserted_id;
        echo '{"success":"true","data":' . json_encode($data) . '}';
    }

    function add_reservation_ui()
    {
        $this->product_reservation_model->add();
        echo '{"success":"true"}';
    }

    /*
    * Funkcja pozytywnej odpowiedzi z P24
    */
    function payment_p24_ok_ui()
    {
        //pobieramy dane z $_POST
        $p24_session_id =  $_POST["p24_session_id"];
        $p24_order_id = $_POST["p24_order_id"];
        $p24_id_sprzedawcy = $_POST['p24_id_sprzedawcy'];
        //pobiermay cene z bazy
        $obj = $this->payment_p24_user_model->load($p24_session_id, 'p24_session_id');
        $p24_kwota = $obj['p24_kwota'];
        //Weryfikujemy
        $WYNIK = $this->payment_p24_weryfikacja($p24_id_sprzedawcy, $p24_session_id, $p24_order_id, $p24_kwota);

        unset($_POST);
        if ($WYNIK[0] == "TRUE") {
            // transakcja prawidłowa
            $_POST['status'] = '1';
            $this->payment_p24_user_model->edit($obj['id']);
            //rezerwacja zmiana statusu
            $_POST = array();
            $_POST['active'] = '1';
            $obj2 = $this->product_reservation_model->load($obj['id'],'id_payment_p24_user');
            $this->product_reservation_model->edit($obj2['id']);
            echo '{"success":"true"}';
        } else {
            // transakcja błędna
            // $WYNIK[1] - kod błędu
            // $WYNIK[2] - opis
            $_POST = array();
            $_POST['status'] = '-1';
            $this->payment_p24_user_model->edit($obj['id']);
            //rezerwacja zmiana statusu
            $_POST = array();
            $_POST['active'] = '-1';
            $obj2 = $this->product_reservation_model->load($obj['id'],'id_payment_p24_user');
            $this->product_reservation_model->edit($obj2['id']);
            echo '{"success":"false"}';
        }

    }

    /*
     * Funkcja negatywnej odpowiedzi z P24
     */
    function payment_p24_error_ui()
    {
        //pobieramy dane z $_POST
        $p24_session_id = $_POST["p24_session_id"];
        //$p24_order_id = $_POST["p24_order_id"];
        //$p24_id_sprzedawcy = $_POST['p24_id_sprzedawcy'];

        $obj = $this->payment_p24_user_model->load($p24_session_id, 'p24_session_id');
        //zmiana statusu na nieudany
        $_POST = array();
        $_POST['status'] = '-1';
        $this->payment_p24_user_model->edit($obj['id']);
        //rezerwacja zmiana statusu
        $_POST = array();
        $_POST['active'] = '-1';
        $obj2 = $this->product_reservation_model->load($obj['id'],'id_payment_p24_user');
        $this->product_reservation_model->edit($obj2['id']);
        echo '{"success":"false"}';
    }

    function payment_p24_weryfikacja($p24_id_sprzedawcy, $p24_session_id, $p24_order_id, $p24_kwota)
    {
        $P = array();
        $RET = array();
        //$url = "https://secure.przelewy24.pl/transakcja.php";
        $url = "https://sandbox.przelewy24.pl/transakcja.php";
        $P[] = "p24_id_sprzedawcy=" . $p24_id_sprzedawcy;
        $P[] = "p24_session_id=" . $p24_session_id;
        $P[] = "p24_order_id=" . $p24_order_id;
        $P[] = "p24_kwota=" . $p24_kwota;
        $P[] = "p24_crc=" . md5($p24_session_id . "|" . $p24_order_id . "|" . $p24_kwota . "|" . $this->p24_crc . "");
        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        if (count($P)) curl_setopt($ch, CURLOPT_POSTFIELDS, join("&", $P));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($ch);
        curl_close($ch);
        $T = explode(chr(13) . chr(10), $result);
        $res = false;
        foreach ($T as $line) {
            $line = ereg_replace("[\n\r]", "", $line);
            if ($line != "RESULT" and !$res) continue;
            if ($res) $RET[] = $line;
            else $res = true;
        }
        return $RET;
    }

}