<?php
class Clientes extends Metodos{
	function Clientes($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Cliente_Nome LIKE ? OR t1.Cliente_Email LIKE ?)";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Cliente_Status=?";
			$params[] = $s;
		}

        if($dd['a']){
            $a = 1;
            if($dd['a']==2){
                $a = 0;
            }

            $busca .= " AND t1.Cliente_Admin=?";
            $params[] = $a;
        }

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Cliente_ID) FROM clientes as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Cliente_ID,
		                                    t1.Cidade_ID,
		                                    t1.Estado_ID,
		                                    t1.Cliente_Nome,
		                                    t1.Cliente_Email,
		                                    t1.Cliente_Departamento,
		                                    t1.Cliente_Cargo,
		                                    t1.Cliente_Login,
		                                    t1.Cliente_Contato1,
		                                    t1.Cliente_Contato2,
		                                    t1.Cliente_Contato3,
		                                    t1.Cliente_Status,
		                                    date_format(t1.Cliente_LogInclusao, '%d/%m/%Y às %H:%i') as Cliente_LogInclusao,
		                                	t2.Cidade_Nome,
		                                	t3.Estado_UF,
		                                	t4.CE_Nome
		                                FROM
		                                    clientes as t1
		                                    LEFT JOIN cidades as t2 on (t2.Cidade_ID=t1.Cidade_ID)
		                                    LEFT JOIN estados as t3 on (t3.Estado_ID=t1.Estado_ID)
		                                    LEFT JOIN cidade_empresas as t4 on (t4.CE_ID=t1.CE_ID)
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Cliente_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.CE_Nome,
		                                    t3.Cidade_Nome
		                                FROM
		                                    clientes as t1
		                                    LEFT JOIN cidade_empresas as t2 ON (t2.CE_ID=t1.CE_ID)
		                                    LEFT JOIN cidades as t3 ON (t3.Cidade_ID=t1.Cidade_ID)
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Cliente_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['cidade'],
			$dd['estado'],
			$dd['empresa'],
			$dd['nome'],
			$dd['email'],
			$dd['departamento'],
			$dd['cargo'],
			$dd['login'],
			$dd['senha'],
			$dd['contato1'],
			$dd['contato2'],
			$dd['contato3'],
			$dd['admin']
		);
		return $this->Conn->execWrite("INSERT INTO clientes SET
								Empresa_ID=?,
								Cidade_ID=?,
								Estado_ID=?,
								CE_ID=?,
								Cliente_Nome=?,
								Cliente_Email=?,
								Cliente_Departamento=?,
								Cliente_Cargo=?,
								Cliente_Login=?,
								Cliente_Senha=sha1(?),
								Cliente_Contato1=?,
								Cliente_Contato2=?,
								Cliente_Contato3=?,
								Cliente_Admin=?,
								Cliente_Status=1,
								Cliente_LogInclusao=now()", $params);
	}

	function Edita($dd, $Cliente_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
			$dd['cidade'],
			$dd['estado'],
			$dd['empresa'],
			$dd['nome'],
			$dd['email'],
			$dd['departamento'],
			$dd['cargo'],
			$dd['login'],
			$dd['contato1'],
			$dd['contato2'],
			$dd['contato3'],
            $dd['admin']
		);

		if($dd['senha']) {
			$query = ", Cliente_Senha=?";
			$params[] = sha1($dd['senha']);
		}

		$params[] = $Cliente_ID;
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$this->Conn->execWrite("UPDATE clientes SET
								Cidade_ID=?,
								Estado_ID=?,
								CE_ID=?,
								Cliente_Nome=?,
								Cliente_Email=?,
								Cliente_Departamento=?,
								Cliente_Cargo=?,
								Cliente_Login=?,
								Cliente_Contato1=?,
								Cliente_Contato2=?,
								Cliente_Contato3=?,
                                Cliente_Admin=?,
								Cliente_Status=1
								$query
							WHERE 
								Cliente_ID=? AND Empresa_ID=?", $params);

		return true;
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM clientes WHERE Empresa_ID=? AND Cliente_ID=?", $params);
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Cliente_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE clientes SET Cliente_Status=?  WHERE Empresa_ID=? AND Cliente_ID=?", $params);

		return $status;
	}

	public function Cidades() {
		return $this->Conn->execReader("SELECT Cidade_ID, Cidade_Nome FROM cidades WHERE Cidade_tatus=1", null, 2);
	}

	function ListaAutoComplete($Busca){
		$params = array($_SESSION['_user']['Empresa_ID'], "%$Busca%");
		return $this->Conn->execReader("SELECT
		                                    t1.Cliente_ID,
		                                    t1.Cidade_ID,
		                                    t1.Estado_ID,
		                                    t1.Cliente_Nome,
		                                    t1.Cliente_Email,
		                                    t1.Cliente_Departamento,
		                                    t1.Cliente_Cargo,
		                                    t1.Cliente_Login,
		                                    t1.Cliente_Contato1,
		                                    t1.Cliente_Contato2,
		                                    t1.Cliente_Contato3,
		                                    t1.Cliente_Status,
		                                    date_format(t1.Cliente_LogInclusao, '%d/%m/%Y às %H:%i') as Cliente_LogInclusao,
		                                	t2.Cidade_Nome,
		                                	t3.Estado_UF,
		                                	t4.CE_Nome
		                                FROM
		                                    clientes as t1
		                                    LEFT JOIN cidades as t2 on (t2.Cidade_ID=t1.Cidade_ID)
		                                    LEFT JOIN estados as t3 on (t3.Estado_ID=t1.Estado_ID)
		                                    LEFT JOIN cidade_empresas as t4 on (t4.CE_ID=t1.CE_ID)
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Cliente_Status=1 AND 
		                                    t1.Cliente_Nome LIKE ?
		                                ORDER BY Cliente_Nome", $params, 2);
	}

	function ValidaLogin($login){
		$params = array($login);
		$dd = $this->Conn->execReader("SELECT
										count(*)
									FROM
										clientes
									WHERE
										Cliente_Login=?", $params, 4);

		if($dd){
			return true;
		}
	}

	function ValidaEmail($email){
		$params = array($email);
		$dd = $this->Conn->execReader("SELECT
										count(*)
									FROM
										clientes
									WHERE
										Cliente_Email=?", $params, 4);

		if($dd){
			return true;
		}
	}
}