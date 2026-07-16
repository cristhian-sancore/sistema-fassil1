<?php
class Categoria extends Metodos{
	function Categoria($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		if($dd['b']){
			$busca = "AND (t1.Usuario_Email LIKE ? OR t2.Pessoa_Nome LIKE ? OR t2.Pessoa_NomeFantasia LIKE ?)";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";

			$buscaPage = "&b={$dd['b']}";
		}

		$maxLinks = 3;
		$fpg = 20;
		$ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Categoria_ID) FROM categorias as t1 LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_Pai_ID WHERE 1=1 $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Categoria_ID,
		                                    t1.Categoria_Pai_ID,
		                                    t1.Categoria_Titulo,
		                                    t1.Categoria_Status,
		                                    t2.Categoria_Titulo as Categoria_TituloPai
		                                FROM
		                                    categorias as t1
		                                    LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_Pai_ID
		                                WHERE
		                                    1=1
		                                    $busca
		                                ORDER BY Categoria_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
		$paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

		return array("d" => $dados, "t" => $total, "paginador" => $paginador);

	}

	function ListaIE(){
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Categoria_ID,
		                                    t1.Categoria_Pai_ID,
		                                    t1.Categoria_Titulo,
		                                    t1.Categoria_Status,
		                                    t2.Categoria_Titulo as Categoria_TituloPai
		                                  FROM
		                                    categorias as t1
		                                    LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_Pai_ID", null, 2);
        foreach ($dados as $i => $dd) {
            $dados[$i]['Qtd'] = $this->Conn->execReader("SELECT count(Categoria_ID) FROM categorias WHERE Categoria_Pai_ID=?", array($dd['Categoria_ID']), 4);
        }

		return $dados;
	}

	function Dados($ID){
		return $this->Conn->execReader("SELECT
		                                    Categoria_ID,
		                                    Categoria_Pai_ID,
		                                    Categoria_Titulo,
		                                    Categoria_Status
		                                FROM
		                                    categorias
		                                WHERE
		                                    Categoria_ID=?", array($ID), 3);
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

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
		$this->VerificaToken($dd['token']);

		$status = $this->verifica_status($dd['status']);
		$tituloSlug = $this->verifica_slug('Categoria_Titulo', 'categorias', $dd['titulo'], $ID);

		$params = array($dd['categoria_pai'], $dd['titulo'], $tituloSlug, $status, $ID);
		$this->Conn->execWrite("UPDATE categorias SET
									Categoria_Pai_ID=?,
									Categoria_Titulo=?,
									Categoria_TituloSlug=?,
									Categoria_Status=?
								WHERE
									Categoria_ID=?", $params);
	}

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM categorias WHERE Categoria_ID=?", array($ID));
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Categoria_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$this->Conn->execWrite("UPDATE categorias SET Categoria_Status=? WHERE Categoria_ID=?", array($status, $ID));

		return $status;
	}
}