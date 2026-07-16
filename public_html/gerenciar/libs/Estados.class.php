<?php
class Estados extends Metodos{
	function Estados($Conn){
		$this->Conn = $Conn;
	}

	function Lista(){
		return $this->Conn->execReader("SELECT
		                                    Estado_ID,
		                                    Estado_UF,
		                                    Estado_Nome
		                                FROM
		                                    estados
		                                WHERE
		                                    Estado_Status=1", null, 2);
	}
}