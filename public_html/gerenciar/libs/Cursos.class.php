<?php
class Cursos extends Metodos{
	function Cursos($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Curso_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Curso_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Curso_ID) FROM cursos as t1 WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    date_format(t1.Curso_DataInicial,'%d/%m/%Y') as Curso_DataInicial,
		                                    date_format(t1.Curso_DataFinal,'%d/%m/%Y') as Curso_DataFinal
		                                FROM
		                                    cursos as t1
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Curso_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($id){
		$params = array($_SESSION['_user']['Empresa_ID'], $id);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    date_format(t1.Curso_DataInicial,'%d/%m/%Y') as Curso_DataInicial,
		                                    date_format(t1.Curso_DataFinal,'%d/%m/%Y') as Curso_DataFinal,
		                                    t2.Anexo_Caminho
		                                FROM
		                                    cursos as t1
		                                    left join cursos_anexos as t2 on (t1.Curso_ID=t2.Curso_ID)
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Curso_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd, $midias){
		$this->VerificaToken($dd['token']);
		
		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['nome'],
			$dd['tipo'],
			$dd['background'],
			$dd['color'],
			$dd['nome_orador'],
			$dd['conteudo'],
			$this->dataDB($dd['data_inicio']),
			$this->dataDB($dd['data_fim'])
		);

		$ID = $this->Conn->execWrite("INSERT INTO cursos SET
								Empresa_ID=?,
								Curso_Nome=?,
								Curso_Tipo=?,
								Curso_Background=?,
								Curso_Color=?,
								Curso_NomeOrador=?,
								Curso_Conteudo=?,
								Curso_DataInicial=?,
								Curso_DataFinal=?,
								Curso_Status=1,
								Curso_LogInclusao=now()", $params);
		
		$this->Upload($midias, $ID);
		
		return $ID;
		
	}
	
	
	function Edita($dd, $midias, $ID){
		$this->VerificaToken($dd['token']);

		$params = array(
			$dd['nome'],
			$dd['tipo'],
			$dd['background'],
			$dd['color'],
			$dd['nome_orador'],
			$dd['conteudo'],
			$this->dataDB($dd['data_inicio']),
			$this->dataDB($dd['data_fim']),
			$ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE cursos SET
								Curso_Nome=?,
								Curso_Tipo=?,
								Curso_Background=?,
								Curso_Color=?,
								Curso_NomeOrador=?,
								Curso_Conteudo=?,
								Curso_DataInicial=?,
								Curso_DataFinal=?
							  WHERE
								Curso_ID=? AND Empresa_ID=?", $params);
		
		$this->Upload($midias, $ID);

	}
	
	function Upload($Arquivos, $ID){
        foreach ($Arquivos['name'] as $i => $Arq){
            if($Arq) {
                $subextensao = explode('.', $Arq);
                $quant = count($subextensao);
                $extensao = strtolower($subextensao[$quant - 1]);

                $diretorio = "../../midias/cursos";
                if (!file_exists($diretorio)) {
                    mkdir($diretorio, 0777);
                }

                $nome = "orador_".$ID."_".substr(md5(uniqid(time())), 0, 16) . "." . $extensao;
                move_uploaded_file($Arquivos['tmp_name'][$i], $diretorio . "/" . $nome);
                
                $this->Conn->execWrite("UPDATE cursos SET
									Curso_Imagem=?
								WHERE
									Curso_ID=?", [$diretorio . "/" . $nome, $ID]);
            }
        }
    }
	
	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM cursos WHERE Empresa_ID=? AND Curso_ID=?", $params);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Curso_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE cursos SET Curso_Status=? WHERE Empresa_ID=? AND Curso_ID=?", $params);

		return $status;
	}
}