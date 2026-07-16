<?php
class Estatisticas extends Metodos{
	function Estatisticas($Conn){
		$this->Conn = $Conn;
	}

	function Lista($Campo, $Tabela, $Condicao=null, $CondicaoHoje=null){
		if($Condicao){
			$Condicao = "AND ".$Condicao;
		}

		if($CondicaoHoje){
			$CondicaoHoje = "AND date_format($CondicaoHoje,'%Y-%m-%d')=curdate()";
		}

		$params = array(
			$Campo,
			$_SESSION['_user']['Empresa_ID']
		);
		$dd = $this->Conn->execReader("SELECT
		                                    count(?) as Total
		                                FROM
		                                    $Tabela
		                                WHERE
		                                	Empresa_ID=?
		                                    $Condicao
		                                    $CondicaoHoje", $params, 3);

		return $dd;
	}
}