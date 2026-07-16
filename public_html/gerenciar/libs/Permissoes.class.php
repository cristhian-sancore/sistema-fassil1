<?php
class Permissoes extends Metodos{
	function Permissoes($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Permissao_nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Permissao_Status=?";
			$params[] = $s;
		}

		$maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Permissao_ID) FROM permissoes as t1 WHERE 1=1 $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Permissao_ID,
		                                    t1.Empresa_ID,
		                                    t1.Permissao_Nome,
		                                    t1.Permissao_Modulos,
		                                    t1.Permissao_Status,
		                                    t2.Empresa_Nome
		                                FROM
		                                    permissoes as t1
		                                    LEFT JOIN empresas as t2 on (t2.Empresa_ID=t1.Empresa_ID)
		                                WHERE
		                                    1=1
		                                    $busca
		                                ORDER BY Permissao_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Permissao_ID,
		                                    t1.Empresa_ID,
		                                    t1.Permissao_Nome,
											t1.Permissao_Modulos,
		                                    t1.Permissao_Status
		                                FROM
		                                    permissoes as t1
		                                WHERE
		                                    t1.Permissao_ID=?", $params, 3);

		$dd['mod'] = explode('*', $dd['Permissao_Modulos']);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$modulos = null;
		foreach ($dd['per'] as $i => $d){
			$modulos .= "$i*";
		}

		$params = array(
			$dd['empresa'],
			$dd['nome'],
			$modulos
		);

		return $this->Conn->execWrite("INSERT INTO permissoes SET
								Empresa_ID=?,
								Permissao_Nome=?,
								Permissao_Modulos=?,
								Permissao_Status=1", $params);
	}

	function Edita($dd, $Permissao_ID){
		$this->VerificaToken($dd['token']);

		$modulos = null;
		foreach ($dd['per'] as $i => $d){
			$modulos .= "$i*";
		}

		$params = array(
			$dd['empresa'],
			$dd['nome'],
			$modulos,
			$Permissao_ID
		);

		$this->Conn->execWrite("UPDATE permissoes SET
								Empresa_ID=?,
								Permissao_Nome=?,
								Permissao_Modulos=?
							  WHERE
								Permissao_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$params = array($ID);
		$this->Conn->execWrite("DELETE FROM permissoes WHERE Permissao_ID=?", $params);
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Permissao_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE permissoes SET Permissao_Status=? WHERE Permissao_ID=?", $params);

		return $status;
	}

	function ListaPermissoes(){
		//$params = array($_SESSION['_user']['Empresa_ID']);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Permissao_ID,
		                                    t1.Permissao_Nome,
											t1.Permissao_Modulos,
		                                    t1.Permissao_Status
		                                FROM
		                                    permissoes as t1/*
		                                WHERE
		                                	t1.Empresa_ID=?*/", null, 2);

		return $dd;
	}
}