<?php
class Generico extends Metodos{
	function Generico($Conn){
		$this->Conn = $Conn;
	}

    function Logar($dd){
		$params = array($dd['c_login'], $dd['c_senha']);
		$dados = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.CE_Nome
		                                FROM
		                                    clientes as t1
		                                    LEFT JOIN cidade_empresas as t2 ON t2.CE_ID=t1.CE_ID
		                                WHERE
		                                	t1.Cliente_Login=? AND
		                                    t1.Cliente_Senha=sha1(?) AND 
		                                    t1.Cliente_Status=1", $params, 3);
		/*echo "<pre>";
		print_r($dados);
		die;*/
		if($dados){
			$_SESSION['xxx']['login']['Cliente_ID'] = $dados['Cliente_ID'];
			$_SESSION['xxx']['login']['Empresa_ID'] = $dados['Empresa_ID'];
			$_SESSION['xxx']['login']['Cliente_Nome'] = $dados['Cliente_Nome'];
			$_SESSION['xxx']['login']['Cliente_Email'] = $dados['Cliente_Email'];

			/* Seta um cookie para encerrar quando usuario fechar a pagina*/
			setcookie("xxx", $dados['Cliente_ID']."-".$dados['Cliente_Nome'], 0);
		}
	}

	function ListaOS($dd, $noPg=false){
		$busca = null;
		$buscaPage = null;
		if($dd['b']){
			$busca .= "AND (t1.OS_Protocolo LIKE ? OR t6.Grupo_Nome LIKE ? OR t8.Tipo_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";

			$buscaPage = "_{$dd['b']}";
		}

		if($dd['Cliente_ID']){
			$busca .= " AND t1.Cliente_ID=?";
			$params[] = $dd['Cliente_ID'];
		}

		$total = $this->Conn->execReader("SELECT count(t1.OS_ID) FROM os as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t1.Cliente_ID)
		                                    LEFT JOIN cidades as t4 on (t4.Cidade_ID=t3.Cidade_ID)
		                                    LEFT JOIN os_prioridades as t5 on (t5.Prioridade_ID=t1.Prioridade_ID)
		                                    LEFT JOIN os_grupos as t6 on (t6.Grupo_ID=t1.Grupo_ID)
		                                    LEFT JOIN os_status as t7 on (t7.Status_ID=t1.Status_ID)
											LEFT JOIN os_tipos as t8 on (t8.Tipo_ID=t1.Tipo_ID)
											LEFT JOIN cidade_empresas as t9 on (t9.CE_ID=t3.CE_ID) WHERE 1=1 $busca", $params,4);

		$limite = null;
		if(!$noPg) {
			$maxLinks = 3;
			$params[] = ($dd['pg'] * $dd['fpg']) - $dd['fpg'];
			$params[] = $dd['fpg'];
			$limite = "LIMIT ?,?";
		}
		$dados = $this->Conn->execReader("SELECT
		                                   	t1.OS_ID,
		                                   	t1.Usuario_ID,
											t1.Cliente_ID,
											t1.Prioridade_ID,
											t1.Grupo_ID,
											t1.Tipo_ID,
											t1.Status_ID,
											CONCAT(t1.OS_DataInicio, ' ', t1.OS_HorasInicio) as DataHoraInicio,
											date_format(t1.OS_DataInicio, '%d/%m/%Y') as OS_DataInicio,
											date_format(t1.OS_HorasInicio, '%H:%i') as OS_HorasInicio,
											t1.OS_Descricao,
											t1.OS_Protocolo,
											t1.OS_DataFechamento,
		                                    date_format(t1.OS_DataFechamento, '%d/%m/%Y às %H:%i') as DataFechamento,
		                                    date_format(t1.OS_LogInclusao, '%d/%m/%Y às %H:%i') as OS_LogInclusao,
		                                    t2.Usuario_Nome,
		                                    t3.Cliente_Nome,
		                                    t3.Cidade_ID,
		                                    t4.Cidade_Nome,
		                                    t5.Prioridade_Titulo,
		                                    t5.Prioridade_Background,
		                                    t5.Prioridade_Color,
		                                    t6.Grupo_Nome,
		                                    t6.Grupo_Background,
		                                    t6.Grupo_Color,
		                                    t7.Status_Nome,
		                                    t7.Status_Icon,
		                                    t7.Status_Color,
		                                    t8.Tipo_Nome,
		                                    t9.CE_Nome,
		                                    (SELECT count(Msg_ID) FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=0 AND Msg_Visualizada=0) as Msg_Total_Visualizada,
		                                    (SELECT count(Fechamento_ID) FROM os_fechamentos WHERE OS_ID=t1.OS_ID AND Cliente_ID=t1.Cliente_ID) as OS_Relatorio
		                                FROM
		                                    os as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t1.Cliente_ID)
		                                    LEFT JOIN cidades as t4 on (t4.Cidade_ID=t3.Cidade_ID)
		                                    LEFT JOIN os_prioridades as t5 on (t5.Prioridade_ID=t1.Prioridade_ID)
		                                    LEFT JOIN os_grupos as t6 on (t6.Grupo_ID=t1.Grupo_ID)
		                                    LEFT JOIN os_status as t7 on (t7.Status_ID=t1.Status_ID)
											LEFT JOIN os_tipos as t8 on (t8.Tipo_ID=t1.Tipo_ID)
											LEFT JOIN cidade_empresas as t9 on (t9.CE_ID=t3.CE_ID)
		                                WHERE
		                                	1=1
		                                    $busca
		                                ORDER BY OS_ID DESC
		                                $limite", $params, 2);

		foreach ($dados as $i => $d){
			if($d['OS_DataFechamento']){
				$dados[$i]['TempoOS'] = $this->CalculaDif($d['DataHoraInicio'], $d['OS_DataFechamento']);
			}
		}

		/*PAGINAÇÃO*/
		$paginador = $this->PaginadorBusca($dd['pg'], $dd['fpg'], $dd['link'], $maxLinks, $total, $buscaPage);

		return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function DadosOS_Confirmacao($protocolo, $confirmacao){
		$params = array($confirmacao, $protocolo);
		$dd =  $this->Conn->execReader("SELECT
										t1.OS_ID,
										t1.Cliente_ID as Cliente_ID_Fechamento,
										t1.Fechamento_Status,
										t2.*,
										t3.Cliente_Email,
										t5.Prioridade_Titulo,
										t5.Prioridade_Background,
										t5.Prioridade_Color,
										t6.Grupo_Nome,
										t7.Status_Nome,
										t8.Tipo_Nome
									FROM
										os_fechamentos as t1
										LEFT JOIN os as t2 on (t2.OS_ID=t1.OS_ID)
										LEFT JOIN clientes as t3 on t3.Cliente_ID=t1.Cliente_ID
										LEFT JOIN os_prioridades as t5 on (t5.Prioridade_ID=t2.Prioridade_ID)
										LEFT JOIN os_grupos as t6 on (t6.Grupo_ID=t2.Grupo_ID)
										LEFT JOIN os_status as t7 on (t7.Status_ID=t2.Status_ID)
										LEFT JOIN os_tipos as t8 on (t8.Tipo_ID=t2.Tipo_ID)
									WHERE
										t1.Fechamento_Codigo=? AND
										t2.OS_Protocolo=?", $params, 3);

		$params = array($dd['OS_ID']);
		$dd['f'] = $this->Conn->execReader("SELECT
										t1.*,
										if(t1.Fechamento_Status=1,'Confirmado','Aguardando Confirmação') as Status,
										t2.Cliente_Nome,
										t2.Cliente_Departamento,
										t2.Cliente_Cargo
									FROM
										os_fechamentos as t1
										LEFT JOIN clientes as t2 on t2.Cliente_ID=t1.Cliente_ID
									WHERE
										t1.OS_ID=?", $params, 2);

		return $dd;
	}

	function DadosOS($dd){
		if(!$this->VerificaOS($dd)){
			return false;
		}

		$params = array($dd['Protocolo']);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.OS_ID,
		                                   	t1.Usuario_ID,
											t1.Cliente_ID,
											t1.Prioridade_ID,
											t1.Grupo_ID,
											t1.Tipo_ID,
											t1.Status_ID,
											CONCAT(t1.OS_DataInicio, ' ', t1.OS_HorasInicio) as DataHoraInicio,
											date_format(t1.OS_DataInicio, '%d/%m/%Y') as OS_DataInicio,
											date_format(t1.OS_HorasInicio, '%H:%i') as OS_HorasInicio,
											t1.OS_Descricao,
											t1.OS_Protocolo,
											t1.OS_NotaUsuario,
											t1.OS_DataFechamento,
											date_format(t1.OS_DataFechamento, '%d/%m/%Y às %H:%i') as DataFechamento,
		                                    date_format(t1.OS_LogInclusao, '%d/%m/%Y às %H:%i') as OS_LogInclusao,
		                                    t1.OS_Relatorio,
		                                    (if(t1.OS_Relatorio=1,'Não', if(t1.OS_Relatorio=2, 'Sim','...'))) as Relatorio,
		                                    t1.OS_Avaliacao,
		                                    t1.OS_AvaliacaoComentario,
		                                    t2.Usuario_Nome,
		                                    t2.Usuario_Cargo,
		                                    t3.Cliente_Nome,
		                                    t3.Cliente_Email,
		                                    t3.Cliente_Departamento,
		                                    t3.Cliente_Cargo,
		                                    t3.Cliente_Login,
		                                    t3.Cliente_Contato1,
		                                    t3.Cliente_Contato2,
		                                    t3.Cliente_Contato3,
		                                    t4.CE_Nome,
		                                    t5.Prioridade_Titulo,
		                                    t5.Prioridade_Background,
		                                    t5.Prioridade_Color,
		                                    t7.Cidade_Nome,
		                                    t8.Estado_Nome,
		                                    t8.Estado_UF,
		                                    t9.Grupo_Nome,
		                                    t10.Status_Nome,
		                                    t11.Tipo_Nome
		                                FROM
		                                    os as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t1.Cliente_ID)
		                                    LEFT JOIN cidade_empresas as t4 on (t4.CE_ID=t3.CE_ID)
		                                    LEFT JOIN os_prioridades as t5 on (t5.Prioridade_ID=t1.Prioridade_ID)
		                                    LEFT JOIN cidades as t7 on (t7.Cidade_ID=t3.Cidade_ID)
		                                    LEFT JOIN estados as t8 on (t8.Estado_ID=t3.Estado_ID)
											LEFT JOIN os_grupos as t9 on (t9.Grupo_ID=t1.Grupo_ID)
											LEFT JOIN os_status as t10 on (t10.Status_ID=t1.Status_ID)
											LEFT JOIN os_tipos as t11 on (t11.Tipo_ID=t1.Tipo_ID)
		                                WHERE
		                                    t1.OS_Protocolo=?", $params, 3);
		$dd['TempoOS'] = $this->CalculaDif($dd['DataHoraInicio'], $dd['OS_DataFechamento']);

		$params = array($dd['OS_ID']);
		$dd['a'] = $this->Conn->execReader("SELECT
										*
									FROM
										os_anexos
									WHERE
										OS_ID=?", $params, 2);

		$params = array($dd['OS_ID']);
		$dd['r'] = $this->Conn->execReader("SELECT
										*
									FROM
										os_relatorios
									WHERE
										OS_ID=?", $params, 3);

		$params = array($dd['OS_ID']);
		$dd['f'] = $this->Conn->execReader("SELECT
										t1.*,
										if(t1.Fechamento_Status=1,'Confirmado','Aguardando Confirmação') as Status,
										t2.Cliente_Nome,
										t2.Cliente_Departamento,
										t2.Cliente_Cargo
									FROM
										os_fechamentos as t1
										LEFT JOIN clientes as t2 on t2.Cliente_ID=t1.Cliente_ID
									WHERE
										t1.OS_ID=?", $params, 2);

		return $dd;
	}

	function ListaFechamentosOS($dd, $noPg=false){
		$busca = null;
		$buscaPage = null;
		if($dd['b']){
			$busca .= "AND (t2.OS_Protocolo LIKE ? OR t3.Cliente_Nome LIKE ? OR t9.Grupo_Nome LIKE ? OR t11.Tipo_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";
			$params[] = "%{$dd['b']}%";

			$buscaPage = "_{$dd['b']}";
		}

		if($dd['Cliente_ID']){
			$busca .= " AND t1.Cliente_ID=?";
			$params[] = $dd['Cliente_ID'];
		}

		$total = $this->Conn->execReader("SELECT count(t1.Fechamento_ID) FROM 
											os_fechamentos as t1
		                                    LEFT JOIN os as t2 on (t2.OS_ID=t1.OS_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t2.Cliente_ID)
		                                    LEFT JOIN cidade_empresas as t4 on (t4.CE_ID=t3.CE_ID)
		                                    LEFT JOIN cidades as t5 on (t5.Cidade_ID=t3.Cidade_ID)
		                                    LEFT JOIN estados as t6 on (t6.Estado_ID=t3.Estado_ID)
		                                    LEFT JOIN usuarios as t7 on (t7.Usuario_ID=t2.Usuario_ID)
											LEFT JOIN os_prioridades as t8 on (t8.Prioridade_ID=t2.Prioridade_ID)
											LEFT JOIN os_grupos as t9 on (t9.Grupo_ID=t2.Grupo_ID)
											LEFT JOIN os_status as t10 on (t10.Status_ID=t2.Status_ID)
											LEFT JOIN os_tipos as t11 on (t11.Tipo_ID=t2.Tipo_ID) WHERE 1=1 $busca", $params,4);

		$limite = null;
		if(!$noPg) {
			$maxLinks = 3;
			$params[] = ($dd['pg'] * $dd['fpg']) - $dd['fpg'];
			$params[] = $dd['fpg'];
			$limite = "LIMIT ?,?";
		}
		$dados = $this->Conn->execReader("SELECT
		                                   	t1.*,
		                                   	t2.Usuario_ID,
											t2.Cliente_ID,
											t2.Prioridade_ID,
											t2.Grupo_ID,
											t2.Tipo_ID,
											t2.Status_ID,
											date_format(t2.OS_DataInicio, '%d/%m/%Y') as OS_DataInicio,
											date_format(t2.OS_HorasInicio, '%H:%i') as OS_HorasInicio,
											t2.OS_Descricao,
											t2.OS_Protocolo,
											t2.OS_NotaUsuario,
											date_format(t2.OS_DataFechamento, '%d/%m/%Y às %H:%i') as DataFechamento,
		                                    date_format(t2.OS_LogInclusao, '%d/%m/%Y às %H:%i') as OS_LogInclusao,
		                                   	t3.Cliente_Nome,
		                                    t3.Cliente_Email,
		                                    t3.Cliente_Departamento,
		                                    t3.Cliente_Login,
		                                    t3.Cliente_Contato1,
		                                    t3.Cliente_Contato2,
		                                    t3.Cliente_Contato3,
		                                    t5.Cidade_Nome,
		                                    t6.Estado_Nome,
		                                    t6.Estado_UF,
		                                    t8.Prioridade_Titulo,
		                                    t8.Prioridade_Background,
		                                    t8.Prioridade_Color,
		                                    t9.Grupo_Nome,
		                                    t9.Grupo_Background,
		                                    t9.Grupo_Color,
		                                    t10.Status_Nome,
		                                    t10.Status_Icon,
		                                    t10.Status_Color,
		                                    t11.Tipo_Nome
		                                FROM
		                                    os_fechamentos as t1
		                                    LEFT JOIN os as t2 on (t2.OS_ID=t1.OS_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t2.Cliente_ID)
		                                    LEFT JOIN cidade_empresas as t4 on (t4.CE_ID=t3.CE_ID)
		                                    LEFT JOIN cidades as t5 on (t5.Cidade_ID=t3.Cidade_ID)
		                                    LEFT JOIN estados as t6 on (t6.Estado_ID=t3.Estado_ID)
		                                    LEFT JOIN usuarios as t7 on (t7.Usuario_ID=t2.Usuario_ID)
											LEFT JOIN os_prioridades as t8 on (t8.Prioridade_ID=t2.Prioridade_ID)
											LEFT JOIN os_grupos as t9 on (t9.Grupo_ID=t2.Grupo_ID)
											LEFT JOIN os_status as t10 on (t10.Status_ID=t2.Status_ID)
											LEFT JOIN os_tipos as t11 on (t11.Tipo_ID=t2.Tipo_ID)
		                                WHERE
		                                	1=1 AND 
		                                	t1.Fechamento_Status=0
		                                    $busca
		                                ORDER BY t1.Fechamento_ID DESC
		                                $limite", $params, 2);

		/*PAGINAÇÃO*/
		$paginador = $this->PaginadorBusca($dd['pg'], $dd['fpg'], $dd['link'], $maxLinks, $total, $buscaPage);

		return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function VerificaOS($dd){
		$params = array($dd['Protocolo'], $dd['Cliente_ID']);
		$dp = $this->Conn->execReader("SELECT
										t1.*
									FROM
										os as t1
									WHERE
										t1.OS_Protocolo=? AND
										t1.Cliente_ID=?", $params, 2);

		/*echo "<pre>";
		print_r($dp);
		die;*/
		if($dp){
			return true;
		}

		$params = array($dd['Protocolo'], $dd['Cliente_ID']);
		$df = $this->Conn->execReader("SELECT
										t1.*
									FROM
										os_fechamentos as t1
										LEFT JOIN os as t2 on (t2.OS_ID=t1.OS_ID)
									WHERE
										t1.Fechamento_Status=0 AND 
										t2.OS_Protocolo=? AND
										t1.Cliente_ID=? ", $params, 2);
		/*echo "<pre>";
		print_r($df);
		die;*/
		if($df){
			return true;
		}
	}

	function DadosClientes($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    t2.CE_Nome,
		                                    t3.Cidade_Nome
		                                FROM
		                                    clientes as t1
		                                    LEFT JOIN cidade_empresas as t2 ON (t2.CE_ID=t1.CE_ID)
		                                    LEFT JOIN cidades as t3 ON (t3.Cidade_ID=t1.Cidade_ID)
		                                WHERE
		                                    t1.Cliente_ID=?", $params, 3);

		return $dd;
	}

	function Estatisticas($tab, $cond){
		return $this->Conn->execReader("SELECT
		                                    count(*)
		                                FROM
		                                    $tab
		                                WHERE
		                                    $cond", null, 4);
	}
}