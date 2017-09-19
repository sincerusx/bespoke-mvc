<?php

class Application
{

	/**
	 * Router object
	 *
	 * @var Router $router
	 */
	protected $router;

	/**
	 * Benchmark Object
	 *
	 * @var Benchmark $Benchmark
	 */
	protected $Benchmark;

	public function __construct()
	{
		Application::loadLibrary('utils/Benchmark.class');
		$this->Benchmark = new Benchmark();
	}

	public function __initSite()
	{
		$this->setCurrentLang();
		$this->loadGlobalVariables();
	}

	private function loadGlobalVariables()
	{

	}

	private function setCurrentLang()
	{
		//this is for homepage keywords and desc
		if(isset($_REQUEST[ 'lang_select' ])){
			$_SESSION[ 'user' ][ 'current_lang' ] = $_REQUEST[ 'lang_select' ];
		}
		elseif( !isset($_SESSION[ 'user' ][ 'current_lang' ])){
			$_SESSION[ 'user' ][ 'current_lang' ] = DEFAULT_LANG;
		}
		$this->current_lang = $_SESSION[ 'user' ][ 'current_lang' ];

	}

	// load core files
	public static function loadBase($class)
	{
		if(class_exists($class, false) || interface_exists($class, false)){
			return;
		}
		$file = DOC_ROOT . 'application/core/' . $class . EXT;
		if(is_file($file)){
			return include_once($file);
		}

		return;
	}

	// load models
	public function loadModel($class)
	{
		if(class_exists($class, false) || interface_exists($class, false)){
			return true;
		}
		$file = DOC_ROOT . 'application/models/' . $class . EXT;
		if(is_file($file)){
			return include_once($file);
		}

		return;
	}

	// load service
	public function loadService($class)
	{
		if(class_exists($class, false) || interface_exists($class, false)){
			return;
		}
		$file = DOC_ROOT . 'services/' . $class . EXT;
		if(is_file($file)){
			return include_once($file);
		}

		return;
	}

	// load controller
	public function loadController($class)
	{
		if(class_exists($class, false) || interface_exists($class, false)){
			return true;
		}
		$file = DOC_ROOT . 'application/controllers/' . $class . EXT;
		if(is_file($file)){
			return include_once($file);
		}

		return;
	}

	// load library
	public function loadLibrary($class)
	{
		if(class_exists($class, false) || interface_exists($class, false)){
			return;
		}
		$file = DOC_ROOT . 'application/libraries/' . $class . EXT;
		if(is_file($file)){
			return include_once($file);
		}

		return;
	}

	// load helper
	public static function loadHelper($class)
	{
		$file = DOC_ROOT . 'application/helpers/' . $class . '_helper' . EXT;
		if(is_file($file)){
			return include_once($file);
		}
		// var_dump(debug_backtrace());
		die('cannot load helper ' . $class);
	}
}