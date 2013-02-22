<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "main";
$route['scaffolding_trigger'] = "";

// routing
$route['artykul'] = "main/run/article/display/$1";
$route['artykul/(:any)'] = "main/run/article/display_single/$1";
$route['product/(:any)'] = "main/run/product/display_product_single/$1";
$route['kategoria/(:any)'] = "main/run/product/display_category_single_list/$1";
$route['marka/(:any)'] = "main/run/product/display_vendor_single_list/$1";
//routing do walidacji
$route['weryfikacja/(:any)'] = "main/run/user/display_verification/$1";
$route['przypomnienie-hasla/(:any)'] = "main/run/user/password_reset_confirm/$1";
//routing usera
$route['wyloguj'] = "main/run/user/display_logout/$1";
//routing do panelu usera
$route['panel'] = "main/run/user_panel/display_front/$1";
$route['panel-dane'] = "main/run/user_panel/display_data/$1";
$route['panel-zamowienia'] = "main/run/user_panel/display_orders/$1";
$route['panel-zmiana-hasla'] = "main/run/user_panel/display_change_password/$1";
/////////////////////PARTNER//////////////////////////////////////
//routing do walidacji
$route['partner-weryfikacja/(:any)'] = "main/run/partner/display_verification/$1";
$route['partner-przypomnienie-hasla/(:any)'] = "main/run/partner/password_reset_confirm/$1";
//routing usera
$route['partner-wyloguj'] = "main/run/partner/display_logout/$1";
//routing do panelu partnera
$route['partner-logowanie'] = "main/run/partner/display_login/$1";
$route['partner-rejestracja'] = "main/run/partner/display_registration/$1";
$route['partner-przypomnienie-hasla'] = "main/run/partner/partner_user_password_reminder/$1";

$route['partner-panel'] = "main/run/partner_panel/display_front/$1";
$route['partner-panel-dane'] = "main/run/partner_panel/display_data/$1";
$route['partner-panel-zmiana-hasla'] = "main/run/partner_panel/display_change_password/$1";
$route['partner-panel-wystaw'] = "main/run/partner_panel/display_add_product/$1";
$route['partner-panel-produkty'] = "main/run/partner_panel/display_products/$1";
$route['partner-panel-produkty-odrzucone'] = "main/run/partner_panel/display_rejected_products/$1";
$route['partner-panel-rezerwacje'] = "main/run/partner_panel/display_reservations/$1";
$route['partner-panel-rezerwacje-aktualne'] = "main/run/partner_panel/display_reservations_actual/$1";
$route['partner-panel-historia-rezerwacji'] = "main/run/partner_panel/display_reservation_history/$1";
$route['partner-panel-saldo'] = "main/run/partner_panel/display_pauyment_balance/$1";
$route['partner-panel-historia-wplat'] = "main/run/partner_panel/display_payment_history/$1";

//routing koszyka
$route['koszyk'] = "main/run/cart/display_cart/$1";
$route['koszyk/podsumowanie'] = "main/run/cart/display_cart_summary/$1";
$route['koszyk/platnosc24-ok'] = "main/run/cart/display_platnosci24_ok/$1";
$route['koszyk/platnosc24-error'] =  "main/run/cart/display_platnosci24_error/$1";

    /*$route['kontakt/(:any)'] = "main/run/kontakt/display/$1";
    $route['kontakt/send'] = "main/run/kontakt/send";
    $route['contact_en/(:any)'] = "main/run/contact_en/display/$1";
    $route['contact_en/send'] = "main/run/contact_en/send";
    $route['kontakt_de/(:any)'] = "main/run/kontakt_de/display/$1";
    $route['kontakt_de/send'] = "main/run/kontakt_de/send";
    $route['glowna/(:any)'] = "main/run/glowna/language_set/$1";
    $route['home/(:any)'] = "main/run/home/language_set/$1";
    $route['zuhause/(:any)'] = "main/run/zuhause/language_set/$1";
    $route['pl'] = "main/run/glowna/language_set/pl";
    $route['en'] = "main/run/home/language_set/en";
    $route['de'] = "main/run/zuhause/language_set/de";*/
$route['(:any)'] = "main/run/$1";;

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */