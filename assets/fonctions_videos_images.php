<link rel="stylesheet" href="res/css/fonctions_videos_images.css" />

<?php /*function connexion_facebook_pour_bouton_partager(){?>
        <div id="fb-root"></div><! Connexion à Facebook pour les boutons partager>
        <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.12';
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        <?php
}*/


function affichage_videos($videos){
	//Connexion à la base de donnée
    include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");

    $compteur_video_ligne=1;//Afin de creer un <div> pour chaque ligne de videos(3videos)
	while($vid = $videos->fetch()){//Parcourt tout la bdd
        if($compteur_video_ligne==1){echo '<div class="ligne_vid">';}?>
        	<div class="conteneur">
	            <div id="background_video">
					
	            	<?php
	                
	                //Le choix de la source permet de changer de lecteur si la vidéo vient de youtube
	                if($vid['source']=="youtube"){echo '<iframe width="252" height="252" src="'.$vid['url'].'?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>';}
	                elseif($vid['source']=="fichier"){echo '<video id="video1" src='.$vid['url'].' controls contextmenu="return false;" oncontextmenu="return false;" controlsList="nodownload" width="252" height="252"></video>';}?>

	                <div id="legende">
	                    <p id="legende_nom"><?php echo $vid['nom'];?></p>
	                    <p id="legende_categorie"><?php echo $vid['categorie'];?></p>
	                    <div id="div_pouce">
	                        
	                        <?php 
	                        if (!empty($_SESSION['id'])){
	                            $url_like= 'assets/traitement_like.php?source=video&like_dislike=like&id_video='.$vid['id'].'&id_membre='.$_SESSION['id'].''; //crée l'url personnalisé pour chaque vidéo
	                            $url_dislike= 'assets/traitement_like.php?source=video&like_dislike=dislike&id_video='.$vid['id'].'&id_membre='.$_SESSION['id'].'';
	                        }
	                        else{
	                            $url_like = 'assets/traitement_like.php?source=video&like_dislike=like&id_video='.$vid['id'].'&id_membre=not_connected'; //crée l'url personnalisé pour chaque vidéo
	                            $url_dislike = 'assets/traitement_like.php?source=video&like_dislike=dislike&id_video='.$vid['id'].'&id_membre=not_connected';
	                        }?>
	                        
	                        <?php
	                        if(isset($_SESSION['id'])){
	                        	$is_liked_disliked=$bdd->prepare('SELECT * FROM likes_videos WHERE id_video=? AND id_membre=?');
		                        $is_liked_disliked->execute(array($vid['id'],$_SESSION['id']));
		                        $is_liked_disliked_2=$is_liked_disliked->fetch();
	                        }
	                        else{
	                        	$is_liked_disliked_2 = false;
	                        }
	                        ?>
	                        <input type="image" title="Like" alt="Like" id="<?php echo 'likevid'.$vid['id'];?>" class="input_pouce" src="<?php if($is_liked_disliked_2 && $is_liked_disliked_2['like_dislike']=="like"){echo "res/img/pouce_vert_rempli.png";} else{echo "res/img/pouce_vert.png";}?>" name="input_pouce_vert" id="input_pouce_vert" onclick="<?php if(!isset($_SESSION['id'])){ echo 'alertNonConnecte()'; } else{ echo 'traitementlike(\''.$url_like.'\',\'likevid'.$vid['id'].'\',\'vid'.$vid['id'].'\')'; } ?>">

	            			<?php
	                        //Récupère le nb de like de la vidéo
	                        $compteur_like=$bdd->prepare('SELECT nb_like FROM videos WHERE id=?');
	                        $compteur_like->execute(array($vid['id']));
	                        $compteur_likes=$compteur_like->fetch();
	                        $compteur_like->closeCursor();
	                        //$compteur_dislikes['nb_dislike']

	            			//Récupère le nb de dislike de la vidéo
	            			$compteur_dislike=$bdd->prepare('SELECT nb_dislike FROM videos WHERE id=?');
	                        $compteur_dislike->execute(array($vid['id']));
	                        $compteur_dislikes=$compteur_dislike->fetch();
	            			$compteur_dislike->closeCursor();
	            			//$compteur_dislikes['nb_dislike']
	                        ?>

	                        <div class="vote">
	                        	<div class="votebar">
	                        		<div class="vote_progress" id="<?php echo 'progressbarvid'.$vid['id'];?>" style="width:
	                        			<?php //Implémentation barre de like
	                        			if($compteur_likes['nb_like']+$compteur_dislikes['nb_dislike'] == 0) {echo '50%';} 
	                        			else{$cpt=0; $cpt = 100 * $compteur_likes['nb_like']/($compteur_likes['nb_like']+$compteur_dislikes['nb_dislike']); echo $cpt.'%';}
	                        			?>
	                        			">
	                        		</div>
	                        		<div class="compteur">
	                                    <script type="text/javascript">
	                                    </script>
		                    		 	<?php
			                			if($compteur_likes['nb_like']+$compteur_dislikes['nb_dislike'] == 0) {
			                				echo '<div id="cptlikevid'.$vid['id'].'" class="pourcent_like">0</div>';
	                                        echo '<button id="video'.$vid['id'].'" class="copy">Get link</button>';
			                				echo '<div id="cptdislikevid'.$vid['id'].'" class="pourcent_dislike">0</div>';
			                			}
			                			else{
			                    			echo '<div id="cptlikevid'.$vid['id'].'" class="pourcent_like">'.$compteur_likes['nb_like'].'</div>';
	                                        echo '<button id="video'.$vid['id'].'" class="copy">Get link</button>';
			                    			echo '<div id="cptdislikevid'.$vid['id'].'" class="pourcent_dislike">'.$compteur_dislikes['nb_dislike'].'</div>';
			                    		}
			                			?>
			                			<!-- Get Link-->
	                                    <script type="text/javascript">
	                                    document.getElementById(<?php echo '"video'.$vid['id'].'"';?>).onclick = function(event) {

	                                        var lien = <?php echo '"http://www.labeautedunet.fr/menu_videos.php?recherche_saisie='.$vid['nom'].'&categorie='.$vid['categorie'].'"';?>

	                                        var container = document.createElement("div");
	                                        container.innerHTML = lien;
	                                        //container.style.opacity = 0; // si on veut rendre invisible tout en restant "selectionable" 
	                                        document.body.appendChild(container);
	                                    
	                                        var sel = window.getSelection();
	                                        var rangeObj = document.createRange();
	                                        rangeObj.selectNodeContents(container);
	                                        sel.removeAllRanges();
	                                        sel.addRange(rangeObj);
	                                        if (document.execCommand('copy')) {
	   											alert("L'URL a été copiée dans le presse papier");
	   										}
	   										else {
	   											alert("Impossible de copier le lien");
	   										}
	                                    };
	                                    </script>
		                			</div>
	                        	</div>
	                        </div>

	                        <input type="image" title="Dislike" alt="Dislike" id="<?php echo 'dislikevid'.$vid['id'];?>" class="input_pouce" src="<?php if($is_liked_disliked_2 && $is_liked_disliked_2['like_dislike']=="dislike"){echo "res/img/pouce_rouge_rempli.png";} else{echo "res/img/pouce_rouge.png";}?>" name="input_pouce_rouge" id="input_pouce_rouge" onclick="<?php if(!isset($_SESSION['id'])){ echo 'alertNonConnecte()'; } else{ echo 'traitementlike(\''.$url_dislike.'\',\'dislikevid'.$vid['id'].'\',\'vid'.$vid['id'].'\')'; } ?>">
	                    </div>

	                    <?php /*
	                    <!Bouton Partager Facebook>
	                    <?php $nom_explose=explode(" ",$vid['nom']);
	                    $nom_vid_facebook='';
	                    for ($i=0;$i<=count($nom_explose)-1;$i++){
	                        if ($i<count($nom_explose)-1){
	                            $nom_explose[$i].="+";
	                            $nom_vid_facebook.=$nom_explose[$i];}
	                        else{$nom_vid_facebook.=$nom_explose[$i];}
	                    }?>
	                    <div class="fb-share-button" id="fb-share-button" data-href=<?php echo 'http://lebestduweb.fr/menu_videos?recherche_saisie='.$nom_vid_facebook.'&recherche_categorie=Toutes+les+catégories&Rechercher=Valider';?> data-layout="button_count" data-size="small" data-mobile-iframe="true">
	                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fbestduweb%2Fmenu_videos&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a>
	                    </div>
	                    <!Fin Bouton Partager Facebook>
	                    */?>

	                </div>
	                
	            </div>

	            <div class="bouton_signaler">
					<?php 
                    if (!empty($_SESSION['id'])){
                        $url_signaler= 'assets/traitement_signaler.php?source=video&vid_titre='.$vid['nom'].'&id_video='.$vid['id'].'&id_membre='.$_SESSION['id'].'&pseudo_membre='.$_SESSION['pseudo'].'&email_membre='.$_SESSION['email'].'';
                    }
                    else{
                        $url_signaler = 'assets/traitement_signaler.php?&id_membre=not_connected';
                    }?>
		            <input type="image" src="res/img/img_signaler.png" title="Signaler un lien mort" name="signaler" id="img_signaler" alt="Signaler" onclick="send('<?php echo $url_signaler;?>')">
		        </div>
		    </div>

            <?php
            $compteur_video_ligne++;
        if($compteur_video_ligne>3){$compteur_video_ligne=1;echo '</div>';}//Ferme la balise de ligne lorsque 3 videos ont été affichées?>
        <?php 
    }
    if($compteur_video_ligne!=1){echo '</div>';} //Ferme la balise de ligne à la fin de l'affichage
    $videos->closeCursor();
}

function affichage_videos_a_moderer_admin($videos){
    //Connexion à la base de donnée
    include("assets/connexion_bdd.php");

    $compteur_video_ligne=1;//Afin de creer un <div> pour chaque ligne de videos(3videos)
    while($vid = $videos->fetch()){//Parcourt tout la bdd
        if($compteur_video_ligne==1){echo '<div class="ligne_vid">';}?>
            <div id="background_video">
                <?php //Affichage du cadre avec la vidéo
                echo '<iframe width="252" height="252" src="'.$vid['url'].'?rel=0&showinfo=0" frameborder="0"></iframe>';
                ?>

                <div id="legende">
                    <p id="legende_nom"><?php echo $vid['nom'];?></p>
                    <p id="legende_categorie"><?php echo $vid['categorie'];?></p>
                    <div id="div_pouce">
                        <script type="text/javascript">
                            function send(url_var) {
                                var DSLScript  = document.createElement("script");
                                DSLScript.src  = url_var;
                                DSLScript.type = "text/javascript";
                                document.body.appendChild(DSLScript);
                                document.body.removeChild(DSLScript);
                            }
                            
                            function callback(sMessage) {
                                alert(sMessage);
                            }
                        
                        </script>
                        <?php 
                        $url_validation= 'assets/traitement_moderation.php?choix=valide&id_video='.$vid['id']; //crée l'url personnalisé pour chaque vidéo
                        $url_refus= 'assets/traitement_moderation.php?choix=refuse&id_video='.$vid['id'];
                        ?>
                        
                        <input type="image" src="res/img/pouce_vert.png" class="input_pouce" alt="Valider" title="Valider" name="input_pouce_vert" id="input_pouce_vert" onclick="send('<?php echo $url_validation;?>')">
                        <input type="image" src="res/img/pouce_rouge.png" class="input_pouce" alt="Refuser" title="Refuser" name="input_pouce_rouge" id="input_pouce_rouge" onclick="send('<?php echo $url_refus;?>')">
                    </div>
                </div>
                
            </div>
            <?php
            $compteur_video_ligne++;
        if($compteur_video_ligne>3){$compteur_video_ligne=1;echo '</div>';}//Ferme la balise de ligne lorsque 3 videos ont été affichées?>
        <?php 
    }
    if($compteur_video_ligne!=1){echo '</div>';} //Ferme la balise de ligne à la fin de l'affichage
    $videos->closeCursor();                   
}

function affichage_videos_a_moderer($videos){
    //Connexion à la base de donnée
    include("assets/connexion_bdd.php");

    $compteur_video_ligne=1;//Afin de creer un <div> pour chaque ligne de videos(3videos)
    while($vid = $videos->fetch()){//Parcourt tout la bdd
        if($compteur_video_ligne==1){echo '<div class="ligne_vid">';}?>
            <div id="background_video">
                <?php //Affichage du cadre avec la vidéo
                echo '<iframe width="252" height="252" src="'.$vid['url'].'?rel=0&showinfo=0" frameborder="0"></iframe>';
                ?>

                <div id="legende">
                    <p id="legende_nom"><?php echo $vid['nom'];?></p>
                    <p id="legende_categorie"><?php echo $vid['categorie'];?></p>
                </div>
                
            </div>
            <?php
            $compteur_video_ligne++;
        if($compteur_video_ligne>3){$compteur_video_ligne=1;echo '</div>';}//Ferme la balise de ligne lorsque 3 videos ont été affichées?>
        <?php 
    }
    if($compteur_video_ligne!=1){echo '</div>';} //Ferme la balise de ligne à la fin de l'affichage
    $videos->closeCursor();                   
}

function affichage_images($images){
	//Connexion à la base de donnée
    include("assets/connexion_bdd.php");

    $compteur_image_ligne=1;//Afin de creer un <div> pour chaque ligne d'images(3videos)
    while($img = $images->fetch()){//Parcourt toute la bdd
        if($compteur_image_ligne==1){echo '<div class="ligne_img">';}?>
        	<div class="conteneur">
	            <div id="background_image">
					<script>
						function affichage_overlay_on(id) {
						    document.getElementById(id).style.display = "block";
						}

						function affichage_overlay_off(id) {
						    document.getElementById(id).style.display = "none";
						}
					</script>

	            	<?php
	                echo '<img class="affichage_image" onclick="affichage_overlay_on('.$img['id'].')" src="res/Images_bdd/'.$img['url'].'" contextmenu="return false;" oncontextmenu="return false;" width="252" height="252"/>';
	                echo '<div id="'.$img['id'].'" class="overlay"><img class="overlay_zoom" onclick="affichage_overlay_off('.$img['id'].')" src="res/Images_bdd/'.$img['url'].'" contextmenu="return false;" oncontextmenu="return false;" width="504" height="504"/></div>';?>

	                <div id="legende">
	                    <p id="legende_nom"><?php echo $img['nom'];?></p>
	                    <p id="legende_categorie"><?php echo $img['categorie'];?></p>
	                    <div id="div_pouce">
	                        
	                        <?php 
	                        if (!empty($_SESSION['id'])){
	                            $url_like= 'assets/traitement_like.php?source=image&like_dislike=like&id_image='.$img['id'].'&id_membre='.$_SESSION['id'].''; //crée l'url personnalisé pour chaque vidéo
	                            $url_dislike= 'assets/traitement_like.php?source=image&like_dislike=dislike&id_image='.$img['id'].'&id_membre='.$_SESSION['id'].'';
	                        }
	                        else{
	                            $url_like = 'assets/traitement_like.php?source=image&like_dislike=like&id_image='.$img['id'].'&id_membre=not_connected'; //crée l'url personnalisé pour chaque vidéo
	                            $url_dislike = 'assets/traitement_like.php?source=image&like_dislike=dislike&id_image='.$img['id'].'&id_membre=not_connected';
	                        }?>

	                        <?php
	                        if(isset($_SESSION['id'])){
	                        	$is_liked_disliked=$bdd->prepare('SELECT * FROM likes_images WHERE id_image=? AND id_membre=?');
		                        $is_liked_disliked->execute(array($img['id'],$_SESSION['id']));
		                        $is_liked_disliked_2=$is_liked_disliked->fetch();
	                        }
	                        else{
	                        	$is_liked_disliked_2 = false;
	                        }
	                        ?>
	                        
	                        <input type="image" title="Like" alt="Like" id="<?php echo 'likeimg'.$img['id'];?>" class="input_pouce" src="<?php if($is_liked_disliked_2 && $is_liked_disliked_2['like_dislike']=="like"){echo "res/img/pouce_vert_rempli.png";} else{echo "res/img/pouce_vert.png";}?>" name="input_pouce_vert" id="input_pouce_vert" onclick="<?php if(!isset($_SESSION['id'])){ echo 'alertNonConnecte()'; } else{ echo 'traitementlike(\''.$url_like.'\',\'likeimg'.$img['id'].'\',\'img'.$img['id'].'\')'; } ?>">
	                        
	                        <?php
	                        //Récupère le nb de like de l'image
	                        $compteur_like=$bdd->prepare('SELECT nb_like FROM images WHERE id=?');
	                        $compteur_like->execute(array($img['id']));
	                        $compteur_likes=$compteur_like->fetch();
	                        $compteur_like->closeCursor();

	                        //Récupère le nb de dislike de la vidéo
	                        $compteur_dislike=$bdd->prepare('SELECT nb_dislike FROM images WHERE id=?');
	                        $compteur_dislike->execute(array($img['id']));
	                        $compteur_dislikes=$compteur_dislike->fetch();
	                        $compteur_dislike->closeCursor();
	                        ?>

	                        <div class="vote">
	                        	<div class="votebar">
	                        		<div class="vote_progress" id="<?php echo 'progressbarimg'.$img['id'];?>" style="width:
	                        			<?php //Implémentation barre de like
	                        			if($compteur_likes['nb_like']+$compteur_dislikes['nb_dislike'] == 0) {echo '50%';} 
	                        			else{$cpt=0; $cpt = 100 * $compteur_likes['nb_like']/($compteur_likes['nb_like']+$compteur_dislikes['nb_dislike']); echo $cpt.'%';}
	                        			?>
	                        			">
	                        		</div>
	                        		<div class="compteur">
		                    		 	<?php
			                			if($compteur_likes['nb_like']+$compteur_dislikes['nb_dislike'] == 0) {
			                				echo '<div id="cptlikeimg'.$img['id'].'" class="pourcent_like">0</div>';
	                                        echo '<button id="image'.$img['id'].'" class="copy">Get link</button>';
			                				echo '<div id="cptdislikeimg'.$img['id'].'" class="pourcent_dislike">0</div>';
			                			}
			                			else{
			                    			echo '<div id="cptlikeimg'.$img['id'].'" class="pourcent_like">'.$compteur_likes['nb_like'].'</div>';
	                                        echo '<button id="image'.$img['id'].'" class="copy">Get link</button>';
			                    			echo '<div id="cptdislikeimg'.$img['id'].'" class="pourcent_dislike">'.$compteur_dislikes['nb_dislike'].'</div>';
			                    		}
			                			?>
	                                    <script type="text/javascript">
	                                    document.getElementById(<?php echo '"image'.$img['id'].'"';?>).onclick = function(event) {

	                                        var lien = <?php echo '"http://www.labeautedunet.fr/menu_images.php?recherche_saisie='.$img['nom'].'&categorie='.$img['categorie'].'"';?>

	                                        var container = document.createElement("div");
	                                        container.innerHTML = lien;
	                                        //container.style.opacity = 0; // si on veut rendre invisible tout en restant "selectionable" 
	                                        document.body.appendChild(container);
	                                    
	                                        var sel = window.getSelection();
	                                        var rangeObj = document.createRange();
	                                        rangeObj.selectNodeContents(container);
	                                        sel.removeAllRanges();
	                                        sel.addRange(rangeObj);
	                                        if (document.execCommand('copy')) {
	   											alert("L'URL a été copiée dans le presse papier");
	   										}
	   										else {
	   											alert("Impossible de copier le lien");
	   										}
	                                    };
	                                    </script>
		                			</div>
	                        	</div>
	                        </div>

	                        <input type="image" title="Dislike" alt="Dislike" id="<?php echo 'dislikeimg'.$img['id'];?>" class="input_pouce" src="<?php if($is_liked_disliked_2 && $is_liked_disliked_2['like_dislike']=="dislike"){echo "res/img/pouce_rouge_rempli.png";} else{echo "res/img/pouce_rouge.png";}?>" name="input_pouce_rouge" id="input_pouce_rouge" onclick="<?php if(!isset($_SESSION['id'])){ echo 'alertNonConnecte()'; } else{ echo 'traitementlike(\''.$url_dislike.'\',\'dislikeimg'.$img['id'].'\',\'img'.$img['id'].'\')'; } ?>">
	                    </div>
	                    <?php /*
	                    <!Bouton Partager Facebook>
	                    <?php $nom_explose=explode(" ",$img['nom']);
	                    $nom_vid_facebook='';
	                    for ($i=0;$i<=count($nom_explose)-1;$i++){
	                        if ($i<count($nom_explose)-1){
	                            $nom_explose[$i].="+";
	                            $nom_vid_facebook.=$nom_explose[$i];}
	                        else{$nom_vid_facebook.=$nom_explose[$i];}
	                    }?>
	                    <div class="fb-share-button" id="fb-share-button" data-href=<?php echo 'http://lebestduweb.fr/menu_videos?recherche_saisie='.$nom_vid_facebook.'&recherche_categorie=Toutes+les+catégories&Rechercher=Valider';?> data-layout="button_count" data-size="small" data-mobile-iframe="true">
	                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fbestduweb%2Fmenu_videos&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a>
	                    </div>
	                    <!Fin Bouton Partager Facebook>
	                    */?>

	                </div>
	            </div>
            	<div class="bouton_signaler">
					<?php 
                    if (!empty($_SESSION['id'])){
                        $url_signaler= 'assets/traitement_signaler.php?source=image&img_titre='.$img['nom'].'&id_image='.$img['id'].'&id_membre='.$_SESSION['id'].'&pseudo_membre='.$_SESSION['pseudo'].'&email_membre='.$_SESSION['email'].'';
                    }
                    else{
                        $url_signaler = 'assets/traitement_signaler.php?&id_membre=not_connected';
                    }?>
		            <input type="image" src="res/img/img_signaler.png" title="Signaler un problème" name="signaler" id="img_signaler" alt="Signaler" onclick="send('<?php echo $url_signaler;?>')">
		        </div>
		    </div>
		        <?php
            $compteur_image_ligne++;
        if($compteur_image_ligne>3){$compteur_image_ligne=1;echo '</div>';}?>
        <?php 
    }
    if($compteur_image_ligne!=1){echo '</div>';}
    $images->closeCursor();
}?>