<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:13
 */

if( !function_exists('site_url')){
	function site_url($path = '')
	{
		//if path is external show user the reditect site url
		if(preg_match("/http:\/\//", $path)){
			return base_url('redirection.php?url=' . $path);
		}
		else{
			//else show proper url
			// we prepend server name to issue the full URL
			return "http://" . $_SERVER[ 'SERVER_NAME' ] . SITE_URL . '' . $path;
			//return SITE_URL.'index.php/'.$path;
		}
	}
}
if( !function_exists('base_url')){
	function base_url($path = '')
	{
		return SITE_URL . $path;
	}
}
if( !function_exists('cms_image_url')){
	function cms_image_url($path = '')
	{
		return SITE_URL . 'media/uploads/images/' . $path;
	}
}
if( !function_exists('media_url')){
	function media_url($path = '')
	{
		return SITE_URL . 'media/' . $path;
	}
}
if( !function_exists('theme_url')){
	function theme_url($path = '')
	{
		return SITE_URL . 'themes/Default/' . $path;
	}
}
if( !function_exists('redirect_url')){
	function redirect_url($path)
	{
		$path = site_url($path);
		if( !headers_sent()){
			header("Location: $path");
			exit(0);

			return true;
		}
		else{
			echo '<script>window.location.href = "' . $path . '";</script>';
			exit(0);
		}
	}
}
if( !function_exists('count_segments')){
	function count_segments($max = 0)
	{
		if( !isset($_SERVER[ 'PATH_INFO' ])){
			return 0;
		}
		$request_uri = $_SERVER[ 'PATH_INFO' ];
		$uri_array = explode('/', $request_uri);
		if($max == 0){
			return count($uri_array) - 1;
		}
		if((count($uri_array) - 1)>=$max){
			return $max;
		}
	}
}
if( !function_exists('uri_segment')){
	function uri_segment($section)
	{
		/*if(!isset($_SERVER['PATH_INFO']))
			return false;
		$request_uri = $_SERVER['PATH_INFO'];*/
		$request_uri = URI::getPathURI();

		$uri_array = explode('/', $request_uri);

		return isset($uri_array[ $section ]) ? $uri_array[ $section ] : false;

	}
}
if( !function_exists('make_url_friendly')){
	function make_url_friendly($string)
	{
		$string = stripslashes($string);
		$string = preg_replace("/'/", "", $string);
		$string = preg_replace("/[\s]/", "-", $string);
		//$string = utf8_encode($string);

//	    $string = preg_replace ('/&amp;([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);/', '$1', $string);
		//$string = iconv("ISO-8859-1", "UTF-8", $string);
		//echo $string ;
		$string = strtolower($string);

		return $string;

	}
}
if( !function_exists('embed_cache_page')){
	function embed_cache_page($page)
	{
		return DOC_ROOT . 'cache' . DS . $page . '.php';
	}
}
if( !function_exists('getIP')){
	function getIP()
	{
		$ip = "UNKNOWN";
		if(isset($_SERVER[ 'HTTP_CLIENT_IP' ]) && !empty($_SERVER[ 'HTTP_CLIENT_IP' ])){
			$ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
		}
		elseif(isset($_SERVER[ 'HTTP_X_FORWARDED_FOR' ]) && !empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])){
			$ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		}
		elseif(isset($_SERVER[ 'REMOTE_ADDR' ]) && !empty($_SERVER[ 'REMOTE_ADDR' ])){
			$ip = $_SERVER[ 'REMOTE_ADDR' ];
		}

		return $ip;
	}
}

if( !function_exists('cashier_url')){
	function cashier_url($current_lang = 'fr', $cur = 'EUR', $url = 'deposit', $login, $pass, $idcasino = 1, $cartec = 'casino770')
	{
		return site_url('redirect/?url=' . $url);
		if($url == 'deposit'){
			$url = 'depot';
		}
		elseif($url == 'withdraw'){
			$url = 'retrait/retrait';
		}
		elseif($url == 'transfer'){
			$url = 'transfer/transfer';
		}
		elseif($url == 'history'){
			$url = 'historique/bet_history';
		}

		$url = 'http://fcg.casino770.com/fcg-games/depot_new/?url=' . $url . '&id=' . $idcasino . '&cartec=' . $cartec . '&lang=' . $current_lang . '&cur=' . $cur . '&login=' . $login . '&pass=' . $pass . '&cashier_system=bet&idaffiliation=' . @$_COOKIE[ 'idaffiliation' ];

		//$url = base_url('redirect_cashier.php?url='.$url);


		return $url;
	}
}

if( !function_exists('embedFlash')){
	function embedFlash($path = '', $width, $height, $params = array())
	{

		$query_string = http_build_query($params);

		//$lang = ($lang)?$lang.'/':'';
		$path_to_flash = SITE_URL . $path;
		$embed_code = '<script type="text/javascript">
			AC_FL_RunContent( \'codebase\',\'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\',\'width\',\'' . $width . '\',\'height\',\'' . $height . '\',\'src\',\'$path_to_flash?' . $query_string . '\',\'quality\',\'high\',\'pluginspage\',\'http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\',\'wmode\',\'transparent\',\'movie\',\'' . $path_to_flash . '?' . $query_string . '\' ); //end AC code
	</script>
	<noscript>
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="' . $width . '" height="' . $height . '">
          <param name="movie" value="' . $path_to_flash . '.swf?' . $query_string . '" />
          <param name="quality" value="high" />
          <param name="wmode" value="transparent" />
          <embed src="' . $path_to_flash . '.swf?' . $query_string . '" width="' . $width . '" height="' . $height . '" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" wmode="transparent"></embed>
        </object>
      </noscript>';

		return $embed_code;

	}
}

if( !function_exists('wget')){
	function wget($url, $file, $cache = 3600)
	{
		//if file last modified time is
		//$file_mtime = (@file_exists($file))?filemtime($file):0;
		//if (time() - $cache < $file_mtime) {
		$out = fopen($file, 'w');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FILE, $out);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);
		curl_close($ch);
		fclose($out);
		chmod($file, 0777);
		//}
	}
}
if( !function_exists('getIP')){
	function getIP()
	{
		if( !empty($_SERVER[ 'HTTP_CLIENT_IP' ])){
			return $_SERVER[ 'HTTP_CLIENT_IP' ];
		}
		elseif( !empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])){
			return $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		}
		else{
			return $_SERVER[ 'REMOTE_ADDR' ];
		}
	}
}
if( !function_exists('trace')){
	function trace($object, $exit = false)
	{
		if(can_debug()){
			echo "<pre>";
			if($object){
				print_r($object);
			}
			else{
				var_dump($object);
			}
			echo "</pre>";
			if($exit){
				exit();
			}
		}
	}
}
if( !function_exists('parseJavaTime')){
	function parseJavaTime($time, $format = "%a %d/%m/%y, %H:%M")
	{
		return strftime($format, strtotime($time));
	}
}
if( !function_exists('getTimeToEvent')){
	function getTimeToEvent($time, $lang)
	{

		$timestamp = strtotime($time);
		//return $timestamp;
		$diff = $timestamp - time();
		$rest = ($diff % 3600);
		$restdays = ($diff % 86400);
		$restweeks = ($diff % 604800);
		$weeks = ($diff - $restweeks) / 604800;
		$days = ($diff - $restdays) / 86400;
		$hours = ($diff - $rest) / 3600;
		$seconds = ($rest % 60);
		$minutes = ($rest - $seconds) / 60;
		$date = '';

		if($weeks>1){
			return $date . "$weeks weeks";
		}
		elseif($days>1){
			return $date . "$days " . $lang[ 'days' ];
		}
		elseif($hours>1){
			return $date . "$hours " . $lang[ 'hours' ];
		}
		elseif($hours == 1){
			return $date . "1 " . $lang[ 'hour' ] . ", $minutes " . $lang[ 'minutes' ];
		}
		elseif($minutes == 0){
			return $date . "$seconds " . $lang[ 'seconds' ] . "" . $lang[ '' ];
		}
		elseif($minutes == 1){
			return $date . "1 " . $lang[ 'minute' ];
		}
		elseif($seconds<60){
			return $date . "$minutes " . $lang[ 'minutes' ];
		}
	}
}


if( !function_exists('getFullRemainingTime')){
	function getFullRemainingTime($time)
	{
		$diff = $time - time();
		$seconds = 0;
		$hours = 0;
		$minutes = 0;

		if($diff % 86400<=0){
			$days = $diff / 86400;
		}  // 86,400 seconds in a day
		if($diff % 86400>0){
			$rest = ($diff % 86400);
			$days = ($diff - $rest) / 86400;
			if($rest % 3600>0){
				$rest1 = ($rest % 3600);
				$hours = ($rest - $rest1) / 3600;
				if($rest1 % 60>0){
					$rest2 = ($rest1 % 60);
					$minutes = ($rest1 - $rest2) / 60;
					$seconds = $rest2;
				}
				else{
					$minutes = $rest1 / 60;
				}
			}
			else{
				$hours = $rest / 3600;
			}
		}

		if($days>0){
			$days = '<b>' . $days . '</b> days, ';
		}
		else{
			$days = false;
		}
		if($hours>0){
			$hours = '<b>' . $hours . '</b>  hours, ';
		}
		else{
			$hours = false;
		}
		if($minutes>0){
			$minutes = '<b>' . $minutes . '</b>  minutes, ';
		}
		else{
			$minutes = false;
		}
		$seconds = '<b>' . $seconds . '</b>  seconds'; // always be at least one second

		/*
		return array(
			'days' => $days,
			'hours' => $hours,
			'minutes' => $minutes,
			'seconds' => $seconds,

		);
		*/

		return $days . '' . $hours . '' . $minutes . '' . $seconds;

	}
}
if( !function_exists('number_formatter')){
	function number_formatter($number)
	{
		return $number;
	}
}
if( !function_exists('formatMoney')){
	function formatMoney($value, $currency = false, $type = 'short')
	{
		$value = sprintf("%0.2f", $value);

		if($currency == false){
			return $value;
		}
		if($type == 'long'){
			return "$value $currency";
		}
		elseif($type == 'short' && $currency != false){
			if($currency == 'EUR'){
				return "$value &euro;";
			}
			if($currency == 'GBP'){
				return "&pound; $value";
			}
		}
	}
}
if( !function_exists('formatCurrency')){
	function formatCurrency($currency, $type = 'short')
	{
		if($type == 'long'){
			return $currency;
		}
		elseif($type == 'short' && $currency != false){
			if($currency == 'EUR'){
				return "&euro;";
			}
			if($currency == 'GBP'){
				return "&pound;";
			}
		}
	}
}
if( !function_exists('convertStake')){
	function convertStake($totalStake, $stake, $exchange_rate)
	{
		return formatMoney(($totalStake / number_format($stake, 2)) * $stake * $exchange_rate);
	}
}
if( !function_exists('formatOdds')){
	function formatOdds($value)
	{
		//$value = str_replace(",", ".", $value);
		return sprintf("%0.2f", $value);
		if(strlen($value) == 1 || strlen(strstr($value, '.')) == 2){
			return sprintf("%0.2f", $value);
		}
		else{
			return $value;
		}
	}
}
if( !function_exists('findInArray')){
	function findInArray($array, $key, $value, $output)
	{
		foreach($array as $k => $v){
			$v = (gettype($v) == 'object') ? (array)$v : $v;
			if($v[ $key ] == $value){
				return $v[ $output ];
			}
		}

		return false;
	}
}

if( !function_exists('microtime_diff')){
	function microtime_diff($start, $end = null)
	{
		if( !$end){
			$end = microtime();
		}
		list($start_usec, $start_sec) = explode(" ", $start);
		list($end_usec, $end_sec) = explode(" ", $end);
		$diff_sec = intval($end_sec) - intval($start_sec);
		$diff_usec = floatval($end_usec) - floatval($start_usec);

		return floatval($diff_sec) + $diff_usec;
	}
}

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

if( !function_exists('showTree')){
	function showTree($array, $show = null)
	{
		?>
		<ul>
			<? foreach($array as $k => $v) : ?>
				<li <? if($v[ 'url' ] != $show && $show != null && $v[ 'parent_id' ] == 0) : ?>style="display: none;"<? endif; ?>>
					<a href="<?=site_url($v[ 'url' ])?>"><?=$v[ 'page_title' ]?></a>
					<? if(count($v[ 'tree' ])>0) : ?>
						<? showTree($v[ 'tree' ]) ?>
					<? endif; ?>
				</li>
			<? endforeach; ?>
		</ul>
		<?
	}
}
if( !function_exists('get_outcome_selection')){
	function get_outcome_selection($outcomeId, $bet_items)
	{

		foreach($bet_items as $item_marketId => $item){
			foreach($item[ 'data' ] as $item_outcomeId => $item_data){
				if($item_data[ 'outcomeId' ] == $outcomeId){
					return (count($item[ 'data' ])>1) ? 'multiple_selected' : 'selected';
				}
			}
		}

		return 'comb';
	}
}

if( !function_exists('objectSort')){

	function objectSort(&$data, $key)
	{
		for($i = count($data) - 1; $i>=0; $i--){
			$swapped = false;
			for($j = 0; $j<$i; $j++){
				if($data[ $j ]->$key>$data[ $j + 1 ]->$key){
					$tmp = $data[ $j ];
					$data[ $j ] = $data[ $j + 1 ];
					$data[ $j + 1 ] = $tmp;
					$swapped = true;
				}
			}
			if( !$swapped) return;
		}
	}
}


if( !function_exists('get_bet_type')){
	function get_bet_type($systemType)
	{
		if($systemType->slips == 1 && $systemType->system == 0 && $systemType->totalTips == 1){
			$bet_type = 'single_bet';
		}
		elseif($systemType->slips == 1 && $systemType->system == 0 && $systemType->totalTips>1){
			$bet_type = 'combi_bet';
		}
		elseif($systemType->slips>1 && $systemType->system == 0){
			$bet_type = 'multiple_bet';
		}
		elseif($systemType->system>=1 && $systemType->banks>=1){
			$bet_type = 'system_bet_with_banks';
		}
		elseif($systemType->system>=1 && $systemType->banks == 0){
			$bet_type = 'system_bet';
		}
		elseif($systemType->slips>1 && $systemType->system>0){
			$bet_type = 'multiple_system_bet';
		}

		return $bet_type;
	}
}
if( !function_exists('http_parse_query')){
	function http_parse_query($array = null, $convention = '%s')
	{
		if(count($array) == 0){
			return '';
		}
		else{
			if(function_exists('http_build_query')){
				$query = http_build_query($array);
			}
			else{
				$query = '';
				foreach($array as $key => $value){
					if(is_array($value)){
						$new_convention = sprintf($convention, $key) . '[%s]';
						$query .= http_parse_query($value, $new_convention);
					}
					else{
						$key = urlencode($key);
						$value = urlencode($value);
						$query .= sprintf($convention, $key) . "=$value&";
					}
				}
			}

			return $query;
		}
	}
}
if( !function_exists('json_safe_encode')){
	function json_safe_encode($a)
	{
		if(is_null($a)) return 'null';
		if($a === false) return 'false';
		if($a === true) return 'true';
		if(is_scalar($a)){
			$a = addslashes($a);
			// $a = utf8_encode($a);
			$a = str_replace("\n", '\n', $a);
			$a = str_replace("\r", '\r', $a);
			$a = preg_replace('{(</)(script)}i', "$1'+'$2", $a);

			return "\"$a\"";
		}
		$isList = true;
		for($i = 0, reset($a); $i<count($a); $i++, next($a))
			if(key($a) !== $i){
				$isList = false;
				break;
			}
		$result = array();
		if($isList){
			foreach($a as $v) $result[] = json_safe_encode($v);

			return '[ ' . join(', ', $result) . ' ]';
		}
		else{
			foreach($a as $k => $v)
				$result[] = json_safe_encode($k) . ': ' . json_safe_encode($v);

			return '{ ' . join(', ', $result) . ' }';
		}
	}
}
if( !function_exists('make_bet_url_friendly')){
	function make_bet_url_friendly($string)
	{
		$string = make_url_friendly($string);
		$string = str_replace("-", "_", $string);
		$string = str_replace("/", "", $string);
		$string = str_replace(":", "-", $string);

		return $string;

	}
}
if( !function_exists('cmp')){
	function cmp($a, $b)
	{
		return strlen($b) - strlen($a);
	}
}

if( !function_exists('sprintf_clean')){
	function sprintf_clean($str, $vars, $char = '%')
	{
		if(is_array($vars)){
			uksort($vars, "cmp");
			foreach($vars as $k => $v){
				$str = str_replace($char . $k, $v, $str);
			}
		}

		return $str;
	}
}
if( !function_exists('is_restricted')){
	function is_restricted()
	{
		//return true; //remove this..
		$ip = getIP();
		//if($ip == '217.45.165.134' || $ip == '217.41.34.61' || $ip == '217.45.165.129' || $ip == '213.123.225.199' || $ip == '65.13.45.104') {
		if($ip == '217.45.165.134' || $ip == '217.41.34.61' || $ip == '217.45.165.129' || $ip == '213.123.225.199' || $ip == '65.13.45.104' || $ip == '81.196.180.195' || $ip == '24.244.141.91'){
			return true;
		}
		else{
			return false;
		}
	}
}
if( !function_exists('can_debug')){
	function can_debug()
	{
		$ip = getIP();
		//if($ip == '217.45.165.134' || $ip == '217.41.34.61' || $ip == '217.45.165.129' || $ip == '213.123.225.199' || $ip == '65.13.45.104') {
		if($ip == '217.45.165.134' || $ip == '217.41.34.61' || $ip == '217.45.165.129' || $ip == '213.123.225.199' || $ip == '65.13.45.104' || $ip == '81.196.180.195' || $ip == '24.244.141.91'){
			return true;
		}
		else{
			return false;
		}
	}
}
if( !function_exists('strip_cdata')){
	function strip_cdata($string)
	{
		preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);

		return str_replace($matches[ 0 ], $matches[ 1 ], $string);
	}
}

if( !function_exists('formatCountdown')){
	function formatCountdown($time)
	{
		$timestamp = strtotime($time);
		//return $timestamp;
		$diff = $timestamp - time();
		$rest = ($diff % 3600);
		$restdays = ($diff % 86400);
		$restweeks = ($diff % 604800);
		$weeks = ($diff - $restweeks) / 604800;
		$days = ($diff - $restdays) / 86400;
		$hours = ($diff - $rest) / 3600;
		$seconds = ($rest % 60);
		$minutes = ($rest - $seconds) / 60;
		$date = '';

		if(strlen($hours) == 1) $hours = "0" . $hours;
		if(strlen($minutes) == 1) $minutes = "0" . $minutes;

		return $date . "$hours:$minutes";
	}
}
if( !function_exists('bet_params')){
	function bet_params($outcome, $eventInfo)
	{
		(isset($outcome->name)) ? $outcome_name = $outcome->name : $outcome_name = $outcome->eventName;
		(isset($outcome->outcomeName)) ? $outcome_name = $outcome->outcomeName : $outcome_name = $outcome->name;
		@$params = "'" . $outcome->outcomeId . "', '" . @str_replace("'",
																	 "\'",
																	 $eventInfo[ 'name' ]) . "', '" . str_replace("'",
																												  "\'",
																												  $eventInfo[ 'betType' ]) . "', '" . $outcome->marketId . "', '" . str_replace("'",
																																																"\'",
																																																$outcome_name) . "', '" . $outcome->odds . "', '" . $outcome->eventId . "', '" . $outcome->sportId . "', '" . $outcome->regionId . "', '" . $outcome->leagueId . "'";

		return $params;
	}
}

if( !function_exists('formatTextSize')){
	function formatTextSize($string, $max_length, $limit)
	{
		if(strlen($string)>$max_length){
			return substr($string, 0, $limit) . '...';
		}
		else{
			return $string;
		}
	}

}
if( !function_exists('getOutcomeShort')){
	function getOutcomeShort($outcome, $market)
	{
		$outcome = (strlen($outcome)>5) ? substr($outcome, 0, 5) . '...' : $outcome;

		return $outcome;
		$market_name = $market->name;
		foreach($market->outcomes as $k => $v){
			$outcome_status = (strpos($v->name, $market_name) !== false) ? true : false;
			//var_dump(strpos($v->name, $market_name));

			if($outcome_status == true){
				break;
			}

			/*if($outcome_status) {



			}*/

		}


		return $outcome_status;
		/*
			$params = $event->outcomeId . ", '" .$event->name."', '".$event->betType ."', '".$event->marketId ."', '".$event->name ."', '".$event->odds ."', '".$event->eventId ."', '".$event->sportId ."', '".$event->regionId ."', '".$event->leagueId . "'";
			return $params;
			*/

	}
}


if( !function_exists('tabs_url')){
	function tabs_url($site, $current_lang)
	{

		if((isset($_COOKIE[ 'idaffiliation' ]) && !empty($_COOKIE[ 'idaffiliation' ])) && (isset($_COOKIE[ 'member' ]) && !empty($_COOKIE[ 'member' ]))){
			$idf_real = "idaffiliation=" . $_COOKIE[ 'idaffiliation' ] . "&member=" . $_COOKIE[ 'member' ];
		}
		elseif(isset($_COOKIE[ 'idaffiliation' ]) && !empty($_COOKIE[ 'idaffiliation' ])){
			$idf_real = "idaffiliation=" . $_COOKIE[ 'idaffiliation' ];
		}
		elseif(isset($_COOKIE[ 'member' ]) && !empty($_GET[ 'member' ])){
			$idf_real = "member=" . $_COOKIE[ 'member' ];
		}
		else{
			$idf_real = "";
		}

		if((isset($_GET[ 'idaffiliation' ]) && !empty($_GET[ 'idaffiliation' ])) && (isset($_GET[ 'member' ]) && !empty($_GET[ 'member' ]))){
			$idf_real = "idaffiliation=" . $_GET[ 'idaffiliation' ] . "&member=" . $_GET[ 'member' ];
		}
		elseif(isset($_GET[ 'idaffiliation' ]) && !empty($_GET[ 'idaffiliation' ])){
			$idf_real = "idaffiliation=" . $_GET[ 'idaffiliation' ];
		}
		elseif(isset($_GET[ 'member' ]) && !empty($_GET[ 'member' ])){
			$idf_real = "member=" . $_GET[ 'member' ];
		}
		else{
			$idf_real = "";
		}

		switch($site){
			case 'casino':
				$current_lang = ($current_lang == 'fr') ? '' : $current_lang;
				$param_link = ($idf_real == "") ? '' : '?' . $idf_real;
				$location = 'http://www.casino770.com/' . $current_lang . $param_link;
				break;
			case 'poker':
				$param_link = ($idf_real == "") ? '' : '?' . $idf_real;
				$location = 'http://www.poker770.com/' . $current_lang . $param_link;
				break;
			case 'arcade':
				$param_link = ($idf_real == "") ? '' : '&amp;' . $idf_real;
				$location = 'http://www.casino770.com/jeux.php?lang=' . $current_lang . $param_link;
				break;
			case 'bet':
				$param_link = ($idf_real == "") ? '' : '&amp;' . $idf_real;
				$location = '?lang_select=' . $current_lang . $param_link;
				break;
			case 'bingo':
				$param_link = ($idf_real == "") ? '' : '&amp;' . $idf_real;
				$location = 'http://www.bingo770.com/index.php?lang_select=' . $current_lang . $param_link;
				break;
		}

		return $location;
	}
}

if( !function_exists('trace')){
	function trace($object, $exit = false)
	{
		if(can_debug()){
			echo "<pre>";
			if($object){
				print_r($object);
			}
			else{
				var_dump($object);
			}
			echo "</pre>";
			if($exit){
				exit();
			}
		}
	}
}

if( !function_exists('get_avatar')){
	function get_avatar($user_info, $size, $mode = "Profile")
	{
		$path = POKER770_ROOT . "images/avatars" . $mode . "/";
		if($user_info[ 'pic' ] && !strpos($user_info[ 'pic' ], '.')){
			if( !file_exists($path . $size . "/" . $user_info[ 'pic' ] . ".jpg")){
				$avatar = $size . "/default.png";
			}
			else{
				$avatar = $size . "/" . $user_info[ 'pic' ] . ".jpg";
			}
		}
		elseif($user_info[ 'pic' ]){
			if( !file_exists($path . $size . "/" . $user_info[ 'pic' ])){
				$avatar = $size . "/default.png";
			}
			else{
				$avatar = $size . "/" . $user_info[ 'pic' ];
			}
		}
		else{
			$avatar = $size . "/default.png";
		}

		return 'http://www4.poker770.com/images/avatars' . $mode . '/' . $avatar;
	}
}


//------------------------------------------------//
// Returns Domain name
//------------------------------------------------//
if( !function_exists('getDomain')){
	function getDomain($url)
	{

		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === false){
			return false;
		}
		/*** get the url parts ***/
		$parts = parse_url($url);

		/*** return the host domain ***/
		return substr($parts[ 'host' ], strpos($parts[ 'host' ], '.') + 1, strlen($parts[ 'host' ]));
	}
}

//------------------------------------------------//
// Returns Protocol http or https
//------------------------------------------------//
if( !function_exists('GetProtocol')){
	function GetProtocol()
	{
		if(empty($_SERVER[ "HTTPS" ])){
			$s = '';
		}
		elseif($_SERVER[ "HTTPS" ] == "on"){
			$s = 's';
		}
		else{
			$s = '';
		}

		return "http" . $s;
	}
}
//------------------------------------------------//
// Returns Host  name
//------------------------------------------------//
if( !function_exists('getHost')){
	/*function getHost() {
		$host = "UNKNOWN";
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST']))
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		elseif(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']))
			$host = $_SERVER['HTTP_HOST'];
		return $host;
	}*/

	function getHost()
	{
		$host = "UNKNOWN";
		if(isset($_SERVER[ 'HTTP_X_FORWARDED_HOST' ]) && !empty($_SERVER[ 'HTTP_X_FORWARDED_HOST' ])){
			$host_array = explode(",", $_SERVER[ 'HTTP_X_FORWARDED_HOST' ]);
			$host = (count($host_array)>1) ? trim($host_array[ 0 ]) : $_SERVER[ 'HTTP_X_FORWARDED_HOST' ];
		}
		elseif(isset($_SERVER[ 'HTTP_HOST' ]) && !empty($_SERVER[ 'HTTP_HOST' ])){
			$host = $_SERVER[ 'HTTP_HOST' ];
		}

		return $host;
	}
}

//------------------------------------------------//
// Returns Domain name
//------------------------------------------------//
if( !function_exists('getDomainHost')){
	function getDomainHost($url = null)
	{
		$url = (is_null($url)) ? getHost() : $url;
		$url = str_replace('www3.', '', $url);
		$url = str_replace('www2.', '', $url);
		$url = str_replace('www.', '', $url);
		$url = str_replace('www1.', '', $url);
		$url = str_replace('internal1.', '', $url);
		$url = str_replace('beta.', '', $url);
		$url = str_replace('beta2.', '', $url);
		$url = str_replace('web.', '', $url);
		$url = str_replace('www4.', '', $url);

		return $url;
	}
}


if( !function_exists('isValidIp')){
	function isValidIp($bypass_ip = '10.1.10')
	{
		$ip = $_SERVER[ 'REMOTE_ADDR' ];

		$valid_ips = array(
			'109.68.198.43'
			/*Gimo Staff*/,
			'213.123.225.199',
			/*Gimo Management Directors*/

			'213.120.119.45'
			/*GIMO*/,
			'217.41.34.61'
			/*GIMO*/,
			'213.123.225.199'
			/*DIRECTORS*/,
			'81.196.180.195'
			/*ROMINIA*/,

		);
		if(strtoupper($bypass_ip) == 'ALL'){
			//no check
			return true;
		}
		elseif(in_array($ip, $valid_ips)){
			//checking ip
			return true;
		}
		elseif(strpos($ip, $bypass_ip) === 0){
			//by pass ip or network or ip
			return true;
		}

		return false;
	}
}