<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:12
 */

if( !function_exists('truncate')){
	function truncate($str, $len)
	{
		return substr($str, 0, $len) . ((strlen($str)>$len) ? ' ......' : '');
	}
}
if( !function_exists('site_bc')){
	function site_bc($theme)
	{

		$bc_string = '<a href="' . $theme->sitePath() . '">Home</a>';
		$base_path = basename($_SERVER[ 'REQUEST_URI' ]);
		$flag_return_bc = true;
		switch($base_path){
			default:
			case '':
				$flag_return_bc = false;
				break;

			case 'about':
				$bc_string .= ' / About Us';
				break;

			case 'what':
				$bc_string .= ' / What We Do';
				break;

			case 'joinus':
				$bc_string .= ' / Join Us';
				break;

			case 'contact':
				$bc_string .= ' / Get in Touch';
				break;

			case 'web-development':
				$bc_string .= ' / Web Development';
				break;

			case 'affiliate-marketing':
				$bc_string .= ' / Affiliate Marketing';
				break;

			case 'seo':
				$bc_string .= ' / Search Engine Optimisation (SEO)';
				break;

			case 'social-media':
				$bc_string .= ' / Social Media Marketing';
				break;

			case 'web-analysis':
				$bc_string .= ' / Web and E-mail Campaign Analysis';
				break;

			case 'content-translation':
				$bc_string .= ' / Content and Translation Department';
				break;

			case 'mobile-apps':
			case 'mobile-web':
				$bc_string .= ' / Mobile';
				if($base_path == 'mobile-web'){
					$bc_string .= ' / Mobile Web Development';
				}
				else{
					$bc_string .= ' / Mobile Applications';
				}
				break;

			case 'support-technical':
			case 'support-customer':
				$bc_string .= ' / Support';
				if($base_path == 'support-technical'){
					$bc_string .= ' / Technical Support';
				}
				else{
					$bc_string .= ' / Customer Support';
				}
				break;

			case 'flash':
				$bc_string .= ' / Flash/Game Development';
				break;

			case 'privacy-policy':
				$bc_string .= ' / Privacy Policy';
				break;

			case 'site-map':
				$bc_string .= ' / Map';
				$bc_string .= ' / Site Map';
				break;

		}
		/*echo"<pre>";
		echo basename($_SERVER['REQUEST_URI'])."<br>"; // get last string from url
		print_r($_SERVER['REQUEST_URI']);
		echo"</pre>";*/
		//$bc_string .= '<hr>';
		$bc_string = '<div class="page-link" >' . $bc_string . '<hr /></div>';

		if($flag_return_bc){
			return $bc_string;
		}
	}
}
if( !function_exists('html2txt')){
	function html2txt($document)
	{
		$search = array(
			'@<script[^>]*?>.*?</script>@si',
			// Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',
			// Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',
			// Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'
			// Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, '', $document);

		return $text;
	}
}

if( !function_exists('modify_content')){
	function modify_content($content)
	{
		if(@$_GET[ 'debug' ] == 1 || true){


			/*include DOC_ROOT.'libs/htmLawed.php';
			$cfg['tidy'] = true;
			$content = htmLawed($content, $cfg);*/
			//phpinfo();

			$config = HTMLPurifier_Config::createDefault();
			$config->set('Core.Encoding', 'ISO-8859-1'); // replace with your encoding
			$config->set('HTML.Doctype', 'HTML 4.01 Transitional'); // replace with your doctype
			$purifier = new HTMLPurifier($config);
			$content = $purifier->purify($content);

			require_once DOC_ROOT . 'libs/StripAttributes.php';

			$sa = new StripAttributes();
			$sa->allow = array(
				'id',
				'class',
			);
			$sa->exceptions = array(
				'img' => array(
					'src',
					'alt',
				),
				'a'   => array(
					'href',
					'title',
				),
			);
			$sa->ignore = array();
			$content = $sa->strip($content);
			//$content = preg_replace($content, '<span>');
			$content = preg_replace("/<span>\s*<strong>/", "<h2>", $content);
			$content = preg_replace("/<\/span>\s*<\/strong>/", "</h2>", $content);

			$content = preg_replace("/<p>\s*<span>/", "<p>", $content);
			$content = preg_replace("/<\/p>\s*<\/span>/", "</p>", $content);
			$content = $purifier->purify($content);
			//trace($content);
			//die();
		}
		//	phpinfo();
//die();
		//trace($content);
		//die();
		return $content;
	}
}