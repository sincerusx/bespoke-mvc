<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:13
 */

if( !function_exists('getSiteLanguages')){
	function getSiteLanguages()
	{
		//get all possible langs
		$sql = "SELECT * FROM " . TABLE_PREFIX . "site_languages WHERE is_online = 1";
		$db = new Database();

		$result_id = $db->query($sql);

		$all_language_options = $db->fetchToArray($result_id);
		$all_langs = array();

		foreach($all_language_options as $k => $v){
			$all_langs[] = $v[ 'lang' ];
		}

		return $all_langs;
	}
}

if( !function_exists('getCurrentLang')){
	function getCurrentLang($request_uri, $default = 'fr')
	{
		/*
			get language

			if lang explicitly specified, then use that.
			else use session
			else default=fr

			we don't use the page, because as of now, the
			language is used to determine the page, and not
			the other way around - i.e. pages may exist in
			multiple languages with the same url.
		*/

		return $_SESSION[ 'user' ][ 'current_lang' ];
	}
}