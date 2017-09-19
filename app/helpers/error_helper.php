<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:12
 */

if( !function_exists('isset_full')){
	function isset_full($var)
	{
		return (isset($var) && !empty($var));
	}
}

if( !function_exists('dd')){
	function dd($expression = null, $die = true)
	{
		$debug_backtrace = debug_backtrace();

		$output = "<b><u>SCRIPT DIED</u></b><br><br>";
		$output .= "<b>Line: </b>" . $debug_backtrace[ 0 ][ 'line' ] . "<br>";
		$output .= "<b>Time: </b>" . date("D M Y") . ", <u>" . date("G:i:s") . "</u><br>";
		$output .= "<b>Path: </b>" . $debug_backtrace[ 0 ][ 'file' ] . "<br>";

		echo $output;

		if( !is_null($expression)){
			echo "<pre>";
			print_r($expression);
			echo "</pre>";
		}

		if($die){
			die();
		}
	}
}

if( !function_exists('pretty')){
	function pretty($expression, $var_dump = false, $return = null)
	{
		if(App::isValidDevCookie('_sitedebug') || App::isLocalhost()){
			echo "<pre>";
			if($var_dump){
				var_dump($expression, $return);
			}
			else{
				print_r($expression, $return);
			}
			echo "</pre> <hr> <br>";
		}
	}
}