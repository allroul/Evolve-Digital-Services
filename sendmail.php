<?php 
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'phpmailer/src/Exception.php';
	require 'phpmailer/src/PHPMailer.php';

	$mail = new PHPMailer(true);
	$mail->Charset = 'UTF-8';
	$mail->setLanguage('en', 'phpmailer/language/');
	$mail->isHTML(true);

	// From whom mail
	$mail->setFrom('pilipchuk.alexanderbb@gmail.com');
	// To whom
	$mail->addAddress('hello@evolvedigitalservices.com');
	// Тема письма
	$mail->Subject = 'Hi! The new client has been applied!';

	$body = '<h1>Here we got a new client!</h1>';

	if(trim(!empty($_POST['name']))){
		$body.='<br><p><strong>Name:</strong> '.$_POST['name'].'</p>';
	}

	if(trim(!empty($_POST['business']))){
		$body.='<br><p><strong>Business:</strong> '.$_POST['business'].'</p>';
	}

	if(trim(!empty($_POST['tel']))){
		$body.='<br><p><strong>Phone number:</strong> '.$_POST['tel'].'</p>';
	}

	$mail->Body = $body;

	if(!$mail->send()){
		$message = 'Error';
	} else {
		$message = 'Your request has been submitted. We’ll contact you soon.';
	}

	$response = ['message' => $message];

	header('Content-type: application/json');
	echo json_encode($response);


 ?>