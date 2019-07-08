<?php //Redirection si pas connecté
if(empty($_SESSION['id'])){
    header('Location: index.php?not_connected=true');
    exit();
}
else{
?>

<!DOCTYPE html>
<html>

<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>

    <head>
        <link rel="stylesheet" href="res/css/soumettre_une_video.css" />
        <meta name="robots" content="noindex">
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
                <h1>Soumettre une vidéo :</h1>
                <form method="post" action=""><!Creation du formulaire de recherche>
                    <input class="input_form" type="text" name="nom" id="nom" placeholder="Titre de la vidéo" size="30" required="required" />
                    <input class="input_form" type="text" name="url" id="url" placeholder="Url youtube" size="30" required="required"/>
                    <input class="input_form" type="text" name="categorie" id="categorie" placeholder="Catégorie (Vous pouvez proposer une nouvelle catégorie)" size="30" required="required"/>
                    <input class="input_form" type="submit" name="Valider" id="valider_insertion"/>
                </form>
                <?php
                if(!empty($_POST['Valider']) && !empty($_POST['nom']) && !empty($_POST['url']) && !empty($_POST['categorie']) && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $_POST['url'], $match)){

                    $url="https://www.youtube.com/embed/".$match[1];

                    $req = $bdd->prepare('INSERT INTO videos_en_attente_de_validation(id_membre,url,nom,date_soumission,categorie) VALUES (:id_membre,:url,:nom,NOW(),:categorie)');
                    $req->execute(array(
                        'id_membre'=>$_SESSION['id'],
                        'url'=>htmlentities($url),
                        'nom' =>htmlentities($_POST['nom']),
                        'categorie' => htmlentities($_POST['categorie'])));
                    $req->closeCursor();
                    echo('<p class="succes">Nous avons bien reçu votre vidéo, nous allons l\'examiner au plus vite !</p>');}
                elseif(!empty($_POST['Valider']) && !preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $_POST['url'], $match)){echo '<p class="echec">L\'url youtube ne correspond pas.</p>';}
                elseif(!empty($_POST['Valider'])){echo '<p class="echec">Impossible de soumettre la vidéo, si le problème persiste merci de nous contacter.</p>';}
                ?>

                <h1>Mes vidéos en attente de modération :</h1>
                <?php
                $videos = $bdd->prepare('SELECT id,url,nom,categorie FROM videos_en_attente_de_validation WHERE id_membre=? ORDER BY id');
                $videos->execute(array($_SESSION['id']));
                affichage_videos_a_moderer($videos);
                $videos->closeCursor();
                

                if(empty($_GET['page'])){$_GET['page']=1;} //Si le numero de page n'est pas indiqué on lui attribu 1
                $limite = ($_GET['page']-1)*12;//Cela sert dans LIMIT, permet de paginer (ici affichage de 12 videos par page)
                ?>

                <h1>Mes vidéos acceptées :</h1>
                <?php
                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_videos FROM videos WHERE id_membre_ajout=?'); //Compte le nombre de videos à afficher
                $compteur_page->execute(array($_SESSION['id']));

                $videos = $bdd->prepare('SELECT id,nom,date_ajout,url,categorie,source,rapport_like_dislike FROM videos WHERE id_membre_ajout=? ORDER BY date_ajout DESC LIMIT '.$limite.',12');
                $videos->execute(array($_SESSION['id']));
                affichage_videos($videos);
                $videos->closeCursor();

                $compteur_pages=$compteur_page->fetch();
                $compteur_page->closeCursor();

                if($compteur_pages['nb_videos']==0){
                    $nb_pages=1;
                    echo '<h3>Aucune vidéo trouvée</h3>';
                }//Permet d'afficher x/1 et non x/0
                else{
                    $nb_pages=ceil($compteur_pages['nb_videos']/12); //ceil() arrondi un float à l'entier supérieur
                    affichage_videos($videos); //Affiche les videos
                    $videos->closeCursor();
                }
                ?>
                <form method="get" action="">
                    <?php
                    echo '<input type="number" name="page" id="page" value="'.$_GET['page'].'" min=1 max="'.$nb_pages.'">';
                    ?>
                    <label id="nb_pages_max"><?php echo '/'.$nb_pages;?></label>
                    <input type="submit" name="Go" id="Go" value="Go">
                </form>
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>
<?php } ?>