<?php
class Usuarios extends Metodos{
	function Usuarios($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Usuario_Email LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Usuario_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Usuario_ID) FROM usuarios as t1 WHERE 1=1 $busca", $params,4);

		if($_SESSION['_user']['Usuario_Tipo'] != 'G'){
			$adm = " AND t1.Empresa_ID=? AND t1.Usuario_ID > 1";
			$params[] = $_SESSION['_user']['Empresa_ID'];
		}

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Usuario_ID,
											t1.Empresa_ID,
											t1.Permissao_ID,
		                                    t1.Usuario_Nome,
		                                    t1.Usuario_Login,
		                                    t1.Usuario_Email,
		                                    t1.Usuario_Status,
		                                    if(t1.Usuario_Tipo='G','Administrador','Usuário') as Usuario_Tipo,
		                                    date_format(t1.Usuario_LogInclusao, '%d/%m/%Y às %H:%i') as Usuario_LogInclusao,
		                                    t2.Empresa_Nome,
		                                    t3.Permissao_Nome
		                                FROM
		                                    usuarios as t1
		                                    LEFT JOIN empresas as t2 on (t2.Empresa_ID=t1.Empresa_ID)
		                                    LEFT JOIN permissoes as t3 on (t3.Permissao_ID=t1.Permissao_ID)
		                                WHERE
		                                    1=1
		                                    $adm
		                                    $busca
		                                ORDER BY Usuario_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Usuario_ID,
											t1.Empresa_ID,
											t1.Permissao_ID,
		                                    t1.Usuario_Nome,
		                                    t1.Usuario_Cargo,
		                                    t1.Usuario_Login,
		                                    t1.Usuario_Email,
		                                    t1.Usuario_Status,
		                                    t1.Usuario_Tipo,
		                                    t2.Permissao_Nome
		                                FROM
		                                    usuarios as t1
		                                    LEFT JOIN permissoes as t2 on (t2.Permissao_ID=t1.Permissao_ID)
		                                WHERE
		                                    t1.Usuario_ID=?", array($ID), 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		if(!$dd['empresa']){
			$dd['empresa'] = $_SESSION['_user']['Empresa_ID'];
		}

		$params = array(
			$dd['empresa'],
			$dd['permissao'],
			$dd['nome'],
			$dd['cargo'],
			$dd['login'],
			$dd['email'],
			$dd['senha']
		);

		return $this->Conn->execWrite("INSERT INTO usuarios SET
								Empresa_ID=?,
								Permissao_ID=?,
								Usuario_Nome=?,
								Usuario_Cargo=?,
								Usuario_Login=?,
								Usuario_Email=?,
								Usuario_Senha=sha1(?),
								Usuario_LastAcesso=now(),
								Usuario_Status=1,
								Usuario_Tipo='U',
								Usuario_LogInclusao=now()", $params);
	}

	function Edita($dd, $Usuario_ID){
		$this->VerificaToken($dd['token']);

		$query = null;

		if(!$dd['empresa']){
			$dd['empresa'] = $_SESSION['_user']['Empresa_ID'];
		}

		$params = array(
			$dd['empresa'],
			$dd['permissao'],
			$dd['nome'],
			$dd['cargo'],
			$dd['login'],
			$dd['email']
		);

		if($dd['senha']) {
			$query = ", Usuario_Senha=?";
			$params[] = sha1($dd['senha']);
		}

		$params[] = $Usuario_ID;
		$this->Conn->execWrite("UPDATE usuarios SET
								Empresa_ID=?,
								Permissao_ID=?,
								Usuario_Nome=?,
								Usuario_Cargo=?,
								Usuario_Login=?,
								Usuario_Email=?
								$query
							  WHERE
								Usuario_ID=?", $params);
		return true;
	}

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM usuarios WHERE Usuario_ID=?", array($ID));
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Usuario_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE usuarios SET Usuario_Status=?  WHERE Usuario_ID=?", $params);

		return $status;
	}

	public function Cidades() {
		return $this->Conn->execReader("SELECT id, nome FROM cidades WHERE status=1", null, 2);
	}

	function ListaAutoComplete($Busca){
		$params = array($_SESSION['_user']['Empresa_ID'], "%$Busca%", "%$Busca%");
		return $this->Conn->execReader("SELECT
		                                    t1.Usuario_ID,
		                                    t1.Usuario_Nome,
		                                    t1.Usuario_Login,
		                                    t1.Usuario_Email,
		                                    t1.Usuario_Status,
		                                    if(t1.Usuario_Tipo='G','Administrador','Usuário') as Usuario_Tipo,
		                                    date_format(t1.Usuario_LogInclusao, '%d/%m/%Y às %H:%i') as Usuario_LogInclusao
		                                FROM
		                                    usuarios as t1
		                                WHERE
		                                    t1.Usuario_Status=1 AND
		                                    t1.Empresa_ID=? AND
		                                    t1.Usuario_Nome LIKE ? OR 
		                                    t1.Usuario_Login LIKE ?
		                                ORDER BY Usuario_Nome", $params, 2);

	}
}