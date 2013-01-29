//
// main configuration
//
var site_url = 'http://vertesprojekty.pl/hurtownia';
var base_url = 'index.php/main/run';

//
// paging limit per load
//
var _paging_limit = 100;

//
// ui base include
//
document.write('<script type="text/javascript" src="javascript/configuration/ui_katashi.js"></script>');

//
// ui include
//
document.write('<script type="text/javascript" src="javascript/configuration/ui_'+ base_ui +'.js"></script>');

//
// tab definitions
//
tab_selected_id = '0';
tab_selected_tree = '0';