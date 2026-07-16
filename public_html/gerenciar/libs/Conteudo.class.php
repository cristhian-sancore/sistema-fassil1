<?php
include("Resize.class.php");

class Conteudo extends Metodos{
	function Conteudo($Conn){
		$this->Conn = $Conn;
    }

	function Lista($dd){
        if($dd['b']){
            $busca = "AND (t1.Conteudo_Titulo LIKE '%{$dd['b']}%')";
            $buscaPage = "&b={$dd['b']}";
        }

        $params = null;

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

        $total = $this->Conn->execReader("SELECT count(t1.Conteudo_ID) FROM conteudo as t1 WHERE 1=1 $busca", $params,4);

        $params[] = $ipg;
        $params[] = $fpg;
        $dados = $this->Conn->execReader("SELECT
		                                    t1.Conteudo_ID,
		                                    t1.Conteudo_Titulo,
		                                    t1.Conteudo_Descricao,
		                                    t1.Conteudo_Texto,
		                                    t1.Conteudo_Status,
		                                    t1.Conteudo_DataInicio,
		                                    t1.Conteudo_DataFim
		                                FROM
		                                    conteudo as t1
		                                WHERE
		                                    1=1
		                                    $busca
		                                ORDER BY t1.Conteudo_ID DESC
		                                LIMIT ?,?", $params, 2);

        foreach($dados as $key => $d){
            $dados[$key]['categorias'] = $this->Conn->execReader("SELECT
                                                          t1.Conteudo_Categoria_ID,
                                                          t1.Categoria_ID,
                                                          t2.Categoria_Titulo
                                                        FROM
                                                          conteudo_categorias as t1
                                                          LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_ID
                                                        WHERE
                                                          t1.Conteudo_ID=?", array($d['Conteudo_ID']), 2);
        }

        /*PAGINAÇÃO*/
        //$paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $busca, $buscaPage, "conteudo");
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage, "conteudo");


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
		                                    LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_Pai_ID
		                                  WHERE
		                                  t1.Categoria_Pai_ID!=0", null, 2);
        foreach ($dados as $i => $dd) {
            $dados[$i]['Qtd'] = $this->Conn->execReader("SELECT count(Categoria_ID) FROM categorias WHERE Categoria_Pai_ID=?", array($dd['Categoria_ID']), 4);
        }

		return $dados;
	}

	function Dados($ID){
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Conteudo_ID,
		                                    t1.Conteudo_Titulo,
		                                    t1.Conteudo_Descricao,
		                                    t1.Conteudo_Texto,
		                                    t1.Conteudo_Status,
		                                    date_format(t1.Conteudo_DataInicio,'%d/%m/%Y') as Conteudo_DataInicio,
		                                    date_format(t1.Conteudo_DataFim,'%d/%m/%Y') as Conteudo_DataFim,
		                                    t2.Midia_Caminho as Imagem_Destaque
		                                FROM
		                                    conteudo as t1
		                                    LEFT JOIN conteudo_midias as t2 on (t2.Conteudo_ID=t1.Conteudo_ID AND t2.Midia_Destaque=1)
		                                WHERE
		                                    t1.Conteudo_ID=?", array($ID), 3);

        $dados['imagens'] = $this->Conn->execReader("SELECT
                                                      Midia_ID,
                                                      Midia_Caminho,
                                                      Midia_Legenda,
                                                      Midia_Destaque,
                                                      Midia_Tipo
                                                    FROM
                                                      conteudo_midias
                                                    WHERE
                                                      Conteudo_ID=?", array($ID), 2);

        $dados['categorias'] = $this->Conn->execReader("SELECT
                                                          Conteudo_Categoria_ID,
                                                          Categoria_ID
                                                        FROM
                                                          conteudo_categorias
                                                        WHERE
                                                          Conteudo_ID=?", array($ID), 2);

        $dados['links'] = $this->Conn->execReader("SELECT
                                                          Link_ID,
                                                          Link_Caminho,
                                                          Link_Tipo
                                                        FROM
                                                          conteudo_links
                                                        WHERE
                                                          Conteudo_ID=?", array($ID), 2);

        return $dados;
	}

	function Grava($dd){
		$status = $this->verifica_status($dd['status']);
		$tituloSlug = $this->titulo_slug($dd['$titulo']);

		$params = array($dd['titulo'], $tituloSlug, $dd['descricao'], $dd['conteudo'], $this->dataDB($dd['data_inicio']), $this->dataDB($dd['data_fim']), $status);
		$ID = $this->Conn->execWrite("INSERT INTO conteudo SET
										Conteudo_Titulo=?,
										Conteudo_TituloSlug=?,
										Conteudo_Descricao=?,
										Conteudo_Texto=?,
										Conteudo_DataInicio=?,
										Conteudo_DataFim=?,
										Conteudo_Status=?", $params);

        foreach($dd['categoria'] as $dd){
            $this->Conn->execWrite("INSERT INTO conteudo_categorias SET
                                        Conteudo_ID=?,
                                        Categoria_ID=?", array($ID, $dd));
        }

		return $ID;
	}

	function Edita($dd){
		$status = $this->verifica_status($dd['status']);
		$tituloSlug = $this->verifica_slug('Conteudo_Titulo', 'conteudo', $dd['titulo'], $dd['Conteudo_ID']);

		$params = array($dd['titulo'], $tituloSlug, $dd['descricao'], $dd['conteudo'], $this->dataDB($dd['data_inicio']), $this->dataDB($dd['data_fim']), $status, $dd['Conteudo_ID']);
        $this->Conn->execWrite("UPDATE conteudo SET
                                    Conteudo_Titulo=?,
                                    Conteudo_TituloSlug=?,
                                    Conteudo_Descricao=?,
                                    Conteudo_Texto=?,
                                    Conteudo_DataInicio=?,
                                    Conteudo_DataFim=?,
                                    Conteudo_Status=?
                                WHERE
                                    Conteudo_ID=?", $params);

        $this->Conn->execWrite("DELETE FROM conteudo_categorias WHERE Conteudo_ID=?", array($dd['Conteudo_ID']));
        foreach($dd['categoria'] as $cat){
            $this->Conn->execWrite("INSERT INTO conteudo_categorias SET
                                        Conteudo_ID=?,
                                        Categoria_ID=?", array($dd['Conteudo_ID'], $cat));
        }

        return $dd['Conteudo_ID'];
    }

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM conteudo WHERE Conteudo_ID=?", array($ID));
	}

    function Destaque($ID){
        $dMidia = $this->Conn->execReader("SELECT Conteudo_ID, Midia_Tipo FROM conteudo_midias WHERE Midia_ID=?", array($ID), 3);

        if($dMidia['Midia_Tipo'] == 'img'){
            $this->Conn->execWrite("UPDATE conteudo_midias SET Midia_Destaque=0 WHERE Conteudo_ID=?",array($dMidia['Conteudo_ID']));

            $this->Conn->execWrite("UPDATE conteudo_midias SET
                                        Midia_Destaque=1
                                    WHERE
                                        Midia_ID=?", array($ID));

            return true;
        }
    }

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Conteudo_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$this->Conn->execWrite("UPDATE conteudo SET Conteudo_Status=? WHERE Conteudo_ID=?", array($status, $ID));

		return $status;
	}

    function DeletaMidia($ID){
        $midia = $this->Conn->execReader("SELECT Midia_Caminho FROM conteudo_midias WHERE Midia_ID=?", array($ID), 4);

        /* deleta arquivo do servidor */
        @unlink("../../midias/".$midia);

        //DELETA BANCO
        $this->Conn->execWrite("DELETE FROM conteudo_midias WHERE Midia_ID=?", array($ID));
    }

    function Upload($File, $dd) {
        $subextensao = explode('.', $File['Filedata']['name']);
        $quant = count($subextensao);
        $extensao = strtolower($subextensao[$quant - 1]);

        $img = array("jpg","jpeg","png");
        $doc = array("doc","docx","xls","pdf");
        $permitidos = array_merge($img, $doc);;
        if(in_array($extensao, $permitidos)){
            if(in_array($extensao, $img)){
                $tipo = "img";
            }else{
                $tipo = "doc";
            }

            $pasta = date("d-m-Y");
            $diretorio = "../../midias/".$pasta;

            if (!file_exists($diretorio)){
                mkdir($diretorio, 0777);
            }

            $midia = "c_".$_REQUEST['metadado']."_".substr(md5(uniqid(rand(0, 1000000))), 0, 16).".".$extensao;

            move_uploaded_file($File['Filedata']['tmp_name'], $diretorio.'/'.$midia);

            //Redimensiona imagem
            /*$resizeObj = new Resize("$diretorio/$midia");
            $resizeObj -> resizeImage(800, 600, 'crop');
            $resizeObj -> saveImage("$diretorio/$midia", 100);*/

            $destaque = 0;
            if ($_SESSION['incremento_uploadify'] == 0) {
                $destaque = 1;
            }

            $ID = $this->Conn->execWrite("INSERT INTO conteudo_midias SET
                                            Conteudo_ID=?,
                                            Midia_Caminho=?,
                                            Midia_Tipo=?,
                                            Midia_Destaque=?", array($dd['id'], $pasta . '/' . $midia, $tipo, $destaque));

            $_SESSION['incremento_uploadify']++;

            return array($ID, $destaque, $pasta . '/' . $midia, $tipo);
        }else{
            return "erro";
        }
    }

    function GravaLink($dd){
        $link = str_replace("http://", "", $dd['link']);

        $params = array($dd['Conteudo_ID'], $link, $dd['link_titulo'], $dd['link_tipo']);
        $ID = $this->Conn->execWrite("INSERT INTO conteudo_links SET
                                        Conteudo_ID=?,
										Link_Caminho=?,
										Link_Legenda=?,
										Link_Tipo=?", $params);

        return $ID;
    }

    function DeletaLink($ID){
        $this->Conn->execWrite("DELETE FROM conteudo_links WHERE Link_ID=?", array($ID));
    }

    function DadosMidia($ID){
        return $this->Conn->execReader("SELECT
                                          Midia_ID,
                                          Midia_Caminho,
                                          Midia_Legenda,
                                          Midia_Link,
                                          Midia_Destaque,
                                          Midia_Tipo
                                        FROM
                                          conteudo_midias
                                        WHERE
                                          Midia_ID=?", array($ID), 3);
    }

    function EditaMidia($dd){
        $link = str_replace("http://", "", $dd['link']);
        $link = str_replace("https://", "", $dd['link']);

        $params = array($dd['legenda'], $link, $dd['Midia_ID']);
        $ID = $this->Conn->execWrite("UPDATE conteudo_midias SET
                                        Midia_Legenda=?,
										Midia_Link=?
									  WHERE
									    Midia_ID=?", $params);

        return $ID;
    }
}