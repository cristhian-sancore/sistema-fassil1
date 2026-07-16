<!--**
 * @author Cesar Szpak - Celke -   cesar@celke.com.br
 * @pagina desenvolvida usando framework bootstrap,
 * o código é aberto e o uso é free,
 * porém lembre -se de conceder os créditos ao desenvolvedor.
 *-->
 <?php
	session_start();
	include_once('conexao.php');
    ?>
<!DOCTYPE html>
    <html lang="pt-br">
    <head>
    <meta charset="utf-8"/>

		<title>Inscrições</title>
	<head>
	<body>
		<?php
		
		$codigo = (isset($_REQUEST['c']))?$_REQUEST['c']:null;
		$nome = (isset($_REQUEST['n']))?$_REQUEST['n']:null;
		$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;
		$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;
		$entidade = (isset($_REQUEST['e']))?$_REQUEST['e']:null;
		$cidade = (isset($_REQUEST['cid']))?$_REQUEST['cid']:null;
		
		if($codigo){
		$buscas.="AND (t1.Inscricao_ID=$codigo)";
		}
		
		if($nome){
		$buscas.="AND (t1.Inscricao_Nome LIKE '%$nome%')";
		}
		
		if($busca){
		$buscas.="AND (t1.Inscricao_CursoID=$busca)";
		}
		if($cidade){
		$buscas.="AND (t2.Cidade_ID=$cidade)";
		}
		
		if($status){
			$s = 1;
			if($status==2){
				$s = 0;
			}
			$buscas.= " AND (t1.Inscricao_Status=$s)";
		}
		
		if($entidade){
			$e = 0;
			
			if($entidade==1){
				$e = 'Prefeitura Municipal';
			}elseif($entidade==2){
			 $e = 'Câmara Municipal';
			}elseif($entidade==3){
			 $e = 'Previdência Municipal';
			}elseif($entidade==4){
			 $e = 'Fundação, Consórcio e demais entidades';
			}

			$buscas.= " AND (t1.Inscricao_Entidade LIKE '%$e%')";
		}
		
		// Definimos o nome do arquivo que será exportado
		$arquivo = 'inscricoes.xls';
		
		// Criamos uma tabela HTML com o formato da planilha
		$html = '';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="10" style="text-align:center;font-size:36px">Planilha de Inscrições</tr>';
		$html .= '</tr>';
		
		
		$html .= '<tr>';
		$html .= '<td><b>ID</b></td>';
		$html .= '<td><b>Nome</b></td>';
		$html .= '<td><b>E-mail</b></td>';
		$html .= '<td><b>Contato</b></td>';
		$html .= '<td><b>Cidade</b></td>';
		$html .= '<td><b>Entidade</b></td>';
		$html .= '<td><b>Setor</b></td>';
		$html .= '<td><b>Cargo</b></td>';
		$html .= '<td><b>Status</b></td>';
		$html .= '<td><b>Data</b></td>';
		$html .= '</tr>';
		
		//Selecionar todos os itens da tabela 
		$result_msg_contatos = "SELECT t1.*,
		                                t2.Cidade_ID,
		                                t2.Cidade_Nome,
		                                t2.Cidade_UF
		                            FROM cursos_inscricoes as t1
		                            left join cidades as t2 on (t2.Cidade_ID=t1.Inscricao_Cidade)
		                            WHERE 1=1 $buscas";
		                            
		$resultado_msg_contatos = mysqli_query($conn , $result_msg_contatos);
		
		while($row_msg_contatos = mysqli_fetch_assoc($resultado_msg_contatos)){
			$html .= '<tr>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_ID"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_Nome"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_Email"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_Contato"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Cidade_Nome"]." - ".$row_msg_contatos["Cidade_UF"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_Entidade"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_Setor"].'</td>';
			$html .= '<td>'.$row_msg_contatos["Inscricao_Cargo"].'</td>';
			if($row_msg_contatos["Inscricao_Status"] == 0){
			    $row_msg_contatos["Inscricao_Status"] = 'Pendente';
			}
			if($row_msg_contatos["Inscricao_Status"] == 1){
			    $row_msg_contatos["Inscricao_Status"] = 'Pago';
			}
			$html .= '<td>'.$row_msg_contatos["Inscricao_Status"].'</td>';
			$data = date('d/m/Y H:i:s',strtotime($row_msg_contatos["Inscricao_DataInclusao"]));
			$html .= '<td>'.$data.'</td>';
			$html .= '</tr>';
		;
		}
		// Configurações header para forçar o download
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: application/x-msexcel");
		header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
		header ("Content-Description: PHP Generated Data" );
		// Envia o conteúdo do arquivo
		echo $html;
		exit; ?>
	</body>
</html>