<?php //Permet de rediriger les utilisateur vers l'index s'il veulent accéder au tableau de bord
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
        <link rel="stylesheet" href="res/css/tableau_de_bord.css" />
        <?php
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
    </head>

    <body>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>


        <div class="corps">

            <?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php"); ?>

            <section>
                <article>
                    <h1>Tableau de bord</h1>
                	<?php
                        $compteur_membre = $bdd->query('SELECT count(*) as nb_membres FROM membres');
                        $compteur_membre2 = $compteur_membre->fetch();

                        $compteur_videos = $bdd->query('SELECT count(*) as nb_videos FROM videos');
                        $compteur_videos2 = $compteur_videos->fetch();

                        $compteur_videos_en_attente_de_validation = $bdd->query('SELECT count(*) as nb_videos_en_attente_de_validation FROM videos_en_attente_de_validation');
                        $compteur_videos_en_attente_de_validation2 = $compteur_videos_en_attente_de_validation->fetch();

                        $compteur_like_videos = $bdd->query('SELECT count(*) as nb_like_videos FROM likes_videos WHERE like_dislike="like"');
                        $compteur_like_videos2 = $compteur_like_videos->fetch();

                        $compteur_dislike_videos = $bdd->query('SELECT count(*) as nb_dislike_videos FROM likes_videos WHERE like_dislike="dislike"');
                        $compteur_dislike_videos2 = $compteur_dislike_videos->fetch();

                        $compteur_images = $bdd->query('SELECT count(*) as nb_images FROM images');
                        $compteur_images2 = $compteur_images->fetch();

                        $compteur_like_images = $bdd->query('SELECT count(*) as nb_like_images FROM likes_images WHERE like_dislike="like"');
                        $compteur_like_images2 = $compteur_like_images->fetch();

                        $compteur_dislike_images = $bdd->query('SELECT count(*) as nb_dislike_images FROM likes_images WHERE like_dislike="dislike"');
                        $compteur_dislike_images2 = $compteur_dislike_images->fetch();
                    ?>
                    <h2>Infos sur les membres</h2>
                    <table>
                        <tr>
                            <th>Nombre de membres :</th>
                            <th class="th_nombre"><?php echo $compteur_membre2['nb_membres']; $compteur_membre->closeCursor(); ?></th>
                        </tr>
                    </table>
                    <h2>Infos sur les vidéos</h2>
                    <table>
                        <tr>
                            <th>Nombre de vidéos :</th>
                            <th class="th_nombre"><?php echo $compteur_videos2['nb_videos']; $compteur_videos->closeCursor(); ?></th>
                        </tr>
                        <tr>
                            <th>Nombre de vidéos en attente de validation :</th>
                            <th class="th_nombre"><?php echo $compteur_videos_en_attente_de_validation2['nb_videos_en_attente_de_validation']; $compteur_videos_en_attente_de_validation->closeCursor(); ?></th>
                        </tr>
                        <tr>
                            <th>Nombre de likes sur les vidéos :</th>
                            <th class="th_nombre"><?php echo $compteur_like_videos2['nb_like_videos']; $compteur_like_videos->closeCursor(); ?></th>
                        </tr>
                        <tr>
                            <th>Nombre de dislikes sur les vidéos :</th>
                            <th class="th_nombre"><?php echo $compteur_dislike_videos2['nb_dislike_videos']; $compteur_dislike_videos->closeCursor(); ?></th>
                        </tr>
                    </table>
                    <h2>Infos sur les images</h2>
                    <table>
                        <tr>
                            <th>Nombre d'images :</th>
                            <th class="th_nombre"><?php echo $compteur_images2['nb_images']; $compteur_images->closeCursor(); ?></th>
                        </tr>
                        <tr>
                            <th>Nombre de likes sur les images :</th>
                            <th class="th_nombre"><?php echo $compteur_like_images2['nb_like_images']; $compteur_like_images->closeCursor(); ?></th>
                        </tr>
                        <tr>
                            <th>Nombre de dislikes sur les images :</th>
                            <th class="th_nombre"><?php echo $compteur_dislike_images2['nb_dislike_images']; $compteur_dislike_images->closeCursor(); ?></th>
                        </tr>
                    </table>
                </article>
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>
<?php } ?>

