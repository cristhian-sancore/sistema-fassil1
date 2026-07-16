<?php
class Entidade extends Metodos{
	function Entidade($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		if($dd['b']){
			$busca = "AND (t2.Entidade_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

			$buscaPage = "&b={$dd['b']}";
		}

		$maxLinks = 3;
		$fpg = 20;
		$ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Entidade_ID) FROM entidades as t1 WHERE 1=1 $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Entidade_ID,
		                                    t1.Entidade_Nome,
		                                    t1.Entidade_Status
		                                FROM
		                                    entidades as t1
		                                WHERE
		                                    1=1
		                                    $busca
		                                ORDER BY Entidade_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
		$paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

		return array("d" => $dados, "t" => $total, "paginador" => $paginador);

	}

	function Dados($ID){
		$dd = $this->Conn->execReader("SELECT
		                                    Entidade_ID,
		                                    Entidade_Nome,
		                                    Entidade_Imagem,
		                                    Entidade_Status
		                                FROM
		                                    entidades
		                                WHERE
		                                    Entidade_ID=?", array($ID), 3);

		$dd['orgaos'] = $this->Conn->execReader("SELECT
		                                    *
		                                FROM
		                                    entidade_orgaos
		                                WHERE
		                                    Entidade_ID=?", array($ID), 2);

		return $dd;
	}

	function Grava($dd, $midias){
		$this->VerificaToken($dd['token']);

        $status = $this->verifica_status($dd['status']);

		$params = array($dd['nome'], $status);
		$ID = $this->Conn->execWrite("INSERT INTO entidades SET
										Entidade_Nome=?,
										Entidade_Status=?,
										Entidade_LogInclusao=now()", $params);

		$this->Upload($midias, $ID);

		if($dd['orgao']) {
            foreach ($dd['orgao'] as $i => $d) {
                $sistema = $this->verifica_status($dd['orgao_sis'][$i][0]);
                $consultoria = $this->verifica_status($dd['orgao_con'][$i][0]);

                $params = array($ID, $d[0], $dd['link'][$i][0], $sistema, $consultoria);
                $this->Conn->execWrite("INSERT INTO entidade_orgaos SET
										Entidade_ID=?,
										Orgao_Nome=?,
										Orgao_Link=?,
										Orgao_Sistema=?,
										Orgao_Consultoria=?,
										Orgao_LogInclusao=now()", $params);
            }
        }

		return $ID;
	}

	function Edita($dd, $midias, $ID){
		$this->VerificaToken($dd['token']);

		$status = $this->verifica_status($dd['status']);

		$params = array($dd['nome'], $status, $ID);
		$this->Conn->execWrite("UPDATE entidades SET
									Entidade_Nome=?,
									Entidade_Status=?
								WHERE
									Entidade_ID=?", $params);

        $this->Upload($midias, $ID);

        if($dd['orgao']) {
            foreach ($dd['orgao'] as $i => $d) {
                $sistema = $this->verifica_status($dd['orgao_sis'][$i][0]);
                $consultoria = $this->verifica_status($dd['orgao_con'][$i][0]);

                $params = array($ID, $d[0], $dd['link'][$i][0], $sistema, $consultoria);
                $this->Conn->execWrite("INSERT INTO entidade_orgaos SET
										Entidade_ID=?,
										Orgao_Nome=?,
										Orgao_Link=?,
										Orgao_Sistema=?,
										Orgao_Consultoria=?,
										Orgao_LogInclusao=now()", $params);
            }
        }
	}

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM entidades WHERE Entidade_ID=?", array($ID));
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Entidade_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$this->Conn->execWrite("UPDATE entidades SET Entidade_Status=? WHERE Entidade_ID=?", array($status, $ID));

		return $status;
	}

    function Upload($Arquivos, $ID){
        foreach ($Arquivos['name'] as $i => $Arq){
            if($Arq) {
                $subextensao = explode('.', $Arq);
                $quant = count($subextensao);
                $extensao = strtolower($subextensao[$quant - 1]);

                $diretorio = "../../midias/entidades";
                if (!file_exists($diretorio)) {
                    mkdir($diretorio, 0777);
                }

                $nome = substr(md5(uniqid(time())), 0, 16) . "." . $extensao;
                move_uploaded_file($Arquivos['tmp_name'][$i], $diretorio . "/" . $nome);

                $this->Conn->execWrite("UPDATE entidades SET
									Entidade_Imagem=?
								WHERE
									Entidade_ID=?", [$diretorio . "/" . $nome, $ID]);
            }
        }
    }

    function DelOrgao($ID){
        $this->Conn->execWrite("DELETE FROM entidade_orgaos WHERE Orgao_ID=?", array($ID));
    }
}