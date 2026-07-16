<?php
class Tipos extends Metodos{
	function Tipos($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Tipo_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Tipo_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Tipo_ID) FROM os_tipos as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Tipo_ID,
		                                    t1.Tipo_Nome,
											t1.Tipo_Status
		                                FROM
		                                    os_tipos as t1
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Tipo_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Tipo_ID,
		                                    t1.Tipo_Nome,
											t1.Tipo_Status
		                                FROM
		                                    os_tipos as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Tipo_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['nome']
		);

		return $this->Conn->execWrite("INSERT INTO os_tipos SET
								Empresa_ID=?,
								Tipo_Nome=?,
								Tipo_Status=1,
								Tipo_LogInclusao=now()", $params);
	}

	function Edita($dd, $Tipo_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
			$dd['nome'],
			$Tipo_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE os_tipos SET
								Tipo_Nome=?
							  WHERE
								Tipo_ID=? AND Empresa_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM os_tipos WHERE Empresa_ID=? AND Tipo_ID=?", $params);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Tipo_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE os_tipos SET Tipo_Status=? WHERE Empresa_ID=? AND Tipo_ID=?", $params);

		return $status;
	}
}