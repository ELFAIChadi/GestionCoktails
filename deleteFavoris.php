<?php
require_once("include/Donnees.inc.php");
require_once ("tableauFavoris.php");
//require_once ("index.php");
session_start();
$monfichier = 'favoris.txt';
$lecture_fichier = file_get_contents($monfichier);
$tabFavoris = unserialize($lecture_fichier);

$fichierFavCo = 'favorisConnecte.txt';
$lecture_fichier3 = file_get_contents($fichierFavCo);
$tabFavCo = unserialize($lecture_fichier3);

if(isset($_SESSION['login'])){
    $pseudo = $_SESSION['login'];
}

function estSoumis()
{
    if (isset($_GET['cle'])) return true;
    return false;
}
function estConnecter(){
    if(isset($_SESSION['login'])){
        if($_SESSION['login'])
            return true;
    }
    else return false;
}
if(isset($_GET['cle']))
    $cle = $_GET['cle'];
else $cle = null;

$tab = array();

if(estConnecter()) {
    if(estSoumis()) {
        foreach ($tabFavCo as $key => $value) {
            if ($key === $pseudo) {
                $tab = $value;
                echo array_search($cle, $tab);

                unset($tab[array_search($cle, $tab)]);
            }
        }
        $tabFavCo[$pseudo] = $tab;
    }
}
else {
    if(estSoumis()) {

        unset($tabFavoris[array_search($cle, $tabFavoris)]);
    }
}


var_dump($tabFavoris);

file_put_contents($fichierFavCo, serialize($tabFavCo));

file_put_contents($monfichier, serialize($tabFavoris));
