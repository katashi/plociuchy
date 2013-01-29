<?php  //if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| System Constants
|--------------------------------------------------------------------------
*/

// site url for an application (access for back office console )
define('SITE_URL', 'http://vertesprojekty.pl/hurtownia');

// config file
define('CONFIGURATION', 'katashi');

// config api
define('API_DEBUG', 0);
define('API_DATAFORMAT', 'OBJECT'); // OBJECT, JSON

// database access
define('DB_HOSTNAME','sql.vertesdesign.nazwa.pl:3307');
define('DB_USERNAME','vertesdesign_58');
define('DB_PASSWORD','HUrt#12345');
define('DB_DATABASE','vertesdesign_58');

// image resize method
define('MEDIA_DIM', 'both'); //both, width, height
define('MEDIA_WATERMARK', false);

// mail sender
define('EMAIL_PROTOCOL', 'sendmail');
define('EMAIL_MODE', 'html');
define('EMAIL_ENCODING', 'utf-8');

// storage configuration
define('STORAGE_FILE', 0);
define('STORAGE_IMAGE', 0);
define('STORAGE_VIDEO', 0);
define('STORAGE_ACTIVE', 0);
define('STORAGE_TYPE', 'S3');
define('STORAGE_S3_AWS_KEY', '');
define('STORAGE_S3_AWS_SECRET_KEY', '');
define('STORAGE_S3_AWS_ACCOUNT_ID', '');

// transcode video
define('MEDIA_VIDEO_TRANSCODE', 0);

//
//
//
// do not modify nothing below
//
//
//
define('BASE_URL', '/console/index.php/main/run');
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