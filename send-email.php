<?php
require_once 'vendor/PHPMailer/PHPMailerAutoload.php';
require_once 'vendor/PHPMailer/class.phpmailer.php';
require_once 'vendor/PHPMailer/class.smtp.php';
require_once 'vendor/PHPMailer/class.pop3.php';


function sendMail()
{
   global $email;

   $debugLevel = 0;
   $mail = new PHPMailer(true);
   $mail->SMTPDebug = $debugLevel;
   $mail->SMTPAuth = true;
   $mail->IsSMTP();
   $mail->Host = $email['Host'];
   $mail->Port = $email['Port'];
   $mail->Username = $email['Username'];
   $mail->Password = $email['Password'];
   $mail->CharSet = "UTF-8";
   $mail->ContentType = 'text/html; charset=utf-8\r\n';
   $mail->Encoding = '8bit';
   $mail->SetFrom($email['SetFrom']['email'], $email['SetFrom']['name']);
   if (isset($email['AddReplyTo'])) {
		$mail->AddReplyTo($email['AddReplyTo']['email'], $email['AddReplyTo']['name']);
		$mail->AddCC($email['AddReplyTo']['email'], $email['AddReplyTo']['name']);
	}
   $mail->Subject = $email['Subject'];
   $mail->MsgHTML($email['MsgHTML']);
   $mail->AddAddress($email['To']['email'], $email['To']['name']);
   $result = $mail->Send();

   if ($result) return array("status"=>true);
   else return array("status"=>false, "error"=>$mail->ErrorInfo);
}
