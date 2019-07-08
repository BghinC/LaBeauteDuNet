<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="google-site-verification" content="hjPlmkQUdN567WYc-2RJuosGZLSgdpdVbTXN5lQ1JRM" />
        <link rel="stylesheet" href="res/css/index.css" />
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
                <article>
                    <?php
                    if(isset($_GET['not_connected'])){
                        echo '<p class="not_connected">Veuillez vous connecter pour accéder à cette page</p>';
                    }
                    
                    for ($i=1;$i<=4;$i++){
                        if ($i==1){?>
                            <h1>Les meilleures vidéos</h1>
                                <?php
                                $videos = $bdd->query('SELECT id,nom,date_ajout,url,categorie,source,rapport_like_dislike FROM videos ORDER BY rapport_like_dislike DESC LIMIT 0,3'); //Order by like-dislike
                                affichage_videos($videos);
                                $videos->closeCursor();?>
                            <?php
                        }
                        if ($i==2){?>
                            <h1>Les dernières vidéos</h1>
                                <?php
                                $videos = $bdd->query('SELECT id,nom,date_ajout,url,categorie,source FROM videos ORDER BY date_ajout DESC LIMIT 0,3');
                                affichage_videos($videos);
                                $videos->closeCursor();?>
                            <?php
                        }
                        elseif ($i==3){?>
                            <h1>Les meilleures images</h1>
                                <?php
                                $images = $bdd->query('SELECT id,nom,date_ajout,url,categorie,rapport_like_dislike FROM images ORDER BY rapport_like_dislike DESC LIMIT 0,3');//Order by like-dislike
                                affichage_images($images);
                                $images->closeCursor();?>
                            <?php
                        }
                        elseif ($i==4){?>
                            <h1>Les dernières images</h1>
                                <?php
                                $images = $bdd->query('SELECT id,nom,date_ajout,url,categorie FROM images ORDER BY date_ajout DESC LIMIT 0,3');
                                affichage_images($images);
                                $images->closeCursor();?><?php 
                        }                
                    }?>
                </article>
            </section> 
        </div>

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    
    </body>
        
</html>