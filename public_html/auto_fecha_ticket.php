<?php
$diretorio = "";
include($diretorio . "_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Conn = new Database();
$Email = new Email($Conn);
$OrdemServicos = new OrdemServicos($Conn);

echo"Iniciando processo de verificacao de tickets...<br><br>";

$dd = $Conn->execReader("SELECT
                            t1.OS_ID,
                            t1.OS_LogUpdate,
                            t2.Usuario_Nome,
                            t3.Cliente_Nome,
                            t3.Cliente_Email,
                            (SELECT Msg_LogInclusao FROM os_msg WHERE OS_ID=t1.OS_ID ORDER BY Msg_ID DESC LIMIT 1) AS Msg_LogInclusao
                        FROM
                            os as t1
                            LEFT JOIN usuarios as t2 on (t2.Usuario_ID=t1.Usuario_ID)
                            LEFT JOIN clientes as t3 on (t3.Cliente_ID=t1.Cliente_ID)
                        WHERE 
                            t1.Status_ID=4
                        ORDER BY t1.OS_ID DESC",null,2);
/*echo "<pre>";
print_r($dd);
die;*/
foreach ($dd as $i => $d){
    $data_hoje = date('Y-m-d H:i:s');

    $data_log_update = $d['OS_LogUpdate'];
    if(isset($d['Msg_LogInclusao']) && $data_log_update < $d['Msg_LogInclusao']) {
        $data_log_update = $d['Msg_LogInclusao'];
    }

    $data_update = date('Y-m-d H:i:s', strtotime($data_log_update."+2 days"));

    if($data_hoje > $data_update){
        echo "fechado: ".$d['OS_ID']."<br>";

        //altera status
        $Conn->execWrite("UPDATE os SET Status_ID=3, OS_DataFechamento=now(), OS_Fechado_Automatico=1 WHERE OS_ID=?", array($d['OS_ID']));
        $d = $OrdemServicos->Dados($d['OS_ID'], true);

        /*ENVIA E-MAIL*/
        if($_SERVER['SERVER_NAME']!='localhost'){
            $d['url'] = urlBase;
            $d['data'] = date("d/m/Y H:i");

            $smarty->assign('dados', $d);
            $template = $smarty->fetch('_email_ticket_fechamento.html');

            $Email->Ticket($d, $template);
        }
    }
}
/*echo"<pre>";
print_r($dd);*/

echo"<br>Fecha Ticket finalizado!";

?>