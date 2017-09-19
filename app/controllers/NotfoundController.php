<?php

/**
 * Created by Liam Nelson.
 * Email: liam.nelson@gimo.co.uk
 * Date: 25/04/2017
 * Time: 18:43
 */
class NotfoundController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		header('HTTP/1.0 404 Not Found', true, 404);
	}


	public function indexAction()
	{
		$this->view->assign('body', 'underconstruction');
		$this->view->display('index');
	}

}