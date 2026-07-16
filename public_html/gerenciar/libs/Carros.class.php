<?php
class Carros extends Metodos{
	function Carros($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Auto_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Auto_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Auto_ID) FROM automoveis as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Auto_ID,
		                                    t1.Auto_Marca,
		                                    t1.Auto_Modelo,
		                                    t1.Auto_Nome,
		                                    t1.Auto_Placa,
		                                    t1.Auto_KM,
		                                    t1.Auto_Status
		                                FROM
		                                    automoveis as t1
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Auto_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Auto_ID,
		                                    t1.Auto_Marca,
		                                    t1.Auto_Modelo,
		                                    t1.Auto_Nome,
		                                    t1.Auto_Placa,
		                                    t1.Auto_KM,
		                                    t1.Auto_Status
		                                FROM
		                                    automoveis as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Auto_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['marca'],
			$dd['modelo'],
			$dd['nome'],
			$dd['placa'],
			$dd['km']
		);

		return $this->Conn->execWrite("INSERT INTO automoveis SET
								Empresa_ID=?,
								Auto_Marca=?,
								Auto_Modelo=?,
								Auto_Nome=?,
								Auto_Placa=?,
								Auto_KM=?,
								Auto_Status=1", $params);
	}

	function Edita($dd, $Auto_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
		    $dd['marca'],
			$dd['modelo'],
			$dd['nome'],
			$dd['placa'],
			$dd['km'],
			$Auto_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE automoveis SET
		                        Auto_Marca=?,
								Auto_Modelo=?,
								Auto_Nome=?,
								Auto_Placa=?,
								Auto_KM=?
							  WHERE
								Auto_ID=? AND Empresa_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM automoveis WHERE Empresa_ID=? AND Auto_ID=?", $params);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Auto_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE automoveis SET Auto_Status=? WHERE Empresa_ID=? AND Auto_ID=?", $params);

		return $status;
	}
}