<?php

date_default_timezone_set('Europe/London');

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors', 1);

/*---------------------------------------------------------------
 * GLOBAL APPLICATION PATHS
 *--------------------------------------------------------------- */
/**
 * @const DS Directory Separator
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * @const EXT PHP extension
 */
define('EXT', '.php');

/**
 * @const DOC_ROOT Document Root Path
 */
define('DOC_ROOT', realpath(dirname(__DIR__)) . DS);

$app = (file_exists(DOC_ROOT . 'app/bootstrap.php')) ? require DOC_ROOT . 'app/bootstrap.php' : require '../app/bootstrap.php';

$router = Router::factory($_SERVER[ 'REQUEST_URI' ])->action();
# dd($router, false);
$dispatcher = new Dispatcher($router, null);
# dd($dispatcher);

function convertMem($size)
{
	$unit = array(
		'b',
		'kb',
		'mb',
		'gb',
		'tb',
		'pb',
	);

	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[ $i ];
}