<?php

class EmailModel
{

	/**
	 * @var null|Database $db
	 */
	private $db = null;

	function __construct()
	{

		// $this->db = Registry::get('db777');

	}


	/**
	 * getting template by id
	 *
	 * @param int $templateID
	 *
	 * @return string $message
	 */
	public function getTemplate($templateID)
	{
		if($templateID == 0){
			$message[ 'template' ] = file_get_contents(DOC_ROOT . 'emails/blush.php');
			$message[ 'subject' ] = 'Rhys Selects, Coming Soon...';
			$message[ 'sender' ] = 'notifications@rhysselects.co.uk';
		}
		else{
			$query = "SELECT id, template, subject, sender FROM mdpadmin.mailingtemplate WHERE id = '$templateID'";
			$result = $this->db->query($query);
			$row = $this->db->fetchToRow($result);

			$message[ 'template' ] = stripslashes($row[ 'template' ]);
			$message[ 'subject' ] = stripslashes($row[ 'subject' ]);
			$message[ 'sender' ] = $this->getSender($row[ 'sender' ]);
		}


		return $message;
	}

	private function getSender($senderID)
	{
		$query = "SELECT name FROM mdpadmin.mailingfrom WHERE id = '$senderID'";
		$result = $this->db->query($query);
		$row = $this->db->fetchToRow($result);

		return $row[ 'name' ];
	}

	public function sendMail($email_to, $name_to, $template_id, $params)
	{

		$mail = new PHPMailer();

		$template = $this->getTemplate($template_id);
		if(empty($template)){
			return false;
		}

		foreach($params as $k => $v){
			$template = str_replace('[!' . $k . '!]', $v, $template);
		}

		// our system use utf-8 but all email templates are iso-8859-1, so now we need to convert back to iso-8859-1 before send
		$template[ 'template' ] = utf8_decode($template[ 'template' ]);
		$template[ 'subject' ] = utf8_decode($template[ 'subject' ]);

		$mail->CharSet = 'iso-8859-1';
		$mail->Subject = $template[ 'subject' ];
		$mail->From = $template[ 'sender' ];
		$mail->FromName = 'RhysSelects.co.uk';
		$mail->msgHTML($template[ 'template' ]);

		$mail->addAddress($email_to, $name_to);
		$mail->send();

		return true;
	}

}

?>