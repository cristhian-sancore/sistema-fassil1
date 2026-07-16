<?
include("_config.inc.php");

session_start();
unset($_SESSION['_user']);

echo("<script>location.href = 'index.php';</script>");
?>