<?php
class Contact extends Hub {

    function Contact($_ci) {
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

    function display_form($template = null, $title_call = null) {
        if (isset($_POST['submit'])) {
                $name = $_POST['name'];
                $email = $_POST['email'];
                //$firma = $_POST['firma'];
                    $why = $_POST['why'];
                $tel = $_POST['tel'];
                $message = $_POST['message'];
                $from = 'KNS Design';
                //$to = 'osiadaczrobert@gmail.com';
                $to = 'biuro@tworzeniestron.pl';
                $subject = 'Formularz ze strony TworzenieStron.pl';
                $subject2 = 'Potwierdzenie - KNS Design';
                    $header = "From: " . $name . " <" . $email . ">\r\n"; //optional headerfields
                    $header .= "Content-Type: text/html; charset=utf-8\r\n";
                    $header .= "Content-Transfer-Encoding: 8bit\r\n";
                    $header2 = "From: " . $from . " <" . $to . ">\r\n"; //optional headerfields
                    $header2 .= "Content-Type: text/html; charset=utf-8\r\n";
                    $header2 .= "Content-Transfer-Encoding: 8bit\r\n";
                    $human = $_POST['human'];

                    $body = "<span style='font-family: Calibri;font-size:14px;'>Dane: TworzenieStron.pl:<br><br>imię: " . $_POST['name'] . ",<br>";
                    $body .= "email: " . $_POST['email'] . ",<br>";
                    //$body .= "firma: " . $_POST['firma'] . ",<br>";
                    $body .= "tel: " . $_POST['tel'] . ",<br>";
                    $body .= "tresc: " . $_POST['message'] . "<br>";
                    $body .= "typ: " . $_POST['why'] . "<br></span>";

                    $body2 = "<span style='font-family: Calibri;font-size:14px;color:#056cb1;'>Witamy,<br><br>";
                    $body2 .= "Dziękujemy za przesłanie wiadomości.<br>";
                    $body2 .= "Odpowiemy na Twoje zapytanie najszybciej jak będzie to możliwe.<br><br>";
                    $body2 .= "Z Pozdrowieniami,<br>Zespół KNS Design<br>";
                    $body2 .= "Tel. +48 494 34 84<br>";
                    $body2 .= "www.TworzenieStron.pl<br>";
                    $body2 .= "biuro@tworzeniestron.pl<br></span><Br>";
                    $body2 .= "<img width='181' height='64' src='http://www.tworzeniestron.pl/images/tworzenie-stron.png' alt='logo KNS Design' />";

                if ($_POST['submit'] && $human == '5') {
                    if (mail($to, $subject, $body, $header)) {

                        echo '<html><head> <meta http-equiv=\'content-type\' content=\'text/html; charset=utf-8\' /></head><body><p><span style=\'font-family: Trebuchet MS; font-size:12px;\'>Dziękujemy. <Br><Br>Wiadomość została wysłana poprawnie. <br/>Odpowiemy najszybciej jak będzie to możliwe.</p></span></body></html>';
                        mail($email, $subject2, $body2, $header2);
                    } else {
                        echo '<p><span style=\'font-family: Trebuchet MS;font-size:12px;\'>Przepraszamy, proszę spróbować jeszcze raz</p>.
                    <a href=\'javascript:history.go(-1)\'>Wróć</a>
                    </span>';
                    }
                } else if ($_POST['submit'] && $human != '5') {
                    echo '<p><span style=\'font-family: Trebuchet MS;font-size:12px;\'>Jesteś spamem?! Źle wpisano sumę.
                    <a href=\'javascript:history.go(-1)\'>Wróć</a> i spróbuj jeszcze raz.
                </p></span>';
                }
        }
        $this->smarty_display('extension/contact_form');
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