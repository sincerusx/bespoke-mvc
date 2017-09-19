<?php

class DevController extends Controller
{

	/**
	 * @var string $CODE_debug
	 */
	private $CODE_debug;

	/**
	 * @var EmailModel $Emails
	 */
	private $Emails;

	public function __construct()
	{
		parent::__construct();

		if(false === App::isDevIP()){
			header('HTTP/1.0 404 Not Found', true, 404);
			$this->view->assign('body', 'underconstruction');
			$this->view->display('index');
			exit;
		}

		$this->CODE_debug = App::$CODE_debug;
	}

	public function indexAction()
	{
		// all cookies
		$cookiesArray = array(
			'_sitedebug' => 'ALLPAGES: Display application DEV environment in footer',
			'_jQuery'    => 'jQuery: Check that jQuery is working',
		);

		$redirection = isset($_GET[ 'redir' ]) ? $_GET[ 'redir' ] : '/dev';

		// with SESSIONS
		if(isset($_GET[ 'activate' ])){
			$cookie_add = 'dev' . trim($_GET[ 'activate' ]);
			setcookie($cookie_add, $this->CODE_debug, time() + 3600000, "/", DOMAIN_HOST);
			App::Redirect($redirection);
		}

		if(isset($_GET[ 'remove' ])){
			$cookie_add = 'dev' . trim($_GET[ 'remove' ]);
			setcookie($cookie_add, '', time() - 3600, "/", DOMAIN_HOST);
			App::Redirect($redirection);
		}

		header('Content-Type: text/html; charset=utf-8');
		$this->view->assign('cookiesArray', $cookiesArray);
		$this->view->display('dev');
	}

	public function whoamiAction()
	{
		phpinfo();
	}

	public function sendEmailAction()
	{
		$this->Emails = new EmailModel();

		$details = array(
			0 => array(
				'email_to' => 'sincere92@ymail.com',
				'name_to'  => 'Sincere',
				'params'   => array(
					'firstname'       => 'Liam',
					'COMPANY_NAME'    => 'Rhys Selects',
					'COMPANY_ADDRESS' => 'COMPANY_ADDRESS',
				),
			),
			1 => array(
				'email_to' => 'rhysjnelson@hotmail.co.uk',
				'name_to'  => 'Rhys J',
				'params'   => array(
					'firstname'       => 'Rhys J',
					'COMPANY_NAME'    => 'Rhys Selects',
					'COMPANY_ADDRESS' => 'COMPANY_ADDRESS',
				),
			),
			2 => array(
				'email_to' => 'rhysmarcusjay@gmail.com',
				'name_to'  => 'Rhys Marcus Jay',
				'params'   => array(
					'firstname'       => 'Rhys Marcus Jay',
					'COMPANY_NAME'    => 'Rhys Selects',
					'COMPANY_ADDRESS' => 'COMPANY_ADDRESS',
				),
			),
			3 => array(
				'email_to' => 'sanchellederby@hotmail.co.uk',
				'name_to'  => 'Sanchelle',
				'params'   => array(
					'firstname'       => 'Sanchelle',
					'COMPANY_NAME'    => 'Rhys Selects',
					'COMPANY_ADDRESS' => 'COMPANY_ADDRESS',
				),
			),
		);


		foreach($details as $k => $v){
			$this->Emails->sendMail($v[ 'email_to' ], $v[ 'name_to' ], 0, $v[ 'params' ]);
			echo 'Email sent to ' . $v[ 'email_to' ] . ' <br>';
		}

		/*$email_to = 'sincere92@ymail.com';
		$name_to = 'Sincere';
		$template_id = 0;
		$params['firstname'] = 'Liam';

		$this->Emails->sendMail($email_to, $name_to, $template_id, $params);*/
	}

}