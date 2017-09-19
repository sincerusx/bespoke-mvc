<?php

/**
 * Check for ssl request and save http or https in global var URL_SCHEME.
 */
if( !isset($_SERVER[ 'HTTPS' ]) || $_SERVER[ 'HTTPS' ] != 'on'){
	define("URL_SCHEME", "http://");
}
else{
	define("URL_SCHEME", "https://");
}

define('DOMAIN_NAME', $_SERVER[ 'HTTP_HOST' ]);
define('BRAND_NAME', 'SINCERUSX');
define('DEFAULT_LANG', 'en');
define('START_MONTH', '');
define('LAUNCH_MONTH', '');
define('START_YEAR', '2016');
define('DEVELOPER', 'SINCERUSX');

define('SITE_URL', URL_SCHEME . DOMAIN_NAME . '/');
define('ASSETS_URL', SITE_URL . 'assets/');
define('JS_URL', ASSETS_URL . 'js/');
define('CSS_URL', ASSETS_URL . 'css/');
define('IMAGE_URL', ASSETS_URL . 'img/');

define('APP_DIR', DOC_ROOT . 'app' . DS . 'controllers' . DS);
define('CONTROLLER_DIR', DOC_ROOT . 'app' . DS . 'controllers' . DS);
define('MODEL_DIR', DOC_ROOT . 'app' . DS . 'models' . DS);
define('LIBRARY_DIR', DOC_ROOT . 'app' . DS . 'library' . DS);

define('APP_SECRET', '');
define('CODE_DEBUG', base64_encode(gzencode('')));

define('PASSCODE', 'qmpz');
define('DOMAIN_HOST', DOMAIN_NAME); // used to set a session and this session to work on every subdomain

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *		localhost
 *		development
 *		staging
 *		production
 */
if($_SERVER[ "REMOTE_ADDR" ] == "127.0.0.1" || $_SERVER[ "REMOTE_ADDR" ] == "::1"){
	define('ENVIRONMENT', 'localhost');
	define("SHOW_DEBUG", true);
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	define('DEFAULT_THEME', 'boilerplate');
}
elseif($_SERVER[ 'HTTP_HOST' ] == 'dev.liamnelson.co.uk'){
	define('ENVIRONMENT', 'Development');
	define("SHOW_DEBUG", true);
	error_reporting(-1);
	ini_set('display_errors', 1);
	define('DEFAULT_THEME', 'boilerplate');
}
elseif($_SERVER[ 'HTTP_HOST' ] == 'stage.liamnelson.co.uk'){
	define('ENVIRONMENT', 'Stage');
	define("SHOW_DEBUG", false);
	error_reporting(-1);
	ini_set('display_errors', 1);
	define('DEFAULT_THEME', 'boilerplate');
}
elseif($_SERVER[ 'HTTP_HOST' ] == 'liamnelson.co.uk' || 'www.liamnelson.co.uk'){
	define('ENVIRONMENT', 'Production');
	define("SHOW_DEBUG", false);
	ini_set('display_errors', 0);
	if(version_compare(PHP_VERSION, '5.3', '>=')){
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
	}
	else{
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
	}
	define('DEFAULT_THEME', 'boilerplate');
}
else{
	header('HTTP/1.1 503 Service Unavailable.', true, 503);
	echo 'The application environment is not set correctly.';
	exit(1); // EXIT_ERROR
}

// the IPs allowed to view the site
$IP_debug = array(
	// Local development/testing
	"127.0.0.1",
	"::1",
	// 95 Clapham
	"90.197.68.231",
	"94.10.100.152",

	"2a02:c7d:48e:3a00:d8c9:5b80:fbfc:1828",
	"2a02:c7d:48e:3a00:bd64:6c4a:2a12:382a",
	"2a02:c7d:48e:3a00:ed61:9fa9:4305:8a86",
);

Application::loadBase('Registry');
Registry::set($IP_debug, 'IP_debug');

// collecting info about the system load
# Registry::set(microtime( true ),'time');
# Registry::set(memory_get_usage(),'memory');

/*
 * Load env. configurations
 */
$env_db = DOC_ROOT . 'configs' . DS . strtolower(ENVIRONMENT) . '/database.php';
if(file_exists($env_db)){
	include_once($env_db);
}

// Register database connections
$db = array(
	"DBHOST" => DBHOST,
	"DBUSER" => DBUSER,
	"DBPASS" => DBPASS,
	"DBNAME" => DBNAME,
	"DBPORT" => DBPORT,
);
Registry::set(new Database($db), "dbCMS");