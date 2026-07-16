<?php
class Conteudo extends Metodos{
	function Conteudo($Conn){
		$this->Conn = $Conn;
	}

    function Lista($dd){
        $maxLinks = 3;
        $fpg = $dd['fpg'];
        $ipg = ($dd['pg'] * $fpg) - $fpg;

        $params = array($dd['Categoria_ID']);
        $total = $this->Conn->execReader("SELECT count(*) FROM
		                                    conteudo as t1
		                                    LEFT JOIN conteudo_midias as t2 ON (t2.Conteudo_ID=t1.Conteudo_ID AND Midia_Destaque=1)
		                                WHERE
		                                    (SELECT COUNT(c.Categoria_ID) FROM conteudo_categorias as c WHERE c.Conteudo_ID=t1.Conteudo_ID AND c.Categoria_ID=?) > 0 AND 
		                                    Conteudo_Status=1", $params,4);

        $params = array($dd['Categoria_ID'], $ipg, $fpg);
        $dados = $this->Conn->execReader("SELECT
		                                    t1.Conteudo_ID,
		                                    t1.Conteudo_Titulo,
		                                    t1.Conteudo_TituloSlug,
		                                    t1.Conteudo_Descricao,
		                                    t1.Conteudo_Texto,
		                                    t1.Conteudo_Status,
		                                    t1.Conteudo_DataInicio,
		                                    t1.Conteudo_DataFim,
		                                    t2.Midia_Caminho as Conteudo_Imagem
		                                FROM
		                                    conteudo as t1
		                                    LEFT JOIN conteudo_midias as t2 ON (t2.Conteudo_ID=t1.Conteudo_ID AND Midia_Destaque=1)
		                                WHERE
		                                    (SELECT COUNT(c.Categoria_ID) FROM conteudo_categorias as c WHERE c.Conteudo_ID=t1.Conteudo_ID AND c.Categoria_ID=?) > 0 AND 
		                                    Conteudo_Status=1
		                                ORDER BY t1.Conteudo_ID DESC
		                                LIMIT ?,?", $params, 2);

        foreach($dados as $key => $d){
            $dados[$key]['c'] = $this->Conn->execReader("SELECT
                                                          t1.Conteudo_Categoria_ID,
                                                          t1.Categoria_ID,
                                                          t2.Categoria_Titulo
                                                        FROM
                                                          conteudo_categorias as t1
                                                          LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_ID
                                                        WHERE
                                                          t1.Conteudo_ID=?", array($d['Conteudo_ID']), 2);

            $dados[$key]['m'] = $this->ConteudoMidias($d['Conteudo_ID'], 'img');
        }

        /*PAGINAÇÃO*/
        $paginador = $this->PaginadorBusca($dd['pg'], $dd['fpg'], $dd['link'], $maxLinks, $total, '');

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
    }

    function Dados($Conteudo_ID, $Slug=null){
        $condicao = "t1.Conteudo_ID=?";
        $params = array($Conteudo_ID);

        if($Slug){
            $condicao = "t1.Conteudo_TituloSlug=?";
            $params = array($Slug);
        }

        $dados = $this->Conn->execReader("SELECT
                                        t1.*,
                                        date_format(t1.Conteudo_DataInicio, '%d/%m/%Y') as DataInicio,
                                        date_format(t1.Conteudo_DataFim, '%d/%m/%Y') as DataFim,
                                        t2.Midia_Caminho as Imagem_Destaque
                                    FROM
                                        conteudo as t1
                                        LEFT JOIN conteudo_midias as t2 on (t2.Conteudo_ID=t1.Conteudo_ID AND t2.Midia_Destaque=1)
                                    WHERE
                                        t1.Conteudo_Status=1 AND 
                                        $condicao", $params, 3);

        $dados['c'] = $this->Conn->execReader("SELECT
                                                      t1.Conteudo_Categoria_ID,
                                                      t1.Categoria_ID,
                                                      t2.Categoria_Titulo
                                                    FROM
                                                      conteudo_categorias as t1
                                                      LEFT JOIN categorias as t2 on t2.Categoria_ID=t1.Categoria_ID
                                                    WHERE
                                                      t1.Conteudo_ID=?", array($dados['Conteudo_ID']), 2);

        $dados['m'] = $this->ConteudoMidias($dados['Conteudo_ID'], 'img');

        return $dados;
    }

	function Slide($Categoria_ID, $limite){
        $params = array($Categoria_ID, $limite);
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Conteudo_ID,
		                                    t1.Conteudo_Titulo,
		                                    t1.Conteudo_TituloSlug,
		                                    t1.Conteudo_Descricao,
		                                    t1.Conteudo_Texto,
		                                    date_format(t1.Conteudo_DataInicio,'%d') as data_d,
		                                    date_format(t1.Conteudo_DataInicio,'%m') as data_m,
		                                    t2.Midia_Caminho,
		                                    t4.Link_Caminho
		                                FROM
		                                    conteudo as t1
		                                    LEFT JOIN conteudo_midias as t2 on t2.Conteudo_ID=t1.Conteudo_ID and t2.Midia_Destaque=1
		                                    LEFT JOIN conteudo_categorias as t3 on t3.Conteudo_ID=t1.Conteudo_ID
		                                    LEFT JOIN conteudo_links as t4 on t4.Conteudo_ID=t1.Conteudo_ID
		                                WHERE
		                                    t1.Conteudo_Status=1 AND
		                                    t3.Categoria_ID=?
		                                ORDER BY RAND()
		                                LIMIT ?", $params, 2);
        foreach($dados as $i=>$dd){
            $dados[$i]['categoria'] = $this->Conn->execReader("SELECT
		                                    t2.Categoria_ID,
		                                    t3.Categoria_Titulo as Titulo,
		                                    t3.Categoria_TituloSlug as TituloSlug,
		                                    t4.Categoria_Titulo as TituloPai,
		                                    t4.Categoria_TituloSlug as TituloSlugPai
		                                FROM
		                                    conteudo_categorias as t2
		                                    LEFT JOIN categorias as t3 on t3.Categoria_ID=t2.Categoria_ID
		                                    LEFT JOIN categorias as t4 on t4.Categoria_ID=t3.Categoria_Pai_ID
		                                WHERE
		                                    t2.Conteudo_ID=?
		                                ORDER BY t2.Categoria_ID DESC
		                                LIMIT 1", array($dd['Conteudo_ID']), 3);

            $dados[$i]['midias'] = $this->Conn->execReader("SELECT
                                                      Midia_ID,
                                                      Midia_Caminho,
                                                      Midia_Legenda,
                                                      Midia_Link,
                                                      Midia_Destaque,
                                                      Midia_Tipo
                                                    FROM
                                                      conteudo_midias
                                                    WHERE
                                                      Conteudo_ID=? AND
                                                      Midia_Tipo='img'
                                                      ORDER BY Midia_ID DESC", array($dd['Conteudo_ID']), 2);

			foreach($dados[$i]['midias'] as $j=>$dm){
				$dMidias[] = array('Midia_Legenda' => $dm['Midia_Legenda'],
								  'Midia_Link' => $dm['Midia_Link'],
								  'Midia_Caminho' => $dm['Midia_Caminho']);
			}
        }

		$dados['slide'] = $dMidias;

        return $dados;
    }

    function ConteudoMidias($Conteudo_ID, $tipo){
        $dados = $this->Conn->execReader("SELECT
                                        t1.Midia_Caminho,
                                        t1.Midia_Legenda,
                                        t1.Midia_Link,
                                        t1.Midia_Destaque
                                    FROM
                                        conteudo_midias as t1
                                    WHERE
                                        t1.Conteudo_ID=? AND
                                        t1.Midia_Tipo=?", array($Conteudo_ID, $tipo), 2);
        return $dados;
    }

    function Entidades(){
        $dd = $this->Conn->execReader("SELECT
		                                    Entidade_ID,
		                                    Entidade_Nome,
		                                    Entidade_Imagem,
		                                    Entidade_Status
		                                FROM
		                                    entidades
		                                WHERE
		                                    Entidade_Status=1
		                                ORDER BY Entidade_Nome", null, 2);

        foreach ($dd as $i => $d) {
            $dd[$i]['Entidade_Imagem'] = 'gerenciar/'.explode('../../', $d['Entidade_Imagem'])[1];
            $dd[$i]['orgaos'] = $this->Conn->execReader("SELECT * FROM entidade_orgaos WHERE Entidade_ID=?", array($d['Entidade_ID']), 2);
        }

        return $dd;
    }
}