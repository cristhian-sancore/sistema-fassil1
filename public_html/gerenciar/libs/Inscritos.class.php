<?php
class Inscritos extends Metodos{
	function Inscritos($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
		if($dd['c']){
            $busca .= "AND (t1.Inscricao_ID=?)";
			$params[] = $dd['c'];

            $buscaPage = "&c={$dd['c']}";
        }
		if($dd['n']){
            $busca .= "AND (t1.Inscricao_Nome LIKE ?)";
			$params[] = "%{$dd['n']}%";

            $buscaPage = "&n={$dd['n']}";
        }
		
        if($dd['b']){
            $busca .= "AND (t1.Inscricao_CursoID=?)";
			$params[] = $dd['b'];

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND (t1.Inscricao_Status=?)";
			$params[] = $s;
		}
		
		if($dd['e']){
			$e = 0;
			
			if($dd['e']==1){
				$e = 'Prefeitura Municipal';
			}elseif($dd['e']==2){
			 $e = 'Câmara Municipal';
			}elseif($dd['e']==3){
			 $e = 'Previdência Municipal';
			}elseif($dd['e']==4){
			 $e = 'Fundação, Consórcio e demais entidades';
			}

			$busca .= " AND (t1.Inscricao_Entidade=?)";
			$params[] = $e;
		}
		
		if($dd['cid']){
            $busca .= "AND (t3.Cidade_ID=?)";
			$params[] = $dd['cid'];

            $buscaPage = "&cid={$dd['cid']}";
        }

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Inscricao_ID) FROM cursos_inscricoes as t1 left join cursos as t2 on (t2.Curso_ID=t1.Inscricao_CursoID)
		                                    left join cidades as t3 on (t3.Cidade_ID=t1.Inscricao_Cidade)
		                                    left join cursos_status as t4 on (t4.Status_ID=t1.Inscricao_Status)
		                                    WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.*,
		                                    t3.Cidade_ID,
		                                    t3.Cidade_Nome,
		                                    t3.Cidade_UF,
		                                    t4.Status_ID,
		                                    t4.Status_Nome
		                                FROM
		                                    cursos_inscricoes as t1
		                                    left join cursos as t2 on (t2.Curso_ID=t1.Inscricao_CursoID)
		                                    left join cidades as t3 on (t3.Cidade_ID=t1.Inscricao_Cidade)
		                                    left join cursos_status as t4 on (t4.Status_ID=t1.Inscricao_Status)
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Inscricao_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array(
		    $_SESSION['_user']['Empresa_ID'],
		    $ID
		    );
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.*,
		                                    t3.Cidade_ID,
		                                    t3.Cidade_Nome,
		                                    t3.Cidade_UF,
		                                    t4.Status_ID,
		                                    t4.Status_Nome
		                                FROM
		                                    cursos_inscricoes as t1
		                                    left join cursos as t2 on (t2.Curso_ID=t1.Inscricao_CursoID)
		                                    left join cidades as t3 on (t3.Cidade_ID=t1.Inscricao_Cidade)
		                                    left join cursos_status as t4 on (t4.Status_ID=t1.Inscricao_Status)
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Inscricao_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);
        
		$params = array(
		    $dd['curso'],
			$dd['firstname'],
			$dd['email'],
			$dd['phone'],
			$dd['city'],
			$dd['entity'],
			$dd['setor'],
			$dd['cargo'],
			$dd['autenticacao'],
			$dd['status']
		);

		return $this->Conn->execWrite("INSERT INTO cursos_inscricoes SET
		                        Empresa_ID=1,
		                        Inscricao_CursoID=?,
								Inscricao_Nome=?,
								Inscricao_Email=?,
								Inscricao_Contato=?,
								Inscricao_Cidade=?,
								Inscricao_Entidade=?,
								Inscricao_Setor=?,
								Inscricao_Cargo=?,
								Inscricao_CodAutenticacao=?,
								Inscricao_Status=?,
								Inscricao_DataInclusao=now()", $params);
	}

	function Edita($dd, $Inscricao_ID){
		$this->VerificaToken($dd['token']);

		$params = array(
		    $dd['curso'],
			$dd['firstname'],
			$dd['email'],
			$dd['phone'],
			$dd['city'],
			$dd['entity'],
			$dd['setor'],
			$dd['cargo'],
			$dd['autenticacao'],
			$dd['status'],
			$Inscricao_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE cursos_inscricoes SET
		                        Inscricao_CursoID=?,
								Inscricao_Nome=?,
								Inscricao_Email=?,
								Inscricao_Contato=?,
								Inscricao_Cidade=?,
								Inscricao_Entidade=?,
								Inscricao_Setor=?,
								Inscricao_Cargo=?,
								Inscricao_CodAutenticacao=?,
								Inscricao_Status=?
							  WHERE
								Inscricao_ID=? AND Empresa_ID=?", $params);
		return true;
	}
	
	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM cursos_inscricoes WHERE Empresa_ID=? AND Inscricao_ID=?", $params);
	}
	
	public function Cursos(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cursos as t1
		                                WHERE
		                                	1=1
		                                ORDER BY t1.Curso_Nome", null, 2);
	}
	
	public function Cidades(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cidades as t1
		                                WHERE
		                                	t1.Cidade_UF='MT'
		                                ORDER BY t1.Cidade_Nome", null, 2);
	}
	
	public function Status_Inscricao(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cursos_status as t1", null, 2);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Inscricao_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE cursos_inscricoes SET
		                            Inscricao_Status=?
		                        WHERE
		                            Empresa_ID=? AND Inscricao_ID=?", $params);

		return $status;
	}
}