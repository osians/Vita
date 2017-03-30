<?php

	require '../../classes/class.app.php';
	$app = new app();

	require("class.phpmailer.php");
	$mail = new PHPMailer();
	
	$mail->IsMail();
	$mail->IsHTML(true);
	
	$mail->CharSet	= "UTF-8";
	$mail->Priority	= 1; // Email priority (1 = High, 3 = Normal, 5 = low).
	
	$mail->From		= "suporte@medimixlatam.com.br";
	$mail->FromName	= "Medimix Datamanager";
	
	$mail->AddAddress("rcheberle@gmail.com");
	
	// -------------------------------------------------------------
	
	$_conteudo_email  = gmail_header;
	$_conteudo_email .= '<h3 style="font: normal bold 14px Arial, Helvetica, sans-serif; margin: 0; line-height: 22px; color: blue">Ativação de Conta de Usuário</h3><hr>';
	$_conteudo_email .= '<p  style="font: normal normal 12px Arial, Helvetica, sans-serif; margin: 0; line-height: 18px; color: #000000">';
	$_conteudo_email .= "Sua conta de usuário foi criada em " . date('d/m/Y') . " às " . date('H:i') . " - Informações:</p><br />";
	$_conteudo_email .= '<table style="border-collapse: collapse; width: 600px; font-size: 12px">';
	$_conteudo_email .= '<tr>';
	$_conteudo_email .= '<td style="padding: 5px; border: 1px solid #CCC; font-weight: bold; text-align: left">Nome:</td>';
	$_conteudo_email .= '<td style="padding: 5px; border: 1px solid #CCC; text-align: left">elvis</td>';
	$_conteudo_email .= '</tr>';
	$_conteudo_email .= '<tr>';
	$_conteudo_email .= '<td style="padding: 5px; border: 1px solid #CCC; font-weight: bold; text-align: left">CPF:</td>';
	$_conteudo_email .= '<td style="padding: 5px; border: 1px solid #CCC; text-align: left">3746938764398746398476</td>';
	$_conteudo_email .= '</tr>';
	$_conteudo_email .= '<tr>';
	$_conteudo_email .= '<td style="padding: 5px; border: 1px solid #CCC; font-weight: bold; text-align: left">Email:</td>';
	$_conteudo_email .= '<td style="padding: 5px; border: 1px solid #CCC; text-align: left">rcheberle@gmail.com</td>';
	$_conteudo_email .= '</tr>';
	$_conteudo_email .= '</table>';
	$_conteudo_email .= '';
	$_conteudo_email .= '<p style="color: #000000; font: normal normal 12px Arial, Helvetica, sans-serif; line-height: 18px;">';
	$_conteudo_email .= 'Para ativar sua conta de usuário clique no link: ';
	$_conteudo_email .= '<p style="color: #000000; font: normal normal 12px Arial, Helvetica, sans-serif; line-height: 18px;"><span style="color: red; font-weight: bold;">';
	$_conteudo_email .= 'ATENÇÃO:</span> o código de ativação é válido por 48 horas.</p>';
	
	// -------------------------------------------------------------
	
	$mail->Subject	= "Ativação Conta de Usuário";
	$mail->Body		= $_conteudo_email; // "Ativação de Conta.";
	
	$mail->Send();

?>
