<?php
class Email extends Metodos {
	function Email($Conn){
		$this->Conn = $Conn;
	}

	public function Cadastro($dados, $template) {
		$email = $dados['Cliente_Email'];
		$email_contato = $dados['Cliente_Email'];
		$email_remetente = "sistema@grupofassil.com.br";
		$titulo = utf8_decode('Grupo Fassil');
		$assunto = utf8_decode('Cadastro - ').$dados['Cliente_Nome'];

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
		$titulo = utf8_decode('Grupo Fassil');
		$assunto = utf8_decode('Ticket - ').$dados['OS_Protocolo'];

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
