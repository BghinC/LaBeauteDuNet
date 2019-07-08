<?php
if(!empty($_GET['tri']) && ($_GET['tri'] != 'Aléatoire' && $_GET['tri'] != 'Plus récente' && $_GET['tri'] != 'Plus ancienne' && $_GET['tri'] != 'Like/Dislike')){
    header('Location: menu_images');
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
        <link rel="stylesheet" href="res/css/menu_images.css" />
        <?php
        include ($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_videos_images.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
        <script type="text/javascript" src="./scripts/videos_images.js"></script>
    </head>


    <body>

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>

        <div class="corps">

            <?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php"); ?>

            <section>
                <?php //connexion_facebook_pour_bouton_partager()?>

                <?php
                if(empty($_GET['page'])){$_GET['page']=1;} //Si le numero de page n'est pas indiqué on lui attribu 1
                $limite = ($_GET['page']-1)*12;//Cela sert dans LIMIT, permet de paginer (ici affichage de 12 images par page)

                    if(!empty($_GET['recherche_saisie'])){//Recherche avec le champ recherche et les catégories
                        if(!empty($_GET['categorie']) && $_GET['categorie']!='Toutes les catégories'){//Si recherche saisie et categorie
                            ?><h1><?php echo 'Recherche de "'.htmlentities($_GET['recherche_saisie']).'" dans la catégorie : '.$_GET['categorie'];?></h1><?php

                            $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images WHERE nom LIKE ? AND categorie=?');

                            $recherche_explosee=explode(" ",htmlentities($_GET['recherche_saisie']));//Place la phrase dans un tableau, chaque mot a un index

                            for($i=0;$i<=count($recherche_explosee)-1;$i++){//Répété (le nombre de mot) fois
                                if(empty($_GET['tri']) || $_GET['tri']=='Aléatoire'){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie FROM images WHERE nom LIKE ? AND categorie=? ORDER BY rand()');
                                }
                                elseif(!empty($_GET['tri']) && $_GET['tri']=='Plus récente'){
                                    $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie, FROM images WHERE nom LIKE ? AND categorie=? ORDER BY date_ajout DESC');
                                }
                                elseif(!empty($_GET['tri']) && $_GET['tri']=='Plus ancienne'){
                                    $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie FROM images WHERE nom LIKE ? AND categorie=? ORDER BY date_ajout');
                                }  
                                elseif(!empty($_GET['tri']) && $_GET['tri']=='Like/Dislike'){
                                    $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie,rapport_like_dislike FROM images WHERE nom LIKE ? AND categorie=? ORDER BY rapport_like_dislike DESC');
                                }    
                                $compteur_page->execute(array('%'.$recherche_explosee[$i].'%',$_GET['categorie']));                      
                                $images->execute(array('%'.$recherche_explosee[$i].'%',$_GET['categorie']));
                            }                   
                        }
                        else{//Si recherche saisie dans toutes les catégories
                            ?><h1><?php echo 'Recherche de "'.htmlentities($_GET['recherche_saisie']).'" dans toutes les catégories';?></h1><?php

                            $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images WHERE nom LIKE ?');

                            $recherche_explosee=explode(" ",htmlentities($_GET['recherche_saisie']));//Place la phrase dans un tableau, chaque mot a un index

                            for($i=0;$i<=count($recherche_explosee)-1;$i++){//Répété (le nombre de mot) fois
                                if(empty($_GET['tri']) || $_GET['tri']=='Aléatoire'){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie FROM images WHERE nom LIKE ? ORDER BY rand()');
                                }
                                elseif(!empty($_GET['tri']) && $_GET['tri']=="Plus récente"){
                                    $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie FROM images WHERE nom LIKE ? ORDER BY date_ajout DESC');
                                }
                                elseif(!empty($_GET['tri']) && $_GET['tri']=='Plus ancienne'){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie FROM images WHERE nom LIKE ? ORDER BY date_ajout');
                                }  
                                elseif(!empty($_GET['tri']) && $_GET['tri']=='Like/Dislike'){
                                    $images = $bdd->prepare('SELECT id,nom,url,categorie,rapport_like_dislike FROM images WHERE nom LIKE ? ORDER BY rapport_like_dislike DESC');
                                }  
                                $compteur_page->execute(array('%'.$recherche_explosee[$i].'%'));                                                 
                                $images->execute(array('%'.$recherche_explosee[$i].'%'));
                            }
                        }
                    }

                    elseif (!empty($_GET['categorie']) && $_GET['categorie']!="Toutes les catégories"){//Si l'utilisateur a cliqué sur le menu et ne fait pas de saisie_recherche'?>
                        <h1><?php echo 'Recherche dans la catégorie : '.$_GET['categorie'];?></h1><?php

                        $compteur_page=$bdd->prepare('SELECT COUNT(*) as nb_images FROM images WHERE categorie=?');
                        $compteur_page->execute(array($_GET['categorie']));

                        if(empty($_GET['tri']) || $_GET['tri']=='Aléatoire'){
                            $images = $bdd->prepare('SELECT id,nom,url,categorie FROM images WHERE categorie=? ORDER BY rand()');
                        }
                        elseif(!empty($_GET['tri']) && $_GET['tri']=="Plus récente"){
                            $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie FROM images WHERE categorie=? ORDER BY date_ajout DESC');
                        }
                        elseif(!empty($_GET['tri']) && $_GET['tri']=="Plus ancienne"){
                            $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie FROM images WHERE categorie=? ORDER BY date_ajout');
                        }    
                        elseif(!empty($_GET['tri']) && $_GET['tri']=="Like/Dislike"){
                            $images = $bdd->prepare('SELECT id,date_ajout,nom,url,categorie,rapport_like_dislike FROM images WHERE categorie=? ORDER BY rapport_like_dislike DESC');
                        }                                                        
                        $images->execute(array($_GET['categorie']));
                        
                    }

                    else{//L'utilisateur a cliqué sur le header "Images"?>
                        <h1>Toutes les images</h1>
                        <?php
                        $compteur_page=$bdd->query('SELECT COUNT(*) as nb_images FROM images'); //Compte le nombre d'images à afficher
                        if(empty($_GET['tri']) || $_GET['tri']=='Plus récente'){
                            $images = $bdd->query('SELECT id,date_ajout,nom,url,categorie FROM images ORDER BY date_ajout DESC');
                        }
                        elseif(!empty($_GET['tri']) && $_GET['tri']=='Aléatoire'){
                            $images = $bdd->query('SELECT id,nom,url,categorie FROM images ORDER BY rand()');
                        }
                        elseif(!empty($_GET['tri']) && $_GET['tri']=='Plus ancienne'){
                            $images = $bdd->query('SELECT id,date_ajout,nom,url,categorie FROM images ORDER BY date_ajout');
                        }   
                        elseif(!empty($_GET['tri']) && $_GET['tri']=='Like/Dislike'){
                            $images = $bdd->query('SELECT id,date_ajout,nom,url,categorie,rapport_like_dislike FROM images ORDER BY rapport_like_dislike DESC');
                        }                       
                    }?>

                    <form method="get" action="" id="formulaire_recherche"><!Creation du formulaire de recherche>
                        <?php 
                        if(!empty($_GET['categorie'])){ 
                            echo '<input type="hidden" name="categorie" value="'.$_GET['categorie'].'" id="categorie">';}//Champ caché pour conservé la catégorie
                        if(!empty($_GET['recherche_saisie'])){//Permet de conserver la recherche dans la barre de recherche
                            echo '<input type="search" name="recherche_saisie" id="recherche_saisie" value="'.htmlentities($_GET['recherche_saisie']).'" size="30">';}
                        else{
                            echo '<input type="search" name="recherche_saisie" id="recherche_saisie" placeholder="Recherche par mot clé" size="30">';}
                        ?>
                        <select name="tri" id="tri">
                            <option <?php if(!empty($_GET['tri']) && $_GET['tri']=="Aléatoire"){echo 'selected="selected"';}?>>Aléatoire</option>
                            <option <?php if(empty($_GET['tri']) || $_GET['tri']=="Plus récente"){echo 'selected="selected"';}?>>Plus récente</option>
                            <option <?php if(!empty($_GET['tri']) && $_GET['tri']=="Plus ancienne"){echo 'selected="selected"';}?>>Plus ancienne</option>
                            <option <?php if(!empty($_GET['tri']) && $_GET['tri']=="Like/Dislike"){echo 'selected="selected"';}?>>Like/Dislike</option>
                        </select>
                        <input type="submit" name="Rechercher" id="valider_recherche" value="Rechercher">
                    </form>
                    <?php
                    //Parcourt la bdd pour connaitre le nombre d'images
                    $compteur_pages=$compteur_page->fetch();
                    $compteur_page->closeCursor();

                    if($compteur_pages['nb_images']==0){
                        $nb_pages=1; 
                        echo '<h3>Aucune image trouvée</h3>';
                    }//Permet d'afficher x/1 et non x/0
                    else{
                        $nb_pages=ceil($compteur_pages['nb_images']/12); //ceil() arrondi un float à l'entier supérieur
                        affichage_images($images);//Appelle de la fonction affichage_images pour afficher le resultat
                        $images->closeCursor();
                    }
                    ?>
                    <form method="get" action="">
                        <?php
                        if (!empty($_GET['categorie'])){echo '<input type="hidden" name="categorie" value="'.$_GET['categorie'].'">';}
                        if (!empty(($_GET['recherche_saisie']))){echo '<input type="hidden" name="recherche_saisie" value="'.htmlentities($_GET['recherche_saisie']).'">';}
                        if (!empty($_GET['tri'])){echo '<input type="hidden" name="tri" value="'.$_GET['tri'].'">';}

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