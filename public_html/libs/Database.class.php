<?php
/**
 * <b>Database.class:</b>
 * Classe responsável por Conexão e leituras/inserções genéricas no banco de dados!
 *
 * @copyright (c) 2014, Gustavo Mendes Foccoweb
 */
class Database{
    /** @var MYSQLI */
    public $sqli;

    /**
     * Conecta com o banco de dados.
     * Retorna um objeto MYSQLI!
     */
    public function Database() {
        // Tenta conectar até 3 vezes caso o MySQL ainda esteja subindo/importando as tabelas no Docker
        for ($tentativa = 1; $tentativa <= 3; $tentativa++) {
            $this->sqli = @new mysqli(HOST, USER, PASS, DBSA);
            if (!$this->sqli->connect_error) {
                break;
            }
            if ($tentativa < 3) {
                sleep(1);
            }
        }

        if ($this->sqli->connect_error) {
            die("<div style='font-family: Arial, sans-serif; padding: 20px; text-align: center;'><h2>⏳ Aguardando Banco de Dados...</h2><p>O servidor MySQL está finalizando a inicialização e importando as tabelas. Por favor, <b>atualize a página (F5)</b> em cerca de 10 a 20 segundos.</p><p><small style='color: #666;'>Detalhes: (" . $this->sqli->connect_errno . ") " . $this->sqli->connect_error . "</small></p></div>");
        }

        $this->sqli->query("SET TIME_ZONE = '-04:00'");
        $this->sqli->query("SET SESSION sql_mode = ''");

        return $this->sqli;
    }

    public function execWrite($query, $vars=array(), $mostra=false){
        if ($mostra){
            echo "<br>$query";
            echo "<pre>";
            print_r($vars);
        }
        $st = $this->sqli->prepare($query);
        if($st){
            if ($vars) $this->paramsMount($st,$vars);
            if ($mostra) print_r($vars);

            $st->execute();
            if (!$this->sqli->error){
                $reg = $this->sqli->insert_id;

                //inserir aud

                return $reg;
            } else {
                if (trim($this->sqli->errno)=='1062')
                   echo'<h2>ENTRADA DUPLICADA</h2><p>Tentou inserir um registro já existente no Banco de Dados.</p>';
                else
                    echo "erro msg - ".$this->sqli->error;
            }
        }else echo "erro nao inseriu/atualizou";
    }

    public function execReader($query, $vars='', $r=1, $mostra=false){
        if ($mostra){
            echo "<br>$query";
            echo "<pre>";
            print_r($vars);
        }
        $st = $this->sqli->prepare($query);
        if($st){
            $rows = array();
            if ($vars) $this->paramsMount($st,$vars);
            if ($st->execute()){
                $res = $st->get_result();
                if (!$r) {
                    $rows=$res->fetch_row();
                } elseif ($r==1) {
                    while ($row=$res->fetch_row()) $rows[]=$row;
                } elseif($r==2) {
                    while ($row=$res->fetch_array(MYSQLI_ASSOC)) $rows[]=$row;
                } elseif($r==3) {
                    return $res->fetch_array(MYSQLI_ASSOC);
                } elseif($r==4) {
                    $rows=$res->fetch_row();
                    return $rows[0];
                }
                return $rows;
            }else echo "Erro ao executar query: $query - " . $this->sqli->error;
        }else echo "Prepare mysqli - Erro (" . $this->sqli->errno . "): " . $this->sqli->error . "<br><b>Query:</b> $query";
    }

    public function paramsMount(&$st, &$vars=''){
        $strType = '';
        $params = array();
        foreach ($vars as $key => $vr) {
            $chrType = substr((string)gettype($vr), 0, 1);
            $strType .= (!in_array($chrType, array("i", "d", "s"))) ? "b" : $chrType;
            $params[$key] = $vr;
        }

        $bindParams = array();
        $bindParams[] = $st;
        $bindParams[] = $strType;
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }

        call_user_func_array('mysqli_stmt_bind_param', $bindParams);
    }
}