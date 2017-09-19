<?php

/**
 * Class Site
 */
class Site
{

	/**
	 * Database conection
	 *
	 * @var db $db
	 */
	private $db;

	/**
	 * Page object
	 *
	 * @var $page
	 */
	public $page;

	/**
	 * all pages for current lang (needed to show the proper links in our view)
	 *
	 * @var $aliasInfoArray
	 */
	public $aliasInfoArray;

	/**
	 * current Casino page
	 *
	 * @var $current_page
	 */
	public $current_page;

	/**
	 * hold controller name, action and additional params
	 *
	 * @var array $uri
	 */
	public $uri = array();

	/**
	 * all available languages for this site
	 *
	 * @var array $available_languages
	 */
	public $available_languages = array();

	/**
	 * lang switch
	 *
	 * @var array $language_options
	 */
	public $language_options = array();

	/**
	 * language for this user
	 *
	 * @var $current_language
	 */
	public $current_language;

	function __construct()
	{
		$this->db = Registry::get('dbCMS');
		$this->available_languages = $this->getAllLangs();
	}

	public function setLangOptions()
	{

		$sql = "SELECT * FROM " . $this->db->prefix . "site_languages WHERE is_online = 1";
		$result_id = $this->db->query($sql);
		$this->language_options = $this->db->fetchToArray($result_id);
		$options = array();

		foreach($this->language_options AS $option){

			$options[ $option[ 'lang' ] ][ 'lang' ] = $option[ 'lang' ];
			$options[ $option[ 'lang' ] ][ 'language' ] = ucfirst($option[ 'language' ]);

		}

		$this->language_options = $options;
	}

	// return all available site languages from database (array)
	private function getAllLangs()
	{
		$options = array();
		$sql = "SELECT * FROM " . $this->db->prefix . "site_languages WHERE is_online = 1";
		$result = $this->db->query($sql);
		$available_languages = array();
		while($row = $this->db->fetchToRow($result)){
			$available_languages[] = $row[ 'lang' ];
			$options[ $row[ 'lang' ] ][ 'lang' ] = $row[ 'lang' ];
			$options[ $row[ 'lang' ] ][ 'language' ] = ucfirst($row[ 'language' ]);
		}
		$this->language_options = $options;

		return $available_languages;
	}

	// set user language
	public function setLanguage($lang = false)
	{
		if($lang){
			if(in_array($lang, $this->available_languages)){
				$this->current_language = $lang;
			}
			else{
				$this->current_language = DEFAULT_LANG;
			}
		}
		else{
			$this->current_language = DEFAULT_LANG;
		}
		$this->saveLanguage();

		return $this->current_language;
	}

	private function saveLanguage()
	{
		$_SESSION[ 'user' ][ 'current_lang' ] = $this->current_language;
		$cookie_time = time() + 60 * 60 * 24 * 730;
		$lang = base64_encode($this->current_language);
		setcookie(COOKIE_LANG, $lang, $cookie_time, "/", "." . getDomainHost());
		unset($cookie_time, $lang);
	}

	// load page info
	public function loadPage($name = false)
	{
		return (array)$this->getAliasPage($name);
	}

	public function getAliasInfoArray()
	{

		$aliasInfoArray = array();

		$sql = "SELECT 
					*
				FROM 
					`" . $this->db->prefix . "pages`
				WHERE 
					lang=" . $this->db->quotes($this->current_language) . "";

		$result = $this->db->query($sql);
		while($row = $this->db->fetchToRow($result)){
			$aliasInfoArray[ $row[ 'alias' ] . '_url' ] = $row[ 'page_url' ];
		}

		return $aliasInfoArray;
	}

	private function getAltPages($alias)
	{
		$altpages = array();
		$sql = "SELECT  
					page_url,
					redirection_url,
					lang,
					alias
				FROM 
					" . $this->db->prefix . "pages
				WHERE 
					alias=" . $this->db->quotes($alias) . "";

		$rs = $this->db->query($sql);
		while($row = $this->db->fetchToRow($rs)){
			$altpages[ $row[ 'lang' ] ] = $row;
		}

		return $altpages;
	}

	// page from alias
	private function getAliasPage($name)
	{

		// get the page alias from page_url
		$sql = "	SELECT 
						* 
					FROM 
						" . $this->db->prefix . "pages
					WHERE 
						page_url = " . $this->db->quotes($name) . "
					;";
		$rs = $this->db->query($sql);
		$pageinfo = $this->db->fetchToArray($rs);

		if(count($pageinfo) == 1){
			$pageinfo = $pageinfo[ 0 ];
			if(isset($pageinfo[ 'lang' ])){
				if($this->current_language != $pageinfo[ 'lang' ]){
					if($this->uri[ 'forcelang' ] == false){
						$url = str_replace(URL_SCHEME . DOMAIN_NAME . '/',
										   URL_SCHEME . DOMAIN_NAME . '/' . $pageinfo[ 'lang' ] . '/',
										   $this->uri[ 'address' ]);
					}
					else{
						$url = str_replace($this->current_language, $pageinfo[ 'lang' ], $this->uri[ 'address' ]);
						$url = str_replace('/' . DEFAULT_LANG, "", $url);
					}
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $url);
					exit;
				}
			}
		}
		elseif(count($pageinfo)>1){
			// sometimes we have the same url for 2 or more languages ;)
			foreach($pageinfo as $key => $value){
				if($this->current_language == $value[ 'lang' ]){
					$pageinfo = $value;
					break;
				}
			}
		}
		else{
			$sql = "	SELECT 
							* 
						FROM 
							" . $this->db->prefix . "rules
						WHERE 
							rule_url = " . $this->db->quotes($name) . "
						LIMIT 1
						;";
			$rs = $this->db->query($sql);
			$row = $this->db->fetchToRow($rs);
			if(isset($row[ 'rule_url' ]) && $row[ 'rule_url' ] != ''){
				$this->uri[ 'params' ][ 0 ] = $name;
				$this->uri[ 'action' ] = 'index';

				return $this->getAliasPage('game');
			}

			if( !empty($pageinfo[ 0 ])) $pageinfo = $pageinfo[ 0 ];
		}

		// meta tags
		if(isset($pageinfo[ 'id' ])){
			$pageinfo[ 'meta' ] = $this->getMetaTagsData($pageinfo[ 'id' ],
														 $this->current_language);
		}
		else{
			$pageinfo[ 'meta' ] = array();
		}

		// alternative pages
		if(isset($pageinfo[ 'alias' ])){
			$pageinfo[ 'alt_pages' ] = $this->getAltPages($pageinfo[ 'alias' ]);
		}
		else{
			$pageinfo[ 'alt_pages' ] = array();
		}

		return $pageinfo;
	}

	public function getFrontPage()
	{

		$sql = "SELECT id,
						page_title,
						menu_title,
						page_url,
						parent_id,
						date_modified, 	
						date_created,
						creator_id,
						is_hidden,
						show_sidebar,
						is_frontpage,
						order_no,
						extra_page,
						controller,
						redirection_url,
						lang, 	
						type,
						alias, 	
						page_access FROM " . $this->db->prefix . "pages WHERE lang='" . $this->current_language . "' AND parent_id=0 AND is_frontpage=1";

		$rs = $this->db->query($sql);
		$pageinfo = $this->db->fetchToRow($rs);

		$pageinfo[ 'meta' ] = $this->getMetaTagsData($pageinfo[ 'id' ], $this->current_language);

		if(isset($pageinfo[ 'alias' ])){
			$pageinfo[ 'alt_pages' ] = $this->getAltPages($pageinfo[ 'alias' ]);
		}
		else{
			$pageinfo[ 'alt_pages' ] = array();
		}

		return $pageinfo;

	}

	public function getMetaTagsData($id = 0, $lang = null)
	{


		if($id == 0) return array();

		$sql = "SELECT 
					a.type, 
					a.attribute, 
					b.content 
				FROM 
					`" . $this->db->prefix . "meta_tag_data` a,
					`" . $this->db->prefix . "meta_tags` b
				WHERE 
					a.id=b.meta_tag_data_id
					AND 
					b.page_id=" . (int)$id;

		if(isset($lang)) $sql .= " AND b.lang='" . $lang . "'";

		$result_id = $this->db->query($sql);

		return $this->db->fetchToArray($result_id);
	}

	public function parseUrl()
	{
		$url = URL_SCHEME . DOMAIN_NAME . $_SERVER[ 'REQUEST_URI' ]; // the request

		// <!-- SEO requirement - redirect to lowercase
		if($url != strtolower($url)){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: " . strtolower($url));
		}

		// SEO requirement - redirect to address without "/" at the end
		$sla = substr($url, -1);
		$is_lang_root = false;

		foreach($this->available_languages as $k => $v){

			// default lang (in our case fr) should redirect to our main domain, without fr/ at the end
			if($v == DEFAULT_LANG && ($url == URL_SCHEME . DOMAIN_NAME . '/' . $v . '/' || $url == URL_SCHEME . DOMAIN_NAME . '/' . $v)){
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: " . URL_SCHEME . DOMAIN_NAME . '/');
				exit;
			} // all other langs should end with / if we are on homepage
			elseif($url == URL_SCHEME . DOMAIN_NAME . '/' . $v){
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: " . $url . '/');
				exit;
			} // if we are on homepage for spec. lang we should stop removing / at the end
			elseif($url == URL_SCHEME . DOMAIN_NAME . '/' . $v . '/'){
				$is_lang_root = true;
				break;
			}
		}

		// every other request should be without / at the end
		if($sla == '/' && URL_SCHEME . DOMAIN_NAME . '/' != $url && $is_lang_root === false){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: " . substr($url, 0, -1));
		}
		//  end SEO -->


		$pos = strstr($url, '?'); // the query string
		$url = str_replace($pos, "", $url); // remove get
		$parse = str_replace(SITE_URL, '', $url); // remove site main url

		$this->uri[ 'address' ] = $url;
		$this->uri[ 'forcelang' ] = false;

		// here we have all needed
		$e = explode("/", $parse);

		if(isset($e[ 0 ]) && !empty($e[ 0 ])){

			// if first parameter match the site languages -> set the language 
			if(in_array($e[ 0 ], $this->available_languages)){
				// if $this->uri['forcelang'] has a language key, site will force to use it
				if($e[ 0 ] == DEFAULT_LANG){
					$url = str_replace('/' . DEFAULT_LANG, "", $url);
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $url);
					exit;
				}

				$this->uri[ 'forcelang' ] = $e[ 0 ];
				// $this->current_language = $this->setLanguage($e[0]);
				array_shift($e);
			}

			if(isset($e[ 0 ]) && !empty($e[ 0 ])){
				$this->current_page = $this->loadPage($e[ 0 ]);
				if(isset($this->current_page[ 'id' ])){
					// if redirection is set
					if($this->current_page[ 'redirection_url' ]){
						System::redirect($this->current_page[ 'redirection_url' ]);
						exit;
					}
					// if controller is set for this page
					if(trim($this->current_page[ 'controller' ]) != ''){
						$arr = explode('/', $this->current_page[ 'controller' ]);
						$this->uri[ 'controller' ] = ucfirst($arr[ 0 ]);
						if(isset($arr[ 1 ]) && !empty($arr[ 1 ])){
							$this->uri[ 'action' ] = $arr[ 1 ];
						}
						else{
							if(isset($e[ 1 ]) && !empty($e[ 1 ])){
								$this->uri[ 'action' ] = $e[ 1 ];
							}
							else{
								$this->uri[ 'action' ] = 'index';
							}
						}
					}
					else{
						// default Page controller/action
						$this->uri[ 'controller' ] = 'DisplayPage';
						$this->uri[ 'action' ] = 'index';
					}
					array_shift($e);
				}
				else{
					$this->uri[ 'controller' ] = ucfirst($e[ 0 ]);
					array_shift($e);
					if(isset($e[ 0 ]) && !empty($e[ 0 ])){
						$this->uri[ 'action' ] = $e[ 0 ];
						array_shift($e);
					}
					else{
						$this->uri[ 'action' ] = 'index';
					}
				}
			}
			else{
				// we don't have params at all, so we should load Front page
				$this->current_page = $this->getFrontPage();

				// if homepage found
				if(isset($this->current_page[ 'id' ])){
					// if redirection is set
					if($this->current_page[ 'redirection_url' ]){
						System::redirect($this->current_page[ 'redirection_url' ]);
						exit;
					}
					// if controller is set for this page
					if(trim($this->current_page[ 'controller' ]) != ''){
						$arr = explode('/', $this->current_page[ 'controller' ]);
						$this->uri[ 'controller' ] = ucfirst($arr[ 0 ]);
						if(isset($arr[ 1 ]) && !empty($arr[ 1 ])){
							$this->uri[ 'action' ] = $arr[ 1 ];
						}
						else{
							$this->uri[ 'action' ] = 'index';
						}
					}
				}
				else{
					// default HomePage controller/action
					$this->uri[ 'controller' ] = 'HomePage';
					$this->uri[ 'action' ] = 'index';
				}
			}

		}
		else{
			// we don't have params at all, so we should load Front page
			$this->current_page = $this->getFrontPage();

			// if homepage found
			if(isset($this->current_page[ 'id' ])){
				// if redirection is set
				if($this->current_page[ 'redirection_url' ]){
					System::redirect($this->current_page[ 'redirection_url' ]);
					exit;
				}
				// if controller is set for this page
				if(trim($this->current_page[ 'controller' ]) != ''){
					$arr = explode('/', $this->current_page[ 'controller' ]);
					$this->uri[ 'controller' ] = ucfirst($arr[ 0 ]);
					if(isset($arr[ 1 ]) && !empty($arr[ 1 ])){
						$this->uri[ 'action' ] = $arr[ 1 ];
					}
					else{
						$this->uri[ 'action' ] = 'index';
					}
				}
			}
			else{
				// default HomePage controller/action
				$this->uri[ 'controller' ] = 'HomePage';
				$this->uri[ 'action' ] = 'index';
			}
		}


		// get aditional params
		if(isset($e[ 0 ]) && !empty($e[ 0 ])){
			for($i = 0; $i<count($e); $i++){
				$this->uri[ 'params' ][ $i ] = $e[ $i ];
			}
		}

		$this->aliasInfoArray = $this->getAliasInfoArray();

	}

}

?>
