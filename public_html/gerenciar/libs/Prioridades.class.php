<?php
class Prioridades extends Metodos{
	function Prioridades($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Prioridade_Titulo LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Prioridade_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Prioridade_ID) FROM os_prioridades as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Prioridade_ID,
		                                    t1.Prioridade_Titulo,
		                                    t1.Prioridade_Background,
		                                    t1.Prioridade_Color,
		                                    t1.Prioridade_Status
		                                FROM
		                                    os_prioridades as t1
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Prioridade_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Prioridade_ID,
		                                    t1.Prioridade_Titulo,
		                                    t1.Prioridade_Background,
		                                    t1.Prioridade_Color,
		                                    t1.Prioridade_Status
		                                FROM
		                                    os_prioridades as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Prioridade_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['titulo'],
			$dd['background'],
			$dd['color']
		);

		return $this->Conn->execWrite("INSERT INTO os_prioridades SET
								Empresa_ID=?,
								Prioridade_Titulo=?,
								Prioridade_Background=?,
								Prioridade_Color=?,
								Prioridade_Status=1,
								Prioridade_LogInclusao=now()", $params);
	}

	function Edita($dd, $Prioridade_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
			$dd['titulo'],
			$dd['background'],
			$dd['color'],
			$Prioridade_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE os_prioridades SET
								Prioridade_Titulo=?,
								Prioridade_Background=?,
								Prioridade_Color=?
							  WHERE
								Prioridade_ID=? AND Empresa_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM os_prioridades WHERE Empresa_ID=? AND Prioridade_ID=?", $params);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Prioridade_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE os_prioridades SET Prioridade_Status=? WHERE Empresa_ID=? AND Prioridade_ID=?", $params);

		return $status;
	}
}