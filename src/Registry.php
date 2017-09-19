<?php

class Registry
{

	private static $register = array();

	public static function set($var, $name = "")
	{
		self::$register[ $name ] = $var;
	}

	public static function get($name)
	{
		if( !self::isRegister($name)){
			echo "The variable " . $name . " can not be found";
		}

		return self::$register[ $name ];
	}

	public static function isRegister($name)
	{
		return array_key_exists($name, self::$register);
	}

	public static function getAllRegistered()
	{
		return self::$register;
	}
}