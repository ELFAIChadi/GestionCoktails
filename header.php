<header class="rd-flex flex-row" > 
      <ul class="p-2">
        <li class=""><a class="nav" href="#">Navigation</a></li>
        <li class=""><a class="nav" href="#">Recettes</a></li>
        <li class=""><input class="search-bar" type="text" ></li>
        <li class=""><button class="search-bar" type="submit">Chercher</button></li>
      </ul>
      </li>
        <?php 
        if($isConnect !== 'yes') {echo 
        '<form action="#" name="submit" method="post" class="col-7">
          <ul>
            <li class=""><label class="nav login">Login :</label></li>
            <li class=""><input name="login" class="search-bar" type="text"></li>
            <li class=""><label class="nav">Password :</label></li>
            <li class=""><input name="password" class="search-bar" type="password"></li>
            <li class=""><button class="search-bar" type="submit">Valider</button></li>
            <li class=""><button onclick="location.href=\'register.php\';" class="search-bar">S&lsquo;inscire</button></li>
          </ul>
        </form>';
        }
        ?>
      
      <?php if($isConnect === 'yes'){
          echo '<ul class="p-2"><li><a class="login d-flex justify-content-end" href="profil.php">' . $_SESSION['login'] . '</a></li>';
          echo '<li><button onclick="location.href=\'profil.php\';" class="login-bar d-flex justify-content-end">Profil</button></li>';
          echo '<li><button onclick="location.href=\'logout.php\';" class="login-bar d-flex justify-content-end">Se deconnecter</button></li></uL>';

        }  
      ?> 
    </header>