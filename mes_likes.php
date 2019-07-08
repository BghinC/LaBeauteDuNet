<?php //Redirection si pas connecté
if(empty($_SESSION['id'])){
    header('Location: index?not_connected=true');
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
            <link rel="stylesheet" href="res/css/mes_likes.css" />
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
                        <h1>Mes likes</h1>
                        <?php
                        $verif_pas_de_modif=0;
                        $categories = $bdd->query('SELECT DISTINCT categorie FROM videos');
                        while($cat = $categories->fetch()){
                            if (!empty($_POST['categorie']) && ($_POST['categorie']=="Toutes les catégories" || $cat['categorie']==$_POST['categorie'])){
                                $verif_pas_de_modif+=1;
                            }
                        }$categories->closeCursor();
                        if(!empty($_POST['categorie']) && $verif_pas_de_modif != 1 && $_POST['categorie']!="Toutes les catégories"){
                            //Si l'utilisateur modifie le menu déroulant et que ça ne correspond pas
                        }

                        if(!empty($_POST['tri']) && ($_POST['tri'] != 'Aléatoire' && $_POST['tri'] != 'Like le plus récent' && $_POST['tri'] != 'Like le plus ancien' && $_POST['tri'] != 'Nombre de like')){
                            //Si l'utilisateur modifie le menu déroulant et que ça ne correspond pas
                        }

                        if(!empty($_POST['type']) && ($_POST['type'] != 'Vidéos' && $_POST['type'] != 'Images' && $_POST['type'] != 'Tout types')){
                            //Si l'utilisateur modifie le menu déroulant et que ça ne correspond pas
                        }

                        if(empty($_POST['page'])){$_POST['page']=1;} //Si le numero de page n'est pas indiqué on lui attribu 1
                        $limite = ($_POST['page']-1)*12;//Cela sert dans LIMIT, permet de paginer (ici affichage de 12 videos par page)

                        ?>
                        <form method="post" action="" id="formulaire_recherche"><!Creation du formulaire de recherche>
                            <?php 
                            /*
                            if(!empty($_POST['recherche_saisie'])){//Permet de conserver la recherche dans la barre de recherche
                            echo '<input type="search" name="recherche_saisie" id="recherche_saisie" value="'.htmlspecialchars($_POST['recherche_saisie']).'" size="30">';}
                            else{
                                echo '<input type="search" name="recherche_saisie" id="recherche_saisie" placeholder="Recherche par mot clé" size="30">';}
                            */

                            $categories = $bdd->query('SELECT DISTINCT categorie FROM videos ORDER BY categorie');?>
                            <select name="categorie" class="select_recherche">
                                <option>Toutes les catégories</option>
                                <?php while($cat = $categories->fetch()) {?>
                                    <option <?php if(!empty($_POST['categorie']) && $cat['categorie']==$_POST['categorie']){echo 'selected="selected"';}?>><?php echo $cat['categorie'];?></option>
                                <?php }
                                $categories->closeCursor()?>
                            </select>

                            <select name="tri" class="select_recherche">
                                <option <?php if(empty($_POST['tri']) || $_POST['tri']=="Aléatoire"){echo 'selected="selected"';}?>>Aléatoire</option>
                                <option <?php if(!empty($_POST['tri']) && $_POST['tri']=="Like le plus récent"){echo 'selected="selected"';}?>>Like le plus récent</option>
                                <option <?php if(!empty($_POST['tri']) && $_POST['tri']=="Like le plus ancien"){echo 'selected="selected"';}?>>Like le plus ancien</option>
                                <option <?php if(!empty($_POST['tri']) && $_POST['tri']=="Nombre de like"){echo 'selected="selected"';}?>>Nombre de like</option>
                            </select>

                            <select name="type" class="select_recherche">
                                <option <?php if(empty($_POST['type']) || $_POST['type']=="Tout types"){echo 'selected="selected"';}?>>Tout types</option>
                                <option <?php if(!empty($_POST['type']) && $_POST['type']=="Vidéos"){echo 'selected="selected"';}?>>Vidéos</option>
                                <option <?php if(!empty($_POST['type']) && $_POST['type']=="Images"){echo 'selected="selected"';}?>>Images</option>
                            </select>
                            <input type="submit" name="Rechercher" id="valider_recherche" value="Rechercher">
                        </form><?php

                        if(!empty($_POST['type']) && $_POST['type'] == 'Vidéos'){// RECHERCHE DANS VIDEOS
                            if(!empty($_POST['tri']) && $_POST['tri'] == "Like le plus récent"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',12');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',12');
                                }
                            }
                            elseif(!empty($_POST['tri']) && $_POST['tri'] == "Like le plus ancien"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',12');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',12');
                                }
                            }
                            elseif(!empty($_POST['tri']) && $_POST['tri'] == "Nombre de like"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,rapport_like_dislike,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',12');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,rapport_like_dislike,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',12');
                                }
                            }
                            else{ //(empty($_POST['tri']) || $_POST['tri'] == "Aléatoire" )
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike FROM videos,likes_videos WHERE  categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',12');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',12');
                                }
                            }
                            
                            if(empty($_POST['categorie']) || $_POST['categorie'] == "Toutes les catégories"){
                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_videos FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like"');
                                $compteur_page->execute(array($_SESSION['id']));
                                $compteur_pages=$compteur_page->fetch();
                                $compteur_page->closeCursor();
                                $nb_pages=ceil($compteur_pages['nb_videos']/12); //ceil() arrondi un float à l'entier supérieur
                                if($compteur_pages['nb_videos']==0){
                                    $nb_pages=1;
                                    echo '<h2 class="rien_trouve">Aucune vidéo trouvée dans vos likes</h2>';
                                }//Permet d'afficher x/1 et non x/0 pour le numéro de page
                                else{
                                    ?><h2 class="titre_vid_img">Vidéos (<?php if($compteur_pages['nb_videos']==''){echo '0';}else{echo $compteur_pages['nb_videos'];}?>)</h2></h2><?php
                                    $videos->execute(array($_SESSION['id']));
                                    affichage_videos($videos); //Affiche les videos
                                    $videos->closeCursor();
                                }
                            }
                            else {
                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_videos FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like"');
                                $compteur_page->execute(array($_POST['categorie'],$_SESSION['id']));
                                $compteur_pages=$compteur_page->fetch();
                                $compteur_page->closeCursor();
                                $nb_pages=ceil($compteur_pages['nb_videos']/12); //ceil() arrondi un float à l'entier supérieur
                                if($compteur_pages['nb_videos']==0){
                                    $nb_pages=1;
                                    echo '<h2 class="rien_trouve">Aucune vidéo trouvée dans vos likes</h2>';
                                }//Permet d'afficher x/1 et non x/0 pour le numéro de page
                                else{
                                    ?><h2 class="titre_vid_img">Vidéos (<?php if($compteur_pages['nb_videos']==''){echo '0';}else{echo $compteur_pages['nb_videos'];}?>)</h2><?php
                                    $videos->execute(array($_POST['categorie'],$_SESSION['id']));
                                    affichage_videos($videos); //Affiche les videos
                                    $videos->closeCursor();
                                }
                            }

                        }
                        elseif(!empty($_POST['type']) && $_POST['type'] == 'Images'){
                            if(!empty($_POST['tri']) && $_POST['tri'] == "Like le plus récent"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',12');
                                }
                                else{
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',12');
                                }
                            }
                            elseif(!empty($_POST['tri']) && $_POST['tri'] == "Like le plus ancien"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',12');
                                }
                                else{
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',12');
                                }
                            }
                            elseif(!empty($_POST['tri']) && $_POST['tri'] == "Nombre de like"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,rapport_like_dislike,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',12');
                                }
                                else{
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,rapport_like_dislike,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',12');
                                }
                            }
                            else{ //(empty($_POST['tri']) || $_POST['tri'] == "Aléatoire" )
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike FROM images,likes_images WHERE  categorie=? AND id_membre=? AND id=id_image AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',12');
                                }
                                else{
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',12');
                                }
                            }

                            if(empty($_POST['categorie']) || $_POST['categorie'] == "Toutes les catégories"){
                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like"');
                                $compteur_page->execute(array($_SESSION['id']));
                                $compteur_pages=$compteur_page->fetch();
                                $compteur_page->closeCursor();
                                $nb_pages=ceil($compteur_pages['nb_images']/12); //ceil() arrondi un float à l'entier supérieur
                                if($compteur_pages['nb_images']==0){
                                    $nb_pages=1;
                                    echo '<h2 class="rien_trouve">Aucune vidéo trouvée dans vos likes</h2>';
                                }//Permet d'afficher x/1 et non x/0 pour le numéro de page
                                else{
                                    ?><h2 class="titre_vid_img">Images (<?php if($compteur_pages['nb_images']==''){echo '0';}else{echo $compteur_pages['nb_images'];}?>)</h2><?php
                                    $images->execute(array($_SESSION['id']));
                                    affichage_images($images); //Affiche les images
                                    $images->closeCursor();
                                }
                            }
                            else {
                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_image AND like_dislike="like"');
                                $compteur_page->execute(array($_POST['categorie'],$_SESSION['id']));
                                $compteur_pages=$compteur_page->fetch();
                                $compteur_page->closeCursor();
                                $nb_pages=ceil($compteur_pages['nb_images']/12); //ceil() arrondi un float à l'entier supérieur
                                if($compteur_pages['nb_images']==0){
                                    $nb_pages=1;
                                    echo '<h2 class="rien_trouve">Aucune vidéo trouvée dans vos likes</h2>';
                                }//Permet d'afficher x/1 et non x/0 pour le numéro de page
                                else{
                                ?><h2 class="titre_vid_img">Images (<?php if($compteur_pages['nb_images']==''){echo '0';}else{echo $compteur_pages['nb_images'];}?>)</h2><?php
                                    $images->execute(array($_POST['categorie'],$_SESSION['id']));
                                    affichage_images($images); //Affiche les images
                                    $images->closeCursor();
                                }
                            }

                        }
                        else{//RECHERCHE DANS VIDEOS ET IMAGES
                            $limite = ($_POST['page']-1)*6;//Cela sert dans LIMIT, permet de paginer (ici affichage de 6 videos + 6 images par page)
                            if(!empty($_POST['tri']) && $_POST['tri'] == "Like le plus récent"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',6');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like DESC LIMIT '.$limite.',6');
                                }
                            }
                            elseif(!empty($_POST['tri']) && $_POST['tri'] == "Like le plus ancien"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',6');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY date_like LIMIT '.$limite.',6');
                                }
                            }
                            elseif(!empty($_POST['tri']) && $_POST['tri'] == "Nombre de like"){
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,rapport_like_dislike,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,rapport_like_dislike,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',6');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,rapport_like_dislike,id_video,id_membre,like_dislike,date_like FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,rapport_like_dislike,id_image,id_membre,like_dislike,date_like FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY rapport_like_dislike DESC LIMIT '.$limite.',6');
                                }
                            }
                            else{ //(empty($_POST['tri']) || $_POST['tri'] == "Aléatoire" )
                                if(!empty($_POST['categorie']) && $_POST['categorie'] != "Toutes les catégories"){
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike FROM videos,likes_videos WHERE  categorie=? AND id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike FROM images,likes_images WHERE  categorie=? AND id_membre=? AND id=id_image AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',6');
                                }
                                else{
                                    $videos = $bdd->prepare('SELECT id,nom,url,categorie,source,id_video,id_membre,like_dislike FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',6');
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,id_image,id_membre,like_dislike FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like" ORDER BY rand() LIMIT '.$limite.',6');
                                }
                            }

                            if(empty($_POST['categorie']) || $_POST['categorie'] == "Toutes les catégories"){
                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_videos FROM videos,likes_videos WHERE id_membre=? AND id=id_video AND like_dislike="like"');
                                $compteur_page->execute(array($_SESSION['id']));
                                $compteur_pages_videos=$compteur_page->fetch();
                                $compteur_page->closeCursor();

                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images,likes_images WHERE id_membre=? AND id=id_image AND like_dislike="like"');
                                $compteur_page->execute(array($_SESSION['id']));
                                $compteur_pages_images=$compteur_page->fetch();
                                $compteur_page->closeCursor();

                                if($compteur_pages_videos['nb_videos']>$compteur_pages_images['nb_images']){
                                    $nb_pages=ceil($compteur_pages_videos['nb_videos']/6); //ceil() arrondi un float à l'entier supérieur
                                }
                                else{
                                    $nb_pages=ceil($compteur_pages_images['nb_images']/6); //ceil() arrondi un float à l'entier supérieur
                                }
                                if($compteur_pages_videos['nb_videos'] + $compteur_pages_images['nb_images']==0){
                                    $nb_pages=1;
                                    echo '<h2 class="rien_trouve">Aucune vidéo/image trouvée dans vos likes</h2>';
                                }//Permet d'afficher x/1 et non x/0 pour le numéro de page
                                else{
                                    ?><h2 class="titre_vid_img">Vidéos (<?php if($compteur_pages_videos['nb_videos']==''){echo '0';}else{echo $compteur_pages_videos['nb_videos'];}?>)</h2><?php
                                    $videos->execute(array($_SESSION['id']));
                                    affichage_videos($videos); //Affiche les videos
                                    $videos->closeCursor();

                                    ?><h2 class="titre_vid_img">Images (<?php if($compteur_pages_images['nb_images']==''){echo '0';}else{echo $compteur_pages_images['nb_images'];}?>)</h2><?php
                                    $images->execute(array($_SESSION['id']));
                                    affichage_images($images); //Affiche les images    
                                    $images->closeCursor();
                                }
                            }
                            else {
                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_videos FROM videos,likes_videos WHERE categorie=? AND id_membre=? AND id=id_video AND like_dislike="like"');
                                $compteur_page->execute(array($_POST['categorie'],$_SESSION['id']));
                                $compteur_pages_videos=$compteur_page->fetch();
                                $compteur_page->closeCursor();

                                $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images,likes_images WHERE categorie=? AND id_membre=? AND id=id_image AND like_dislike="like"');
                                $compteur_page->execute(array($_POST['categorie'],$_SESSION['id']));
                                $compteur_pages_images=$compteur_page->fetch();
                                $compteur_page->closeCursor();

                                if($compteur_pages_videos['nb_videos']>$compteur_pages_images['nb_images']){
                                    $nb_pages=ceil($compteur_pages_videos['nb_videos']/6); //ceil() arrondi un float à l'entier supérieur
                                }
                                else{
                                    $nb_pages=ceil($compteur_pages_images['nb_images']/6); //ceil() arrondi un float à l'entier supérieur
                                }

                                if($compteur_pages_videos['nb_videos'] + $compteur_pages_images['nb_images']==0){
                                    $nb_pages=1;
                                    echo '<h2 class="rien_trouve">Aucune vidéo/image trouvée dans vos likes</h2>';
                                }//Permet d'afficher x/1 et non x/0 pour le numéro de page
                                else{
                                    ?><h2 class="titre_vid_img">Vidéos (<?php if($compteur_pages_videos['nb_videos']==''){echo '0';}else{echo $compteur_pages_videos['nb_videos'];}?>)</h2><?php
                                    $videos->execute(array($_POST['categorie'],$_SESSION['id']));
                                    affichage_videos($videos); //Affiche les videos
                                    $videos->closeCursor();

                                    ?><h2 class="titre_vid_img">Images (<?php if($compteur_pages_images['nb_images']==''){echo '0';}else{echo $compteur_pages_images['nb_images'];}?>)</h2><?php
                                    $images->execute(array($_POST['categorie'],$_SESSION['id']));
                                    affichage_images($images); //Affiche les images
                                    $images->closeCursor();
                                }
                            }
                        }
                        ?>
                        <form method="post" action="">
                            <?php
                            if (!empty($_POST['categorie'])){echo '<input type="hidden" name="categorie" value="'.$_POST['categorie'].'">';}
                            if (!empty($_POST['tri'])){echo '<input type="hidden" name="tri" value="'.$_POST['tri'].'">';}
                            if (!empty($_POST['type'])){echo '<input type="hidden" name="type" value="'.$_POST['type'].'">';}

                            echo '<input type="number" name="page" id="page" value="'.$_POST['page'].'" min=1 max="'.$nb_pages.'">';

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