<?php
class Diarios extends Metodos{
	function Diarios($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$params[] = $_SESSION['_user']['Empresa_ID'];

		$busca = null;
        if($dd['a']){
            $busca .= "AND (t2.Auto_ID=?)";
			$params[] = "{$dd['a']}";

            $buscaPage = "&a={$dd['a']}";
        }
        
        if($dd['u']){
            $busca .= "AND (t3.Usuario_ID=?)";
			$params[] = "{$dd['u']}";

            $buscaPage = "&b={$dd['u']}";
        }
        if($dd['c']){
            $busca .= "AND (t4.Cidade_ID=?)";
			$params[] = "{$dd['c']}";

            $buscaPage = "&c={$dd['c']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Diario_Status=?";
			$params[] = $s;
		}
		
		if ($dd['d1'] && !$dd['d2']) {
            $busca .= " AND t1.Diario_DiaSaida=?";
            $params[] = $this->dataDB($dd['d1']);
            $buscaPage .= "&d1={$dd['d1']}";
        } elseif ($dd['d1'] && $dd['d2']) {
            $busca .= " AND t1.Diario_DiaSaida >= ? AND t1.Diario_DiaSaida <= ?";
            $params[] = $this->dataDB($dd['d1']);
            $params[] = $this->dataDB($dd['d2']);
            $buscaPage .= "&d1={$dd['d1']}";
            $buscaPage .= "&d2={$dd['d2']}";
        }

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Diario_ID) FROM
		                                            diarios as t1
		                                        LEFT JOIN automoveis as t2 on (t2.Auto_ID=t1.Diario_Automovel)
											    LEFT JOIN usuarios as t3 on (t3.Usuario_ID=t1.Diario_Motorista)
											    LEFT JOIN cidades as t4 on (t4.Cidade_ID=t1.Diario_Cidade)
		                                            WHERE t1.Empresa_ID=? $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    date_format(t1.Diario_DiaSaida, '%d/%m/%Y') as Diario_DiaSaida,
		                                    date_format(t1.Diario_DiaChegada, '%d/%m/%Y') as Diario_DiaChegada,
		                                    t2.*,
											t3.*,
		                                  	t4.*,
		                                  	t5.*,
		                                  	t6.OS_ID
		                                FROM
		                                    diarios as t1
											LEFT JOIN automoveis as t2 on (t2.Auto_ID=t1.Diario_Automovel)
											LEFT JOIN usuarios as t3 on (t3.Usuario_ID=t1.Diario_Motorista)
											LEFT JOIN cidades as t4 on (t4.Cidade_ID=t1.Diario_Cidade)
											LEFT JOIN empresas as t5 on (t5.Empresa_ID=t1.Empresa_ID)
											LEFT JOIN os as t6 on (t6.OS_ID=t1.Diario_OS)
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY Diario_ID DESC
		                                LIMIT ?,?", $params,2);

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$params = array($_SESSION['_user']['Empresa_ID'], $ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*,
		                                    date_format(t1.Diario_DiaSaida, '%d/%m/%Y') as Diario_DiaSaida,
		                                    date_format(t1.Diario_DiaChegada, '%d/%m/%Y') as Diario_DiaChegada,
		                                    t2.*,
											t3.*,
		                                  	t4.*,
		                                  	t5.*,
		                                  	t6.OS_ID,
		                                  	t7.*
		                                FROM
		                                    diarios as t1
											LEFT JOIN automoveis as t2 on (t2.Auto_ID=t1.Diario_Automovel)
											LEFT JOIN usuarios as t3 on (t3.Usuario_ID=t1.Diario_Motorista)
											LEFT JOIN cidades as t4 on (t4.Cidade_ID=t1.Diario_Cidade)
											LEFT JOIN empresas as t5 on (t5.Empresa_ID=t1.Empresa_ID)
											LEFT JOIN os as t6 on (t6.OS_ID=t1.Diario_OS)
											LEFT JOIN diarios_empresa as t7 on (t7.DiarioEmp_ID=t1.Diario_Empresa)
		                                WHERE
		                                	t1.Empresa_ID=? AND
		                                    t1.Diario_ID=?", $params, 3);
	
		return $dd;
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);
		
		$params = array(
		    $_SESSION['_user']['Empresa_ID'],
			$dd['automovel'],
			$dd['cidade'],
			$dd['requisicao'],
			$dd['empresa'],
			$dd['usuario_id'],
			$dd['kmsaida'],
			$_SESSION['_user']['Usuario_ID']
		);

	     $this->Conn->execWrite("INSERT INTO diarios SET
								Empresa_ID=?,
								Diario_Automovel=?,
								Diario_Cidade=?,
								Diario_NRequisicao=?,
								Diario_Empresa=?,
		                       	Diario_Motorista=?,
		                        Diario_DiaSaida=now(),
		                        Diario_KMSaida=?,
		                        Diario_LogInclusao=?,
		                        Diario_LogDataInclusao=now(),
		                        Diario_Status=1", $params);
		                        
	    $auto = $dd['automovel'];
	        
        $this->Conn->execWrite("UPDATE automoveis SET
                                Auto_Status=0
                                WHERE
                                Auto_ID=$auto", $params);	 
		                        
	}

	function Edita($dd, $Diario_ID){
		$this->VerificaToken($dd['token']);
		
		$params = array(
			$dd['automovel'],
			$dd['requisicao'],
			$dd['usuario_id'],
			$dd['cidade'],
			$dd['os'],
			$dd['empresa'],
			$dd['descricao'],
			$dd['kmsaida'],
			$dd['kmchegada'],
			$dd['status'],
			$Diario_ID,
			$_SESSION['_user']['Empresa_ID']
		);

		$this->Conn->execWrite("UPDATE diarios SET
								Diario_Automovel=?,
								Diario_NRequisicao=?,
		                       	Diario_Motorista=?,
		                        Diario_Cidade=?,
		                        Diario_OS=?,
		                        Diario_Empresa=?,
		                        Diario_Descricao=?,
		                        Diario_KMSaida=?,
		                        Diario_KMChegada=?,
		                        Diario_Status=?
		                            WHERE
									Diario_ID=? AND Empresa_ID=?", $params);
		                        
		return true;
	}
	
	    function Deleta($ID){
		    $params = array($_SESSION['_user']['Empresa_ID'], $ID);
		    $this->Conn->execWrite("DELETE FROM diarios WHERE Empresa_ID=? AND Diario_ID=?", $params);
	    }
	
    function Diario_Confirm($dd, $Diario_ID, $Auto_ID){
	        $this->VerificaToken($dd['token']);
	        
	        $params =  array(
			$dd['kmchegada'],
			$dd['os'],
			$dd['descricao'],
			$_SESSION['_user']['Usuario_ID'],
			$Diario_ID,
			$_SESSION['_user']['Empresa_ID']
			);
	        
	        $this->Conn->execWrite("UPDATE diarios SET 
	                            Diario_KMChegada=?,
	                            Diario_OS=?,
	                            Diario_Descricao=?,
	                            Diario_LogAlteracao=?,
		                        Diario_Status=0,
		                        Diario_DiaChegada=now(),
		                        Diario_LogDataAlteracao=now(),
	                            Diario_DiaChegada=now()
	                                WHERE
	                                Diario_ID=? AND Empresa_ID=?", $params);
	    
	    $auto = $dd['automovel'];
	    $params =  array(
	        $dd['kmchegada'],
	        $_SESSION['_user']['Empresa_ID']
	        );
	        
        $this->Conn->execWrite("UPDATE automoveis SET
                                Auto_KM=?,
                                Auto_Status=1
                                WHERE
                                Auto_ID=$auto AND Empresa_ID=?", $params);
	               
    }
   
	public function Auto($ID){
	    $data = date('dmY');
		$params = array($ID);
		$dd = $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    automoveis as t1
										WHERE
											t1.Empresa_ID=?", $params, 2);

		return $dd;
	}
	
	public function Usuarios(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    usuarios as t1
		                                WHERE
		                                	t1.Usuario_Status=1
		                                ORDER BY t1.Usuario_Nome", null, 2);
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
	
	public function Empresas(){
		return $this->Conn->execReader("SELECT
		                                    t1.*
		                                FROM
		                                    diarios_empresa as t1
		                                WHERE
		                                	t1.DiarioEmp_Status=1
		                                ORDER BY t1.DiarioEmp_Nome", null, 2);
	}
	
	function ListaRelatorio($dd, $noPg=false, $Usuario_ID=false)
    {
        $params[] = $_SESSION['_user']['Empresa_ID'];

        $order_by = "t1.Diario_ID";
        $busca = null;
        $buscaPage = null;
        
        if ($dd['a']) {
            $busca .= " AND t2.Auto_ID=?";
            $params[] = $dd['a'];
            $buscaPage .= "&a={$dd['a']}";
        }
        
        if ($dd['u']) {
            $busca .= " AND t3.Usuario_ID=?";
            $params[] = $dd['u'];
            $buscaPage .= "&u={$dd['u']}";
        }

        if ($dd['c']) {
            $busca .= " AND t4.Cidade_ID=?";
            $params[] = $dd['c'];
            $buscaPage .= "&c={$dd['c']}";
        }
        
        if ($dd['s']) {
            $busca .= " AND t1.Diario_Status=?";
            $params[] = $dd['s'];
            $buscaPage .= "&s={$dd['s']}";
        }
        
        if ($dd['d1'] && !$dd['d2']) {
            $busca .= " AND t1.Diario_DiaSaida=?";
            $params[] = $this->dataDB($dd['d1']);
            $buscaPage .= "&d1={$dd['d1']}";
        } elseif ($dd['d1'] && $dd['d2']) {
            $busca .= " AND t1.Diario_DiaSaida >= ? AND t1.Diario_DiaSaida <= ?";
            $params[] = $this->dataDB($dd['d1']);
            $params[] = $this->dataDB($dd['d2']);
            $buscaPage .= "&d1={$dd['d1']}";
            $buscaPage .= "&d2={$dd['d2']}";
        }

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

        $total = $this->Conn->execReader("SELECT count(t1.Diario_ID) FROM
                                            diarios as t1
		                                        LEFT JOIN automoveis as t2 on (t2.Auto_ID=t1.Diario_Automovel)
											    LEFT JOIN usuarios as t3 on (t3.Usuario_ID=t1.Diario_Motorista)
											    LEFT JOIN cidades as t4 on (t4.Cidade_ID=t1.Diario_Cidade)
											 WHERE t1.Empresa_ID=? $busca", $params, 4);

        $limite = null;
        if (!$noPg) {
            $params[] = $ipg;
            $params[] = $fpg;
            $limite = "LIMIT ?,?";
        }
        $dados = $this->Conn->execReader("SELECT
                                        t1.*,
                                        date_format(t1.Diario_DiaSaida, '%d/%m/%Y') as Diario_DiaSaida,
		                                date_format(t1.Diario_DiaChegada, '%d/%m/%Y') as Diario_DiaChegada,
                                        t2.*,
                                        t3.*,
                                        t4.*
		                                FROM
		                                    diarios as t1
		                                        LEFT JOIN automoveis as t2 on (t2.Auto_ID=t1.Diario_Automovel)
											    LEFT JOIN usuarios as t3 on (t3.Usuario_ID=t1.Diario_Motorista)
											    LEFT JOIN cidades as t4 on (t4.Cidade_ID=t1.Diario_Cidade)
		                                WHERE
		                                    t1.Empresa_ID=?
		                                    $busca
		                                ORDER BY $order_by DESC
		                                $limite", $params, 2);

        return array("d" => $dados, "t" => $total);
	}

	/*function Ativar($ID){
		$dd = $this->Dados($ID);

		if($dd['Grupo_Status'] == 0){
			$status = 1;
		}
		if($dd['Grupo_Status'] == 1){
			$status = 0;
		}

		$params[] = $status;
		$params[] = $_SESSION['_user']['Empresa_ID'];
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE diarios SET
		                        Diario_Status=?,
		                        Diario_DiaChegada=now(),
		                        Diario_LogDataAlteracao=now()
		                        WHERE
		                        Empresa_ID=? AND Diario_ID=?", $params);

		return $status;
	}*/
	
}