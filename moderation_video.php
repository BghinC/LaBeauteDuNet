<?php //Permet de rediriger les utilisateur vers l'index s'il veulent accéder à la page d'ajout
if($_SESSION['niveau_privilege'] != "admin"){
    header('Location: http://www.labeautedunet.fr');
    exit();
}
elseif ($_SESSION['niveau_privilege'] == "admin"){?>

<!DOCTYPE html>
<html>

<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>

    <head>
        <link rel="stylesheet" href="res/css/soumettre_une_video.css" />
        <?php
        include ($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_videos_images.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
    </head>

    <body>
        

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>

        <div class="corps">

            <?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php"); ?>

            <section>
            	<h1>Vidéos à modérer :</h1>
                <?php
                $videos = $bdd->query('SELECT id,id_membre,url,nom,categorie FROM videos_en_attente_de_validation ORDER BY id');
                affichage_videos_a_moderer_admin($videos);
                $videos->closeCursor();
                ?>
            </section>
        </div>
            <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>
<?php } ?>