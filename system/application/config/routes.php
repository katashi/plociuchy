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
//link do walidacji
$route['weryfikacja/(:any)'] = "main/run/user/display_verification/$1";
$route['przypomnienie-hasla/(:any)'] = "main/run/user/password_reset_confirm/$1";
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