<?php

require_once 'vendor/PHPMailer/PHPMailerAutoload.php';
require_once 'vendor/PHPMailer/class.phpmailer.php';
require_once 'vendor/PHPMailer/class.smtp.php';
require_once 'vendor/PHPMailer/class.pop3.php';

class SendEmailMessage
{
	public $config;

	public function __construct($config = null)
	{
		global $email;
		if (isset($contfig)) $this->config = $config;
		else {
			$this->config['Host'] = $email['Host'];
			$this->config['Port'] = $email['Port'];
			$this->config['Username'] = $email['Username'];
			$this->config['Password'] = $email['Password'];

		}
		if (!isset($this->config['Username'])) throw new \Exception("No Username was set as From!");
		if (!isset($this->config['Password'])) throw new \Exception("No Password was set as From!");
		if (!isset($this->config['Host'])) throw new \Exception("No Host was set as From!");
	}

	/**
	 * @var EmailItem
	 */
	public $Sender;


	public function setSender($email, $name = null)
	{
		if (!isset($name)) {
			$emailArray = $this->splitNameEmail($email);
			$email = $emailArray[0];
		}
		$this->Sender =$email;
		$this->addBCC($email);
	}
	/**
	 * @var EmailItem
	 */
	public $From;


	public function setFrom($email, $name = null)
	{
		if (!isset($name)) {
			$emailArray = $this->splitNameEmail($email);
			$email = $emailArray[0];
			$name = $emailArray[1];
		}
		$this->From = new EmailItem($email, $name);
	}

	/**
	 * @var EmailItem[]
	 */
	public $ReplyTo = [];

	public function addReplyTo($email, $name = null)
	{
		if (!isset($name)) {
			$emailArray = $this->splitNameEmail($email);
			$email = $emailArray[0];
			$name = $emailArray[1];
		}
		$this->ReplyTo[] = new EmailItem($email, $name);
	}

	/**
	 * @var EmailItem[]
	 */
	public $CC = [];

	public function addCC($email, $name = null)
	{
		if (!isset($name)) {
			$emailArray = $this->splitNameEmail($email);
			$email = $emailArray[0];
			$name = $emailArray[1];
		}
		$this->CC[] = new EmailItem($email, $name);
	}

	/**
	 * @var EmailItem[]
	 */
	public $BCC = [];

	public function addBCC($email, $name = null)
	{
		if (!isset($name)) {
			$emailArray = $this->splitNameEmail($email);
			$email = $emailArray[0];
			$name = $emailArray[1];
		}
		$this->BCC[] = new EmailItem($email, $name);
	}

	/**
	 * @var EmailItem[]
	 */
	public $To = array();

	public function addTo($email, $name = null)
	{
		if (!isset($name)) {
			$emailArray = $this->splitNameEmail($email);
			$email = $emailArray[0];
			$name = $emailArray[1];
		}
		$this->To[] = new EmailItem($email, $name);
	}

	private function splitNameEmail($string)
	{
		if (strpos($string, "<") == false) $string = $string . "<" . $string . ">";
		$words = explode("<", $string);
		$name = $words[0];
		$email = str_replace(">", "", $words[1]);
		return array($email, $name );
	}

	public function addToList($listStr)
	{
		$listArray = explode(" ", $listStr);
		foreach ($listArray as $to) {
			if ($to == "") continue;
			$this->addTo($to);
		}
	}

	public $Subject = "";
	public $HtmlBody = "";
	/**
	 * @var AttachmentFile[]
	 */
	public $Attachments = array();

	public function addAttachment($path, $name, $encoding = 'base64', $type = 'application/pdf')
	{
		if (!file_exists($path)) throw new \Exception("Attachment file {$path} doesn't not exist!");
		$name = str_replace(':', " ", $name);
		$name = str_replace(' ', "_", $name);
		$this->Attachments[] = new AttachmentFile($path, $name, is_null($encoding) ? 'base64' : $encoding, is_null($type) ? 'application/pdf' : $type);
	}

	public function send()
	{
		$debugLevel = 0;
// $debugLevel = 0;
//   $pop = new POP3();
//   $connected = $pop->Authorise($email['Host'], $email["Port"], 10, $email['Username'], $email['Password'], $debug = $debugLevel);
//   if (!$connected) return false;
		/**
		 * var PHPMailer
		 */

		$mail = new \PHPMailer(true);
		$mail->SMTPDebug = $debugLevel;
		$mail->SMTPAuth = true;
		$mail->IsSMTP();
		$mail->Host = $this->config['Host'];
		$mail->Port = $this->config['Port'];
		$mail->Username = $this->config['Username'];
		$mail->Password = $this->config['Password'];
		$mail->CharSet = "UTF-8";
		$mail->ContentType = 'text/html; charset=utf-8\r\n';
		$mail->Encoding = '8bit';
		if (!isset($this->From)) throw new \Exception("From member of msg is not set!");
		$mail->SetFrom($this->From->email, $this->From->name);
		$mail->Sender = $this->Sender;

		if (isset($this->ReplyTo) && count($this->ReplyTo) ) {
			foreach ($this->ReplyTo as $contact) {
				$mail->addReplyTo($contact->email, $contact->name);
			}
		} else $mail->addReplyTo($this->Sender);

		if (isset($this->CC)) {
			foreach ($this->CC as $contact) {
				$mail->addCC($contact->email, $contact->name);
			}
		}

		if (isset($this->BCC)) {
			foreach ($this->BCC as $contact) {
				$mail->addBCC($contact->email, $contact->name);
			}
		}

		if (!isset($this->Subject)) throw new \Exception("No subject was set for msg!");
		$mail->Subject = $this->Subject;

		if (!isset($this->HtmlBody)) throw new \Exception("No HtmlBody was set for msg!");
		$mail->MsgHTML($this->HtmlBody);

		if (!isset($this->To)) throw new \Exception("To member of msg is not set!");
		foreach ($this->To as $contact) {
			$mail->AddAddress($contact->email, $contact->name);
		}

		if (isset($this->Attachments)) {
			foreach ($this->Attachments as $attachment) {
				$mail->AddAttachment($attachment->path, $attachment->name, $attachment->encoding, $attachment->type);
			}
		}
		$result = $mail->Send();
		if ($result) return "OK";
		else return "ERROR";
	}
}

class EmailItem
{
	public $email;
	public $name;

	public function __construct($email, $name)
	{
		$this->email = $email;
		$this->name = $name;
	}
}

class AttachmentFile
{
	public $name = "";
	public $path = "";
	public $encoding = "";
	public $type = "";

	public function __construct($path, $name, $encoding = 'base64', $type = 'application/octet-stream')
	{
		$this->path = $path;
		$this->name = $name;
		$this->encoding = $encoding;
		$this->type = $type;
	}
}
