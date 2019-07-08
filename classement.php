<?php //Redirection si pas connecté
if(empty($_SESSION['id'])){
    header('Location: index?not_connected=true');
    exit();
}
else{
?>

<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="res/css/classement.css" />
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
                    <h1>Classement</h1>
                    <?php
                    $compteur = $bdd->query('SELECT count(*) as nb_videos_partagees,id_membre_ajout FROM videos GROUP BY id_membre_ajout ORDER BY nb_videos_partagees DESC');
                    ?><table>
                        <tr id="titre">
                            <th>Place</th>
                            <th>Pseudo</th>
                            <th>Nombre de vidéos partagées</th>
                            <!--<th>Points</th>-->
                       </tr>
                        <?php
                        $compteur_place=0;
                        while($cpt=$compteur->fetch()){
                            $compteur_place++;
                            $pseudo = $bdd->prepare('SELECT pseudo FROM membres WHERE id=?');
                            $pseudo->execute(array($cpt['id_membre_ajout']));
                            $pseudo2 = $pseudo->fetch();

                            /*$nb_like_videos = $bdd->prepare('SELECT count(*) as nb_likes_videos FROM likes_videos,videos WHERE id_video=id AND like_dislike="like" AND id_membre_ajout=?');
                            $nb_like_videos->execute(array($cpt['id_membre_ajout']));
                            $nb_like_videos2 = $nb_like_videos->fetch();

                            $nb_dislike_videos = $bdd->prepare('SELECT count(*) as nb_dislike_videos FROM likes_videos WHERE id_video=id AND like_dislike="dislike" AND id_membre_ajout=?');
                            $nb_dislike_videos->execute(array($cpt['id_membre_ajout']));
                            $nb_dislike_videos2 = $nb_dislike_videos->fetch();

                            $nb_like_images = $bdd->prepare('SELECT count(*) as nb_likes_images FROM likes_images WHERE id_image=id AND like_dislike="like" AND id_membre_ajout=?');
                            $nb_like_images->execute(array($cpt['id_membre_ajout']));
                            $nb_like_images2 = $nb_like_images->fetch();

                            $nb_dislike_images = $bdd->prepare('SELECT count(*) as nb_dislikes_images FROM likes_images WHERE id_image=id AND like_dislike="dislike" AND id_membre_ajout=?');
                            $nb_dislike_images->execute(array($cpt['id_membre_ajout']));
                            $nb_dislike_images2 = $nb_dislike_images->fetch();*/

                            if($compteur_place==1){
                                echo'<tr id="first_place">';
                            }
                            elseif($compteur_place==2){
                                echo'<tr id="second_place">';
                            }
                            elseif($compteur_place==3){
                                echo'<tr id="third_place">';
                            }
                            else{
                                echo'<tr>';
                            }
                            //$points = $cpt['nb_videos_partagees']*5 + $nb_like_videos2['nb_likes_videos']*2 - $nb_dislike_videos2['nb_dislikes_videos'];
                            echo'
                                <th>'.$compteur_place.'</th>
                                <th>'.$pseudo2['pseudo'].'</th>
                                <th>'.$cpt['nb_videos_partagees'].'</th>
                            </tr>
                            ';
                            $pseudo->closeCursor();
                        }
                        $compteur->closeCursor();
                        ?>
                    </table>
                </article>
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>
<?php } ?>