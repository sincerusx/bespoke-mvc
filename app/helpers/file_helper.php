<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:12
 */

if( !function_exists('update_file')){
	function update_file($file, $content, $mode = 'w')
	{
		$handle = fopen($file, $mode);
		if( !$handle) throw new Exception('Could not write to cache');
		if(fwrite($handle, $content) === false){
			throw new Exception('Could not write to cache');
		}
		fclose($handle);
	}
}

if( !function_exists('configs_exist')){
	function configs_exist()
	{
		$isOK = (@ file_exists('configs/config.php') && @ file_exists('configs/database.php')
				 && @ file_exists('configs/paths.php') && @ file_exists('admin/configs/paths.php')
				 && @ file_exists('admin/configs/database.php')
		);

		return $isOK;
	}
}