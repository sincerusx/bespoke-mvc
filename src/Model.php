<?php

class Model
{

	public $result;
	public $connection;
	public $db;
	public $objPg = null;

	public function __construct()
	{
		$this->db = Registry::get('db');
	}

	public function passControllerObject($object)
	{
		//need a function to build array of objects in this class and keep them if they are passed thoruoght he controller also
		$disallowed_objects = array(
			'controller',
			'model',
			'view',
			'register',
		);
		foreach($object as $k => $v){
			if( !in_array(strtolower($k), $disallowed_objects) || $k == get_class($this)){
				$this->$k = $v;
			}
		}
	}

	public function getPage($id = 0)
	{

		//following sql will be fast if we are going to use cache.
		$id = (int)$id;
		$sql = " SELECT 
							id,
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
							page_access
							 FROM `" . TABLE_PREFIX . "pages` 
				WHERE `id` =" . $id;
		$sql .= " limit 1";

		$result_id = $this->db->query($sql);

		return $this->db->fetchToRow($result_id);
	}

	public function getMetaTagsData($id = 0, $lang = null)
	{
		if($id == 0) return array();


		$sql = " SELECT 	a.type, a.attribute, b.content 
				FROM `" . TABLE_PREFIX . "meta_tag_data` a,`" . TABLE_PREFIX . "meta_tags` b
					WHERE
				a.id=b.meta_tag_data_id
				AND b.page_id=" . (int)$id
			   . " AND b.visible=1";

		if(isset($lang)){
			$sql .= " AND b.lang='" . $lang . "'";
		}


		$result_id = $this->db->query($sql);

		return $this->db->fetchToArray($result_id);
	}


	function hasPagination()
	{
		return isset($this->objPg);
	}

	function setPagination($objPg = null)
	{
		$this->objPg = $objPg;
	}

	function getPagination()
	{
		return $this->objPg;
	}
}