<?php
class Cidades extends Metodos{
	function Cidades($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		if($dd['b']){
			$busca = "AND (t1.Cidade_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

			$buscaPage = "&b={$dd['b']}";
		}

		$maxLinks = 3;
		$fpg = 20;
		$ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Cidade_ID) FROM cidades as t1 WHERE 1=1 $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Cidade_ID,
		                                    t1.Estado_ID,
		                                    t1.Cidade_UF,
		                                    t1.Cidade_Nome,
		                                    t1.Cidade_Status
		                                FROM
		                                    cidades as t1
		                                WHERE
		                                    1=1
		                                    $busca
		                                ORDER BY t1.Cidade_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
		$paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		return $this->Conn->execReader("SELECT
		                                    Cidade_ID,
		                                    Estado_ID,
		                                    Cidade_UF,
		                                    Cidade_Nome,
		                                    Cidade_Status,
		                                    Cidade_Latitude,
		                                    Cidade_Longitude
		                                FROM
		                                    cidades
		                                WHERE
		                                    Cidade_ID=?", array($ID), 3);
	}

	function Grava($dd){
		$status = $this->verifica_status($dd['status']);
		$tituloSlug = $this->titulo_slug($dd['titulo']);

		$params = array($dd['categoria_pai'], $dd['titulo'], $tituloSlug, $status);
		$ID = $this->Conn->execWrite("INSERT INTO categorias SET
										Categoria_Pai_ID=?,
										Categoria_Titulo=?,
										Categoria_TituloSlug=?,
										Categoria_Status=?", $params);

		return $ID;
	}

	function Edita($dd, $ID){
		$status = $this->verifica_status($dd['status']);

		$params = array($dd['titulo'], $dd['latitude'], $dd['longitude'], $status, $ID);
		$this->Conn->execWrite("UPDATE cidades SET
									Cidade_Nome=?,
									Cidade_Latitude=?,
									Cidade_Longitude=?,
									Cidade_Status=?
								WHERE
									Cidade_ID=?", $params);
	}

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM cidades WHERE Cidade_ID=?", array($ID));
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Cidade_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE cidades SET Cidade_Status=?  WHERE Cidade_ID=?", $params);

		return $status;
	}

	function CidadesEstados($Estado_ID){
		return $this->Conn->execReader("SELECT
		                                    Cidade_ID,
		                                    Estado_ID,
		                                    Cidade_Nome,
		                                    Cidade_UF,
		                                    Cidade_Status
		                                FROM
		                                    cidades
		                                WHERE
		                                	Cidade_Status=1 AND
		                                    Estado_ID=?", array($Estado_ID), 2);
	}

	function listaCidades(){
		return $this->Conn->execReader("SELECT
		                                    Cidade_ID,
		                                    Estado_ID,
		                                    Cidade_Nome,
		                                    Cidade_UF,
		                                    Cidade_Status
		                                FROM
		                                    cidades
		                                WHERE
		                                	Cidade_Status=1", null, 2);
	}

	function CidadesEmpresas($Cidade_ID){
		return $this->Conn->execReader("SELECT
		                                    CE_ID,
		                                    CE_Nome
		                                FROM
		                                    cidade_empresas
		                                WHERE
		                                    Cidade_ID=?", array($Cidade_ID), 2);
	}
}