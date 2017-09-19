<?

class Theme
{

	public $theme_name;

	public function __construct()
	{
		$this->getTheme();
	}

	public function getTheme()
	{
		//allow user to change theme
		if(isset($_GET[ 'passcode' ]) && $_GET[ 'passcode' ] == PASSCODE
		   && isset($_GET[ 'theme' ]) && !empty($_GET[ 'passcode' ])
		){
			$_SESSION[ 'theme' ] = $_GET[ 'theme' ];
		}
		//check with session each time for the theme
		$this->theme_name = isset($_SESSION[ 'theme' ]) ? $_SESSION[ 'theme' ] : DEFAULT_THEME;
	}

	public function sitePath($path = '')
	{
		return SITE_URL . '/' . $path;
	}

	public function themePath($path = '')
	{
		return SITE_URL . 'themes/' . $this->theme_name . '/' . $path;
	}

	public function themeFilePath($path = '')
	{
		return DOC_ROOT . 'themes/' . $this->theme_name . '/' . $path;
	}

	public function jscriptsPath($path = '')
	{
		return SITE_URL . 'themes/' . $this->theme_name . '/jscripts/' . $path;
	}

	public function imagePath($path = '', $lang = false)
	{
		$lang = ($lang) ? $lang . '/' : '';

		return SITE_URL . 'themes/' . $this->theme_name . '/images/' . $lang . $path;
	}

	public function flashPath($path = '', $lang = false)
	{
		$lang = ($lang) ? $lang . '/' : '';

		return SITE_URL . 'themes/' . $this->theme_name . '/flash/' . $lang . $path;
	}

	public function embedFlash($path = '', $width, $height, $lang = false, $params = array())
	{

		$query_string = '?' . http_build_query($params);

		$lang = ($lang) ? $lang . '/' : '';
		$path_to_flash = SITE_URL . 'themes/' . $this->theme_name . '/flash/' . $lang . $path;
		$embed_code = '<script type="text/javascript">
			AC_FL_RunContent( \'codebase\',\'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\',\'width\',\'' . $width . '\',\'height\',\'' . $height . '\',\'src\',\'$path_to_flash' . $query_string . '\',\'quality\',\'high\',\'pluginspage\',\'http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\',\'wmode\',\'transparent\',\'movie\',\'' . $path_to_flash . '\'\';' . $query_string . '\' ); //end AC code
	</script>
	<noscript>
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="' . $width . '" height="' . $height . '">
          <param name="movie" value="' . $path_to_flash . '.swf' . $query_string . '" />
          <param name="quality" value="high" />
          <param name="wmode" value="transparent" />
          <embed src="' . $path_to_flash . '.swf' . $query_string . '" width="' . $width . '" height="' . $height . '" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" wmode="transparent"></embed>
        </object>
      </noscript>';

		return $embed_code;

	}

	public function cssImagesPath($path = '', $lang = false)
	{
		$lang = ($lang) ? $lang . '/' : '';

		return SITE_URL . 'themes/' . $this->theme_name . '/css/images/' . $lang . $path;
	}

	public function getElement($name, $vars = array())
	{
		//FIXME: $elementUrl and $theme variable names are denied!
		$elementUrl = DOC_ROOT . "themes/{$this->theme_name}/elements/{$name}.php";
		if(file_exists($elementUrl)){
			foreach($vars as $n => $v){
				$sqv = str_replace("'", "\\'", $v);
				eval("\${$n} = '{$sqv}';");
			}
			$theme = $this;
			ob_start();
			include($elementUrl);

			return ob_get_clean();
		}

		return null;
	}

	public function cssInclude($file = null)
	{
		if(DEFAULT_THEME == 'mobile'){
			$css_file = SITE_URL . 'themes/' . $this->theme_name . '/assets/css/' . $file;
		}
		else{
			$css_file = SITE_URL . 'themes/' . $this->theme_name . '/css/' . $file;
		}

		return '<link href="' . $css_file . '" rel="stylesheet" type="text/css" />' . "\n";
	}


	public function jsInclude($file = null)
	{
		if(DEFAULT_THEME == 'mobile'){
			$js_file = SITE_URL . 'themes/' . $this->theme_name . '/assets/js/' . $file . '?' . time();
		}
		else{
			$js_file = SITE_URL . 'themes/' . $this->theme_name . '/jscripts/' . $file . '?' . time();
		}

		return '<script language="javascript" type="text/javascript" src="' . $js_file . '"></script>' . "\n";
	}

	public function jsMain()
	{
		$js_file = SITE_URL . 'themes/' . $this->theme_name . '/main_js.php';

		return '<script language="javascript" type="text/javascript" src="' . $js_file . '"></script>' . "\n";
	}

}

?>
