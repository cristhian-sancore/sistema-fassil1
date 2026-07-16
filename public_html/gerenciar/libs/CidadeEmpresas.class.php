<?php
class CidadeEmpresas extends Metodos{
	function CidadeEmpresas($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.CE_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.CE_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.CE_ID) FROM cidade_empresas as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.*
		                                FROM
		                                    cidade_empresas as t1
		                                    LEFT JOIN cidades as t2 ON t2.Cidade_ID=t1.Cidade_ID
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY CE_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cidade_empresas as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.CE_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['cidade'],
			$dd['empresa']
		);

		return $this->Conn->execWrite("INSERT INTO cidade_empresas SET
								Empresa_ID=?,
								Cidade_ID=?,
								CE_Nome=?,
								CE_Status=1", $params);
	}

	function Edita($dd, $CE_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
			$dd['cidade'],
			$dd['empresa'],
			$CE_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE cidade_empresas SET
								Cidade_ID=?,
								CE_Nome=?
							  WHERE
								CE_ID=? AND Empresa_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM os_prioridades WHERE Empresa_ID=? AND Prioridade_ID=?", $params);
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['CE_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE cidade_empresas SET CE_Status=? WHERE Empresa_ID=? AND CE_ID=?", $params);

		return $status;
	}
}