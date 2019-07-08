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
    	<link rel="stylesheet" href="res/css/parametres.css" />
        <?php
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_usuelles.php");
        ?>
    </head>

	<body>

	    <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>

	    <div class="corps">

		    <?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php"); ?>

				<section>

					<?php //Changement de pseudo
					if(!empty($_POST['changement_pseudo'])){
						if(htmlentities($_POST['new_pseudo1']) == htmlentities($_POST['new_pseudo2'])){//Parcours la bdd pour voir si le pseudo est disponnible
		            		$req=$bdd->prepare('SELECT pseudo FROM membres WHERE pseudo=?');
		            		$req->execute(array(htmlentities($_POST['new_pseudo1'])));
		            		$pseudo_non_dispo = $req->fetch();
		            		$req->closeCursor();
						
		            		if ($pseudo_non_dispo){
		            			echo '<p class="changement_rate">Le pseudo choisi n\'est pas disponnible</p>';
		            		}

		            		else{ //Si le pseudo des dispo alors on change
		            			$req=$bdd->prepare('UPDATE membres SET pseudo=? WHERE id=?');
		            			$req->execute(array(htmlentities($_POST['new_pseudo1']),$_SESSION['id']));
		            			$_SESSION['pseudo']=htmlentities($_POST['new_pseudo1']);
		            			echo'<p class="changement_reussi">Votre pseudo a bien été changé</p>';
		            			$req->closeCursor();
		            		}
		        		}
						else{ //Si les deux champs ne correspondent pas
							echo '<p class="changement_rate">Les deux pseudos ne correspondent pas</p>';
						}
					}?>

					<?php //Définir un mot de passe
						if(!empty($_POST['nouveau_mot_de_passe'])){
							$_POST['password1'] = htmlentities($_POST['password1']);
							$_POST['password2'] = htmlentities($_POST['password2']);

							if ( (!empty($_POST['password1']) && !empty($_POST['password2']) && !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", $_POST['password1'])) || (!empty($_POST['password1']) && !empty($_POST['password2']) && ($_POST['password1'] != $_POST['password2']) ) ) {

								if(!empty($_POST['password1']) && !empty($_POST['password2']) && !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", $_POST['password1'])){
			        				echo '<p class="changement_rate">Le format du mot de passe est incorrect</p>';
			        			}

			        			if(!empty($_POST['password1']) && !empty($_POST['password2']) && ($_POST['password1'] != $_POST['password2'])){
			        				//Si les deux mots de passe ne correspondent pas on affiche la phrase et le formulaire à nouveau
			        				echo '<p class="changement_rate">Les mots de passe ne correspondent pas</p>';
			        			}
			        		}

			        		else{
								$pass_hache = password_hash($_POST['password1'], PASSWORD_DEFAULT);
			            		$req=$bdd->prepare('UPDATE membres SET pass=?, isPasswordModified="1" WHERE id=?');
			            		$req->execute(array($pass_hache,$_SESSION['id']));
			            		echo'<p class="changement_reussi">Votre mot de passe a bien été crée</p>';
			            		setcookie('pass',$pass_hache,time()+60*60*24*30,'/','labeautedunet.fr',true,true);
			            		$req->closeCursor();
				            }
						}
					?>

					<?php //Changement de mot de passe
					if(!empty($_POST['changement_mot_de_passe'])){
						$_POST['new_password1'] = htmlentities($_POST['new_password1']);
						$_POST['new_password2'] = htmlentities($_POST['new_password2']);

						//Requête pour vérifier le mot de passe actuel
						$req=$bdd->prepare('SELECT pass FROM membres WHERE id=?');
		            	$req->execute(array(htmlentities($_SESSION['id'])));
		            	$mot_de_passe_actuel = $req->fetch();
		            	$req->closeCursor();

		            	$is_password_correct = password_verify(htmlentities($_POST['actual_password']), $mot_de_passe_actuel['pass']);
		            	if($is_password_correct){

							if ( (!empty($_POST['new_password1']) && !empty($_POST['new_password2']) && !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", $_POST['new_password1'])) || (!empty($_POST['new_password1']) && !empty($_POST['new_password2']) && ($_POST['new_password1'] != $_POST['new_password2']) ) ) {

								if(!empty($_POST['new_password1']) && !empty($_POST['new_password2']) && !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", $_POST['new_password1'])){
			        				echo '<p class="changement_rate">Le format du mot de passe est incorrect</p>';
			        			}

			        			if(!empty($_POST['new_password1']) && !empty($_POST['new_password2']) && ($_POST['new_password1'] != $_POST['new_password2'])){
			        				//Si les deux mots de passe ne correspondent pas on affiche la phrase et le formulaire à nouveau
			        				echo '<p class="changement_rate">Les mots de passe ne correspondent pas</p>';
			        			}
			        		}
								
							else{
								$pass_hache = password_hash($_POST['new_password1'], PASSWORD_DEFAULT);
			            		$req=$bdd->prepare('UPDATE membres SET pass=? WHERE id=?');
			            		$req->execute(array($pass_hache,$_SESSION['id']));
			            		echo'<p class="changement_reussi">Votre mot de passe a bien été changé</p>';
			            		setcookie('pass',$pass_hache,time()+60*60*24*30,'/','labeautedunet.fr',true,true);
			            		$req->closeCursor();
				            }
			        	}
			        	else{
			        		echo'<p class="changement_rate">Le mot de passe actuel est erroné</p>';
			        	}
		        	}
					?>

					<?php //Changement d'email
					if(!empty($_POST['changement_email']) && !empty($_POST['envoi_mail'])){
						if(htmlentities($_POST['new_email1']) == htmlentities($_POST['new_email2'])){
							if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['new_email1'])){
								$req=$bdd->prepare('SELECT pseudo,email FROM membres WHERE email=?');
		            			$req->execute(array($_POST['new_email1']));
		            			$requete = $req->fetch();
		            			if(!empty($requete)){
		            				?><p class="changement_rate">Adresse email déjà utilisée</p><?php
		            			}
		            			else{

		            				$date = date("Y-m-d H:i:s");
                                	$code = md5($requete['pseudo'].$_POST['new_email1'].$date);

		            				//=====Déclaration du destinataire
					                $destinataire = $_POST['new_email1'];
					                //==========

					                //=====Infos expéditeur
					                $nom_expediteur = "LaBeauteDuNet";
					                $mail_expediteur = "contact@labeautedunet.fr";
					                //==========

									//=====Déclaration des messages au format texte et au format HTML.
									$message_txt = "Tu viens de changer ton adresse email ! - Pour confirmer cette nouvelle adresse clique sur ce lien : http://www.labeautedunet.fr/parametres?code=".$code." - Vous recevez cet email car vous venez de valider votre adresse email sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération cela signifie qu'une tiers personne a accès à votre compte. Merci de nous contacter en répondant à cet email.";

									$message_html = "<html><head><link rel=\"stylesheet\" href=\"http://www.labeautedunet.fr/res/css/mail/mail_validation.css\" /></head><body><div><h2><b>Tu viens de changer ton adresse email !</b></h2><p>Pour confirmer cette nouvelle adresse clique sur ce lien :</p><p>http://www.labeautedunet.fr/parametres?code=".$code."</p><p><img src=\"http://www.labeautedunet.fr/res/img/logo_labeautedunet.png\" height=\"125\" width=\"125\"/></p><p class=\"info_sup\">Vous recevez cet email car vous venez de valider votre adresse email sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération cela signifie qu'une tiers personne a accès à votre compte. Merci de nous contacter en répondant à cet email.</p></div></body></html>";
									//==========
									 
									//=====Définition de l'objet.
									$objet = "Changement de votre adresse email !";
									//=========

					                envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet);


		            				$req2=$bdd->prepare('UPDATE membres SET email=?,isEmailValidated="0",codeEmailValidated=? WHERE id=?');
			            			$req2->execute(array(htmlentities($_POST['new_email1']),$code,$_SESSION['id']));
			            			$_SESSION['email']=htmlentities($_POST['new_email1']);
			            			$req2->closeCursor();

			            			?><p class="changement_reussi">Un mail de confirmation vient de vous être envoyé.</p><p class="changement_rate">Vous ne pourrez plus vous connecter si vous ne le validez pas.</p><?php
		            			}
		            			$req->closeCursor();
							}
							else{
								echo '<p class="changement_rate">Adresse email incorrect</p>';
							}
		        		}
						else{ //Si les deux champs ne correspondent pas
							echo '<p class="changement_rate">Les deux adresses email ne correspondent pas</p>';
						}
					}
					elseif(!empty($_GET['code'])){
						$req=$bdd->prepare('SELECT pseudo,email FROM membres WHERE codeEmailValidated=?');
            			$req->execute(array($_GET['code']));
            			$requete = $req->fetch();

            			if(!empty($requete)){
            				$date = date("Y-m-d H:i:s");
                            $code = md5($requete['pseudo'].$requete['pseudo'].$date);

            				$req2=$bdd->prepare('UPDATE membres SET isEmailValidated="1",codeEmailValidated="" WHERE id=?');
            				$req2->execute(array($_SESSION['id']));
            				$req2->closeCursor();

            				?><p class="changement_reussi">Votre adresse mail a bien été validée</p><?php
            			}
            			else{
            				?><p class="changement_rate">Le code n'est plus valide</p><?php
            			}
            			
            			$req->closeCursor();
					}
					?>

					<?php //Supression de compte
					if(!empty($_POST['supprimer_compte'])){
						$delete_compte=$bdd->prepare('DELETE FROM membres WHERE id=?');
	            		$delete_compte->execute(array($_SESSION['id']));
	            		$delete_compte->closeCursor();

	            		$select_video=$bdd->prepare('SELECT id_video,like_dislike FROM likes_videos WHERE id_membre=?');//On selectionne toutes les videos ou l'user a like ou dislike
            			$select_video->execute(array($_SESSION['id']));

            			while($delete = $select_video->fetch()){//On parcours chaquene pour changer la valeur des nb de like
            				if($delete['like_dislike'] == 'like'){
					            //Retire 1 au nombre de like
					            $select_nb_like_video=$bdd->prepare('SELECT nb_like FROM videos WHERE id=?');
					            $select_nb_like_video->execute(array($delete['id_video']));
					            $select_nb_like_video2=$select_nb_like_video->fetch();
					            $select_nb_like_video->closeCursor();

					            $nb_like=$select_nb_like_video2['nb_like']-1;
					            $update_nb_like_video=$bdd->prepare('UPDATE videos SET nb_like = ? WHERE id = ?');
					            $update_nb_like_video->execute(array($nb_like, $delete['id_video']));
					            $update_nb_like_video->closeCursor();

				            	$modif_rapport_video=$bdd->prepare('SELECT nb_like,nb_dislike FROM videos WHERE id=?');
								$modif_rapport_video->execute(array($delete['id_video']));
								$modif_rapport_video2=$modif_rapport_video->fetch();
								$modif_rapport_video->closeCursor();

								$rapport_like_dislike = $req2['nb_like'] - $req2['nb_dislike']; //Nombre de like - Nombre de dislike
								
								$req=$bdd->prepare('UPDATE videos SET rapport_like_dislike=:rapport WHERE id = :id');
								$req->execute(array('rapport'=>$rapport_like_dislike, 'id'=>$delete['id_video'])); 
								$req->closeCursor();

            				}
            				elseif($delete['like_dislike'] == 'dislike'){
					            //Retire 1 au nombre de dislike
					            $req=$bdd->prepare('SELECT nb_dislike FROM videos WHERE id=?');
					            $req->execute(array($delete['id_video']));
					            $req2=$req->fetch();
					            $req->closeCursor();

					            $nb_dislike=$req2['nb_dislike']-1;
					            $req=$bdd->prepare('UPDATE videos SET nb_dislike = ? WHERE id = ?');
					            $req->execute(array($nb_dislike, $delete['id_video']));
					            $req->closeCursor();

				            	$req=$bdd->prepare('SELECT nb_like,nb_dislike FROM videos WHERE id=?');
								$req->execute(array($delete['id_video']));
								$req2=$req->fetch();
								$req->closeCursor();

								$rapport_like_dislike = $req2['nb_like'] - $req2['nb_dislike']; //Nombre de like - Nombre de dislike
								
								$req=$bdd->prepare('UPDATE videos SET rapport_like_dislike=:rapport WHERE id = :id');
								$req->execute(array('rapport'=>$rapport_like_dislike, 'id'=>$delete['id_video']));
								$req->closeCursor();
            				}
            			}$select_video->closeCursor();

            			$delete_likes_videos=$bdd->prepare('DELETE FROM likes_videos WHERE id_membre=?');
            			$delete_likes_videos->execute(array($_SESSION['id']));
            			$delete_likes_videos->closeCursor();

	            		$select_image=$bdd->prepare('SELECT id_image,like_dislike FROM likes_images WHERE id_membre=?');//On selectionne toutes les images ou l'user a like ou dislike 
            			$select_image->execute(array($_SESSION['id']));

            			while($delete = $select_image->fetch()){//On parcours chaquene pour changer la valeur des nb de like
            				if($delete['like_dislike'] == 'like'){
					            //Retire 1 au nombre de like
					            $req=$bdd->prepare('SELECT nb_like FROM images WHERE id=?');
					            $req->execute(array($delete['id_image']));
					            $req2=$req->fetch();
					            $req->closeCursor();

					            $nb_like=$req2['nb_like']-1;
					            $req=$bdd->prepare('UPDATE images SET nb_like = ? WHERE id = ?');
					            $req->execute(array($nb_like, $delete['id_image']));
					            $req->closeCursor();

				                $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM images WHERE id=?');
							    $req->execute(array($delete['id_image']));
							    $req2=$req->fetch();
							    $req->closeCursor();

							    $rapport_like_dislike = $req2['nb_like'] - $req2['nb_dislike']; //Nombre de like - Nombre de dislike

							    $req=$bdd->prepare('UPDATE images SET rapport_like_dislike=:rapport WHERE id = :id');
							    $req->execute(array('rapport'=>$rapport_like_dislike, 'id'=>$delete['id_image'])); 
							    $req->closeCursor();
            				}
            				elseif($delete['like_dislike'] == 'dislike'){
					            //Retire 1 au nombre de dislike
					            $req=$bdd->prepare('SELECT nb_dislike FROM images WHERE id=?');
					            $req->execute(array($delete['id_image']));
					            $req2=$req->fetch();
					            $req->closeCursor();

					            $nb_dislike=$req2['nb_dislike']-1;
					            $req=$bdd->prepare('UPDATE images SET nb_dislike = ? WHERE id = ?');
					            $req->execute(array($nb_dislike, $delete['id_image']));
					            $req->closeCursor();

				                $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM images WHERE id=?');
							    $req->execute(array($delete['id_image']));
							    $req2=$req->fetch();
							    $req->closeCursor();

							    $rapport_like_dislike = $req2['nb_like'] - $req2['nb_dislike']; //Nombre de like - Nombre de dislike

							    $req=$bdd->prepare('UPDATE images SET rapport_like_dislike=:rapport WHERE id = :id');
							    $req->execute(array('rapport'=>$rapport_like_dislike, 'id'=>$delete['id_image'])); 
							    $req->closeCursor();
            				}
            			}$select_image->closeCursor();

            			$delete_likes_images=$bdd->prepare('DELETE FROM likes_images WHERE id_membre=?');
            			$delete_likes_images->execute(array($_SESSION['id']));
            			$delete_likes_images->closeCursor();

            			$_SESSION = array();
						session_destroy();
							echo '<p class="changement_reussi">Votre compte a été supprimé</p>';
					}?>

					<!Infos user>
					<h1> Mes informations </h1>
					<form method="post" action="" class="formulaire_parametres">
				        <p class="paragraphe_formulaire">Pseudo :</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="pseudo" value='.$_SESSION['pseudo'].' disabled="disabled" />';?>
				        <p class="paragraphe_formulaire">Email :</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="pseudo" value='.$_SESSION['email'].' disabled="disabled" />';?>
					</form>

					<hr class="separation_formulaire">

					<h1>Changer mon pseudo</h1>
					<form method="post" action="" class="formulaire_parametres">
						<p class="paragraphe_formulaire">Pseudo actuel:</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="pseudo" value='.$_SESSION['pseudo'].' disabled="disabled" />';?>
				        <p class="paragraphe_formulaire">Nouveau pseudo :</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="new_pseudo1" required="required" />';?>
				        <p class="paragraphe_formulaire">Confimez votre nouveau pseudo :</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="new_pseudo2" required="required" />';?>
				        <p></p>
				        <input type="submit" name="changement_pseudo" class="valider_changement" value="Changer mon pseudo">
				    </form>

				    <hr class="separation_formulaire">
				    <?php
				    $req=$bdd->prepare('SELECT isPasswordModified FROM membres WHERE id=?');
		            $req->execute(array($_SESSION['id']));
		            $ispasswordmodified = $req->fetch();

		            if($ispasswordmodified['isPasswordModified'] == "0"){?>
		            	<h1>Définir un mot de passe</h1>
						<form method="post" action="" class="formulaire_parametres">
					        <p class="paragraphe_formulaire">Mot de passe :</p>
					        <label class="infos_sup_form">6 caractères minimum dont 1 majuscule et 1 minuscule</label></br>
					        <?php echo '<input class ="input_formulaire_parametre" type="password" name="password1" required="required" />';?>
					        <p class="paragraphe_formulaire">Confimez votre mot de passe :</p>
					        <?php echo '<input class ="input_formulaire_parametre" type="password" name="password2" required="required" />';?>
					        <p></p>
					        <input type="submit" name="nouveau_mot_de_passe" class="valider_changement" value="Changer mon mot de passe">
					    </form>
		            <?php }
		           	else{?>
						<h1>Changer mon mot de passe</h1>
						<form method="post" action="" class="formulaire_parametres">
							<p class="paragraphe_formulaire">Mot de passe actuel:</p>
					        <?php echo '<input class ="input_formulaire_parametre" type="password" name="actual_password" required="required" />';?>
					        <p class="paragraphe_formulaire">Nouveau mot de passe :</p>
					        <label class="infos_sup_form">6 caractères minimum dont 1 majuscule et 1 minuscule</label></br>
					        <?php echo '<input class ="input_formulaire_parametre" type="password" name="new_password1" required="required" />';?>
					        <p class="paragraphe_formulaire">Confimez votre nouveau mot de passe :</p>
					        <?php echo '<input class ="input_formulaire_parametre" type="password" name="new_password2" required="required" />';?>
					        <p></p>
					        <input type="submit" name="changement_mot_de_passe" class="valider_changement" value="Changer mon mot de passe">
					    </form>
					<?php } ?>

				    <hr class="separation_formulaire">

				    <h1>Changer mon email</h1>
					<form method="post" action="" class="formulaire_parametres">
						<p class="paragraphe_formulaire">Email actuel:</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="email" value='.$_SESSION['email'].' disabled="disabled" />';?>
				        <p class="paragraphe_formulaire">Nouvelle adresse email :</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="new_email1" required="required" placeholder="exemple@gmail.com" />';?>
				        <p class="paragraphe_formulaire">Confimez votre nouvelle adresse email :</p>
				        <?php echo '<input class ="input_formulaire_parametre" type="text" name="new_email2" required="required" placeholder="exemple@gmail.com" />';?>

				        <input class ="input_formulaire_parametre" type="hidden" name="envoi_mail" required="required" value="1"/>
				        <p></p>
				        <input type="submit" name="changement_email" class="valider_changement" value="Changer mon email">
				    </form>

				    <hr class="separation_formulaire">

				    <h1>Supprimer mon compte</h1>
					<form method="post" action="parametres.php" class="formulaire_parametres">
						<p id="paragraphe_checkbox"><input name="check_box_supprimer_compte" id="check_box_supprimer_compte" type="checkbox" required="required">Je supprimme mon compte</input></p>
				        <input type="submit" name="supprimer_compte" class="valider_changement" value="Confirmer la suppression">
				    </form>

				    <hr class="separation_formulaire">


				</section>
			</div>
		    <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
	</body>
</html>
<?php } ?>