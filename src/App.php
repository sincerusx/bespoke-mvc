<?php

class App
{

	/**
	 * @var string $CODE_debug
	 */
	public static $CODE_debug;

	/**
	 * @var float $START_TIME
	 */
	public static $START_TIME;

	/**
	 * @var int $MEMORY_USAGE
	 */
	public static $MEMORY_USAGE;

	/**
	 * App constructor.
	 */
	public function __construct()
	{
		self::$CODE_debug = 'ndsuoijrth3289hvfesihg374';

		self::$START_TIME = microtime(true);
		self::$MEMORY_USAGE = memory_get_usage();
	}

	/**
	 * Check if dev cookie is valid
	 *
	 * @param $cookie
	 *
	 * @return bool
	 */
	public static function isValidDevCookie($cookie)
	{

		if(isset($_COOKIE[ 'dev' . strtolower($cookie) ])){
			if($_COOKIE[ 'dev' . strtolower($cookie) ] == self::$CODE_debug){
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if remote address is from a developer registered IP
	 *
	 * @return bool
	 */
	public static function isDevIP()
	{
		if(in_array($_SERVER[ 'REMOTE_ADDR' ], Registry::get('IP_debug'))){
			return true;
		}

		return false;
	}

	/**
	 * Redirect to specified url
	 *
	 * @param $url
	 */
	public static function Redirect($url)
	{
		if( !headers_sent()){
			header('Location: ' . $url);
			exit;
		}
		else{
			echo '<script type="text/javascript">';
			echo 'window.location.href="' . $url . '";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
			echo '</noscript>';
		}
		exit;
	}

	/**
	 * Check if server is localhost
	 *
	 * @return bool
	 */
	public static function isLocalhost()
	{
		if($_SERVER[ 'REMOTE_ADDR' ] == '127.0.0.1' || $_SERVER[ 'REMOTE_ADDR' ] == '::1'){
			return true;
		}

		return false;
	}

}