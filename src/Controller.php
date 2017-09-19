<?php

class Controller
{

	/**
	 * View object
	 *
	 * @var View $view
	 */
	protected $view;

	/**
	 * @var array $page_data
	 */
	protected $page_data;

	/**
	 * Google Analytics ID
	 *
	 * @var string $_GA
	 */
	private $_GA = '';

	/**
	 * Google Tag Manager ID
	 *
	 * @var string $_GTM
	 */
	private $_GTM = '';

	public function __construct()
	{

		$this->view = new View();
		$this->view->assign('_GA', $this->_GA);
		$this->view->assign('_GTM', $this->_GTM);

		if(Registry::isRegister('page_data')){
			$this->page_data = Registry::get('page_data');
			$this->view->assign('page_data', $this->page_data);

			// meta title
			if(isset($this->page_data[ 'title' ]) && !empty(isset($this->page_data[ 'title' ]))){
				$this->view->renderMetaTitle($this->page_data[ 'title' ]);
			}

			// meta data
			if(isset($this->page_data[ 'meta' ])){
				$this->view->renderMetaTags($this->page_data[ 'meta' ]);
			}

			// page content
			if(isset($this->page_data[ 'content' ])){
				$this->view->assign('body', $this->page_data[ 'content' ]);
			}
		}
	}

}