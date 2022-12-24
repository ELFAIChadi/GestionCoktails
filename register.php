<?php 
session_start();
//Si le formulaire a été soumis on admet que tout est bon, on modifiera si ce n'est pas le cas
if(isset($_POST['register'])){
    $set = 1;
}
else if (!isset($_POST['register'])){$set = 0;}

if($set){
    $ClassLogin='ok';
    $ClassPass='ok';
	$ClassName='ok';
	$ClassPrenom='ok';
	$ClassNaissance='ok';

if(!preg_match("/^([a-zA-Z0-9]+)$/",$_POST['r_login'])){
    $ErrLogin = "Le login doit être composé lettres non accentuées, minuscules ou MAJUSCULES, et/ou de chiffres"; 
    $ClassLogin='nok';
}

if($_POST['r_password'] === ''){
    $ClassPass='nok';
}

if($_POST['r_name'] !== '' && !preg_match("/^([A-Za-zÀ-ÖØ-öø-ÿ ']+((\-)*[A-Za-zÀ-ÖØ-öø-ÿ']+)*)$/",$_POST['r_name'])){
    $ErrName = "Le nom doit être composé lettres non accentuées, minuscules ou MAJUSCULES, et/ou de chiffres";
    $ClassName = 'nok'; 
    
}  

if($_POST['r_prenom'] !== '' && !preg_match("/^([A-Za-zÀ-ÖØ-öø-ÿ ']+((\-)*[A-Za-zÀ-ÖØ-öø-ÿ']+)*)$/",$_POST['r_prenom'])){
    $ErrPrenom = "Le prénom doit être composé lettres non accentuées, minuscules ou MAJUSCULES, et/ou de chiffres";
    $ClassPrenom = 'nok'; 
    
}
//création de la date d'aujourd'hui
$date1 = date_create($_POST['r_naissance']);
$date2 = date_create(date('Y-m-d'));
$interval = $date1->diff($date2);

$age = $interval->y;
if($_POST['r_naissance'] !== '' && $age < 18){
    $ErrNaissance = "Vous devez avoir 18 ans";
    $ClassNaissance = 'nok';
}

}

//Si tout est bon, on inscrit les données dans le fichier base de donnée texte
if($set && $ClassLogin==='ok' && $ClassPass==='ok' && $ClassName==='ok' && $ClassPrenom==='ok' && $ClassNaissance==='ok'){
    $monfichier = 'bdd.txt';
    $info = array();
    $lecture_fichier = file_get_contents($monfichier);
    $tab_recup = unserialize($lecture_fichier);
    $info['login'] = $_POST['r_login'];
    $info['pass'] = $_POST['r_password'];
    $info['nom'] = $_POST['r_name'];
    $info['prenom'] = $_POST['r_prenom'];
    $info['sexe'] = $_POST['r_sexe'];
    $info['naissance'] = $_POST['r_naissance'];
    
    $tab_recup[] = $info;
   

    file_put_contents($monfichier, serialize($tab_recup));
    header('Location: index.php');
    exit();
    
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
    <header>
        <h1 style="text-align:center;">Inscription</h1>
    </header>
    
    <main class="container-fluid">
        <div class="row">
            <div class="col-sm">

            </div>
            <form class="col-sm" method="post" action="#">
                
                <div class="form-group space">
                    <label>Login:</label><?php if($set && $ClassLogin==='nok')echo '<small style="color:red;" class="form-text">' . $ErrLogin . ' </small>';?>
                    <input name="r_login" <?php if($set && $ClassLogin==='ok')echo 'value="' . $_POST['r_login'] . '"'?> type="text" class="form-control" placeholder="Entrez un login">
                    <small style="color:red;" class="form-text">Obligatoire.</small>
                </div>
                <div class="form-group space">
                    <label>Mot de passe:</label>
                    <input name="r_password" type="password" class="form-control" placeholder="Entrez le mot de passe">
                    <small style="color:red;" class="form-text">Obligatoire.</small>
                </div>
                <div class="form-group space">
                    <label>Nom:</label><?php if($set && $ClassName === 'nok')echo '<small style="color:red;" class="form-text">' . $ErrName . ' </small>'; ?>
                    <input name="r_name" <?php if($set && $ClassName==='ok')echo 'value="' . $_POST['r_name'] . '"'?> type="text" class="form-control" placeholder="Entrez votre nom">
                </div>
                <div class="form-group space">
                    <label>Prénom:</label><?php if($set && $ClassPrenom === 'nok')echo '<small style="color:red;" class="form-text">' . $ErrPrenom . ' </small>'; ?>
                    <input name="r_prenom" <?php if($set && $ClassPrenom==='ok')echo 'value="' . $_POST['r_prenom'] . '"'?> type="text" class="form-control" placeholder="Entrez votre prénom">
                </div>
                <div class="form-group space">
                    <label>Vous êtes : </label>
                    <input type="radio" <?php if(isset($_POST['r_sexe']) && $_POST['r_sexe'] === 'f')echo 'checked="checked"' ?> name="r_sexe" value="f"/> une femme 	
                    <input type="radio" <?php if(isset($_POST['r_sexe']) && $_POST['r_sexe'] === 'h')echo 'checked="checked"' ?> name="r_sexe" value="h"/> un homme
                </div>
                <div class="form-group space">
                    <label>Date de naissance : </label><?php if($set && $ClassNaissance=== 'nok')echo '<small style="color:red;" class="form-text">' . $ErrNaissance. ' </small>'; ?>
                    <input type="date"  name="r_naissance"/>
                </div>
                
                <button type="submit" name="register" class="btn btn-primary">Valider</button>
            </form>
            <div class="col-sm">

            </div>
        </div>
    </main>
</body>
</html>