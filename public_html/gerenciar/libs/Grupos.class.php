<?php
class Grupos extends Metodos{
	function Grupos($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Grupo_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Grupo_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Grupo_ID) FROM os_grupos as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Grupo_ID,
		                                    t1.Grupo_Nome,
		                                    t1.Grupo_Background,
		                                    t1.Grupo_Color,
		                                    t1.Grupo_Status
		                                FROM
		                                    os_grupos as t1
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Grupo_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Grupo_ID,
		                                    t1.Grupo_Nome,
		                                    t1.Grupo_Background,
		                                    t1.Grupo_Color,
		                                    t1.Grupo_Status
		                                FROM
		                                    os_grupos as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Grupo_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['nome'],
			$dd['background'],
			$dd['color']
		);

		return $this->Conn->execWrite("INSERT INTO os_grupos SET
								Empresa_ID=?,
								Grupo_Nome=?,
								Grupo_Background=?,
								Grupo_Color=?,
								Grupo_Status=1,
								Grupo_LogInclusao=now()", $params);
	}

	function Edita($dd, $Grupo_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
			$dd['nome'],
			$dd['background'],
			$dd['color'],
			$Grupo_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE os_grupos SET
								Grupo_Nome=?,
								Grupo_Background=?,
								Grupo_Color=?
							  WHERE
								Grupo_ID=? AND Empresa_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM os_grupos WHERE Empresa_ID=? AND Grupo_ID=?", $params);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Grupo_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE os_grupos SET Grupo_Status=? WHERE Empresa_ID=? AND Grupo_ID=?", $params);

		return $status;
	}
}