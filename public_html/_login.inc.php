<?php
if(isset($_POST['logar'])){
    $Generico->Logar($_POST);

    header("Location:".urlBase."painel");
    die;
}