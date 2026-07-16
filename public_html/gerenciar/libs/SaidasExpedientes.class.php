<?php
class SaidasExpedientes extends Metodos{
	function SaidasExpedientes($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['b']){
            $busca .= "AND (t2.Usuario_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.SaidasExp_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 15;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.SaidasExp_ID)
		                                    FROM 
		                                    saidasexpedientes as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.SaidasExp_RespID)
		                                    LEFT JOIN cidades as t3 on (t3.Cidade_ID=t1.SaidasExp_CidadeID)
		                                    WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.SaidasExp_ID,
		                                    t1.SaidasExp_RespID,
		                                    t1.SaidasExp_CidadeID,
		                                    t1.SaidasExp_Autorizacao,
		                                    t1.SaidasExp_Status,
		                                    t1.SaidasExp_CodOs,
		                                    t1.SaidasExp_LogInclusao,
		                                    date_format(t1.SaidasExp_DataSaida, '%d/%m/%Y') as SaidasExp_DataSaida,
		                                    date_format(t1.SaidasExp_HoraSaida, '%H:%i') as SaidasExp_HoraSaida,
		                                    t1.SaidasExp_Descricao,
											date_format(t1.SaidasExp_DataChegada, '%d/%m/%Y') as SaidasExp_DataChegada,
											date_format(t1.SaidasExp_HoraChegada, '%H:%i') as SaidasExp_HoraChegada,
											t2.*,
											t3.*
		                                FROM
		                                    saidasexpedientes as t1
		                                    LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.SaidasExp_RespID)
		                                    LEFT JOIN cidades as t3 on (t3.Cidade_ID=t1.SaidasExp_CidadeID)
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY SaidasExp_ID DESC
		                                LIMIT ?,?", $params, 2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.SaidasExp_ID,
		                                    t1.SaidasExp_RespID,
		                                    t1.SaidasExp_CidadeID,
		                                    t1.SaidasExp_Autorizacao,
		                                    t1.SaidasExp_Status,
		                                    t1.SaidasExp_CodOs,
		                                    t1.SaidasExp_LogInclusao,
		                                    date_format(t1.SaidasExp_DataSaida, '%d/%m/%Y') as SaidasExp_DataSaida,
		                                    date_format(t1.SaidasExp_HoraSaida, '%H:%i') as SaidasExp_HoraSaida,
		                                    t1.SaidasExp_Descricao,
											date_format(t1.SaidasExp_DataChegada, '%d/%m/%Y') as SaidasExp_DataChegada,
											date_format(t1.SaidasExp_HoraChegada, '%H:%i') as SaidasExp_HoraChegada
		                                FROM
		                                    saidasexpedientes as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.SaidasExp_ID=?", $params, 3);

		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$params = array(
			$_SESSION['_user']['Empresa_ID'],
			$dd['responsavel'],
			$dd['cidade'],
			$dd['autorizacao'],
			$dd['descricao']
		);

		return $this->Conn->execWrite("INSERT INTO saidasexpedientes SET
								Empresa_ID=?,
								SaidasExp_RespID=?,
								SaidasExp_CidadeID=?,
								SaidasExp_Autorizacao=?,
								SaidasExp_Descricao=?,
								SaidasExp_DataSaida=now(),
								SaidasExp_HoraSaida=now(),
								SaidasExp_Status=1,
								SaidasExp_LogInclusao=now()", $params);
	}

	function Edita ($dd, $ID){
		$this->VerificaToken($dd['token']);
		$staus = $dd['status'];
		$dataSaida = $this->dataDB($dd['dtsaida']);
		$horaSaida = $dd['horas_saida'];
		
// 		if($dataSaida=='' || $dataSaida==null ){
// 		    $dataSaida = '';
// 		}
// 		if($horaSaida=='' || $horaSaida==null){
// 		    $horaSaida = '';
// 		}
		if($staus=='2'){
		    $status = '0';
		}else{
		    $status = '1';
		}
		$params = array(
		    $status,
		    $dd['os'],
			$dd['usuario_id'],
			$dd['cidade'],
			$dataSaida,
			$horaSaida,
			$this->dataDB($dd['dtchegada']),
			$dd['horas_chegada'],
			$dd['autorizacao'],
			$dd['descricao'],
			$ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE saidasexpedientes SET
								SaidasExp_Status=?,
								SaidasExp_CodOs=?,
								SaidasExp_RespID=?,
								SaidasExp_CidadeID=?,
								SaidasExp_DataSaida=?,
								SaidasExp_HoraSaida=?,
								SaidasExp_DataChegada=?,
								SaidasExp_HoraChegada=?,
								SaidasExp_Autorizacao=?,
								SaidasExp_Descricao=?,
								SaidasExp_LogAlteracao=now()
							  WHERE
								SaidasExp_ID=? AND Empresa_ID=?", $params);
		
		return true;
								
		$this->Upload($midias, $ID, $dd);
		
		
	}
	
	function Upload($Arquivos, $ID, $dd){
        foreach ($Arquivos['name'] as $i => $Arq){
            if($Arq) {
                $subextensao = explode('.', $Arq);
                $quant = count($subextensao);
                $extensao = strtolower($subextensao[$quant - 1]);

                $diretorio = "../../midias/saidasanexos";
                if (!file_exists($diretorio)) {
                    mkdir($diretorio, 0777);
                }

                $nome = "responsavel_".$ID."_".substr(md5(uniqid(time())), 0, 16) . "." . $extensao;
                move_uploaded_file($Arquivos['tmp_name'][$i], $diretorio . "/" . $nome);
                
                $params = array($ID, $dd['responsavel'], $diretorio .'/'.$nome);
                $this->Conn->execWrite("INSERT INTO saidasexpedientes_anexos SET
									SaidasExp_ID=?,
									Usuario_ID=?,
									SaidasAnexo_Link=?", $params);
            }
            
        }
        if(in_array($Arq)){
            return array($ID, $diretorio . '/' . $nome);
        }else{
            return "erro";
        }
        
    }
	
	public function Cidades(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    cidades as t1
		                                WHERE
		                                	t1.Cidade_Status=1
		                                ORDER BY t1.Cidade_Nome", $params, 2);
	}

	function Deleta($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$this->Conn->execWrite("DELETE FROM saidasexpedientes WHERE Empresa_ID=? AND SaidasExp_ID=?", $params);
	}
	
	public function Usuarios(){
		$params = array($_SESSION['_user']['Empresa_ID']);
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    usuarios as t1
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                	t1.Usuario_Status=1
		                                ORDER BY t1.Usuario_Nome", $params, 2);
	}

	function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['SaidasExp_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE saidasexpedientes SET SaidasExp_Status=? WHERE Empresa_ID=? AND SaidasExp_ID=?", $params);

		return $status;
	}
}