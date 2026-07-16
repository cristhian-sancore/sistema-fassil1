<?php
ob_start();
if($_SERVER['SERVER_NAME']!='localhost') {
    //------------------- CAMINHO SESSION SERVIDOR
    session_save_path("/home/grupofas/tmp");
}

set_time_limit(0);
ini_set('memory_limit', -1);
//ini_set("pcre.backtrack_limit", "1000000");

$diretorio = "../../";
include($diretorio."_config_db.inc.php");
include($diretorio."mpdf60/mpdf.php");

include($diretorio."libs/Sessao.class.php");
include($diretorio."libs/Database.class.php");
include($diretorio."libs/Metodos.class.php");
include($diretorio."libs/OrdemServicos.class.php");

$Conn = new Sessao();
$Conn = new Database();
$OrdemServicos = new OrdemServicos($Conn);

$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;
$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;
$tipo = (isset($_REQUEST['t']))?$_REQUEST['t']:null;
$grupo = (isset($_REQUEST['g']))?$_REQUEST['g']:null;
$usuario = (isset($_REQUEST['u']))?$_REQUEST['u']:null;
$prioridade = (isset($_REQUEST['p']))?$_REQUEST['p']:null;
$data1 = (isset($_REQUEST['d1']))?$_REQUEST['d1']:null;
$data2 = (isset($_REQUEST['d2']))?$_REQUEST['d2']:null;
$cidade = (isset($_REQUEST['c']))?$_REQUEST['c']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['b'] = $busca;
$_REQUEST['s'] = $status;
$_REQUEST['t'] = $tipo;
$_REQUEST['u'] = $usuario;
$_REQUEST['g'] = $grupo;
$_REQUEST['p'] = $prioridade;
$_REQUEST['d1'] = $data1;
$_REQUEST['d2'] = $data2;
$_REQUEST['c'] = $cidade;

$dd = $OrdemServicos->ListaRelatorio($_REQUEST, true);

/*GERA PDF*/
$mpdf = new mPDF('',    // mode - default ''
                'A4-L',    // format - A4, for example, default ''
                0,     // font size - default 0
                'Arial',    // default font family
                5,    // margin_left
                5,    // margin right
                10,    // margin top
                15,    // margin bottom
                6,     // margin header
                9,     // margin footer
                'P');
$mpdf->SetDisplayMode('fullpage');
$css = file_get_contents($diretorio."css/relatorio.css");
$mpdf->WriteHTML($css,1);

$corpo_start = "
    <div class='corpo'>
	    <h1>RELATÓRIO DE ORDEM DE SERVIÇOS</h1>
	    
	    <table class='tabela' cellpadding='5px'>
	        <tr>
	            <th>ID</th>
	            <th>Cliente</th>
	            <th>Cidade</th>
	            <th>Entidade</th>
	            <th>Prioridade</th>
	            <th width='130px'>Data</th>
	            <th>Protocolo</th>
	            <th>Grupo</th>
	            <th>Tipo</th>
	            <th>Descrição/Observação</th>
	            <th>Status</th>
	            <th>Responsável</th>
            </tr>
";
$mpdf->WriteHTML($corpo_start);

for($i=0; $i < count($dd['d']); $i++) {
    $corpo_dd = '';

    $class = '';
    if($i % 2 == 0) {
        $class = 'dif';
    }

    $corpo_dd .= "
        <tr class='".$class."'>
            <td>".$dd['d'][$i]['OS_ID']."</td>
            <td>".$dd['d'][$i]['Cliente_Nome']."</td>
            <td>".$dd['d'][$i]['Cidade_Nome']."</td>
            <td>".$dd['d'][$i]['CE_Nome']."</td>
            <td>".$dd['d'][$i]['Prioridade_Titulo']."</td>
            <td>Início: ".$dd['d'][$i]['OS_DataInicio']." às ".$dd['d'][$i]['OS_HorasInicio']."<br>Fim: ".$dd['d'][$i]['DataFechamento']."<br>Tempo: ".$dd['d'][$i]['TempoOS']."</td>
            <td>".$dd['d'][$i]['OS_Protocolo']."</td>
            <td>".$dd['d'][$i]['Grupo_Nome']."</td>
            <td>".$dd['d'][$i]['Tipo_Nome']."</td>
            <td>".$dd['d'][$i]['OS_Descricao']."</td>
            <td>".$dd['d'][$i]['Status_Nome']."</td>
            <td>".$dd['d'][$i]['Usuario_Nome']."</td>
        </tr>
    ";

    if ($dd['d'][$i]['OS_NotaUsuario']){
        $corpo_dd .= "
            <tr class='".$class."'>
                <td colspan='999'><b style='font-size=14px;'>Descrição/Observação Usuário</b><br>".$dd['d'][$i]['OS_NotaUsuario']."</td>
            </tr>
        ";
    }else{
        $corpo_dd .= "
            <tr class='".$class."'>
                <td colspan='999'><b style='font-size=14px;'>Descrição/Observação Usuário</b><br>Não possui.</td>
            </tr>
        ";    
    }

    //mensagens
    $msg = $dd['d'][$i]['msg'];

    if ($msg){
        $corpo_dd .= "
            <tr class='".$class."'>
                <td colspan='999'>
                    <table cellpadding='1px'style='width: 100%; border-spacing: 0px; font-size: 12px;'>
                        <tr>
                            <th width='50%'>Mensagem Cliente</th>
                            <th width='50%'>Mensagem Usuário</th>
                        </tr>
        ";

        for($j=0; $j < count($msg); $j++) {
            if(!$msg[$j]['Msg_Recebimento']) {
                $corpo_dd .= "
                            <tr>
                                <td>".$msg[$j]['Msg_Conteudo']."<br>".$msg[$j]['Msg_Data']."</td>
                                <td></td>
                            </tr>
                ";//cliente
            }else {
                $corpo_dd .= "
                            <tr>
                                <td></td>
                                <td>".$msg[$j]['Msg_Conteudo']."<br>".$msg[$j]['Usuario_Nome']." | ".$msg[$j]['Msg_Data']."</td>
                            </tr>
                ";//usuario
            }
        }

         $corpo_dd .= "
                    </table>
                </td>
            </tr>
        ";
    }

    $mpdf->WriteHTML($corpo_dd);
}

$corpo_end .="
        </table>
    </div>
";
$mpdf->WriteHTML($corpo_end);

$rodape = "
	    <p align='right'>Página | {PAGENO}</p>
";


$mpdf->SetHTMLFooter($rodape);
$arquivo = 'Relatório '.date("d-m-Y").'.pdf';
$mpdf->Output($arquivo,'I');
//D - download

/*ABRE PDF NO NAVEGADOR*/
header("Content-Type: application/pdf");
header('Content-Disposition: inline; "'.$arquivo.'"');

exit;