<?php
session_start();

// Suppression des cookies de connexion automatique
setcookie('pseudo','',time()+60*60*24*30,'/','labeautedunet.fr',true,true);
setcookie('pass','',time()+60*60*24*30,'/','labeautedunet.fr',true,true);

// Suppression des variables de session et de la session
$_SESSION = array();
session_destroy();

header('Location: http://www.labeautedunet.fr');

?>