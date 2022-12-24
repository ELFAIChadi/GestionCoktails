<?php
require_once("include/Donnees.inc.php");
require_once ("tableauFavoris.php");
session_start();

$monfichier = 'favoris.txt';
$lecture_fichier = file_get_contents($monfichier);
$tabFavoris = unserialize($lecture_fichier);



$fichierFavCo = 'favorisConnecte.txt';
$lecture_fichier3 = file_get_contents($fichierFavCo);
$tabFavCo = unserialize($lecture_fichier3);

$_COOKIE['fav'] = $tabFavoris;
setcookie('fav', null);

if(isset($_GET['cle']))
    $cle = $_GET['cle'];
else $cle = null;




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
$tab = array();

    if(estConnecter()) {
       if(estSoumis()) {

           if (count($tabFavoris) == 0) {
               echo "tableau vide";
                   if (!array_key_exists($pseudo, $tabFavCo)) {
                       array_push($tab, $cle);
                       var_dump($tab);
                       $tabFavCo[$pseudo] = $tab;
                       $tabFavoris = $tab;

                   }else {
                       foreach ($tabFavCo as $key => $values){
                           if($key === $pseudo){
                               if(!in_array($cle, $values)){
                                   $tab = $values;
                                   array_push($tab, $cle);
                                   var_dump($tab);
                                   $tabFavCo[$pseudo] = $tab;
                                   $tabFavoris = $tab;
                               }
                           }

                       }
                   }
               }else{
                    $tab = $tabFavoris;
               if (!array_key_exists($pseudo, $tabFavCo)) {
                   array_push($tab, $cle);
                   var_dump($tab);
                   $tabFavCo[$pseudo] = $tab;
                   $tabFavoris = $tab;

               }
               else{
                   foreach ($tabFavCo as $key => $values){
                       if($key === $pseudo) {
                           foreach($tab as $key2 => $valTab){
                               if(in_array($valTab, $values)) unset($tab[$key2]);

                       }
                           $tab = array_merge($tab, $values);
                           if(!in_array($cle, $values)) {
                               array_push($tab, $cle);
                               $tabFavCo[$pseudo] = $tab;
                               $tabFavoris = $tab;

                           }


                       }
                       }

               }




           }




       }
    }
    else {

        if (estSoumis()) {
            // echo $_SESSION['fil'];
            echo "marche";
            if (!in_array($cle, $tabFavoris)) {
                array_push($tabFavoris, $cle);
                echo "déja aimé";
            } else echo "marche pas";
        }
    }



  // var_dump($tabFavoris);
setcookie('fav', null, -10);

if(!isset($_COOKIE['fav'])){
    foreach ($tabFavoris as $key => $value){
        unset($tabFavoris[$key]);
    }
}
file_put_contents($monfichier, serialize($tabFavoris));
//file_put_contents($bdd, serialize($tabBdd));
file_put_contents($fichierFavCo, serialize($tabFavCo));














?>