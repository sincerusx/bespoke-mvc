<?php

class Exceptions extends Exception
{

	var $message = '';
	var $code    = '';

	public function __construct($errMessage, $errCode = "")
	{
		$this->message = $errMessage;
		$this->code = $errCode;
	}
}