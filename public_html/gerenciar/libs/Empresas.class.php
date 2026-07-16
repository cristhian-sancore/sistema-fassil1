<?php
class Empresas extends Metodos{
	function Empresas($Conn){
		$this->Conn = $Conn;
	}

	function Lista($dd){
		$busca = null;
        if($dd['b']){
            $busca .= "AND (t1.Empresa_Nome LIKE ?)";
			$params[] = "%{$dd['b']}%";

            $buscaPage = "&b={$dd['b']}";
        }

		if($dd['s']){
			$s = 1;
			if($dd['s']==2){
				$s = 0;
			}

			$busca .= " AND t1.Empresa_Status=?";
			$params[] = $s;
		}

        $maxLinks = 3;
        $fpg = 20;
        $ipg = ($dd['pg'] * $fpg) - $fpg;

		$total = $this->Conn->execReader("SELECT count(t1.Empresa_ID) FROM empresas as t1 WHERE 1=1 $busca", $params,4);

		$params[] = $ipg;
		$params[] = $fpg;
		$dados = $this->Conn->execReader("SELECT
		                                    t1.Empresa_ID,
		                                    t1.Empresa_Nome,
		                                    t1.Empresa_Status
		                                FROM
		                                    empresas as t1
		                                WHERE
		                                    1=1
		                                    $busca
		                                ORDER BY Empresa_ID DESC
		                                LIMIT ?,?", $params, 2);

		foreach ($dados as $i => $d){
			$params = array($d['Empresa_ID']);
			$dados[$i]['cidades'] = $this->Conn->execReader("SELECT
		                                    t1.Empresa_ID,
		                                    t1.Cidade_ID,
		                                    t2.Cidade_Nome,
		                                    t3.Estado_UF
		                                FROM
		                                    empresa_cidades as t1
		                                    LEFT JOIN cidades as t2 on (t2.Cidade_ID=t1.Cidade_ID)
		                                    LEFT JOIN estados as t3 on (t3.Estado_ID=t2.Estado_ID)
		                                WHERE
		                                	t1.Empresa_ID=?
		                                ORDER BY t2.Cidade_Nome", $params, 2);
		}

        /*PAGINAÇÃO*/
        $paginador = $this->Paginador($dd['pg'], $fpg, $dd['link'], $maxLinks, $total, $buscaPage);

        return array("d" => $dados, "t" => $total, "paginador" => $paginador);
	}

	function Dados($ID){
		$dd = $this->Conn->execReader("SELECT
		                                    t1.Empresa_ID,
		                                    t1.Empresa_Nome,
		                                    t1.Empresa_Status
		                                FROM
		                                    empresas as t1
		                                WHERE
		                                    t1.Empresa_ID=?", array($ID), 3);

		$params = array($ID);
		$dd['cidades'] = $this->Conn->execReader("SELECT
										t1.Empresa_ID,
										t1.Cidade_ID,
										t2.Cidade_Nome,
										t3.Estado_Nome,
										t3.Estado_UF
									FROM
										empresa_cidades as t1
										LEFT JOIN cidades as t2 on (t2.Cidade_ID=t1.Cidade_ID)
										LEFT JOIN estados as t3 on (t3.Estado_ID=t2.Estado_ID)
									WHERE
										t1.Empresa_ID=?
									ORDER BY t2.Cidade_Nome", $params, 2);

		return $dd;
	}

	function Grava($dd){
		$params = array(
			$dd['nome']
		);

		$Empresa_ID = $this->Conn->execWrite("INSERT INTO empresas SET
												Empresa_Nome=?,
												Empresa_Status=1", $params,1);
		if(isset($dd['cidade'])) {
			foreach ($dd['cidade'] as $c) {
				$params = array(
					$Empresa_ID,
					$c
				);
				$this->Conn->execWrite("INSERT INTO empresa_cidades SET
									Empresa_ID=?,
									Cidade_ID=?", $params);
			}
		}
	}

	function Edita($dd, $Empresa_ID){
		$params = array(
			$dd['nome'],
			$Empresa_ID
		);

		$this->Conn->execWrite("UPDATE empresas SET
									Empresa_Nome=?
								WHERE
									Empresa_ID=?", $params);

		if(isset($dd['cidade'])) {
			foreach ($dd['cidade'] as $c) {
				if ($c) {
					$params = array(
						$Empresa_ID,
						$c
					);
					$this->Conn->execWrite("INSERT INTO empresa_cidades SET
									Empresa_ID=?,
									Cidade_ID=?", $params);
				}
			}
		}
		return true;
	}

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM empresas WHERE Empresa_ID=?", array($ID));
		$this->Conn->execWrite("DELETE FROM empresa_cidades WHERE Empresa_ID=?", array($ID));
	}

	function Deleta_EC($dd){
		$params = array($dd['emp'], $dd['ec']);
		$this->Conn->execWrite("DELETE FROM empresa_cidades WHERE Empresa_ID=? AND Cidade_ID=?", $params);
	}


	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Empresa_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$params[] = $status;
		$params[] = $ID;
		$this->Conn->execWrite("UPDATE empresas SET Empresa_Status=?  WHERE Empresa_ID=?", $params);

		return $status;
	}

	public function Cidades() {
		return $this->Conn->execReader("SELECT id, nome FROM cidades WHERE status=1", null, 2);
	}

	function ListaEmpresas(){
		return $this->Conn->execReader("SELECT
		                                    t1.Empresa_ID,
		                                    t1.Empresa_Nome,
		                                    t1.Empresa_Status
		                                FROM
		                                    empresas as t1
		                                WHERE
		                                    t1.Empresa_Status=1
		                                ORDER BY Empresa_Nome", null, 2);
	}
}