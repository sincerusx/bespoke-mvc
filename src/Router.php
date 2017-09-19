<?php

class Router
{

	/**
	 * Default controller and action
	 *
	 * @var array $_defaults
	 */
	public $_defaults = array(
		'controller' => 'Home',
		'action'     => 'index',
	);

	/**
	 * Default controller and action when page not found
	 *
	 * @var array $_notfound
	 */
	public $_notfound = array(
		'controller' => 'Notfound',
		'action'     => 'index',
	);

	public $_defaultAction = 'index';

	/**
	 * FULL Requested URI
	 *
	 * @var string $_requestUri
	 */
	public $_requestUri;

	/**
	 * Collection of routes
	 *
	 * @var array $_routes
	 */
	public $_routes = array();

	/**
	 * Page data from database
	 *
	 * @var array $page
	 */
	public $page = array();

	/**
	 * Controller and action to call
	 *
	 * @var array $_call
	 */
	public $_call = array();

	/**
	 * @var array $current_page
	 */
	public $current_page;

	public static $params = array();

	/**
	 * Database CMS connection
	 *
	 * @var Database $db
	 */
	public $db;

	private $customSitemap = false;


	public function __construct($requestUri)
	{
		$this->db = Registry::get('dbCMS');

		return $this;
	}

	public static function factory($route)
	{
		$requestUri = self::parseUri($route);

		$router = new Router($requestUri);

		$router->_routes = explode('/', trim($requestUri, '/'));

		return $router;
	}


	private static function parseUri($request)
	{

		if($request == '/'){
			return null;
		}

		$url = URL_SCHEME . DOMAIN_NAME . $request; // the request

		// <!-- SEO requirement - redirect to lowercase
		if($url != strtolower($url)){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: " . strtolower($url));
		}

		// SEO requirement - redirect to address without "/" at the end
		$sla = substr($url, -1);
		$is_lang_root = false;

		// every other request should be without / at the end
		if($sla == '/' && URL_SCHEME . DOMAIN_NAME . '/' != $url && $is_lang_root === false){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: " . substr($url, 0, -1));
		}
		//  end SEO -->

		$qstr = strstr($url, '?'); // the query string
		$request = str_replace($qstr, "", $url); // remove get
		$request = str_replace(SITE_URL, '', $request); // remove site url from request

		return $request;
	}

	public function action()
	{
		// here we have the requested uri
		$e = $this->_routes;

		// check for pre-defined routes set in the application

		// check for sitemap in cms
		if($this->customSitemap && $e[ 0 ] == 'sitemap.xml'){
			$this->_call[ 'controller' ] = 'SiteMap';
			$this->_call[ 'action' ] = $this->_defaultAction;

			return $this->_call;
		}

		// check for homepage in cms
		if(isset($e[ 0 ]) && empty($e[ 0 ])){
			// check database for homepage
			if($this->current_page = $this->loadPage('/')){
				$this->_call[ 'controller' ] = $this->current_page[ 'controller' ];
				$this->_call[ 'action' ] = $this->_defaultAction;
			}
			else{
				$this->_call = $this->_defaults;
			}

			return $this->_call;
		}

		// check for request uri in cms
		if(isset($e[ 0 ]) && false === empty($e[ 0 ])){
			if($this->current_page = $this->loadPage(implode('/', $e))){
				// set page controller
				if(isset($this->current_page[ 'controller' ]) && !empty($this->current_page[ 'controller' ])){
					$e = explode('/', $this->current_page[ 'controller' ]);

					// Set controller to first URI and remove it from route collection
					$this->_call[ 'controller' ] = ucfirst($e[ 0 ]);
					array_shift($e);

					// Check if second URI exists
					if(isset($e[ 0 ]) && !empty($e[ 0 ])){
						$this->_call[ 'action' ] = $e[ 0 ];
					}
					else{
						$this->_call[ 'action' ] = $this->_defaultAction;
					}
				}
				else{
					// if page found in CMS but not controller set CMS defaults
					$this->_call[ 'controller' ] = 'DisplayPage';
					$this->_call[ 'action' ] = 'index';
				}
			}
			else{
				// page not found in database

				// set page data
				$this->current_page = $page = array();

				// Set controller to first URI and remove it from route collection
				$this->_call[ 'controller' ] = ucfirst($e[ 0 ]);
				array_shift($e);

				// Check if second URI exists
				if(isset($e[ 0 ]) && !empty($e[ 0 ])){
					$this->_call[ 'action' ] = $e[ 0 ];
				}
				else{
					$this->_call[ 'action' ] = $this->_defaultAction;
				}
			}
		}

		// Set page data in registry for later use
		Registry::set($this->current_page, 'page_data');

		return $this->_call;
	}

	public function actionWorking()
	{
		// here we have all needed
		$e = $this->_routes;

		/*if($e[0] == '') {
			dd( $e[0] );
		}*/

		// set controller and action
		if(isset($e[ 0 ]) && !empty($e[ 0 ])){

			// check if this page exits in our CMS
			if($this->current_page = $page = $this->loadPage(implode('/', $e))){
				// set page controller
				if(isset($page[ 'controller' ]) && !empty($page[ 'controller' ])){
					$e = explode('/', $page[ 'controller' ]);
					// Set controller to first URI and remove it from route collection
					$this->_call[ 'controller' ] = ucfirst($e[ 0 ]);
					array_shift($e);
					// Check if second URI exists
					if(isset($e[ 0 ]) && !empty($e[ 0 ])){
						$this->_call[ 'action' ] = $e[ 0 ];
					}
					else{
						$this->_call[ 'action' ] = $this->_defaultAction;
					}
				}
				else{
					// set cms defaults
					$this->_call[ 'controller' ] = 'DisplayPage';
					$this->_call[ 'action' ] = 'index';
				}
			}
			else{
				// set page data
				$this->current_page = $page = array();
				// Set controller to first URI and remove it from route collection
				$this->_call[ 'controller' ] = ucfirst($e[ 0 ]);
				array_shift($e);
				// Check if second URI exists
				if(isset($e[ 0 ]) && !empty($e[ 0 ])){

					$this->_call[ 'action' ] = $e[ 0 ];
				}
				else{
					$this->_call[ 'action' ] = $this->_defaultAction;
				}
			}
		}
		else{
			$this->current_page = $page = $this->loadPage('/');
			$this->_call = $this->_defaults;
		}

		array_shift($e);

		// set aditional params
		if(isset($e[ 0 ]) && !empty($e[ 0 ])){
			for($i = 0; $i<count($e); $i++){
				$this->_call[ 'params' ][ $i ] = $e[ $i ];
			}
		}

		// set page data
		if(isset($this->current_page) && !empty($this->current_page)){
			// $this->_call[ 'page_data' ] = $this->current_page;
			Registry::set($this->current_page, 'page_data');
		}

		return $this->_call;
	}

	private function checkController($e)
	{
		if(file_exists(CONTROLLER_DIR . ucfirst($e[ 0 ]) . 'Controller' . EXT)){

			$controller = ucfirst($e[ 0 ]) . 'Controller';

			// Set controller to first URI and remove it from route collection
			$this->_call[ 'controller' ] = ucfirst($e[ 0 ]);
			array_shift($e);
			// Check if second URI exists
			if(method_exists($controller, $e[ 0 ])){
				$this->_call[ 'action' ] = $e[ 0 ];
			}
			else{
				$this->_call[ 'action' ] = $this->_defaultAction;
			}

			return $this->_call;
		}

		return false;
	}

	private function checkDatabase()
	{

	}

	private function loadPage($name = false)
	{
		if($page = $this->getAliasPage($name)){
			return (array)$page;
		}
		else{
			return false;
		}
	}

	private function getAliasPage($requestUri)
	{

		// get the page alias from page_url
		$sql = "SELECT * FROM `cms_pages` WHERE `slug` = " . $this->db->quotes($requestUri) . ";";
		$rs = $this->db->query($sql);

		if(0 === $rs->num_rows){
			return false;
		}

		$pageinfo = $this->db->fetchToRow($rs);

		// meta tags
		if(isset($pageinfo[ 'page_id' ])){
			$pageinfo[ 'meta' ] = $this->getMetaTagsData($pageinfo[ 'page_id' ]);
		}

		// alternative pages
		if(isset($pageinfo[ 'alias' ])){
			$pageinfo[ 'alt_pages' ] = $this->getAltPages($pageinfo[ 'alias' ]);
		}

		return $pageinfo;
	}

	public function getMetaTagsData($id = 0, $lang = null)
	{

		if($id == 0) return array();

		$sql = "SELECT 
					m.`type`, m.`attribute`, c.`meta_content`
				FROM 
					`cms_meta_tags` as m
				LEFT JOIN 
					`cms_meta_content` as c
						USING(`meta_id`)
				LEFT JOIN `cms_pages` as p
						USING(`page_id`)
				WHERE 
					p.`page_id` = " . (int)$id;

		$result_id = $this->db->query($sql);

		return $this->db->fetchToArray($result_id);
	}

	private function getAltPages($alias)
	{
		$altpages = array();

		$sql = "SELECT `page_url`, `redirection_url`, `lang`, `alias` FROM `cms_pages` WHERE `alias` =" . $this->db->quotes($alias);

		$rs = $this->db->query($sql);
		while($row = $this->db->fetchToRow($rs)){
			$altpages[ $row[ 'lang' ] ] = $row;
		}

		return $altpages;
	}

	public function getFrontPage()
	{

		$sql = "SELECT 
					`id`, `page_title`, `menu_title`, `page_url`, `parent_id`, 
					`date_modified`,`date_created`, `creator_id`, `is_hidden`, 
					`show_sidebar`, `is_frontpage`, `order_no`, `extra_page`, 
					`controller`, `redirection_url`, lang, `type`,
					`alias`, `page_access` 
				FROM 
				 	`cms_pages`
				WHERE 
					`parent_id` = 0 AND `is_frontpage` = 1";

		$rs = $this->db->query($sql);
		$pageinfo = $this->db->fetchToRow($rs);

		$pageinfo[ 'meta' ] = $this->getMetaTagsData($pageinfo[ 'id' ]);

		if(isset($pageinfo[ 'alias' ])){
			$pageinfo[ 'alt_pages' ] = $this->getAltPages($pageinfo[ 'alias' ]);
		}
		else{
			$pageinfo[ 'alt_pages' ] = array();
		}

		return $pageinfo;

	}
}