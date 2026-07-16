<?php
class Menu extends Metodos{
	function Menu($Conn){
		$this->Conn = $Conn;
	}

	function MenuBar(){
		$dados = $this->Conn->execReader("SELECT t1.Menu_ID, t1.Menu_Pai_ID, t1.Menu_Titulo, t1.Menu_TituloSlug, t1.Menu_Link, t1.Menu_Icon, t1.Menu_Status, t1.Menu_Desenvolvimento, t2.Menu_Titulo as Menu_TituloPai FROM menu as t1 LEFT JOIN menu as t2 on t2.Menu_ID=t1.Menu_Pai_ID WHERE t1.Menu_Pai_ID=0 AND t1.Menu_Status=1 ORDER BY t1.Menu_Titulo", null, 2);
		foreach ($dados as $i => $dd) {
			$dados[$i]['N2'] = $this->Conn->execReader("SELECT Menu_ID, Menu_Titulo, Menu_TituloSlug, Menu_Link, Menu_Icon, Menu_Desenvolvimento FROM menu WHERE Menu_Pai_ID=? AND Menu_Status=1 ORDER BY Menu_Titulo", array($dd['Menu_ID']), 2);
			if($dados[$i]['N2']){
				foreach ($dados[$i]['N2'] as $j => $dd2) {
					$dados[$i]['N2'][$j]['N3'] = $this->Conn->execReader("SELECT Menu_ID, Menu_Titulo, Menu_Link, Menu_Icon, Menu_Desenvolvimento FROM menu WHERE Menu_Pai_ID=? AND Menu_Status=1 ORDER BY Menu_Titulo", array($dd2['Menu_ID']), 2);
					if($dados[$i]['N2'][$j]['N3']){
						foreach ($dados[$i]['N2'][$j]['N3'] as $k => $dd3) {
							$dados[$i]['N2'][$j]['N3'][$k]['N4'] = $this->Conn->execReader("SELECT Menu_ID, Menu_Titulo, Menu_Link, Menu_Icon , Menu_Desenvolvimento FROM menu WHERE Menu_Pai_ID=? AND Menu_Status=1 ORDER BY Menu_Titulo", array($dd3['Menu_ID']), 2);
							/*if($dados[$i]['N2'][$j]['N3'][$k]['N4']){
								foreach ($dados[$i]['N2'][$j]['N3'][$k]['N4'] as $l => $dd4) {
									$dados[$i]['N2'][$j]['N3'][$k]['N4'][$l]['N5'] = $this->Conn->execReader("SELECT Menu_ID, Menu_Titulo, Menu_Link, Menu_Icon FROM menu WHERE Menu_Pai_ID=? AND Menu_Status=1", array($dd4['Menu_ID']), 2);

								}
							}*/
						}
					}
				}
			}
		}

		/*echo "<pre>";
		print_r($dados);
		die;*/

		return $dados;
	}

	function Lista(){
		return $this->Conn->execReader("SELECT t1.Menu_ID, t1.Menu_Pai_ID, t1.Menu_Titulo, t1.Menu_Link, t1.Menu_Icon, t1.Menu_Status, t1.Menu_Desenvolvimento, t2.Menu_Titulo as Menu_TituloPai FROM menu as t1 LEFT JOIN menu as t2 on t2.Menu_ID=t1.Menu_Pai_ID ORDER BY Menu_ID DESC", null, 2);
	}

	function ListaIE(){
		$dados = $this->Conn->execReader("SELECT t1.Menu_ID, t1.Menu_Pai_ID, t1.Menu_Titulo, t1.Menu_Link, t1.Menu_Icon, t1.Menu_Status, t1.Menu_Desenvolvimento, t2.Menu_Titulo as Menu_TituloPai FROM menu as t1 LEFT JOIN menu as t2 on t2.Menu_ID=t1.Menu_Pai_ID", null, 2);
		foreach ($dados as $i => $dd) {
			$dados[$i]['Menu_Qtd'] = $this->Conn->execReader("SELECT count(Menu_ID) FROM menu WHERE Menu_Pai_ID=?", array($dd['Menu_ID']), 4);
		}

		return $dados;
	}

	function Dados($ID){
		return $this->Conn->execReader("SELECT Menu_ID, Menu_Pai_ID, Menu_Titulo, Menu_Link, Menu_Icon, Menu_Status , Menu_Desenvolvimento FROM menu WHERE Menu_ID=?", array($ID), 3);
	}

	function Grava($dd){
		$this->VerificaToken($dd['token']);

		$status = $this->verifica_status($dd['status']);
		$tituloSlug = $this->titulo_slug($dd['titulo']);
		
		if($dd['desenvolvimento'] == 'on'){
		   $desenvilvimento = 1;
		}else{
		   $desenvilvimento = 0; 
		}

		$params = array($dd['menu_pai'], $dd['titulo'], $tituloSlug, $dd['link'], $dd['icon'], $status, $desenvolvimento);
		$ID = $this->Conn->execWrite("INSERT INTO menu SET
										Menu_Pai_ID=?,
										Menu_Titulo=?,
										Menu_TituloSlug=?,
										Menu_Link=?,
										Menu_Icon=?,
										Menu_Status=?,
										Menu_Desenvolvimento=?,
										Menu_LogInclusao=now()", $params);

		return $ID;
	}

	function Edita($dd, $ID){
		$this->VerificaToken($dd['token']);

		$status = $this->verifica_status($dd['status']);
		$tituloSlug = $this->verifica_slug('Menu_Titulo', 'menu', $dd['titulo'], $ID);
		
		if($dd['desenvolvimento'] == 'on'){
		   $desenvilvimento = 1;
		}else{
		   $desenvilvimento = 0; 
		}

		$params = array($dd['menu_pai'], $dd['titulo'], $tituloSlug, $dd['link'], $dd['icon'], $status, $desenvilvimento, $ID);
		$this->Conn->execWrite("UPDATE menu SET
									Menu_Pai_ID=?,
									Menu_Titulo=?,
									Menu_TituloSlug=?,
									Menu_Link=?,
									Menu_Icon=?,
									Menu_Status=?,
									Menu_Desenvolvimento=?
								WHERE
									Menu_ID=?", $params);
	}

	function Deleta($ID){
		$this->Conn->execWrite("DELETE FROM menu WHERE Menu_ID=?", array($ID));
	}

	function Ativar($ID){

		$dd = $this->Dados($ID);

		if($dd['Menu_Status'] == 0){
			$status = 1;
		}else{
			$status = 0;
		}

		$this->Conn->execWrite("UPDATE menu SET Menu_Status=? WHERE Menu_ID=?", array($status, $ID));

		return $status;
	}

	function Modulos(){
		return $this->Conn->execReader("SELECT
		                                    t1.Menu_ID,
		                                    t1.Menu_Pai_ID,
		                                    t1.Menu_Titulo,
		                                    t1.Menu_Status,
		                                    t1.Menu_Desenvolvimento,
		                                    t2.Menu_Titulo as Menu_TituloPai
		                                FROM
		                                    menu as t1
		                                    LEFT JOIN menu as t2 on t2.Menu_ID=t1.Menu_Pai_ID
		                                WHERE
		                                    t1.Menu_Pai_ID!=0 AND 
		                                    t1.Menu_Status=1
		                                ORDER BY t1.Menu_Titulo", null, 2);
	}

	function dMenu($Link){
		$params = array("$Link%");
		return $this->Conn->execReader("SELECT
		                                    t1.Menu_ID,
		                                    t1.Menu_Pai_ID,
		                                    t1.Menu_Titulo,
		                                    t1.Menu_Status,
		                                    t1.Menu_Desenvolvimento,
		                                    t2.Menu_Titulo as Menu_TituloPai
		                                FROM
		                                    menu as t1
		                                    LEFT JOIN menu as t2 on t2.Menu_ID=t1.Menu_Pai_ID
		                                WHERE
		                                    t1.Menu_Link LIKE ?", $params, 3);
	}
}