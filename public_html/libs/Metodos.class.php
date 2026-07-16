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

	public function verifica_slug($campo, $tabela, $titulo, $titulo_db = null, $condicao = null, $slug = null){

        if($condicao){
            $query = " AND ".$condicao;
        }

        //echo "$titulo-$titulo_db";

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

	public function status($status){
		return ($status)?1:0;
	}

	public function dataDB($date) {
		$foo = explode('/', $date);
		$date = $foo[2]."-".$foo[1]."-".$foo[0];
		return $date;
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

	public function PaginadorFiltro($pg, $fpg, $link, $maxLinks, $total, $busca=null){
        if($busca){
            $b = "_$busca";
        }

        if($total > $fpg){
            $paginas = ceil($total/$fpg);

            $paginador = '<div class="paginacao">
                            <div class="pg_number">';
            for($i = $pg-$maxLinks; $i <= $pg-1; $i++){
                if($i >= 1){
                    $paginador.= '<a href="'.$link.'_'.$i.$b.'">'.$i.'</a>';
                }
            }
            $paginador.= '<a href="'.$link.'_'.$pg.$b.'" class="active">'.$pg.'</a>';
            for($i = $pg+1; $i <= $pg+$maxLinks; $i++){
                if($i <= $paginas){
                    $paginador.= '<a href="'.$link.'_'.$i.$b.'">'.$i.'</a>';
                }
            }
            $paginador.= '  </div>
                          </div>';
        }

        return $paginador;
    }

    public function PaginadorModal($pg, $fpg, $link, $maxLinks, $total, $busca=null){
        if($busca){
            $b = "_$busca";
        }

        if($total > $fpg){
            $paginas = ceil($total/$fpg);

            $paginador = '<div class="paginacao">
                            <div class="pg_number">';
            for($i = $pg-$maxLinks; $i <= $pg-1; $i++){
                if($i >= 1){
                    $paginador.= '<a href="javascript: void(0);" class="pg_modal" id="'.$i.'">'.$i.'</a>';
                }
            }
            $paginador.= '<a href="javascript: void(0);" class="active pg_modal" id="'.$i.'">'.$pg.'</a>';
            for($i = $pg+1; $i <= $pg+$maxLinks; $i++){
                if($i <= $paginas){
                    $paginador.= '<a href="javascript: void(0);" class="pg_modal" id="'.$i.'">'.$i.'</a>';
                }
            }
            $paginador.= '  </div>
                          </div>';
        }

        return $paginador;
    }

    public function PaginadorBusca($pg, $fpg, $link, $maxLinks, $total, $busca=null){
        if($total > $fpg){
            $paginas = ceil($total/$fpg);

            $paginador = '<div class="paginacao">';
            for($i = $pg-$maxLinks; $i <= $pg-1; $i++){
                if($i >= 1){
                    $paginador.= '<a href="'.$link.'_'.$i.$busca.'">'.$i.'</a>';
                }
            }
            $paginador.= '<a href="'.$link.'_'.$pg.$busca.'" class="active">'.$pg.'</a>';
            for($i = $pg+1; $i <= $pg+$maxLinks; $i++){
                if($i <= $paginas){
                    $paginador.= '<a href="'.$link.'_'.$i.$busca.'">'.$i.'</a>';
                }
            }
            $paginador.= '</div>';
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
}
?>
