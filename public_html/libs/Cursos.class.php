<?php
class Cursos extends Metodos{
	function Cursos($Conn){
		$this->Conn = $Conn;
	}
	
	function Lista($dd){
        $dados = $this->Conn->execReader("SELECT
                                        t1.*,
                                        date_format(t1.Curso_DataInicial,'%d/%m/%Y') as Curso_DataInicial,
		                                date_format(t1.Curso_DataFinal,'%d/%m/%Y') as Curso_DataFinal,
		                                t2.*,
		                                t3.*
                                    FROM
                                        cursos as t1
                                        left join cursos_inscricoes as t2 on (t1.Curso_ID=t2.Inscricao_CursoID)
                                        left join cursos_anexos as t3 on (t1.Curso_ID=t3.Curso_ID)
                                        WHERE
                                        1=1
                                        group by t1.Curso_ID
                                        order by t1.CURSO_ID DESC LIMIT 5", null, 2);

        return $dados;
	    
	}

    function Dados($id){
        $params = array($id);
        $busca = null;
        if($dd['b']){
            $busca .= "AND t1.Curso_ID=?";
			$params[] = $dd['b'];

            $buscaPage = "&b={$dd['b']}";
        }
        $dados = $this->Conn->execReader("SELECT
                                        t1.*,
                                        date_format(t1.Curso_DataInicial,'%d/%m/%Y') as Curso_DataInicial,
		                                date_format(t1.Curso_DataFinal,'%d/%m/%Y') as Curso_DataFinal
                                    FROM
                                        cursos as t1
                                        WHERE
                                        t1.Curso_ID=?
                                        $busca LIMIT 1", $params, 3);

        return $dados;
    }
    
    function Grava($dd, $Curso_id){
		$this->VerificaToken($dd['token']);
        
		$params = array(
		    $Curso_id,
			$dd['firstname'],
			$dd['email'],
			$dd['phone'],
			$dd['city'],
			$dd['entity'],
			$dd['setor'],
			$dd['cargo']
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
								Inscricao_Status=0,
								Inscricao_DataInclusao=now()", $params);
	}
	
	function ListaAutenticacao($dd){
	    
        $busca = null;
        if($dd['b']){
            $busca .= "AND t1.Inscricao_CodAutenticacao=?";
			$params[] = $dd['b'];

            $buscaPage = "&b={$dd['b']}";
        }
        
        $dados = $this->Conn->execReader("SELECT
                                        t1.*,
                                        date_format(t2.Curso_DataInicial,'%d/%m/%Y') as Curso_DataInicial,
		                                date_format(t2.Curso_DataFinal,'%d/%m/%Y') as Curso_DataFinal,
                                        t2.Curso_Nome,
                                        t3.Cidade_Nome,
                                        t3.Cidade_UF
                                    FROM
                                        cursos_inscricoes as t1
                                        left join cursos as t2 on (t2.Curso_ID=t1.Inscricao_CursoID)
                                        left join cidades as t3 on (t1.Inscricao_Cidade=t3.Cidade_ID)
                                        WHERE
                                        1=1
                                        $busca", $params, 2);

        return $dados;
        
	    
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
	
	public function Cursos_Status(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cursos_status as t1
		                                WHERE
		                                    t1.Empresa_ID=1
		                                ORDER BY t1.Status_Nome", 2);
	}

}