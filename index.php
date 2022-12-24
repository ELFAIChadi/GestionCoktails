
<?php
session_start();

if(isset($_SESSION['login'])){
    $pseudo = $_SESSION['login'];
}

if(!isset($_GET['nom'])) {
    $fil[] = null;
    $nom = null;
}
else {
    $fil = $_SESSION['fil'];
    $fil[] = $_GET['nom'];
        $nom = $_GET['nom'];

}

if(isset($_POST['recherche_cocktail'])){ // verification si une recherche a été soumise
    $rechercheCocktail = $_POST['recherche_cocktail'];
}else $rechercheCocktail = "null";

if(!isset($_GET['titre'])){
    $titreCocktail = null;
}
else {
    $titreCocktail = $_GET['titre'];
}



function estConnecter(){
    if(isset($_SESSION['login'])){
        if($_SESSION['login'])
            return true;
    }
    else return false;
}
if(!estConnecter())
    if (isset($_GET['nom'])) {
        $nbElement = count($fil);
        if (in_array($_GET['nom'], $_SESSION['fil'])) {
            array_splice($fil, $nbElement);
            $recherche = array_search($_GET['nom'], $fil);
            for ($nbElement - 1; $nbElement > $recherche; $nbElement--) {
                array_splice($fil, $nbElement);
            }
        }
    }



//verifie si l'utilisateur est connecté
if(!isset($_SESSION['login'])) $isConnect = 'no';
if(isset($_SESSION['login'])) $isConnect = 'yes';

//on récupère la base de donnée
$monfichier = 'bdd.txt';
$lecture_fichier = file_get_contents($monfichier);
$bdd = unserialize($lecture_fichier);

//si l'utilisateur n'est pas connecté, on verifie si il a soumis un login et un mot de 
//passe pour faire une tentative de connexion
if($isConnect ==='no'){
  if(isset($_POST['login'])){
    for($i=0; $i < sizeof($bdd); $i++){
      //le login et le mot de passe correspondent
      if($_POST['login'] === $bdd[$i]['login'] && $_POST['password'] === $bdd[$i]['pass']){
        $isConnect = 'yes';
        $_SESSION['login'] = $_POST['login'];
      }
    }
  }
}

function tableauHierarchieRecherche($nomAliment,$tabHierarchie, $tabRecette){
    $titre = null;
    if (($nomAliment != "")){
        $i = 0;
        foreach ($tabRecette as $key1 => $value) {

            if (is_array($value)) {
                foreach ($value['index'] as $key => $value2) {
                    if (SuperAlimentDe($nomAliment, $tabHierarchie, $value2) == 1) {
                        $titre[$i] = $value['titre'];
                        }
                    }

                }
                $i++;


        }
    }else echo "";


   return $titre;


}

function afficheCocktailRecherche($recherche, $tabHierarchie, $tabRecette){
    $tabRecherche = indicationRecherche($recherche, $tabHierarchie);
    $tableauIntermediaire = array();
    $tableauCocktail = array();
    $tableauFusionner = array_merge($tabRecherche['souhaite'], $tabRecherche['nonSouhaite']);
    $tableauFusionner = array_unique($tableauFusionner);


    if($tabRecherche['souhaite'] == null && $tabRecherche['nonSouhaite'] == null){
        echo "Problème dans votre requête : recherche impossible";
    }else {
        foreach ($tableauFusionner as $tab) {
            $tableauIntermediaire[] = tableauHierarchieRecherche($tab, $tabHierarchie, $tabRecette);
        }


        for ($i = 0; $i < count($tableauIntermediaire); $i++) {
            $tableauCocktail = array_merge($tableauCocktail, $tableauIntermediaire[$i]);
        }

        $tableauCocktail = array_unique($tableauCocktail);

        foreach ($tabRecette as $key => $values) {
            foreach ($tableauCocktail as $cocktail) {
                if ($cocktail === $values['titre']) {
                    $nbAliment = 0;
                    foreach ($values['index'] as $ingredients) {
                        if ($tabRecherche['souhaite'] != null && $tabRecherche['nonSouhaite'] != null) {

                            foreach ($tabRecherche['souhaite'] as $ingSouhaite) {
                                if (in_array($ingSouhaite, $values['index']) || SuperAlimentDe($ingSouhaite, $tabHierarchie, $ingredients) == 1 ) {
                                    $nbAliment = 1;
                                }

                            }
                            foreach ($tabRecherche['nonSouhaite'] as $ingNonSouhaite) {
                                if (!in_array($ingNonSouhaite, $values['index'])) {
                                    $nbAliment += 1;
                                }

                            }
                            $pourcentage = $nbAliment / (count($tabRecherche['souhaite']) + count($tabRecherche['nonSouhaite']));
                        } else if ($tabRecherche['souhaite'] != null && $tabRecherche['nonSouhaite'] == null) {
                            foreach ($tabRecherche['souhaite'] as $ingSouhaite) {
                                if (in_array($ingSouhaite, $values['index']) || SuperAlimentDe($ingSouhaite, $tabHierarchie, $ingredients) == 1) {
                                    $nbAliment++;
                                }
                                $pourcentage = $nbAliment / count($tabRecherche['souhaite']);

                            }

                        } else if ($tabRecherche['souhaite'] == null && $tabRecherche['nonSouhaite'] != null) {
                            foreach ($tabRecherche['nonSouhaite'] as $ingNonSouhaite) {
                                if (!in_array($ingNonSouhaite, $values['index'])) {
                                    $nbAliment++;
                                }
                                $pourcentage = $nbAliment / count($tabRecherche['nonSouhaite']);

                            }

                        }
                    }
                    if($pourcentage > 1) $pourcentage = 1;
                    affichageCoktail($cocktail, $tabRecette, $pourcentage);

                }
            }


        }

    }
}

function afficheRecherche($recherche, $tabHierarchie){ //fonction qui permet d'indiquer les aliment souhaité, non souhaité ou non reconnu
    $tabRecherche = indicationRecherche($recherche, $tabHierarchie);
    if(substr_count($recherche, '"') % 2 == 1) { // si la recherche présente un nombre impair de cote alors on envoie un message d'Erreur
        echo "Problème de syntaxe dans votre requête : nombre impair de double-quotes";
    }else {
        foreach ($tabRecherche as $key => $values) { // on parcourt le tableau créer précedemment et on va afficher à l'écran selon la recherche si l'aliment est reconnu ou pas


            if ($key === "souhaite") {
                if ($values != null) { // on vérifie d'Abord si la valeur n'est pas null
                    echo "Liste des aliments souhaités :";
                    foreach ($values as $value) {
                        echo $value . ", ";
                    }
                }
            } else if ($key === "nonSouhaite") {
                if ($values != null) {
                    echo "</br>" . "Liste des aliments non souhaités :";
                    foreach ($values as $value) {
                        echo $value . ", ";
                    }
                }
            } else if ($key === "nonReconnu") {
                if ($values != null) {
                    echo "</br>" . "Élement non reconnus dans la requête :";
                    foreach ($values as $value) {
                        echo $value . ", ";
                    }
                }
            }

        }
    }

}
function indicationRecherche($recherche , $tabCocktail){ //  retourne le tableau des aliment souhaité ou non et des éléments non reconnu de la recherche

    $tabIntermediaire = array();// tableau intermediaire qui va nous permettre de stocker les différentes valeur de la recherche


    $recherche = trim($recherche);

     if(substr_count($recherche, '"') == 0){ // si il n'y a pas de "" dans la recherche on sépare la recherche dans un tableau
        $alimentSepare = explode(' ', $recherche); // on sépare les différents mots ou il y a un espace dans un tableau
        foreach ($alimentSepare as $al){
                array_push($tabIntermediaire, $al); // on stock dans notre tableau Intermediaire les valeurs un par un trouvées précedemment
        }
    }else if (substr_count($recherche, '"') % 2 == 0){ // sinon il contien des "" pair
        $alimentSepare = explode('"', $recherche); // on sépare tout d'abord dans un tableau les mots avec "" des autres
        foreach ($alimentSepare as $al) { // on parcourt ce tableau
            if(!empty($al)) { // si la valeur n'est pas vide
                if (($al[0] == ' ')){ // si le premier caractère est un espace
                    $al = trim($al); // on efface cette espace
                    if(($al[0] == '+') || ($al[0] == 0) || (strpos($al, ' ') != false)) { // si le premier caractère est un + ou un - ou si il contient un espace ce qui veut dire qu'il y a plusieurs requete
                        $nvlChaine = explode(' ', $al); // on sépare dans un tableau lorsqu'il y a un espace
                        foreach ($nvlChaine as $ch) {
                            $ch = trim($ch);
                            array_push($tabIntermediaire, $ch); // on ajoute les valeurs dans le tableau Intermédiaire
                        }
                    }
                }
                else { // sinon si il necontient pas d'Espace en premier caractère
                    array_push($tabIntermediaire, $al); // on ajoute la valeur dans le tableau intérmédiaire

                    for($i = 0; $i<strlen($al); $i++){ // on parcourt la chaine
                        if ($al[$i] == '+' || $al[$i] == '-'){ // si il contient un - ou un +
                            $nvlChaine = explode(' ', $al); // on separe la chaine
                            foreach ($nvlChaine as $ch){
                                $ch = trim($ch);
                                array_push( $tabIntermediaire, $ch); // on met la valeur dans le tableau intermédiaire
                            }
                            unset($tabIntermediaire[array_search($al,$tabIntermediaire)]);
                            break; // on sort de la boucle
                        }


                    }


                }

            }
        }


    }
        foreach ($tabIntermediaire as  $key => $tab){ // on parcourt le tableau intermédiaire
            if($tab == '-' || $tab == '+'){  // si il y'a un - ou un + isolé dans une colonne
                $tabIntermediaire[$key +1] = $tab.$tabIntermediaire[$key +1]; // alors on le concaténe au suivant
                unset($tabIntermediaire[$key]);// on supprime le - ou un + isolé
            }
        }
        $tableauSouhaiter = array();
        $tableauNonSouhaiter = array();
        $tableauNonReconnu = array();

        foreach ($tabIntermediaire as $tab){ // ensuite on stock les valeurs de la recherche selon le symbole - ou + ou vide

            if($tab[0] == '-'){ // si le premier caractère est un - donc liste non souhaité
                $tab = substr($tab,1); // on efface le premier caractère
                if(existeCocktail($tab, $tabCocktail)){ // si se cocktail existe
                    array_push($tableauNonSouhaiter, $tab); // on le met dans le tableau non souhaité
                }
                else{
                    array_push($tableauNonReconnu, $tab); // sinon dans le tableau non reconnu
                }
            }else if($tab[0] == '+'){ // si le premier caractère est un +
                $tab = substr($tab,1); // on supprime le +
                if(existeCocktail($tab, $tabCocktail)){ // si le cocktail existe
                    array_push($tableauSouhaiter, $tab);// on le met dans le tableau  souhaité
                }
                else{
                    array_push($tableauNonReconnu, $tab);// sinon dans le tableau non reconnu
                }

            }else { // sinon il n y a pas de - ou + donc il s'agit quand même d'un ajout
                if(existeCocktail($tab, $tabCocktail)){ // si le cocktail existe
                    array_push($tableauSouhaiter, $tab); // on le met  dans le tableau souhaité
                }
                else{
                    array_push($tableauNonReconnu, $tab); // sinon dans le tableau non reconnu
                }

            }
        }




      $tabRecherche= array ( // on créer un tableau qui va être le retour de la fonction où on va stocker les differents tableaux
            "souhaite" => $tableauSouhaiter,
            "nonSouhaite" => $tableauNonSouhaiter,
            "nonReconnu" => $tableauNonReconnu
    );




    return $tabRecherche; // on retourne le tableau

}
        function Hierarchie($nom, $tab){
                //$j = 0;
              if($nom == null) {
                  echo trim("<li><a href='index.php?nom=Aliment' id='Aliment'  >Aliment </a></li>");
              }
              else {
                 // setcookie('fil', $nom);
                  //$fil[$j] = $nom;
                  echo "<p>sous-catégorie</p>";
                  foreach ($tab as $key => $value) {
                      if ($key == $nom) {

                          if (is_array($value)) {
                              foreach ($value as $key => $value) {
                                  if ($key == "sous-categorie") {
                                      foreach ($value as $key => $value) {
                                          echo "<li>".'<a href ="index.php?nom='.$value.'">'. $value."</a>"."</li>";



                                       }

                                  }
                              }
                          }
                      }

                  }
              }

        }

function nouveauNom($nom){
    $tabCaracSpec = array(
        '/[áàâãªäå]/u' => 'a',
        '/[ÁÀÂÃÄÅ]/u' => 'A',
        '/ç/u' => 'c',
        '/Ç/u' => 'C',
        '/đ/u' => 'd',
        '/Ð/u' => 'D',
        '/[éèêë]/u' => 'e',
        '/[ÉÈÊË]/u' => 'E',
        '/[ÍÌÎÏ]/u' => 'I',
        '/[íìîï]/u' => 'i',
        '/ñ/' => 'n',
        '/Ñ/' => 'N',
        '/[óòôõºöøð]/u' => 'o',
        '/[ÓÒÔÕÖØ]/u' => 'O',
        '/[úùûü]/u' => 'u',
        '/[ÚÙÛÜ]/u' => 'U',
        '/[ŷỳỵỷỹ]/u' => 'y',
        '/[ỸỶỴỲŶ]/u' => 'Y',
        '/[æ]/u' => 'ae',
        '/[Æ]/u' => 'AE',
        '/[œ]/u' => 'oe',
        '/[Œ]/u' => 'OE',
        '/ /u' => '_',
        "/'/u" => '',
    );
    $nom = preg_replace(array_keys($tabCaracSpec), array_values($tabCaracSpec), $nom);
    $nom = strtolower($nom);
    $nom = ucwords($nom);
    return $nom.".jpg";

}

function affichageCocktailDetailler($nomCocktail, $tab){
    foreach($tab as $key => $value){
        if($value['titre'] === $nomCocktail)$idCocktail = $key;
    }
    echo "<h1>".$nomCocktail."</h1>";
    if(!estFavoris($idCocktail))
        echo '<button type="button" onclick="favoris('.$idCocktail.');"> <img id="'.$idCocktail.'" src="icons/coeurVide.jpg"></button>';
    else
        echo '<button type="button" onclick="favoris('.$idCocktail.');"> <img id="'.$idCocktail.'" src="icons/coeurRouge.png"></button>';

    echo "</br>";
    afficheImage($nomCocktail);

    foreach ($tab as $value){
        if(is_array($value) && $value['titre'] === $nomCocktail){

            $ingredients = explode('|', $value['ingredients']);
            foreach ($ingredients as $ingredient){
                echo $ingredient."</br>";
            }

            echo "<p>".$value['preparation']."</p>";

        }
    }


}

function afficheImage($titre){
    $image = nouveauNom($titre);
    $nomFichier = "Photos/".$image;
    if(file_exists($nomFichier))
        echo "<img src = '$nomFichier'>";
    else
        echo "<img src = 'Photos/cocktail.png'>";
}
function estFavoris($idCocktail){
    if(estConnecter()){
        $pseudo = $_SESSION['login'];
        $monfichier = 'favorisConnecte.txt';
        $lecture_fichier = file_get_contents($monfichier);
        $tabFavoris = unserialize($lecture_fichier);
        foreach ($tabFavoris as $key => $values){
            if($key === $pseudo){
                if(in_array($idCocktail,$values)) return true;
            }
        }

    }else {
        $monfichier = 'favoris.txt';
        $lecture_fichier = file_get_contents($monfichier);
        $tabFavoris = unserialize($lecture_fichier);
        foreach ($tabFavoris as $favs){
            if($favs == $idCocktail) return true;
        }
    }

    return false;
}

function affichageCoktail($nom, $tab, $pourcentage){
    $i=0;


    foreach ($tab as $key => $value){
        if($value['titre'] === $nom){
            $idCocktail = $key;
            foreach ($value['index'] as $key => $value){
                $ingredient[$i] = $value;
                $i++;
            }
        }
    }


    echo '<div class="affichageCocktail">' ;
    if(!estFavoris($idCocktail))
        echo '<strong><a href ="index.php?titre='.$nom.'" >'. $nom. '</a></strong>'.'<button type="button" id="button" onclick="favoris('.$idCocktail.');"> <img id="'.$idCocktail.'" src="icons/coeurVide.jpg"></button>'. '</br>';
   else
       echo '<strong><a href ="index.php?titre='.$nom.'" >'. $nom. '</a></strong>'.'<button type="button" id="button" onclick="favoris('.$idCocktail.');"> <img id="'.$idCocktail.'" src="icons/coeurRouge.png"></button>'. '</br>';


    afficheImage($nom);



    for($j = 0; $j < count($ingredient); $j++){
        echo '</br><label>'.$ingredient[$j].'</label>';
    }
    if($pourcentage != null) {
        $nouveauPourcentage = $pourcentage * 100;

        echo '</br>' .number_format($nouveauPourcentage, 2).'%';
    }




    echo '</div>';



}
function SuperAlimentDe($nomAliment, $tabHier, $nomAliment2)
{
    if($nomAliment != $nomAliment2){
        $vrai = 0;
    }
    if ($nomAliment == $nomAliment2) {
        return 1;
    } else {
        foreach ($tabHier[$nomAliment2] as $key => $value) {

            if ($key === "super-categorie") {
                foreach ($value as $key => $super) {
                    $vrai = SuperAlimentDe($nomAliment, $tabHier, $super);

                }

            }
        }

    }


    return $vrai;
}

function existeCocktail($nomCocktail, $tabCocktail){
    foreach($tabCocktail as  $key => $cocktail){
        if($key === $nomCocktail){
            return true;
        }
    }
    return false;
}
function afficheFavorisCo($tabRecette){
    $monfichier = 'favorisConnecte.txt';
    $lecture_fichier = file_get_contents($monfichier);
    $tabFavoris = unserialize($lecture_fichier);

    $pseudo = $_SESSION['login'];

    foreach ($tabFavoris as $key => $favs){
        if($key === $pseudo) {
            if($favs != null) {
                sort($favs);
                foreach ($favs as $fav) {
                    foreach ($tabRecette as $key => $value) {
                        if (($fav != null) && ($key == $fav)) {
                            affichageCoktail($value['titre'], $tabRecette, null);
                        }
                    }
                }
            }
        }
    }


}

function afficheFavoris($tabRecette){
    $monfichier = 'favoris.txt';
    $lecture_fichier = file_get_contents($monfichier);
    $tabFavoris = unserialize($lecture_fichier);
    sort($tabFavoris);


    foreach ($tabFavoris as $favs){
        foreach ($tabRecette as $key => $value){
            if (($favs != null) && ($key == $favs)){
                affichageCoktail($value['titre'], $tabRecette, null);
            }
        }
    }


}


function AfficheHierarchie($tabHier, $tabRece, $nomAliment)
{
    $titre = null;
    if (($nomAliment != "")){
        $i = 0;
        foreach ($tabRece as $key1 => $value) {

            if (is_array($value)) {
                foreach ($value['index'] as $key => $value2) {
                    if (SuperAlimentDe($nomAliment, $tabHier, $value2) == 1) {
                        $titre[$i] = $value['titre'];
                        }
                    }

                }
                $i++;


        }
    }else echo "";



    if($titre != null) {
        foreach ($titre as $tit) {
            affichageCoktail($tit, $tabRece, null);

        }
    }
}


?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cocktails</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
  </head>
  <body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <header class="row" > 
      <ul class="col-5">
        <li class=""><a class="login" href="index.php">Navigation</a></li>
        <li class=""><a class="login" href="index.php?fav=1">Recettes</a></li>
          <div class="form_recherche">
              <form name="fo" method="POST" action="index.php">
                  <input type="text" name="recherche_cocktail"  placeholder="Rechercher votre coktail" />
                  <button type="submit"  name="chercher_cocktail" class="monBouton" title="Envoyer"><img src="icons/index.jpg" alt="" /></button>

              </form>
          </div>
      </ul>
      </li>
        <?php 
        if($isConnect !== 'yes') {echo 
        '<form action="#" name="submit" method="post" class="col-7">
          <ul>
            <li class=""><label class="nav login">Login :</label></li>
            <li class=""><input name="login" class="search-bar" type="text"></li>
            <li class=""><label class="nav login">Password :</label></li>
            <li class=""><input name="password" class="search-bar" type="password"></li>
            <li class=""><button class="search-bar" type="submit">Valider</button></li>
            <li class=""><button type="button" onclick="location.href=\'register.php\';" class="search-bar">S&lsquo;inscire</button></li>
          </ul>
        </form>';
        }
        ?>
      
      <?php if($isConnect === 'yes'){
          echo '<ul class=\'col-7\'><li class="login">' . $_SESSION['login'] . '</li>';
          echo '<li><button onclick="location.href=\'profil.php\';" class="login-bar">Profil</button></li>';
          echo '<li><button onclick="location.href=\'logout.php\';" class="login-bar">Se deconnecter</button></li></uL>';

        }  
      ?>
    </header>
    <main class="conatiner-fluid encadre_noir row">
      
      <nav class="container-fluid col encadre_noir">

          <strong><p>Aliment Courant</p> </strong>
          </br>
          <?php


          foreach ($fil as $key => $value){
                  if($value != null){
                      if($key > 1){ echo "/";}
                  echo " ".'<a href ="index.php?nom='.$value.'">'. $value."</a>"." ";
              }

          }


          ?>
          <ul>
              <?php
              require_once("include/Donnees.inc.php");
              if(isset($_POST['recherche_cocktail']) && (!empty($_POST['recherche_cocktail']))) {
                  afficheRecherche($rechercheCocktail, $Hierarchie);
              }

              else {
                  if (isset($_GET['nom'])) {
                      $nom = $_GET['nom'];
                  } else $nom = null;
                  Hierarchie($nom, $Hierarchie);
              }

              ?>

            <script>

              function favoris(idCocktail){
                  console.log(idCocktail);
                 var img = document.getElementById(idCocktail);

                  if (img.getAttribute("src") == "Photos/coeurVide.jpg"){
                      img.setAttribute("src", "Photos/coeurRouge.png");
                     $.ajax({
                         url : "http://localhost/Projet_Web_L3/programmation-web/favoris.php",
                         data : {cle : idCocktail},
                         type : "GET",
                         success : function(){
                             console.log("ajouté");
                         }
                     });

                  }
                  else{
                      img.setAttribute("src", "Photos/coeurVide.jpg");
                      $.ajax({
                          url : "deleteFavoris.php",
                          data : {cle : idCocktail},
                          type : "GET",
                          success : function(){
                              console.log("supprimé");
                          }
                      });

                  }
              }
            </script>

          </ul>

      </nav>

      <section style="text-align: center;" class="container col-9 encadre_noir">
          <h2>Liste des cocktails</h2>

          <div class="contenuCocktail">
          <?php
          //echo estFavoris(67);


          require_once("include/Donnees.inc.php");
          if(isset($_POST['recherche_cocktail']) && (!empty($_POST['recherche_cocktail']))){
              afficheCocktailRecherche($rechercheCocktail, $Hierarchie, $Recettes);
          }else {
              if (!isset($_GET['fav'])) {

                  if ($nom != null)
                      AfficheHierarchie($Hierarchie, $Recettes, $nom, null);
                  else if ($titreCocktail != null)
                      affichageCocktailDetailler($titreCocktail, $Recettes);
              } else if (estConnecter()) {
                  afficheFavorisCo($Recettes);
              } else {
                  afficheFavoris($Recettes);
              }
          }



          ?>
          </div>

      </section>
      

    </main>

    <footer style="text-align: center;" class="container encadre_noir">
      <p>footer</p>
    </footer>

  </body>
<?php
$_SESSION['fil'] = $fil;

?>
</html>