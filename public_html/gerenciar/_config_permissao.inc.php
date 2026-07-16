<?php
$Mod3 = explode('_',$Mod[2])[0];
$Mod3 = explode('.',$Mod3)[0];

$Link = "$Mod[0]/$Mod[1]/$Mod3";
$dMenu = $Menu->dMenu($Link);
$Mod_ID = $dMenu['Menu_ID'];
$_SESSION['Menu_ID'] = $dMenu['Menu_ID'];

/*echo"<pre>";
print_r($_SESSION);
echo $Link;
print_r($dMenu);
die;*/

if (!isset($_SESSION['_user']) || !in_array($Mod_ID."v", $_SESSION['_user']['Permissao_Modulos'])) {
    echo("<script>location.href = '".urlBase."index.php';</script>");
}