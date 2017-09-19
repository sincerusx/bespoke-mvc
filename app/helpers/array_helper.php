<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:12
 */

if( !function_exists('array_to_object')){
	function array_to_object($array = array())
	{
		if( !empty($array)){
			$data = false;

			foreach($array as $akey => $aval){
				$data->{$akey} = $aval;
			}

			return $data;
		}

		return false;
	}
}