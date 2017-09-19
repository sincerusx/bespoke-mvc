<?php

class HomeController extends Controller
{

	public function indexAction()
	{
		$this->view->assign('body', 'underconstruction');
		$this->view->display('index');
	}
}