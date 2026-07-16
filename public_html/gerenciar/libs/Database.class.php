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
        $this->sqli = new mysqli(HOST, USER, PASS, DBSA);

        if ($this->sqli->connect_error/* and $_SERVER['SERVER_NAME']=='localhost'*/) {
            trigger_error('Database sqli erro: '  . $this->sqli->connect_error, E_USER_ERROR);
        }

        $tm = $this->sqli->prepare("SET TIME_ZONE = '-04:00'");
        $tm->execute();

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
            }else "Erro ao executar";
        }else echo "Prepare mysqli - Tabela inexistente: $query";
    }

    public function paramsMount(&$st,&$vars=''){
        $param='';
        $strType='';
        foreach($vars as $vr){
            $chrType = substr((string)gettype($vr),0,1);
            $strType .= (!in_array($chrType,array("i","d","s"))) ? "b" : $chrType;
            $params[]=$vr;//$this->sqli->real_escape_string(
        }
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($st, $strType),$this->refValues($params)));
    }

    function refValues($arr){
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
                    $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
}