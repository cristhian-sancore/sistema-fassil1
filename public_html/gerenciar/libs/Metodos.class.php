<?php

class Metodos{

	public function titulo_slug($string){
		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
		$b = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
		$string = utf8_decode($string);
		$string = strtr($string, utf8_decode($a), $b);
		$string = strip_tags(trim($string));
		$string = str_replace(" ","-",$string);
		$string = str_replace(array("-----","----","---","--"),"-",$string);
		return strtolower(utf8_encode($string));
	}

	public function verifica_slug($campo, $tabela, $titulo, $ID){

		$dd = $this->Dados($ID);

		if($dd[$campo] != $titulo){
			$params = array($titulo);
			$total = $this->Conn->execReader("SELECT count(*) FROM $tabela WHERE $campo=?", $params, 4);

			if($total > 0){
				$verificador = $total + 1;
				$Titulo_Slug = $this->titulo_slug($titulo)."-".$verificador;
			}else{
				$Titulo_Slug = $this->titulo_slug($titulo);
			}
		}else{
			$Titulo_Slug = $this->titulo_slug($titulo);
		}

		return $Titulo_Slug;
	}

	public function verifica_slug_new($campo, $tabela, $titulo, $titulo_db = null, $condicao = null, $slug = null){

		if($condicao){
			$query = " AND ".$condicao;
		}

		//echo "$titulo - $titulo_db";

		if($titulo_db != $titulo){
			$params = array($titulo);
			$total = $this->Conn->execReader("SELECT count(*) FROM $tabela WHERE $campo=? $query", $params, 4);

			if($total > 0){
				$verificador = $total + 1;
				$Titulo_Slug = $this->titulo_slug($titulo)."-".$verificador;
			}else{
				$Titulo_Slug = $this->titulo_slug($titulo);
			}
		}else{
			$Titulo_Slug = $slug;
		}

		return $Titulo_Slug;
	}

	public function verifica_status($status){
		return ($status)?1:0;
	}

	public function dataDB($date) {
		if($date){
			$foo = explode('/', $date);
			$date = $foo[2]."-".$foo[1]."-".$foo[0];

			return $date;
		}
	}

	public function datahoraDB($date) {
		$foo = explode(' ', $date);

		$d = explode('/', $foo[0]);
		$date = $d[2]."-".$d[1]."-".$d[0];

		return $date." ".$foo[1];
	}

	public function dataForm($date) {
		$foo = explode('-', $date);
		$date = $foo[2]."/".$foo[1]."/".$foo[0];
		return $date;
	}

	public function formataMoeda($valor) {
		return number_format($valor, 2, ',', '.');
	}

	public function desformataMoeda($valor) {
		return str_replace(',','.', str_replace('.','', $valor));
	}

	public function Paginador($pg, $fpg, $link, $maxLinks, $total, $buscaPage){
        if($total > $fpg){
            $paginas = ceil($total/$fpg);

            $paginador = '<div class="box-footer clearfix">
                            <ul class="pagination pagination-sm no-margin pull-right">';
            $paginador.= '<li><a href="'.$link.'?pg=1'.$buscaPage.'">««</a></li>';
            for($i = $pg-$maxLinks; $i <= $pg-1; $i++){
                if($i >= 1){
                    $paginador.= '<li><a href="'.$link.'?pg='.$i.$buscaPage.'">'.$i.'</a></li>';
                }
            }
            $paginador.= '<li class="active"><a href="'.$link.'?pg='.$pg.$buscaPage.'">'.$pg.'</a></li>';
            for($i = $pg+1; $i <= $pg+$maxLinks; $i++){
                if($i <= $paginas){
                    $paginador.= '<li><a href="'.$link.'?pg='.$i.$buscaPage.'">'.$i.'</a></li>';
                }
            }
            $paginador.= '<li><a href="'.$link.'?pg='.$paginas.$buscaPage.'">»»</a></li>';
            $paginador.= '  </ul>
                          </div>';
        }

        return $paginador;
    }

	function CalculaDif($d1, $d2){
		$date_time  = new DateTime($d1);
		$diff = $date_time->diff(new DateTime($d2));
		//echo $diff->format( '%y year(s), %m month(s), %d day(s), %H hour(s), %i minute(s) and %s second(s)' );

		$resultado = null;
		if($diff->format('%d'))
			$resultado .= $diff->format('%d dia(s) ');

		if($diff->format('%H')) {
				$resultado .= $diff->format('%H:');
		}else{
			$resultado .= '00';
		}

		if($diff->format('%i')){
			if($diff->format('%i') < 10) {
				$resultado .= "0".$diff->format('%i');;
			}else{
				$resultado .= $diff->format('%i');;
			}
		}

		return $resultado;
	}

	function VerificaToken($token){
		if(base64_decode($token) != $_SESSION['token']){
			echo "Ops. Token inválido :(";
			header('Refresh: 3;');
			die;
		}
	}

	function Mes($id){
		$mes = null;
        switch ($id) {
            case "1":    $mes = 'Janeiro';     break;
            case "2":    $mes = 'Fevereiro';   break;
            case "3":    $mes = 'Março';       break;
            case "4":    $mes = 'Abril';       break;
            case "5":    $mes = 'Maio';        break;
            case "6":    $mes = 'Junho';       break;
            case "7":    $mes = 'Julho';       break;
            case "8":    $mes = 'Agosto';      break;
            case "9":    $mes = 'Setembro';    break;
            case "10":   $mes = 'Outubro';    break;
            case "11":   $mes = 'Novembro';   break;
            case "12":   $mes = 'Dezembro';   break;
        }

        return $mes;
	}
}
?>
