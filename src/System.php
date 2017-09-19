<?php

class System
{

	public static function createRandomPassword($count = 6)
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime() * 1000000);
		$i = 0;
		$pass = '';
		while($i<$count){
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}

		return $pass;
	}

	public static function seoCharset($string)
	{
		$replace = array(
			'Š' => 'S',
			'š' => 's',
			'Ð' => 'Dj',
			'Ž' => 'Z',
			'ž' => 'z',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'A',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ø' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'B',
			'ß' => 'Ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'a',
			'ç' => 'c',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'o',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ý' => 'y',
			'ý' => 'y',
			'þ' => 'b',
			'ÿ' => 'y',
			'ƒ' => 'f',
		);
		$string = str_replace(" ", "-", $string);
		$string = strtr($string, $replace);

		return strtolower(urlencode($string));
	}

	public function removeSpecialChars($string)
	{

		// Replace other special chars
		$specialCharacters = array(
			'#' => '',
			'$' => '',
			'%' => '',
			'&' => '',
			'@' => '',
			'.' => '',
			'€' => '',
			'+' => '',
			'=' => '',
			'§' => '',
			'\\' => '',
			'/' => '',
		);

		$replace = array(
			'Š' => 'S',
			'š' => 's',
			'Ð' => 'Dj',
			'Ž' => 'Z',
			'ž' => 'z',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'A',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ø' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'B',
			'ß' => 'Ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'a',
			'ç' => 'c',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'o',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ý' => 'y',
			'ý' => 'y',
			'þ' => 'b',
			'ÿ' => 'y',
			'ƒ' => 'f',
		);

		$string = urldecode(str_replace("3%8", "", urlencode($string)));
		$string = strtr($string, $replace);

		return $string;
	}

	public static function specialCharsMap()
	{
		return array(
			"&#260;" => "A",
			"&#261;" => "a",
			"&#262;" => "C",
			"&#263;" => "c",
			"&#264;" => "C",
			"&#265;" => "c",
			"&#266;" => "C",
			"&#267;" => "c",
			"&#268;" => "C",
			"&#269;" => "c",
			"&#280;" => "E",
			"&#281;" => "e",
			"&#282;" => "E",
			"&#283;" => "e",
			"&#286;" => "G",
			"&#287;" => "g",
			"&#304;" => "I",
			"&#305;" => "i",
			"&#321;" => "L",
			"&#322;" => "l",
			"&#323;" => "N",
			"&#324;" => "n",
			"&#327;" => "N",
			"&#328;" => "n",
			"&#344;" => "R",
			"&#345;" => "r",
			"&#346;" => "S",
			"&#347;" => "s",
			"&#351;" => "s",
			"&#352;" => "S",
			"&#353;" => "s",
			"&#354;" => "T",
			"&#355;" => "t",
			"&#377;" => "Z",
			"&#378;" => "z",
			"&#379;" => "Z",
			"&#380;" => "z",
		);
	}

	public static function unallowed_chars()
	{
		return array(
			'!',
			'$',
			'%',
			'^',
			'&',
			'*',
			'=',
			'}',
			'{',
			'[',
			']',
			'#',
			'~',
			';',
			':',
			'<',
			'>',
			'|',
		);
	}

	public static function IsAlpha($sString)
	{
		if(preg_match('/^[a-zA-ZáàäâÁÀÄÂéèëêÉÈËÊíìïîÍÌÏÎóòöôÓÒÖÔúùûÚÙÛÜüñ ]+$/u', $sString) === 1){
			return true;
		}
		else{
			return false;
		}
	}

	static function encryptText($pass)
	{
		$key = 'r5-=3rA32ASFD/.!23';
		if(function_exists('mcrypt_encrypt')){
			return base64_encode(@mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $pass, MCRYPT_MODE_CBC, md5(md5
																											 ($key))));
		}
		else{
			return $pass;
		}
	}

	static function decryptText($pass)
	{
		$key = 'r5-=3rA32ASFD/.!23';
		if(function_exists('mcrypt_decrypt')){
			return rtrim(@mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($pass), MCRYPT_MODE_CBC, md5
			(md5($key))), "\0");
		}
		else{
			return $pass;
		}
	}

	/**
	 * Returns a random letter
	 *
	 * @param array|null $letters [optional] <p>
	 *                            If you would like to specify the letters that
	 *                            you would like to be randomised. If this
	 *                            parameter is not set by default all letters
	 *                            of the alphabet will be randomised.
	 *                            </p>
	 * @param int        $amount  <p>
	 *                            The amount of random letters to be returned
	 *                            by default only 1 will be returned
	 *                            </p>
	 *
	 * @return string
	 */
	public static function randomLetter(array $letters = null, $amount = 1)
	{
		if(is_null($letters)) $letters = range('A', 'Z');
		$i = 1;
		$output = '';
		while($i<=$amount){
			$output = $letters[ array_rand($letters) ];
			$i++;
		}

		return (string)$output;
	}

	/**
	 * Returns a random number
	 *
	 * @param int $amount <p>
	 *                    The amount of random numbers to be returned
	 *                    by default only 1 will be returned
	 *                    </p>
	 *
	 * @return int
	 */
	public static function randomNumber($amount = 1)
	{
		$i = 1;
		$output = '';
		while($i<=$amount){
			$output .= mt_rand(0, 9);
			$i++;
		}

		return (int)$output;
	}

	/**
	 * Round up number
	 *
	 * Rounds up a float to a specified number of decimal places
	 * (basically acts like ceil() but allows for decimal places)
	 *
	 * round_up (56.77001, 2); // displays 56.78
	 * round_up (-0.453001, 4); // displays -0.453
	 *
	 * @param     $value
	 * @param int $places
	 *
	 * @return float|int
	 */
	function round_up($value, $places = 0)
	{
		if($places<0){
			$places = 0;
		}
		$mult = pow(10, $places);

		return ceil($value * $mult) / $mult;
	}

	/**
	 * Rounds a float away from zero to a specified number of decimal places
	 *
	 * round_up (56.77001, 2); // displays 56.78
	 * round_up (-0.453001, 4); // displays -0.453
	 *
	 * @param     $value
	 * @param int $places
	 *
	 * @return float|int
	 */
	function round_out($value, $places = 0)
	{
		if($places<0){
			$places = 0;
		}
		$mult = pow(10, $places);

		return ($value>=0 ? ceil($value * $mult) : floor($value * $mult)) / $mult;
	}

	static function ceil_dec($number, $precision, $separator)
	{
		$numberpart = explode($separator, $number);
		$numberpart[ 1 ] = substr_replace($numberpart[ 1 ], $separator, $precision, 0);
		if($numberpart[ 0 ]>=0){
			$numberpart[ 1 ] = ceil($numberpart[ 1 ]);
		}
		else{
			$numberpart[ 1 ] = floor($numberpart[ 1 ]);
		}
		$ceil_number = array(
			$numberpart[ 0 ],
			$numberpart[ 1 ],
		);

		return implode($separator, $ceil_number);
	}

	public static function getRealUserIp()
	{
		switch(true){
			case ( !empty($_SERVER[ 'HTTP_X_REAL_IP' ])) :
				return $_SERVER[ 'HTTP_X_REAL_IP' ];
			case ( !empty($_SERVER[ 'HTTP_CLIENT_IP' ])) :
				return $_SERVER[ 'HTTP_CLIENT_IP' ];
			case ( !empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])) :
				return $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
			default :
				return $_SERVER[ 'REMOTE_ADDR' ];
		}
	}

	public static function getIP()
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

	public static function is_restricted()
	{
		$ip = getIP();
		if(
			$ip == '109.68.198.43' /*Gimo Staff*/ ||
			$ip == '81.149.83.187' /*Gimo Management*/ ||

			$ip == '217.45.165.134' || $ip == '217.41.34.61' || $ip == '217.45.165.129' || $ip == '213.123.225.199' || $ip == '65.13.45.104'
		){
			if($ip == '109.68.198.43' /*Gimo Staff*/ ||
			   $ip == '213.123.225.199' /*Gimo Management*/ ||
			   $ip == '217.45.165.134' || $ip == '217.41.34.61' || $ip == '217.45.165.129' || $ip == '213.123.225.199' || $ip == '65.13.45.104' || $ip == '81.196.180.195' || $ip == '24.244.141.91'
			){
				return true;
			}
			else{
				return false;
			}
		}
	}

	public static function redirect_url($path)
	{
		if( !headers_sent()){
			header("Location: " . ADMIN_URL . $path);
			exit(0);
		}
		else{
			echo '<script>window.location.href = "' . ADMIN_URL . $path . '";</script>';
			exit(0);
		}
	}

	public static function convertMem($size)
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
}