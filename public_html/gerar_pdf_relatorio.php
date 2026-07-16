<?php
ob_start();
if($_SERVER['SERVER_NAME']!='localhost') {
    //------------------- CAMINHO SESSION SERVIDOR
    session_save_path("/home/grupofas/tmp");
}

$diretorio = "gerenciar/";
include($diretorio."_config_db.inc.php");
include($diretorio."mpdf60/mpdf.php");

include($diretorio."libs/Sessao.class.php");
include($diretorio."libs/Database.class.php");
include($diretorio."libs/Metodos.class.php");
include($diretorio."libs/OrdemServicos.class.php");
include($diretorio."libs/Relatorios.class.php");

$Conn = new Sessao();
$Conn = new Database();
$OrdemServicos = new OrdemServicos($Conn);
$Relatorios = new Relatorios($Conn);

$id = (isset($_REQUEST['id']))?strip_tags(trim($_REQUEST['id'])):0;
$timbre = (isset($_REQUEST['t']))?strip_tags(trim($_REQUEST['t'])):1;
$msg = (isset($_REQUEST['m']))?strip_tags(trim($_REQUEST['m'])):0;

$dc = $OrdemServicos->Dados($id, true);
$dm = $OrdemServicos->Mensagens($id);
$dr = $Relatorios->Dados($id);

/*VERIFICA SE PODE VISUALIZAR O RELATORIO*/
$dd = array(
    'Protocolo' => $dc['OS_Protocolo'],
    'Cliente_ID' => $_SESSION['xxx']['login']['Cliente_ID']
);
$dv = $OrdemServicos->VerificaOS($dd);
if(!$dv){
    echo  "<script type='text/javascript'>window.close();</script>";
    die;
}


$corpo = "
    <div class='corpo'>
	    <h1>RELATÓRIO DE ATENDIMENTO</h1>
	    <div class='cabecalho'>
	        <p>ENTIDADE: <span>".$dc['CE_Nome']." de ".$dc['Cidade_Nome']."</span></p>
	        <p>SOLICITANTE: <span>".$dc['Cliente_Nome']."</span></p>
	        <p>SETOR DE ATENDIMENTO: <span>".$dc['Grupo_Nome']."</span></p>
	        <p>TÉCNICO: <span>".$dc['Usuario_Nome']."</span></p>
	        <p>PROTOCOLO: <span>".$dc['OS_Protocolo']."</span></p>
	        <p>DATA DE ATENDIMENTO:</p>
	        <p style='margin-left:30px;'>Início: <span>".$dc['OS_DataInicio']." às ".$dc['OS_HorasInicio']."</span></p>
	        <p style='margin-left:30px;'>Término: <span>".$dc['DataFechamento']."</span></p>
	        <span>TELEFONE: <b>(65) 3251-3017</b></span><br>
        </div>
        
        <div class='conteudo'>
            <h2>1. INTRODUÇÃO</h2>
            <p>".$dr['Relatorio_Introducao']."</p>
            
            <h2>2. DESCRIÇÕES DOS SERVIÇOS EXECUTADOS</h2>
            <p>".$dr['Relatorio_Descricao']."</p>
            
            <h2>3. RESULTADOS ALCANÇADOS</h2>
            <p>".$dr['Relatorio_Resultado']."</p>
            
            <h2>4. ORIENTAÇÕES</h2>
            <p>".$dr['Relatorio_Orientacao']."</p>
            
            <h2>5. OBSERVAÇÕES</h2>
            <p>".$dr['Relatorio_Observacao']."</p>
            
            <h2>6. CONCLUSÃO</h2>
            <p>".$dr['Relatorio_Conclusao']."</p>
        </div>
";

$assinaturas = "
        <div class='box_assinaturas'>
            <p>Deixo aos cuidados do mesmo a disponibilidade de contato via celular, telefone fixo, e-mail e estarei disponível para qualquer dúvida.</p>
            <h3>".$dc['Cidade_Nome']."/".$dc['Estado_UF'].", ".$dc['OS_DataInicio'].".</h3>
            
        <div class='assinaturas'>
            <h2>".$dc['Usuario_Nome']."</h2>
            <span>".$dc['Usuario_Cargo']."</span>
        </div>
        
        <p align='center' style='margin-top:50px;'>De acordo, assinam abaixo:</p>
";

if($dr['fe']) {
    for($i=0; $i < count($dr['fe']); $i++) {
        $assinaturas .= "  
            <div class='assinaturas'>
                <h2>".$dr['fe'][$i]['Cliente_Nome']."</h2>
                <span>".$dr['fe'][$i]['Cliente_Cargo']."</span>
            </div>
        ";
    }
};

$corpo .= "            
        </div>
    </div>
";

$rodape = "
    <div class='rodape'>
	    <p align='right'>Página | {PAGENO}</p>
    </div>
";

$mensagens = "
    <div class='mensagens'>
        <h1>HISTÓRICO DE MENSAGENS</h1>
        <p></p>
    </div>
";

for($i=0; $i < count($dm); $i++) {
    if(!$dm[$i]['Msg_Recebimento']) {
        if($dm[$i-1]['Msg_Recebimento']!=$dm[$i]['Msg_Recebimento']) {
            $mensagens .= "
            <span class='cliente'>".$dc['Cliente_Nome']."</span>
            ";
        }
        $mensagens .= "
            <div class='linha'>
                <div class='linha_msg1'>".$dm[$i]['Msg_Conteudo']."
                    <div class='linha_anexo'>

        ";
        for($j=0; $j < count($dm[$i]['Anexos']); $j++) {
            $mensagens .= "
                        <span>".$dm[$i]['Anexos'][$j]['Anexo_Nome']."</span>
            ";
        }
        $mensagens .= "
                    </div>
                    <div class='linha_data1'>".$dm[$i]['Msg_Data']."</div>
                </div>
            </div>
        ";
    }else{
        if($dm[$i-1]['Msg_Recebimento']!=$dm[$i]['Msg_Recebimento']) {
            $mensagens .= "
            <p class='user'>".$dc['Usuario_Nome']."</p>
            ";
        }
        $mensagens .= "
            <div class='linha'>
                <div class='linha_msg2'>".$dm[$i]['Msg_Conteudo']."
                    <div class='linha_anexo'>
        ";
        for($j=0; $j < count($dm[$i]['Anexos']); $j++) {
            $mensagens .= "
                        <span>".$dm[$i]['Anexos'][$j]['Anexo_Nome']."</span>
            ";
        }
        $mensagens .= "
                    </div>
                    <div class='linha_data2'>".$dm[$i]['Msg_Data']."</div>
                </div>
            </div>
        ";
    }
}

/*GERA PDF*/
$mpdf = new mPDF('',    // mode - default ''
                'A4',    // format - A4, for example, default ''
                0,     // font size - default 0
                'Arial',    // default font family
                15,    // margin_left
                15,    // margin right
                10,    // margin top
                18,    // margin bottom
                6,     // margin header
                9,     // margin footer
                'L');
$css = file_get_contents($diretorio."css/relatorio.css");
$mpdf->WriteHTML($css,1);
$mpdf->WriteHTML($corpo);
$mpdf->SetHTMLFooter($rodape);
if(count($dm) > 0) {
    $mpdf->AddPage();
    $mpdf->WriteHTML($mensagens);
}
$mpdf->AddPage();
$mpdf->WriteHTML($assinaturas);
$arquivo = $dc['CE_Nome']." ".date("d-m-Y").'.pdf';
$mpdf->Output($arquivo,'I');
//D - download

header("Content-Type: application/pdf");
header('Content-Disposition: inline; "'.$arquivo.'"');

exit;