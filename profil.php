<?php session_start();
$monfichier = 'bdd.txt';
$lecture_fichier = file_get_contents($monfichier);
$bdd = unserialize($lecture_fichier);
$classErr = 'ok';

//simplifie la vérification de la variable var dans le tableau post
function isPost($var){
  if (isset($_POST[$var])) return true;
  return false;
}

//on se positionne sur le profil correspondant et on le memorise dans la variable index
for($i=0; $i < sizeof($bdd); $i++){
  if($_SESSION['login'] === $bdd[$i]['login']){
    $index = $i;
  }
}

//on verifie que si une valeur a été changée, et si elle est valide, on effectue ensuite le changement
if(isPost('newPass')){
  $bdd[$index]['pass'] = $_POST['newPass'];
}

if(isPost('newNom')){
  if($_POST['newNom'] === '' || !preg_match("/^([A-Za-zÀ-ÖØ-öø-ÿ ']+((\-)*[A-Za-zÀ-ÖØ-öø-ÿ']+)*)$/",$_POST['newNom'])){
    $classErr = 'err';
    $_POST['m_nom'] = 'err';
  }
  else $bdd[$index]['nom'] = $_POST['newNom'];
}

if(isPost('newPrenom')){
  if($_POST['newPrenom'] === '' || !preg_match("/^([A-Za-zÀ-ÖØ-öø-ÿ ']+((\-)*[A-Za-zÀ-ÖØ-öø-ÿ']+)*)$/",$_POST['newPrenom'])){
    $classErr = 'err';
    $_POST['m_prenom'] = 'err';
  }
  else $bdd[$index]['prenom'] = $_POST['newPrenom'];
}

if(isPost('newSexe')){
  $bdd[$index]['sexe'] = $_POST['newSexe'];
}

if(isPost('newNaissance')){
  $date1 = date_create($_POST['newNaissance']);
  $date2 = date_create(date('Y-m-d'));
  $interval = $date1->diff($date2);
  $age = $interval->y;
  if($_POST['newNaissance'] === '' || $age < 18){
    $classErr = 'err';
    $_POST['m_naissance'] = 'err';
  }
  else $bdd[$index]['naissance'] = $_POST['newNaissance'];
}

//on sauvegarde les resutats
file_put_contents($monfichier, serialize($bdd));

//variables plus accessibles
$login = $_SESSION['login'];
$pass =  $bdd[$index]['pass'];
$nom = $bdd[$index]['nom'];
$prenom = $bdd[$index]['prenom'];

if($bdd[$index]['sexe'] === 'h')$sexe='Homme';
else if($bdd[$index]['sexe'] === 'f')$sexe='Femme';
else $sexe ='';

if($bdd[$index]['naissance'] !== '') $naissance =  date_format(date_create($bdd[$index]['naissance']),'d/m/Y');
else $naissance ='';

//fonction permettant l'affichage d'un bouton modifier
function Bouton($votre,$value,$modify){
  echo '<h3 class="d-flex align-items-center justify-content-center">Votre ' . $votre .' : ' . $value . '<form action="#" method="post"><input name="' . $modify . '" type="hidden"/><button type="submit" class="login-bar">Modifier</button></form></h3>';
}

//differentes variantes de l'affichage d'un champ de modification suivis d'un bouton modifier
function Champ($votre,$value,$modify,$class){
  echo '<h3 class="d-flex align-items-center justify-content-center">Votre ' . $votre .' : ' . $value . '<form action="#" method="post" style="float:left;"><input class="login-bar ' . $class . '" name="' . $modify . '" type="text"/><button type="submit" class="login-bar">Modifier</button></form></h3>';
}

function ChampNaissance($votre,$value,$modify){
  echo '<h3 class="d-flex align-items-center justify-content-center">Votre ' . $votre .' : ' . $value . '<form action="#" method="post"><input name="' . $modify . '" type="date"/><button type="submit" class="login-bar">Modifier</button></form></h3>';
}

function ChampSexe($votre,$value,$modify){
  echo '<h3 class="d-flex align-items-center justify-content-center">Votre ' . $votre .' : ' . $value . '<form action="#" method="post"><input name="' . $modify . '" type="radio" value="f"/>Une femme<input name="' . $modify . '" type="radio" value="h"/>Un homme<button type="submit" class="login-bar">Modifier</button></form></h3>';
}

?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Inscription</title>
  <link rel="stylesheet" href="css/index.css">
  <script src="script.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>
<body>
  <header class="row" > 
    <ul class="col-5">
      <li class=""><a class="login" href="index.php">Navigation</a></li>
      <li class=""><a class="login" href="#">Recettes</a></li>
      <li class=""><input class="search-bar" type="text" ></li>
    </ul>
    <?php
      //Affichage du menu profil
      echo '<ul class=\'col-7\'><li><a class="login float-right" href="profil.php">' . $_SESSION['login'] . '</a></li>';
      echo '<li><button onclick="location.href=\'profil.php\';" class="login-bar float-right">Profil</button></li>';
      echo '<li><button onclick="location.href=\'logout.php\';" class="login-bar float-right">Se deconnecter</button></li></uL>';
      ?>
  </header>
    
  <main >
    <h1 class="d-flex justify-content-center">Votre profil :</h1>
    <div id="form-profil">
      <div class="d-flex justify-content-center"><img src="icons/User-avatar.png"></div>
      <?php 
      //Si le champ est renseigné on l'affiche
      if($login !== '') echo '<h3 class="d-flex align-items-center justify-content-center">Votre login :' . $login . '</h3>';
      if($pass !== '') if(!isPost('m_pass'))Bouton('mot de passe',$pass,'m_pass'); else Champ('mot de passe',$pass,'newPass',$classErr);
      if($nom !== '') if(!isPost('m_nom'))Bouton('nom',$nom,'m_nom'); else Champ('nom',$nom,'newNom',$classErr);
      if($prenom !== '') if(!isPost('m_prenom'))Bouton('prenom',$prenom,'m_prenom'); else Champ('prenom',$prenom,'newPrenom',$classErr);
      if($sexe !== '') if(!isPost('m_sexe'))Bouton('sexe',$sexe,'m_sexe'); else ChampSexe('sexe',$sexe,'newSexe');
      if($naissance !== '') if(!isPost('m_naissance'))Bouton('date de naissance',$naissance,'m_naissance'); else ChampNaissance('date de naissance',$naissance,'newNaissance');
      ?>
      </div>
  </main>
</body>
</html>