<?php

$getUrl = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
$setUrl = (empty($getUrl)?'home':$getUrl);
$Url = explode('/', $setUrl);

// if ((explode('.', $_SERVER['HTTP_HOST'])[0] != 'www') && $_SERVER['SERVER_NAME'] != 'localhost'){
//     header("Location:http://www.grupofassil.com.br/");
// }

$n1 = (empty($Url[0])?null:$Url[0]);
$n1_p = explode("_", $n1);
$n1_p = $n1_p[0];
$n2 = (empty($Url[1])?null:$Url[1]);
$n2_p = explode("_", $n2);
$n2_p = $n2_p[0];
$n3 = (empty($Url[2])?null:$Url[2]);
$n3_p = explode("_", $n3);
$n3_p = $n3_p[0];
$n4 = (empty($Url[3])?null:$Url[3]);

$nav = array(
    'n1' => $n1, 'n1_p' => $n1_p,
    'n2' => $n2, 'n2_p' => $n2_p,
    'n3' => $n3, 'n3_p' => $n3_p
);

// echo "<pre>";
// print_r($nav);
// die();



$paginas = array('clientes', 'fale-conosco', 'sobre-nos', 'downloads', 'cadastro', 'logout', '404', 'equipe');

if(isset($n1) && in_array($n1_p, $paginas)){
    require "$n1_p.php";
}elseif(isset($n1) && $n1=='home'){
    require "home.php";
}elseif(isset($n1_p) && $n1_p=='recuperar-senha') {
    if(isset($n1) && $n2==''){
        require "recuperar-senha.php";
    }elseif(isset($n1) && $n2){
        require "alterar-senha.php";
    }else{
        require "404.php";
    }
}elseif(isset($n1_p) && $n1_p=='confirmacao-ticket') {
    if (isset($n2) && $n2 && $n3) {
        require "confirmacao-ticket.php";
    } else {
        require "404.php";
    }
}elseif(isset($n1_p) && $n1_p=='galeria-de-fotos') {
    if(isset($n1) && $n2==''){
        require "galeria_lista.php";
    }elseif(isset($n1) && $n2){
        require "galeria.php";
    }else{
        require "404.php";
    }
}elseif(isset($n1_p) && $n1_p=='painel'){
    if(empty($n2)){
        require "painel.php";
    }elseif(isset($n2) && $n2=='ticket' && $n3==''){
        require "ticket.php";
    }elseif(isset($n2) && $n2=='ticket' && $n3){
        require "ticket_view.php";
    }elseif(isset($n2_p) && $n2_p=='notificacoes' && $n3==''){
        require "notificacoes.php";
    }elseif(isset($n2) && $n2=='meus-dados' && $n3==''){
        require "meus-dados.php";
    }else{
        require "404.php";
    }
}elseif(isset($n1) && $n1=='404'){
    require "404.php";
}else{
    require "404.php";
}