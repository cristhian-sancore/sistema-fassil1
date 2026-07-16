<?php

/*echo "<pre>";
print_r($_SESSION);
die;*/
if(!isset($_SESSION['xxx']['login'])){
    header("Location:".urlBase."");
    die;
}