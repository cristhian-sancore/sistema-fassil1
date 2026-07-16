<?php
$token = crypt("Token",substr(md5(uniqid(rand(0, 1000000))), 0, 16));
$_SESSION['token'] = $token;
$_SESSION['token_c'] = base64_encode($token);
/*echo "Token:".$_SESSION['token']."<br>Token Criptografado: ".base64_encode($_SESSION['token']);
die;*/