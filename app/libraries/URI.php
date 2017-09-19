<?

/**
 * Class URI
 */
class URI
{

	public $db;
	public $tree;
	public $all_pages;
	public $current_lang;

	public function __construct()
	{
		$this->db = Registry::get('dbCMS');
	}

	final static function URISegment($section)
	{
		$request_uri = URI::getPathURI();
		$uri_array = explode('/', $request_uri);

		return isset($uri_array[ $section ]) ? $uri_array[ $section ] : false;

	}

	final static function URIParams()
	{
		$request_uri = URI::getPathURI();
		$uri_array = explode('/', $request_uri);
		unset($uri_array[ 0 ]);
		$uri_array = array_values($uri_array);

		return $uri_array;

	}

	/*
	Ideally, we want to be able to rewrite any url to use any
	controller/method combination.  This means being able to
	pass extra URI segments to methods as parameters.
	Therefore:
	1. match the current url as far as possible against the pages table, in the cms.
	2. Take the controller/method property for that page.
	3. Append the remaining (unmatched) segments from the url.
	4. Call the controller.
	*/


	public function getPage($path_info = false)
	{

		if($path_info !== false){
			$path_info = $path_info;
		}
		else{
			$path_info = URI::getPathURI();
		}

		if( !isset($path_info)){
			return false;
		}

		$request_uri = $path_info;

		if(empty($request_uri)){
			return false;
		}


		if($request_uri == '/'){
			// fix for HomePage
			$uri_array[ 0 ] = isset($_GET[ 'lang_select' ]) ? $_GET[ 'lang_select' ] : 'fr';
		}
		else{
			$uri_array = explode('/', $request_uri);
			array_shift($uri_array); // clear the empty portion
		}


		$parent_id = 0;
		$page_data = false;
		$first = true;
		while($first || ($result && count($uri_array)>0)){
			$first = false;
			/*
			$sql = "SELECT  p.id,
							p.page_title,
							p.menu_title,
							p.page_url,
							p.parent_id,
							p.date_modified,
							p.date_created,
							p.creator_id,
							p.is_hidden,
							p.show_sidebar,
							p.is_frontpage,
							p.order_no,
							p.extra_page,
							p.controller,
							p.redirection_url,
							p.lang,
							p.type,
							p.alias,
							p.page_access,
							mt.content
							FROM " . TABLE_PREFIX . "pages AS p
							LEFT JOIN " . TABLE_PREFIX . "meta_tags AS mt
							ON mt.page_id = p.id
							AND mt.lang = p.lang
							WHERE page_url=" . $this->db->quotes($uri_array[0]) . " AND parent_id=" . $parent_id;
			*/

			if(isset($_GET[ 'lang_select' ])){
				$current_lang = $_GET[ 'lang_select' ];
			}
			else{
				$current_lang = 'fr';
			}

			$sql = "SELECT * 
					FROM " . TABLE_PREFIX . "pages
					WHERE   page_url = " . $this->db->quotes($uri_array[ 0 ]) . "
					AND lang = " . $this->db->quotes($current_lang) . "
					AND parent_id = " . $parent_id . ";";

			include_once DOC_ROOT . 'libs/firephp/fb.php';

			FB::log($current_lang);

			// AND lang='" . $_SESSION['current_lang'] . "'
			#echo $sql."<br>";
			// ECHO '<pre>';
			// var_dump($sql);
			// echo '</pre>';

			// die('#1');

			$rs = $this->db->query($sql);
			$result = $this->db->fetchToRow($rs);

			FB::log($result);

			if($result){
				$parent_id = $result[ 'id' ];
				$page_data = $result;
				array_shift($uri_array);
			}
		}


		/* add the unmatched fields to the page_data */
		if($page_data){
			$page_data[ 'uri_param' ] = implode('/', $uri_array);
		}

		if($page_data === false){
			return false;
		}

		$page_data[ 'alt_pages' ] = $this->getAltPages($page_data[ 'alias' ]);

		return $page_data;


	}

	private function getAltPages($alias)
	{
		$sql = "SELECT  
			page_url,
			redirection_url,
			lang,
			alias
			FROM " . TABLE_PREFIX . "pages
			WHERE alias=" . $this->db->quotes($alias) . "";

		$rs = $this->db->query($sql);
		$result = $this->db->fetchToArray($rs);

		return $result;
	}

	public function getFrontPage($lang = 'en')
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
							page_access FROM " . TABLE_PREFIX . "pages WHERE lang='" . $lang . "' AND parent_id=0 AND is_frontpage=1";
		$rs = $this->db->query($sql);

		return $this->db->fetchToRow($rs);

	}


	public function getMetaTagsData($id = 0, $lang = null)
	{
		if($id == 0) return array();

		$sql = "SELECT a.type, a.attribute, b.content 
				FROM `" . TABLE_PREFIX . "meta_tag_data` a,`" . TABLE_PREFIX . "meta_tags` b
				WHERE a.id=b.meta_tag_data_id
				AND b.page_id=" . (int)$id;

		if(isset($lang)){
			$sql .= " AND b.lang='" . $lang . "'";
		}

		$result_id = $this->db->query($sql);

		return $this->db->fetchToArray($result_id);
	}


	public function listPages($current_lang)
	{

		//one database call is enough - store in array and recurse through there
		$sql = "SELECT a.id, a.page_title, a.page_url, a.parent_id, a.menu_title, a.redirection_url, a.alias, a.is_frontpage
				FROM `" . TABLE_PREFIX . "pages` as a
				LEFT JOIN `" . TABLE_PREFIX . "pages` as b ON b.id = a.parent_id
				WHERE a.lang = " . $this->db->quotes($current_lang) . " AND a.extra_page != 1 AND a.is_hidden != 1
				ORDER BY a.is_frontpage DESC, a.`order_no` ASC, a.`id` ASC
				";
		//AND a.is_frontpage != 1		
		$result_id = $this->db->query($sql);
		$this->all_pages = $this->db->fetchToArray($result_id);

		foreach($this->all_pages as $v){
			//check if node has a parent id if not then add to array
			if($v[ 'parent_id' ] == 0){
				//$v['depth'] = 0;
				//$v['has_children'] = $this->hasChildren($v['id']);
				$v[ 'tree' ] = $this->getChildNodes($v[ 'id' ]); //add child nodes to tree
				$this->tree[] = $v;
			}
		}

		return $this->tree;

	}

	//to get page first read first uri, then read 2nd etc until one left

	//simply add to tree
	public function getChildNodes($parent_id)
	{
		$i = 0;
		$children = array();
		foreach($this->all_pages as $k => $v){

			if($v[ 'parent_id' ] == $parent_id){
				$v[ 'page_title' ] = $v[ 'page_title' ];
				$data = $this->getPageByParentId($parent_id);
				$v[ 'tree' ][] = $this->getChildNodes($v[ 'id' ]);
				$children[] = $v;
				//die();
			}
			$i++;

			if($i == count($this->all_pages)){
				return $children;
			}

		}

	}

	private function hasChildren($id)
	{
		foreach($this->all_pages as $k => $v){
			if($v[ 'parent_id' ] == $id){
				return true;
			}
		}

		return false;
	}

	public function getPageFromArray($uri_array)
	{

		foreach($uri_array as $k => $v){
			$data = $this->getPagesByPageURL($v);
		}

	}

	public function getPageByParentId($parent_id)
	{
		foreach($this->all_pages as $k => $v){
			if($v[ 'id' ] == $parent_id){
				$data = $v;
			}
		}

		return $data;
	}

	public function getPagesByPageURL($page_url)
	{
		$data = array();
		foreach($this->all_pages as $k => $v){
			if($v[ 'page_url' ] == $page_url){
				$data[] = $v;
			}
		}

		return $data;
	}

	public function getPathLangSelect()
	{
		$all_langs = getSiteLanguages();
		$lang_regex = implode("|", $all_langs);
		$uri = URI::getRequestURI();
		$lang = preg_match('/^\/(' . $lang_regex . ')$/', $uri, $m);
		if(count($m)>0){
			return $m[ 1 ];
		}
		$lang = preg_match('/^\/(' . $lang_regex . ')\//', $uri, $m);
		if(count($m)>0){
			return $m[ 1 ];
		}

		return false;
	}


	public static function getPathURI()
	{
		if( !isset($_SERVER[ 'PATH_INFO' ])){
			$requesturi = $_SERVER[ 'REQUEST_URI' ];
			$requesturi = str_replace('/dev_new/', '', $requesturi);
			$requesturi = str_replace($_SERVER[ 'SCRIPT_NAME' ], '', $requesturi);
			$requesturi = str_replace('?' . $_SERVER[ 'QUERY_STRING' ], '', $requesturi);
		}
		else{
			$requesturi = $_SERVER[ 'PATH_INFO' ];
		}
		/* remove lang portion */
		$all_langs = getSiteLanguages();
		$lang_regex = implode("|", $all_langs);
		$requesturi = preg_replace('/^\/(' . $lang_regex . ')\//', '/', $requesturi);

		return $requesturi;
	}

	public function getRequestURI()
	{
		/* returns everything after index.php */
		$request_uri = $_SERVER[ 'REQUEST_URI' ];
		$request_uri = preg_replace('/^\/index.php$/', '/', $request_uri);
		$request_uri = preg_replace('/^\/index.php\//', '/', $request_uri);

		return $request_uri;
	}

	public static function URISegmentFromRequestURI($section)
	{
		$request_uri = URI::getRequestURI();
		$uri_array = explode('/', $request_uri);

		return isset($uri_array[ $section ]) ? $uri_array[ $section ] : false;
	}

}

?>
