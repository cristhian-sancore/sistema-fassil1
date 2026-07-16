<?php
class Cadastro extends Metodos{
	function Cadastro($Conn){
		$this->Conn = $Conn;
	}

    function Grava($dd){
        $this->VerificaToken($dd['token']);

        $params = array(
            $dd['cidade'],
            $dd['estado'],
            $dd['empresa'],
            $dd['nome'],
            $dd['email'],
            $dd['departamento'],
            $dd['cargo'],
            $dd['login'],
            $dd['senha'],
            $dd['celular'],
            $dd['telefone']
        );
        return $this->Conn->execWrite("INSERT INTO clientes SET
								Empresa_ID=1,
								Cidade_ID=?,
								Estado_ID=?,
								CE_ID=?,
								Cliente_Nome=?,
								Cliente_Email=?,
								Cliente_Departamento=?,
								Cliente_Cargo=?,
								Cliente_Login=?,
								Cliente_Senha=sha1(?),
								Cliente_Contato1=?,
								Cliente_Contato2=?,
								Cliente_Status=1,
								Cliente_LogInclusao=now()", $params);
    }

    function Edita($dd){
        $this->VerificaToken($dd['token']);

        $params = array(
            $dd['nome'],
            $dd['departamento'],
            $dd['cargo'],
            $dd['celular'],
            $dd['telefone']
        );

        $colunas = null;
        if($dd['senha']){
            $colunas .= ", Cliente_Senha=sha1(?)";
            $params[] = $dd['senha'];
        }

        $params[] = $_SESSION['xxx']['login']['Cliente_ID'];
        $this->Conn->execWrite("UPDATE clientes SET
								Cliente_Nome=?,
								Cliente_Departamento=?,
								Cliente_Cargo=?,
								Cliente_Contato1=?,
								Cliente_Contato2=?
								$colunas
							  WHERE
							    Cliente_ID=?", $params);

        /*Atualiza SESSÃO*/
        $_SESSION['xxx']['login']['Cliente_Nome'] = $dd['nome'];
    }

    function Dados($ID){
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

    function ValidaLogin($login){
        $params = array($login);
        $dd = $this->Conn->execReader("SELECT
										count(*)
									FROM
										clientes
									WHERE
										Cliente_Login=?", $params, 4);

        if($dd){
            return true;
        }
    }

    function ValidaEmail($email){
        $params = array($email);
        $dd = $this->Conn->execReader("SELECT
										count(*)
									FROM
										clientes
									WHERE
										Cliente_Email=?", $params, 4);

        if($dd){
            return true;
        }
    }

    function RecuperarSenha($dd){
        $params = array($dd['email']);
        $Cliente_ID = $this->Conn->execReader("SELECT Cliente_ID FROM clientes WHERE Cliente_Email=?", $params, 4);

        if($Cliente_ID) {
            $codigo = strtoupper(substr(sha1(rand(0, 1000)), 0, 96));

            $params = array($codigo, $Cliente_ID);
            $this->Conn->execWrite("UPDATE clientes SET
									Cliente_Codigo=?
								WHERE
									Cliente_ID=?", $params);

            return $this->Dados($Cliente_ID);
        }
    }

    function AlterarSenha($dd){
        $params = array($dd['senha'], $dd['Cliente_ID']);
        $this->Conn->execWrite("UPDATE clientes SET
                                Cliente_Senha=sha1(?)
                            WHERE
                                Cliente_ID=?", $params);
    }

    function VerificaCodigoSenha($codigo){
        $params = array($codigo);
        $Cliente_ID = $this->Conn->execReader("SELECT Cliente_ID FROM clientes WHERE Cliente_Codigo=?", $params, 4);

        if($Cliente_ID){
            return $this->Dados($Cliente_ID);
        }
    }
}