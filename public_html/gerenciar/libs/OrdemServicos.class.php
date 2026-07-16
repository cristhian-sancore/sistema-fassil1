<?php
class OrdemServicos extends Metodos{
	function OrdemServicos($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd, $noPg=false, $Cliente_ID=false)
    {
        $params[] = $_SESSION['_user']['Empresa_ID'];

        $order_by = "t1.OS_ID";
        $busca = null;
        $buscaPage = null;
        if ($dd['b']) {
            if (is_numeric($dd['b']) && strlen($dd['b']) < 12) {
                $busca .= "AND t1.OS_ID=?";
                $params[] = $dd['b'];
            } else {
                $busca .= "AND (t3.Cliente_Nome LIKE ? OR t1.OS_Protocolo LIKE ?)";
                $params[] = "%{$dd['b']}%";
                $params[] = "%{$dd['b']}%";
            }

            $buscaPage .= "&b={$dd['b']}";
        }

        if ($dd['s']) {
            $busca .= " AND t1.Status_ID=?";
            $params[] = $dd['s'];
            $buscaPage .= "&s={$dd['s']}";
        }

        if ($dd['t']) {
            $busca .= " AND t1.Tipo_ID=?";
            $params[] = $dd['t'];
            $buscaPage .= "&t={$dd['t']}";
        }

        if ($dd['g']) {
            $busca .= " AND t1.Grupo_ID=?";
            $params[] = $dd['g'];
            $buscaPage .= "&g={$dd['g']}";
        }

        if ($dd['p']) {
            $busca .= " AND t1.Prioridade_ID=?";
            $params[] = $dd['p'];
            $buscaPage .= "&p={$dd['p']}";
        }

        if ($dd['d1'] && !$dd['d2']) {
            $busca .= " AND t1.OS_DataInicio=?";
            $params[] = $this->dataDB($dd['d1']);
            $buscaPage .= "&d1={$dd['d1']}";
        } elseif ($dd['d1'] && $dd['d2']) {
            $busca .= " AND t1.OS_DataInicio >= ? AND t1.OS_DataInicio <= ?";
            $params[] = $this->dataDB($dd['d1']);
            $params[] = $this->dataDB($dd['d2']);
            $buscaPage .= "&d1={$dd['d1']}";
            $buscaPage .= "&d2={$dd['d2']}";
        }
        
        if ($dd['u']) {
            $busca .= " AND t1.Usuario_ID=?";
            $params[] = $dd['u'];
            $buscaPage .= "&u={$dd['u']}";
        }

        if ($dd['c']) {
            $busca .= " AND t3.Cidade_ID=?";
            $params[] = $dd['c'];
            $buscaPage .= "&c={$dd['c']}";
        }

        if ($dd['n']) {
            if ($dd['n'] == 1) {
                $busca .= " AND (SELECT count(Msg_ID) FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=1 AND Msg_Visualizada=0) > 0";
            } else {
                $order_by = "(SELECT Msg_LogInclusao FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=1 ORDER BY Msg_LogInclusao DESC LIMIT 1)";
            }
            $buscaPage .= "&n={$dd['n']}";
        }

        if ($Cliente_ID) {
            $busca .= " AND t1.Cidade_ID=?";
            $params[] = $Cliente_ID;
        }

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

        $total = $this->Conn->execReader("SELECT count(t1.OS_ID) FROM os as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t1.Cliente_ID)
		                                    LEFT JOIN os_prioridades as t5 on (t5.Prioridade_ID=t1.Prioridade_ID) WHERE t1.Empresa_ID=? $busca", $params, 4);

        $limite = null;
        if (!$noPg) {
            $params[] = $ipg;
            $params[] = $fpg;
            $limite = "LIMIT ?,?";
        }
        $usser = array(
            $_SESSION['_user']['Usuario_ID']
            );
        $os_aberta = $this->Conn->execReader("SELECT 
                                                count(Usuario_id) as Quantidade_OS_Aberta,
                                                DATEDIFF(CURDATE(), OS_DataInicio) as Quantidade_Dias
                                            FROM
                                            os 
                                            WHERE Usuario_id=? AND
                                            Status_ID=1", $usser, 4);
            
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
		                                    t1.OS_NotaUsuario,
		                                    t2.Usuario_Nome,
		                                    t3.Cliente_Nome,
		                                    t3.Cliente_Admin,
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
		                                    (SELECT count(Msg_ID) FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=1 AND Msg_Visualizada=0) as Msg_Total_Visualizada
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
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY $order_by DESC
		                                $limite", $params, 2);
        //print_r($dados);
        //die;
        foreach ($dados as $i => $d) {
            if ($d['OS_DataFechamento']) {
                $dados[$i]['TempoOS'] = $this->CalculaDif($d['DataHoraInicio'], $d['OS_DataFechamento']);
            }
        }

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        $ano = date('Y');
        $condicao = "YEAR(OS_DataInicio)=$ano";

        if ($dd['d1']) {
            $d1 =  new DateTime($this->dataDB($dd['d1']));
            $condicao = "MONTH(OS_DataInicio)=".$d1->format('m')." AND YEAR(OS_DataInicio)=".$d1->format('Y');
        }

        if ($dd['d2']) {
            $d2 = new DateTime($this->dataDB($dd['d2']));
            $condicao = "YEAR(OS_DataInicio)=".$d2->format('Y');
        }

        $usuarios = $this->Conn->execReader("SELECT 
                                                t1.Usuario_ID 
                                            FROM os as t1 
                                            LEFT JOIN usuarios as t2 on t1.Usuario_ID=t2.Usuario_ID
                                            WHERE 
                                                t1.Usuario_ID!=0 AND 
                                                t2.Usuario_Status=1 AND 
                                                (SELECT COUNT(OS_ID) FROM os WHERE Usuario_ID=t1.Usuario_ID) > 0 GROUP BY t1.Usuario_ID ORDER BY t1.Usuario_ID", null, 2);
        $meses = $this->Conn->execReader("SELECT MONTH(OS_DataInicio) as mes, YEAR(OS_DataInicio) as ano FROM os WHERE 1=1 AND $condicao GROUP BY MONTH(OS_DataInicio) ORDER BY YEAR(OS_DataInicio), MONTH(OS_DataInicio)", null, 2);
        $grafico = null;
        $grafico_linha = null;
        if ($meses){
            foreach ($meses as $i => $m) {
                $grafico[$i]['mes'] = $this->Mes($m['mes']) . "/" . $m['ano'];

                foreach ($usuarios as $j => $u) {
                    $params = array($u['Usuario_ID'], $m['mes'], $m['ano']);
                    $tickets = $this->Conn->execReader("SELECT COUNT(OS_ID) FROM os WHERE Usuario_ID=? AND MONTH(OS_DataInicio)=? AND YEAR(OS_DataInicio)=?", $params, 4);
                    $usuario = $this->Conn->execReader("SELECT Usuario_Nome FROM usuarios WHERE Usuario_ID=?", array($u['Usuario_ID']), 4);
                    $grafico[$i]['user'][] = ['nome' => $usuario, 'total' => $tickets];
                }
            }

            $linha = null;
            foreach ($grafico as $i => $g) {
                if ($i == 0) {
                    $linha[$i][] = 'Mês';

                    foreach ($g['user'] as $j => $u) {
                        $linha[$i][] = $u['nome'];
                    }
                }
            }

            $linh2 = null;
            foreach ($grafico as $i => $g) {
                $linha2[$i][] = $g['mes'];

                foreach ($g['user'] as $j => $u) {
                    $linha2[$i][] = $u['total'];
                }
            }

            $teste = array_merge($linha, $linha2);

            foreach ($teste as $i => $g) {
                if ($i == 0) {
                    $grafico_linha .= "['" . implode('\',\'', $g) . "'],";

                } else {
                    foreach ($g as $j => $d) {
                        if ($j == 0) {
                            $grafico_linha .= "['$d',";
                        } else {
                            $grafico_linha .= "$d";

                            if (count($g) != $j + 1) {
                                $grafico_linha .= ",";
                            }
                        }
                    }
                    $grafico_linha .= "],";
                }
            }
        }
        /*echo "<pre>";
        print_r($grafico);
        print_r($teste);
        print_r($grafico_linha);
        die;*/

        return array("d" => $dados, "t" => $total, "paginador" => $paginador, "g" => $grafico_linha, "os_aberta" => $os_aberta);
	}

	function Dados($ID, $site=false){
		$condicao = null;
		if(!$site){
			$condicao .= " t1.Empresa_ID=? AND";
			$params[] = $_SESSION['_user']['Empresa_ID'];
		}

		$params[] = $ID;
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
		                                    t1.OS_Avaliacao,
		                                    t1.OS_AvaliacaoComentario,
		                                    t2.Usuario_Nome,
		                                    t2.Usuario_Cargo,
		                                    t3.Cliente_Nome,
		                                    t3.Cliente_Email,
		                                    t3.Cliente_Departamento,
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
		                                	1=1 AND
		                                	$condicao
		                                    t1.OS_ID=?", $params, 3);

		$dd['TempoOS'] = $this->CalculaDif($dd['DataHoraInicio'], $dd['OS_DataFechamento']);

		$params = array($ID);
		$dd['a'] = $this->Conn->execReader("SELECT
										*
									FROM
										os_anexos
									WHERE
										OS_ID=?", $params, 2);

		$params = array($ID);
		$dd['r'] = $this->Conn->execReader("SELECT
										*
									FROM
										os_relatorios
									WHERE
										OS_ID=?", $params, 3);

		$params = array($ID);
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

		$params = array($ID);
		$dd['l'] = $this->Conn->execReader("SELECT
										date_format(Log_Inclusao, '%Y-%m-%d') as Data,
										date_format(Log_Inclusao, '%d/%m/%Y') as Data_Formatada
									FROM
										os_log
									WHERE
										OS_ID=?
									GROUP BY Data
									ORDER BY Data DESC", $params, 2);
		foreach ($dd['l'] as $i => $d){
			$params = array($ID, $d['Data']);
			$dd['l'][$i]['reg'] = $this->Conn->execReader("SELECT
										t1.*,
										date_format(t1.Log_Inclusao, '%d/%m/%Y') as Data,
										date_format(t1.Log_Inclusao, '%H:%i') as Horas,
										t2.Usuario_ID as Usuario_Logado_ID,
										t2.Usuario_Nome as Usuario_Logado,
										t3.Usuario_ID as Usuario_Responsavel_ID,
										t3.Usuario_Nome as Usuario_Responsavel,
										t4.Cliente_ID,
										t4.Cliente_Nome
									FROM
										os_log as t1
										LEFT JOIN usuarios as t2 on t2.Usuario_ID=t1.Usuario_ID_Logado
										LEFT JOIN usuarios as t3 on t3.Usuario_ID=t1.Usuario_ID
										LEFT JOIN clientes as t4 on t4.Cliente_ID=t1.Cliente_ID
									WHERE
										t1.OS_ID=? AND
										date_format(t1.Log_Inclusao, '%Y-%m-%d')=?
									ORDER BY t1.Log_ID DESC", $params, 2);
		}
		/*echo"<pre>";
		print_r($dd);
		die;*/
		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$dd['horas_fim'] = ($dd['horas_fim'])?$dd['horas_fim']:null;

		if(!$dd['usuario_id']){
			$dd['usuario_id'] = 0;
		}

		$params = array(
			$dd['empresa_id'],
			$dd['usuario_id'],
			$dd['cliente_id'],
			$dd['prioridade'],
			$dd['grupo'],
			$dd['tipo'],
			$this->dataDB($dd['data_inicio']),
			$dd['horas_inicio'],
			$dd['descricao'],
			$dd['protocolo'],
			$_SESSION['_user']['Usuario_ID']
		);
		$ID = $this->Conn->execWrite("INSERT INTO os SET
								Empresa_ID=?,
								Usuario_ID=?,
								Cliente_ID=?,
								Prioridade_ID=?,
								Grupo_ID=?,
								Tipo_ID=?,
								Status_ID=1,
								OS_DataInicio=?,
								OS_HorasInicio=?,
								OS_Descricao=?,
								OS_Protocolo=?,
								Usuario_ID_Create=?,
								OS_LogInclusao=now()", $params,1);

		/*LOG OS*/
		$this->OS_LOG(array(
			'OS_ID' => $ID,
			'Usuario_Logado' => (isset($_SESSION['_user']['Usuario_ID']))?$_SESSION['_user']['Usuario_ID']:0,
			'Usuario_ID' => $dd['usuario_id'],
			'Cliente_ID' => (isset($_SESSION['xxx']['login']['Cliente_ID']))?$_SESSION['xxx']['login']['Cliente_ID']:0,
			'Comentario' => "Inseriu registro",
		));
		/*FIM LOG OS*/

		return $ID;
	}

	function Edita($dd, $OS_ID){
		$this->VerificaToken($dd['token']);

		$dl = $this->Dados($OS_ID);
		$dd['horas_fim'] = ($dd['horas_fim'])?$dd['horas_fim']:null;

		$params = array(
			$dd['usuario_id'],
			$dd['cliente_id'],
			$dd['prioridade'],
			$dd['grupo'],
			$dd['tipo'],
			$this->dataDB($dd['data_inicio']),
			$dd['horas_inicio'],
			$dd['descricao'],
			$dd['protocolo'],
			$OS_ID,
			$_SESSION['_user']['Empresa_ID']
		);
		$this->Conn->execWrite("UPDATE os SET
									Usuario_ID=?,
									Cliente_ID=?,
									Prioridade_ID=?,
									Grupo_ID=?,
									Tipo_ID=?,
									OS_DataInicio=?,
									OS_HorasInicio=?,
									OS_Descricao=?,
									OS_Protocolo=?
								WHERE
									OS_ID=? AND Empresa_ID=?", $params);

		/*LOG OS*/
		$Comentario = "Alterou dados da OS";
		if($dl['Usuario_ID'] != $dd['usuario_id']) {
			$Comentario = "Alterou responsável";
		}
		$this->OS_LOG(array(
			'OS_ID' => $OS_ID,
			'Usuario_Logado' => $_SESSION['_user']['Usuario_ID'],
			'Usuario_ID' => $dd['usuario_id'],
			'Comentario' => $Comentario,
		));
		/*FIM LOG OS*/

		return true;
	}

	function Edita_OS($dd, $OS_ID){
		$this->VerificaToken($dd['token']);
		$dl = $this->Dados($OS_ID);

		$params[] = $dd['nota'];

		$dados = null;
		if($dd['status']){
			$dados = ", Status_ID=?";
			$params[] = $dd['status'];
		}

		$params[] = $OS_ID;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$this->Conn->execWrite("UPDATE os SET
									OS_NotaUsuario=?
									$dados
								WHERE
									OS_ID=? AND Empresa_ID=?", $params);

		/*LOG OS*/
		if($dl['Status_ID']!=$dd['status'] || $dl['OS_NotaUsuario']!=$dd['nota']) {
			$this->OS_LOG(array(
				'OS_ID' => $OS_ID,
				'Usuario_Logado' => $_SESSION['_user']['Usuario_ID'],
				'Usuario_ID' => $dl['Usuario_ID'],
				'Comentario' => "Alterou dados da OS | Status={$dd['status']} | Nota='{$dd['nota']}'",
			));
		}
		/*FIM LOG OS*/
		return true;
	}

	function Confirmacao_OS($dd, $OS_ID){
		$this->VerificaToken($dd['token']);

		$do = $this->Dados($OS_ID);

        $params[] = $dd['nota'];

        $dados = null;
        if(!$do['Usuario_ID']){
            $dados .= ", Usuario_ID=?";
            $params[] = $_SESSION['_user']['Usuario_ID'];
        }

        if(!$do['OS_Relatorio']){
            $dados .= ", OS_Relatorio=1";
        }

        $params[] = $OS_ID;
        $params[] = $_SESSION['_user']['Empresa_ID'];
		$this->Conn->execWrite("UPDATE os SET
									Status_ID=4,
									OS_NotaUsuario=?
									$dados
								WHERE
									OS_ID=? AND Empresa_ID=?", $params);

		/*VERIFICA SE EXISTE CONFIRMAÇÃO DE FECHAMENTO*/
		$params = array($OS_ID, $dd['Cliente_ID']);
		$dc = $this->Conn->execReader("SELECT Fechamento_ID FROM os_fechamentos WHERE OS_ID=? AND Cliente_ID=?", $params, 4);

		if(!$dc) {
			$codigo = strtoupper(substr(sha1(rand(0,1000)),0,96));

			$params = array(
				$OS_ID,
				$dd['Cliente_ID'],
				$codigo
			);
			$this->Conn->execWrite("INSERT INTO os_fechamentos SET
									OS_ID=?,
									Cliente_ID=?,
									Fechamento_Codigo=?,
									Fechamento_Status=0", $params);
		}

		return true;
	}

	function Confirma_Fechamento_OS($dd){
		$params = array(
			$_SESSION['_user']['Usuario_ID'],
			$dd['os'],
			$dd['Cliente_ID']
		);
		$this->Conn->execWrite("UPDATE os_fechamentos SET
									Usuario_ID=?,
									Fechamento_Status=1,
									Fechamento_Data=now()
								WHERE
									OS_ID=? AND Cliente_ID=?", $params);

		$this->Verifica_Fechamento_OS($dd['os']);
	}

	function Verifica_Fechamento_OS($OS_ID){
		/*VERIFICA SE TODAS CONFIRMAÇÕES ESTÃO FECHADAS*/
		$params = array($OS_ID);
		$dt = $this->Conn->execReader("SELECT
											count(Fechamento_ID)
		                                FROM
		                                    os_fechamentos
		                                WHERE
		                                	OS_ID=? AND
		                                	Fechamento_Status=0", $params, 4);

		if(!$dt) {
			$params = array($OS_ID);
			$this->Conn->execWrite("UPDATE os SET
									Status_ID=3,
									OS_DataFechamento=now()
								WHERE
									OS_ID=?", $params);
		}
	}

	public function Relatorio_OS($dd){
		$params = array($dd['rel'], $dd['id']);
		$this->Conn->execWrite("UPDATE os SET
									OS_Relatorio=?
								WHERE
									OS_ID=?", $params);
	}

	public function Mensagens($ID, $Recebimento=false){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    date_format(t1.Msg_LogInclusao, '%d/%m/%Y às %H:%i') as Msg_Data,
		                                    t2.Usuario_Nome
		                                FROM
		                                    os_msg as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
		                                WHERE
		                                	t1.OS_ID=?
		                                ORDER BY t1.Msg_LogInclusao", $params, 2);
		foreach ($dd as $i => $d){
			$params = array($d['Msg_ID']);
			$dd[$i]['Anexos'] = $this->Conn->execReader("SELECT
															t1.*
														FROM
															os_msg_anexos as t1
														WHERE
															t1.Msg_ID=?", $params, 2);
		}

		if(isset($Recebimento)) {
			$params = array($ID, $Recebimento);
			$this->Conn->execWrite("UPDATE os_msg SET
									Msg_Visualizada=1
								WHERE
									OS_ID=? AND 
									Msg_Recebimento!=?", $params);
		}

		return $dd;
	}

	function Envia_Mensagem_OS($dd){
		$user = null;
		if(isset($_SESSION['_user']['Usuario_ID'])){
			$user = $_SESSION['_user']['Usuario_ID'];
		}

		$params = array(
			$dd['os'],
			$user,
			trim($dd['conteudo']),
			$dd['recebimento']
		);
		return $this->Conn->execWrite("INSERT INTO os_msg SET
									OS_ID=?,
									Usuario_ID=?,
									Msg_Conteudo=?,
									Msg_Recebimento=?", $params);
	}

	function Mensagem_Upload($File, $dd) {
		$subextensao = explode('.', $File['Filedata']['name']);
		$quant = count($subextensao);
		$extensao = strtolower($subextensao[$quant - 1]);

		$img = array("jpg","png");
		$doc = array("doc","docx","xls","xlsx","pdf","xml","txt","rar","zip");
		$permitidos = array_merge($img, $doc);;
		if(in_array($extensao, $permitidos)){
			if(in_array($extensao, $img)){
				$tipo = "img";
			}else{
				$tipo = "doc";
			}

			$pasta = date("d-m-Y");
			$diretorio = $dd['diretorio'].$pasta;

			if (!file_exists($diretorio)){
				mkdir($diretorio, 0777);
			}

			$midia = "msg_".$dd['id']."_".substr(md5(uniqid(rand(0, 1000000))), 0, 16).".".$extensao;

			move_uploaded_file($File['Filedata']['tmp_name'], $diretorio.'/'.$midia);

			$params = array($dd['id'], $pasta . '/' . $midia, $File['Filedata']['name'], $tipo);
			$ID = $this->Conn->execWrite("INSERT INTO os_msg_anexos SET
                                            Msg_ID=?,
                                            Anexo_Caminho=?,
                                            Anexo_Nome=?,
                                            Anexo_Tipo=?", $params);

			return array($ID);
		}else{
			return "erro";
		}
	}

	function Upload($File, $dd) {
		$subextensao = explode('.', $File['Filedata']['name']);
		$quant = count($subextensao);
		$extensao = strtolower($subextensao[$quant - 1]);

		$img = array("jpg","png");
		$doc = array("doc","docx","xls","xlsx","pdf","xml","txt","rar","zip");
		$permitidos = array_merge($img, $doc);;
		if(in_array($extensao, $permitidos)){
			if(in_array($extensao, $img)){
				$tipo = "img";
			}else{
				$tipo = "doc";
			}

			$pasta = date("d-m-Y");
			$diretorio = $dd['dir']."midias/".$pasta;

			if (!file_exists($diretorio)){
				mkdir($diretorio, 0777);
			}

			$midia = "os_".$dd['id']."_".substr(md5(uniqid(rand(0, 1000000))), 0, 16).".".$extensao;

			move_uploaded_file($File['Filedata']['tmp_name'], $diretorio.'/'.$midia);

			$params = array($dd['id'], $pasta . '/' . $midia, $File['Filedata']['name'], $tipo, $dd['envio']);
			$ID = $this->Conn->execWrite("INSERT INTO os_anexos SET
                                            OS_ID=?,
                                            Anexo_Caminho=?,
                                            Anexo_Nome=?,
                                            Anexo_Tipo=?,
                                            Anexo_Envio=?", $params);

			/*LOG OS*/
			$d = $this->Dados($dd['id'], true);
			$this->OS_LOG(array(
				'OS_ID' => $dd['id'],
				'Usuario_Logado' => (isset($_SESSION['_user']['Usuario_ID']))?$_SESSION['_user']['Usuario_ID']:0,
				'Usuario_ID' => $d['Usuario_ID'],
				'Cliente_ID' => (isset($_SESSION['xxx']['login']['Cliente_ID']))?$_SESSION['xxx']['login']['Cliente_ID']:0,
				'Comentario' => "Inseriu anexo: $ID | {$File['Filedata']['name']}($pasta/$midia)"
			));
			/*FIM LOG OS*/

			return array($ID, $pasta . '/' . $midia, $File['Filedata']['name']);
		}else{
			return "erro";
		}
	}

	function Anexo_Excluir($ID, $dir){
		$dm = $this->Conn->execReader("SELECT Anexo_ID, OS_ID, Anexo_Caminho, Anexo_Nome FROM os_anexos WHERE Anexo_ID=?", array($ID), 3);

		/* deleta arquivo do servidor */
		@unlink($dir."midias/".$dm['Anexo_Caminho']);

		//DELETA BANCO
		$this->Conn->execWrite("DELETE FROM os_anexos WHERE Anexo_ID=?", array($ID));

		/*LOG OS*/
		$d = $this->Dados($dm['OS_ID'], true);
		$this->OS_LOG(array(
			'OS_ID' => $dm['OS_ID'],
			'Usuario_Logado' => (isset($_SESSION['_user']['Usuario_ID']))?$_SESSION['_user']['Usuario_ID']:0,
			'Usuario_ID' => $d['Usuario_ID'],
			'Cliente_ID' => (isset($_SESSION['xxx']['login']['Cliente_ID']))?$_SESSION['xxx']['login']['Cliente_ID']:0,
			'Comentario' => "Excluiu anexo: {$dm['Anexo_ID']} | {$dm['Anexo_Nome']}({$dm['Anexo_Caminho']})"
		));
		/*FIM LOG OS*/
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM os WHERE Empresa_ID=? AND OS_ID=?", $params);

		/*LOG OS*/
		$d = $this->Dados($ID);
		$this->OS_LOG(array(
			'OS_ID' => $ID,
			'Usuario_Logado' => $_SESSION['_user']['Usuario_ID'],
			'Usuario_ID' => $d['Usuario_ID'],
			'Comentario' => "Deletou registro",
		));
		/*FIM LOG OS*/
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Cliente_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE os SET OS_Status=? WHERE Empresa_ID=? AND Cliente_ID=?", $params);

		return $status;
	}

	public function Prioridades($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    os_prioridades as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Prioridade_Status=1
		                                ORDER BY t1.Prioridade_Titulo", $params, 2);

		return $dd;
	}

	public function Grupos($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    os_grupos as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Grupo_Status=1
		                                ORDER BY t1.Grupo_Nome", $params, 2);

		return $dd;
	}

	public function Usuarios(){
		$params = array($_SESSION['_user']['Empresa_ID']);
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    usuarios as t1
		                                WHERE
		                                	t1.Empresa_ID=?
		                                ORDER BY t1.Usuario_Nome", $params, 2);
	}

	public function Cidades(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cidades as t1
		                                WHERE
		                                	t1.Cidade_Status=1
		                                ORDER BY t1.Cidade_Nome", null, 2);
	}

	public function Status($cond=false){
		$condicao = null;
		if($cond){
			$condicao = " AND t1.Status_ID NOT IN(3,4)";
		}

		$params = array($_SESSION['_user']['Empresa_ID']);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    os_status as t1
		                                WHERE
		                                	t1.Empresa_ID=?
		                                	$condicao
		                                ORDER BY t1.Status_Nome", $params, 2);

		return $dd;
	}
	
	public function Tipos($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    os_tipos as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Tipo_Status=1
		                                ORDER BY t1.Tipo_Nome", $params, 2);

		return $dd;
	}

	/*public function Tipos($ID){
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    os_tipos as t1
										WHERE
											t1.Empresa_ID=?", $params, 2);

		return $dd;
	}*/

	public function Protocolo(){
		$data = date('Ymd');
		$Protocolo = strtoupper($data.substr(md5(rand(0,1000)),0,4));

		return $this->VerificaProtocolo($Protocolo);
	}

    public function VerificaProtocolo($Protocolo){
        $params = array($Protocolo);
        $dd = $this->Conn->execReader("SELECT
										t1.OS_ID
									FROM
										os as t1
									WHERE
										t1.OS_Protocolo=?", $params, 4);

        if($dd):
            $this->Protocolo();
        else:
            return $Protocolo;
        endif;
    }

	public function Altera_Usuario($OS_ID){
		$params = array(
			$_SESSION['_user']['Usuario_ID'],
			$OS_ID
		);
		$this->Conn->execWrite("UPDATE os SET Usuario_ID=?  WHERE OS_ID=?", $params);

		/*LOG OS*/
		$d = $this->Dados($OS_ID);
		$this->OS_LOG(array(
			'OS_ID' => $OS_ID,
			'Usuario_Logado' => $_SESSION['_user']['Usuario_ID'],
			'Usuario_ID' => $d['Usuario_ID'],
			'Comentario' => "Alterou responsável",
		));
		/*FIM LOG OS*/
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

	public function OS_LOG($dd){
		$campos = null;

		$params[] = $dd['OS_ID'];
		$params[] = $dd['Usuario_Logado'];
		$params[] = $dd['Usuario_ID'];
		$params[] = $dd['Comentario'];

		if($dd['Cliente_ID']){
			$campos .= ", Cliente_ID=?";
			$params[] = $dd['Cliente_ID'];
		}

		$this->Conn->execWrite("INSERT INTO os_log SET
									OS_ID=?,
									Usuario_ID_Logado=?,
									Usuario_ID=?,
									Log_Comentario=?
									$campos", $params);
	}

    public function AvaliaAtendimento($OS_ID, $Nota, $Comentario)    {
        $params = array(
            $Nota,
            $Comentario,
            $OS_ID
        );
        $this->Conn->execWrite("UPDATE os SET OS_Avaliacao=?, OS_AvaliacaoComentario=?  WHERE OS_ID=?", $params);
    }

    function ListaRelatorio($dd, $noPg=false, $Cliente_ID=false)
    {
        $params[] = $_SESSION['_user']['Empresa_ID'];

        $order_by = "t1.OS_ID";
        $busca = null;
        $buscaPage = null;
        if ($dd['b']) {
            if (is_numeric($dd['b']) && strlen($dd['b']) < 12) {
                $busca .= "AND t1.OS_ID=?";
                $params[] = $dd['b'];
            } else {
                $busca .= "AND (t3.Cliente_Nome LIKE ? OR t1.OS_Protocolo LIKE ?)";
                $params[] = "%{$dd['b']}%";
                $params[] = "%{$dd['b']}%";
            }

            $buscaPage .= "&b={$dd['b']}";
        }

        if ($dd['s']) {
            $busca .= " AND t1.Status_ID=?";
            $params[] = $dd['s'];
            $buscaPage .= "&s={$dd['s']}";
        }

        if ($dd['t']) {
            $busca .= " AND t1.Tipo_ID=?";
            $params[] = $dd['t'];
            $buscaPage .= "&t={$dd['t']}";
        }

        if ($dd['g']) {
            $busca .= " AND t1.Grupo_ID=?";
            $params[] = $dd['g'];
            $buscaPage .= "&g={$dd['g']}";
        }

        if ($dd['p']) {
            $busca .= " AND t1.Prioridade_ID=?";
            $params[] = $dd['p'];
            $buscaPage .= "&p={$dd['p']}";
        }

        if ($dd['d1'] && !$dd['d2']) {
            $busca .= " AND t1.OS_DataInicio=?";
            $params[] = $this->dataDB($dd['d1']);
            $buscaPage .= "&d1={$dd['d1']}";
        } elseif ($dd['d1'] && $dd['d2']) {
            $busca .= " AND t1.OS_DataInicio >= ? AND t1.OS_DataInicio <= ?";
            $params[] = $this->dataDB($dd['d1']);
            $params[] = $this->dataDB($dd['d2']);
            $buscaPage .= "&d1={$dd['d1']}";
            $buscaPage .= "&d2={$dd['d2']}";
        }

        if ($dd['u']) {
            $busca .= " AND t1.Usuario_ID=?";
            $params[] = $dd['u'];
            $buscaPage .= "&u={$dd['u']}";
        }

        if ($dd['c']) {
            $busca .= " AND t3.Cidade_ID=?";
            $params[] = $dd['c'];
            $buscaPage .= "&c={$dd['c']}";
        }

        if ($dd['n']) {
            if ($dd['n'] == 1) {
                $busca .= " AND (SELECT count(Msg_ID) FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=1 AND Msg_Visualizada=0) > 0";
            } else {
                $order_by = "(SELECT Msg_LogInclusao FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=1 ORDER BY Msg_LogInclusao DESC LIMIT 1)";
            }
            $buscaPage .= "&n={$dd['n']}";
        }

        if ($Cliente_ID) {
            $busca .= " AND t1.Cidade_ID=?";
            $params[] = $Cliente_ID;
        }

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

        $total = $this->Conn->execReader("SELECT count(t1.OS_ID) FROM os as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
		                                    LEFT JOIN clientes as t3 on (t3.Cliente_ID=t1.Cliente_ID)
		                                    LEFT JOIN os_prioridades as t5 on (t5.Prioridade_ID=t1.Prioridade_ID) WHERE t1.Empresa_ID=? $busca", $params, 4);

        $limite = null;
        if (!$noPg) {
            $params[] = $ipg;
            $params[] = $fpg;
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
		                                    t1.OS_NotaUsuario,
		                                    t2.Usuario_Nome,
		                                    t3.Cliente_Nome,
		                                    t3.Cliente_Admin,
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
		                                    (SELECT count(Msg_ID) FROM os_msg WHERE OS_ID=t1.OS_ID AND Msg_Recebimento!=1 AND Msg_Visualizada=0) as Msg_Total_Visualizada
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
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY $order_by DESC
		                                $limite", $params, 2);
        //print_r($dados);
        //die;
        foreach ($dados as $i => $d) {
            if ($d['OS_DataFechamento']) {
                $dados[$i]['TempoOS'] = $this->CalculaDif($d['DataHoraInicio'], $d['OS_DataFechamento']);
            }
            $dados[$i]['msg'] = $this->Mensagens($d['OS_ID']);
        }

        //echo "<pre>";
        //print_r($dados);
        //die;

        return array("d" => $dados, "t" => $total);
	}
}