<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/assets/cookie.php");
?>

<header>
<nav id="nav_menu">
    <ul>
        <a href="index"><img src="/res/img/logo_labeautedunet.png" id='logo_menu' contextmenu="return false;" oncontextmenu="return false;"></a>
    	<li><a href="/">Accueil</a></li>
    	<li><a href="/menu_images">Images</a></li>
        <li><a href="/menu_videos">Vidéos</a></li>
        <?php if(session_id() == '' || !isset($_SESSION['id']) ) {?>
            <li><a href="/connexion">Connexion</a></li>
            <li><a href="/inscription">Inscription</a></li>
        <?php }
        else{
            if($_SESSION['niveau_privilege']=="admin"){?>
                <li><a href="/tableau_de_bord">Tableau de bord</a></li>;
            <?php }
            else{?>
                <li><a href="/contact">Contact</a></li>
            <?php }
        }?>
    </ul>
</nav>
</header>