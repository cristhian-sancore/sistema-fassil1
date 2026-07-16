<?php
include_once('OrdemServicos.class.php');
include_once('Clientes.class.php');

class Relatorios extends Metodos{
	function Relatorios($Conn){
		$this->Conn = $Conn;
		$this->OS = new OrdemServicos($Conn);
		$this->Clientes = new Clientes($Conn);
	}

	function Verifica_Relatorio($dd){
		$params = array($dd['OS_ID']);
		$Relatorio_ID = $this->Conn->execReader("SELECT
		                                    Relatorio_ID
		                                FROM
		                                    os_relatorios
		                                WHERE
		                                    OS_ID=?", $params, 4);

		if(!$Relatorio_ID){
			$this->Grava($dd);
		}else{
			$this->Edita($dd, $Relatorio_ID);
		}
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);
		$mensagem = $this->verifica_status($dd['mensagem']);

		$params = array(
			$dd['OS_ID'],
			$dd['introducao'],
			$dd['descricao'],
			$dd['resultado'],
			$dd['orientacao'],
			$dd['observacao'],
			$dd['conclusao'],
			$dd['timbre'],
			$mensagem
		);
		$Relatorio_ID = $this->Conn->execWrite("INSERT INTO os_relatorios SET
													OS_ID=?,
													Relatorio_Introducao=?,
													Relatorio_Descricao=?,
													Relatorio_Resultado=?,
													Relatorio_Orientacao=?,
													Relatorio_Observacao=?,
													Relatorio_Conclusao=?,
													Relatorio_Timbre=?,
													Relatorio_Mensagem=?", $params);

		$this->GravaAssinatura($dd, $Relatorio_ID);
	}

	function GravaAssinatura($dd, $Relatorio_ID){
		if($dd['as_nome'][0]) {
			for ($i = 0; $i < count($dd['as_nome']); $i++) {
				$params = array($Relatorio_ID, $dd['as_nome'][$i], $dd['as_cargo'][$i]);
				$this->Conn->execWrite("INSERT INTO os_relatorio_assinaturas SET
										Relatorio_ID=?,
										Assinatura_Nome=?,
										Assinatura_Cargo=?", $params);
			}
		}
	}

	function Edita($dd, $Relatorio_ID){
		$this->VerificaToken($dd['token']);
		$mensagem = $this->verifica_status($dd['mensagem']);

		$params = array(
			$dd['introducao'],
			$dd['descricao'],
			$dd['resultado'],
			$dd['orientacao'],
			$dd['observacao'],
			$dd['conclusao'],
			$dd['timbre'],
			$mensagem,
			$dd['OS_ID']
		);

		$this->Conn->execWrite("UPDATE os_relatorios SET
									Relatorio_Introducao=?,
									Relatorio_Descricao=?,
									Relatorio_Resultado=?,
									Relatorio_Orientacao=?,
									Relatorio_Observacao=?,
									Relatorio_Conclusao=?,
									Relatorio_Timbre=?,
									Relatorio_Mensagem=?
								WHERE
									OS_ID=?", $params);

		$this->GravaAssinatura($dd, $Relatorio_ID);
	}

	function Dados($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    *
		                                FROM
		                                    os_relatorios
		                                WHERE
		                                    OS_ID=?", $params, 3);

		$params = array($ID);
		$dd['fe'] = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.*
		                                FROM
		                                    os_fechamentos as t1
		                                    LEFT JOIN clientes as t2 on (t2.Cliente_ID=t1.Cliente_ID)
		                                WHERE
		                                    t1.OS_ID=?", $params, 2);

		return $dd;
	}

	function Assinatura_Gravar($dd){
		$codigo = strtoupper(substr(sha1(rand(0,1000)),0,96));

		$params = array(
			$dd['os'],
			$dd['id'],
			$codigo
		);
		$ID = $this->Conn->execWrite("INSERT INTO os_fechamentos SET
									OS_ID=?,
									Cliente_ID=?,
									Fechamento_Status=0,
									Fechamento_Codigo=?", $params);

		/*LOG OS*/
		$d = $this->OS->Dados($dd['os']);
		$dc = $this->Clientes->Dados($dd['id']);
		$this->OS->OS_LOG(array(
			'OS_ID' => $dd['os'],
			'Usuario_Logado' => $_SESSION['_user']['Usuario_ID'],
			'Usuario_ID' => $d['Usuario_ID'],
			'Comentario' => "Inseriu assinatura: $ID | {$dc['Cliente_ID']}-{$dc['Cliente_Nome']}"
		));
		/*FIM LOG OS*/
	}

	function Assinatura_Excluir($ID){
		$params = array($ID);
		$df = $this->Conn->execReader("SELECT * FROM os_fechamentos WHERE Fechamento_ID=?", $params, 3);

		$params = array($ID);
		$this->Conn->execWrite("DELETE FROM os_fechamentos WHERE Fechamento_ID=?", $params);

		$d = $this->OS->Dados($df['OS_ID']);
		$this->OS->Verifica_Fechamento_OS($df['OS_ID']);

		/*LOG OS*/
		$dc = $this->Clientes->Dados($df['Cliente_ID']);
		$this->OS->OS_LOG(array(
			'OS_ID' => $df['OS_ID'],
			'Usuario_Logado' => $_SESSION['_user']['Usuario_ID'],
			'Usuario_ID' => $d['Usuario_ID'],
			'Comentario' => "Excluiu assinatura: $ID | {$dc['Cliente_ID']}-{$dc['Cliente_Nome']}"
		));
		/*FIM LOG OS*/
	}
}