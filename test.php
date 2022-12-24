<?php

session_start();
$monfichier = 'bdd.txt';
$lecture_fichier = file_get_contents($monfichier);
$tabFavoris = unserialize($lecture_fichier);

var_dump($tabFavoris);


?>