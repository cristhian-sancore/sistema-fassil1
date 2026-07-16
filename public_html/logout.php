<?php
include("_config.inc.php");

session_start();
unset($_SESSION['xxx']);

header("Location:".urlBase."");
?>