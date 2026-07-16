
<?php
//------------------- CLASSE SMARTY
include("libs/Smarty.class.php");
//------------------- CONFIG ACESSO BANCO DE DADOS
$servidor = "localhost";
	$usuario = "grupofas_admin";
	$senha = "fassil3017#";
	$dbname = "grupofas_sistema";
	
	//Criar a conex達o
	$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);

