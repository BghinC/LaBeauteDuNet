<?php //Connexion à la base de donnée
include("../A_include/connexion_bdd.php");
?>
<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" href="/res/css/erreur.css" />
        <?php
        include ("../assets/fonctions.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
    </head>

    <body>
        
        <header>
            <?php include("../assets/header.php"); ?>
        </header>
        <div class="corps">

            <?php include("../assets/menu.php"); ?>

            <section>
            	<article>
            		<h1>Erreur 404 - Page Introuvable</h1>
            	</article>
            </section>

        </div>  


        <footer>
        <?php include("../assets/footer.php");?>
    	</footer>

    </body>

</html>