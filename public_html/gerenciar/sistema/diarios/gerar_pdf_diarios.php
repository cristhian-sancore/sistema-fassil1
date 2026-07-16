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
include($diretorio."libs/Diarios.class.php");

$Conn = new Sessao();
$Conn = new Database();
$Diarios = new Diarios($Conn);


$cidade = (isset($_REQUEST['c']))?$_REQUEST['c']:null;
$usuario = (isset($_REQUEST['u']))?$_REQUEST['u']:null;
$auto = (isset($_REQUEST['a']))?$_REQUEST['a']:null;
$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;
$data1 = (isset($_REQUEST['d1']))?$_REQUEST['d1']:null;
$data2 = (isset($_REQUEST['d2']))?$_REQUEST['d2']:null;


$_REQUEST['pg'] = $pg;
$_REQUEST['c'] = $cidade;
$_REQUEST['u'] = $usuario;
$_REQUEST['a'] = $auto;
$_REQUEST['s'] = $status;
$_REQUEST['d1'] = $data1;
$_REQUEST['d2'] = $data2;


$dd = $Diarios->ListaRelatorio($_REQUEST, true);

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
	    <h1>RELATÓRIO DE DIÁRIOS DE BORDO</h1>
	    
	    <table class='tabela' cellpadding='5px'>
	        <tr>
	            <th>ID</th>
	            <th>Automóvel</th>
	            <th>Motorista</th>
	            <th>Cidade</th>
	            <th>Dia Saída</th>
	            <th>KM Saída</th>
	            <th>Dia Chegada</th>
	            <th>KM Chegada</th>
	            <th>Observação/Descrição</th>
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
            <td>".$dd['d'][$i]['Diario_ID']."</td>
            <td>".$dd['d'][$i]['Auto_Nome']." / ".$dd['d'][$i]['Auto_Placa']."</td>
            <td>".$dd['d'][$i]['Usuario_Nome']."</td>
            <td>".$dd['d'][$i]['Cidade_Nome']." - ".$dd['d'][$i]['Cidade_UF']."</td>
            <td>".$dd['d'][$i]['Diario_DiaSaida']."</td>
            <td>".$dd['d'][$i]['Diario_KMSaida']."</td>
            <td>".$dd['d'][$i]['Diario_DiaChegada']."</td>
            <td>".$dd['d'][$i]['Diario_KMChegada']."</td>
        <td>".$dd['d'][$i]['Diario_Descricao']."</td>
        </tr>
        
    ";

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