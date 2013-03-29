<?php  //if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| System Constants
|--------------------------------------------------------------------------
*/

// site url for an application (access for back office console )
define('SITE_URL', 'http://plo-ciuchy.pl');

// console path
define('CONSOLE_URL', 'http://plo-ciuchy.pl/console/index.php/main/run');
define('MEDIA_URL', 'http://plo-ciuchy.pl/media');
// configuration

define('CONFIGURATION', 'katashi');
// config api
define('API_DEBUG', 0);
define('API_DATAFORMAT', 'OBJECT'); // OBJECT, JSON

// config payu access
//define('PLATNOSCI_POSID', '101657');
//define('PLATNOSCI_1MD5', '6bdadc0db7f01232a6397b3a8b7cab02');
//define('PLATNOSCI_2MD5', '70fe0953881517e7b02c54f6818849d5');
//define('PLATNOSCI_POSAUTHKEY', 'KRWWIyg');

//
//
//
// do not modify nothing below
//
//
//
define('BASE_URL', '/index.php/main');
define('APP_URL', SITE_URL.BASE_URL);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./system/application/config/constants.php */