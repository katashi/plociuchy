<?php
if (!defined('BASEPATH')) die;

class Payment_P24 extends Main
{
    public $p24_session_id;
    public $p24_id_sprzedawcy;
    public $p24_crc;

    function Payment_P24($_ci = '')
    {
        parent::Controller();
        //
        $this->ci = $_ci;
        // load models
        $this->load->model('main_model');
        //$this->load->model('plociuchy/reservation_model');
        $this->load->model('plociuchy/payment_p24_user_model');
        $this->load->model('plociuchy/payment_p24_partner_model');
        //init platnosci
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

    /*
     * Funkcja pozytywnej odpowiedzi z P24
     */
    function payment_p24_ok()
    {
        //pobieramy dane z $_POST
        $session_id = $_POST["p24_session_id"];
        $order_id = $_POST["p24_order_id"];
        $id_sprzedawcy = $this->p24_id_sprzedawcy;
        $kwota = number_format($_POST['p24_kwota'], 2, '.', '') * 100; //WYNIK POBRANY Z TWOJEJ BAZY(w WALUTA / 100)
        //Weryfikujemy
        $WYNIK = $this->payment_p24_weryfikacja($id_sprzedawcy, $session_id, $order_id, $kwota);

        if ($WYNIK[0] == "TRUE") {
            // transakcja prawidłowa

        } else {
            // transakcja błędna

            // $WYNIK[1] - kod błędu
            // $WYNIK[2] - opis
        }

    }

    /*
     * Funkcja negatywnej odpowiedzi z P24
     */
    function payment_p24_error()
    {
        echo 'error';
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

    function sample_form()
    {
        /*
         * przelewy 24;
         */
        $session_id = $this->p24_session_id;
        echo $p24_id_sprzedawcy = $this->p24_id_sprzedawcy;
        $price = 10.32;
        $cena = number_format($price, 2, '.', '') * 100;

        $crc = md5($session_id . '|' . $p24_id_sprzedawcy . '|' . $cena . '|' . $this->p24_crc);
        echo '<form action="https://sandbox.przelewy24.pl/index.php" method="post" class="form">
                <input type="text" name="p24_session_id" value="' . $session_id . '" />
                <input type="text" name="p24_id_sprzedawcy" value="' . $p24_id_sprzedawcy . '" />
                <input type="text" name="p24_kwota" value="' . $cena . '" />
                <input type="text" name="p24_opis" value="TYTUŁ" />
                <input type="text" name="p24_klient" value="Jan Kowalski" />
                <input type="text" name="p24_adres" value="ul. Polska 33/33" />
                <input type="text" name="p24_kod" value="66-777" />
                <input type="text" name="p24_miasto" value="Poznań" />
                <input type="text" name="p24_kraj" value="PL" />
                <input type="text" name="p24_email" value="robert.osiadacz@tworzeniestron.pl" />
                <input type="text" name="p24_language" value="pl" />
                <input type="text" name="p24_return_url_ok" value="http://www.plo-ciuchy.pl/console/index.php/main/run/plociuchy:payment_p24/payment_p24_ok" />
                <input type="text" name="p24_return_url_error" value="http://www.plo-ciuchy.pl/console/index.php/main/run/plociuchy:payment_p24/payment_p24_error" />
                <input type="hidden" name="p24_crc" value="' . $crc . '" />
                <input name="submit_send" value="wyślij" type="submit" />
                </form>';
    }

    function sample_form2(){
        $_POST['test'] = '1';
        $_POST['p24_session_id'] = 'a';
        var_dump($_POST);
        $url = 'https://sandbox.przelewy24.pl/index.php';
        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
    }

}