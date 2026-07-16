<?php
class Email extends Metodos {
	function Email($Conn){
		$this->Conn = $Conn;
	}
	
	public function Contato($dados, $template) {
		//$email = "gustavomenndez@gmail.com";
		$email = $dados['setor'];
		$email_contato = $dados['email'];
		$email_remetente = "sistema@grupofassil.com.br";
		$titulo = 'Grupo Fassil';
		$assunto = 'Contato - '.$dados['nome'];

		$headers = "MIME-Version: 1.1\n";
	    $headers .= "Content-Type:text/html; charset=UTF-8n\n";
	    $headers .= "From: $titulo <$email_remetente>\n"; // remetente
	    $headers .= "Return-Path: $email_remetente\n"; // return-path
	    $headers .= "Reply-To: $email_contato\n"; // Endereço (devidamente validado) que o seu usuário informou no contato

		$feedback = (mail("$email", "$assunto", "$template", $headers, "-f$email_remetente")) ? "Enviado!" : "Falha de envio!";

		return $feedback;
	}

	public function Cadastro($dados, $template) {
		$email = $dados['email'];
		$email_contato = $dados['email'];
		$email_remetente = "sistema@grupofassil.com.br";
		$titulo = 'Grupo Fassil';
		$assunto = 'Cadastro - '.$dados['nome'];

		$headers = "MIME-Version: 1.1\n";
		$headers .= "Content-Type:text/html; charset=UTF-8n\n";
		$headers .= "From: $titulo <$email_remetente>\n"; // remetente
		$headers .= "Return-Path: $email_remetente\n"; // return-path
		$headers .= "Reply-To: $email_contato\n"; // Endereço (devidamente validado) que o seu usuário informou no contato

		$feedback = (mail("$email", "$assunto", "$template", $headers, "-f$email_remetente")) ? true : false;

		return $feedback;
	}

	public function Ticket($dados, $template) {
		$email = $dados['Cliente_Email'];
		$email_contato = $dados['Cliente_Email'];
		$email_remetente = "sistema@grupofassil.com.br";
		$titulo = 'Grupo Fassil';
		$assunto = 'Ticket - '.$dados['OS_Protocolo'];

		$headers = "MIME-Version: 1.1\n";
		$headers .= "Content-Type:text/html; charset=UTF-8n\n";
		$headers .= "From: $titulo <$email_remetente>\n"; // remetente
		$headers .= "Return-Path: $email_remetente\n"; // return-path
		$headers .= "Reply-To: $email_contato\n"; // Endereço (devidamente validado) que o seu usuário informou no contato

		$feedback = (mail("$email", "$assunto", "$template", $headers, "-f$email_remetente")) ? true : false;

		return $feedback;
	}

	public function Backup($dados, $template) {
		$email = "suporte@faspelinformatica.com.br";
		$email_contato = "suporte@faspelinformatica.com.br";
		$email_remetente = "sistema@grupofassil.com.br";
		$titulo = 'Grupo Fassil';
		$assunto = 'Backup Automático - '.date("d/m/Y");

		$headers = "MIME-Version: 1.1\n";
		$headers .= "Content-Type:text/html; charset=UTF-8n\n";
		$headers .= "From: $titulo <$email_remetente>\n"; // remetente
		$headers .= "Return-Path: $email_remetente\n"; // return-path
		$headers .= "Reply-To: $email_contato\n"; // Endereço (devidamente validado) que o seu usuário informou no contato

		$feedback = (mail("$email", "$assunto", "$template", $headers, "-f$email_remetente")) ? true : false;

		return $feedback;
	}

	public function Enviar($dados, $template) {
		$email = $dados['email'];
		$email_contato = $dados['email'];
		$email_remetente = "sistema@grupofassil.com.br";
		$titulo = $dados['titulo'];
		$assunto = $dados['assunto'];

		$headers = "MIME-Version: 1.1\n";
		$headers .= "Content-Type:text/html; charset=UTF-8n\n";
		$headers .= "From: $titulo <$email_remetente>\n"; // remetente
		$headers .= "Return-Path: $email_remetente\n"; // return-path
		$headers .= "Reply-To: $email_contato\n"; // Endereço (devidamente validado) que o seu usuário informou no contato

		$feedback = (mail("$email", "$assunto", "$template", $headers, "-f$email_remetente")) ? true : false;

		return $feedback;
	}
}
?>
