<?php 
    //on supprime le cookie de connexion, supprime la variable login puis
    //redirige l'utilisateur vers index.php
    session_start();
    if (isset($_COOKIE['PHPSESSID'])) {
        unset($_COOKIE['PHPSESSID']); 
        setcookie('PHPSESSID', null, time() - 3600); 
    }
    unset($_SESSION['login']);
    header('Location: index.php');
?>