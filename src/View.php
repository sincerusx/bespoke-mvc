<?php

class View
{

	/**
	 * @var string $template
	 */
	public $template;

	/**
	 * @var mixed $vars
	 */
	public $vars;

	/**
	 * @var string $template_dir
	 */
	public $template_dir;

	/**
	 * View constructor.
	 */
	public function __construct(){ }

	/**
	 * Render meta title
	 *
	 * @param $title
	 *
	 * @return mixed
	 */
	public function renderMetaTitle($title)
	{
		$meta_title = '<title>' . $title . '</title>' . "\r\n";

		return $this->assign('meta_title', $meta_title);
	}

	/**
	 * Render meta tag data
	 *
	 * @param    $meta    array
	 *
	 * @return    mixed
	 */
	public function renderMetaTags($meta)
	{
		$meta_data = '';
		foreach($meta as $k => $v){
			$meta_data .= '<meta ' . $v[ 'type' ] . '="' . strtolower($v[ 'attribute' ]) . '" content="' . $v[ 'meta_content' ] . '"/>';
			// line break
			$meta_data .= "\r\n";
			// tab/indent
			$meta_data .= "\t";
		}

		// remove last tab
		$meta_data = substr($meta_data, 0, -2);

		return $this->assign('meta_data', $meta_data);
	}

	/**
	 * Render local stylesheet to view
	 *
	 * @param string $stylesheet
	 * @param null   $assetsDir
	 *
	 * @return string
	 */
	public function renderStylesheet($stylesheet, $assetsDir = null)
	{

		if(null === $assetsDir){
			return '<link rel="stylesheet" href="' . SITE_URL . 'assets/css/' . $stylesheet . '.css">';
		}

		return '<link rel="stylesheet" href="' . SITE_URL . $assetsDir . '/' . $stylesheet . '.css">';
	}

	/**
	 * Render local script to view
	 *
	 * @param string $script
	 * @param null   $assetsDir
	 *
	 * @return string
	 */
	public function renderScript($script, $assetsDir = null)
	{

		if(null === $assetsDir){
			return '<script href="' . SITE_URL . 'assets/js/' . $script . '.js" type="text/javascript"></script>';
		}

		return '<script href="' . SITE_URL . $assetsDir . '/' . $script . '.js" type="text/javascript"></script>';
	}

	/**
	 * Assign variable to variable name
	 *
	 * @param $name
	 * @param $value
	 */
	public function assign($name, $value)
	{
		return $this->vars[ $name ] = is_object($value) ? $value : $value;
	}

	/**
	 * @param null $template
	 */
	public function display($template = null)
	{

		// debug site times
		$time = App::$START_TIME;
		$this->assign('time', $time);

		// debug site memory used
		$memory = App::$MEMORY_USAGE;
		$this->assign('memory', $memory);

		// debug sql
		if(Registry::isRegister('db')){
			$db = Registry::get("db");
			$this->assign('db', $db);
		}


		if( !$template) $template = $this->template;
		extract($this->vars);


		ob_start();
		include(DOC_ROOT . 'themes' . DS . DEFAULT_THEME . DS . $template . EXT);
		ob_end_flush();

		if(isset($_GET[ 'benchmark' ])){
			// $benchmark->dump();
		}
	}
}