<?php //Permet de rediriger les utilisateur vers l'index s'il veulent accéder à la page d'ajout
session_start();
if($_SESSION['niveau_privilege'] != ("potes" || "admin")){
    header('Location: http://www.labeautedunet.fr');
    exit();
}
elseif ($_SESSION['niveau_privilege'] == ("potes" || "admin")){?>

<?php

//Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");

if(!empty($_POST['Supprimer'])){
	$delete_partie=$bdd->prepare('DELETE FROM pour_combien WHERE id_partie=?');
	$delete_partie->execute(array($_POST['id_partie']));
	$delete_partie->closeCursor();
}
?>

<!DOCTYPE html>
<html>


    <head>
        <link rel="stylesheet" href="/Pour_combien/feuilles_css/index.css" />
        <link rel="icon" type="image/png" href="/res/img/logo_labeautedunet.png" />
        <?php
        include ("../assets/fonctions.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
        
    </head>

    <body>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>

        <div class="corps">
            <!Insertion du menu>
            <?php include("../assets/menu.php"); ?>
            <!Fin insertion menu>  

            <section>
            	<h1>POUR COMBIEN ... ?</h1>
            	<div class="conteneur">
            		            	
	                <div class="commencer_une_partie">
	                	<h2 class="sous-titre">Commencer une partie</h2>

	                	<?php if(!empty($_POST['Commencer_une_partie'])){//Si on commence une partie

	                		if($_POST['pseudo2'] == $_SESSION['pseudo']){ ?>
	                			<p class="partie_pas_commence">Tu ne peux pas te défier toi même enfin !</p>
	                		<?php
	                		}

	                		else{

								$recup_id = $bdd->prepare('SELECT id FROM membres WHERE pseudo=?');
							    $recup_id -> execute(array(htmlentities($_POST['pseudo2'])));
							    $resultat_id = $recup_id->fetch();


								$req = $bdd->prepare('INSERT INTO pour_combien(id_joueur1,id_joueur2,titre) VALUES (:joueur1,:joueur2,:titre)');
								if($req->execute(array(
									'joueur1' => htmlentities($_POST['pseudo1']),
									'joueur2' => $resultat_id['id'],
									'titre' => htmlentities($_POST['titre'])))){echo '<p class="partie_commence">La partie est lancée</p>';}
								else{echo '<p class="partie_pas_commence">Le pseudo n\'existe pas</p>';}
							    $req->closeCursor();
							}
						}?>

	                	<form method="post" action="" id="formulaire_commencer_une_partie">
	                		<input type="text" name="pseudo1" id="pseudo1" hidden="hidden" value=<?php echo '"'.$_SESSION['id'].'"';?> required="required">
	                		<p class="paragraphe_formulaire">Pseudo de ton adversaire :</p>
	                		<select name="pseudo2" id="pseudo2">
	                			<?php
	                			$recup_pseudo = $bdd->query('SELECT pseudo FROM membres WHERE niveau_privilege=\'potes\'');
						    	while($resultat_pseudo = $recup_pseudo->fetch()){
						    		echo '<option>'.$resultat_pseudo['pseudo'].'</option>';
						    	}
						    	?>
	                		</select>
	                		<p class="paragraphe_formulaire">Pour combien tu </p>
	                		<input type="text" name="titre" id="titre" placeholder="Pour combien tu ?" required="required">
	                		<p class="paragraphe_formulaire"></p>
	                    	<input type="submit" name="Commencer_une_partie" value="Commencer une partie" class="bouton_commencer">
	                	</form>
	                </div>
	                <div class="ligne_ver">
	                	<p id="ligne_verticale"></p>
	                </div>
	                <div class="mes_parties">
	                	<h2 class="sous-titre">Mes parties</h2>
	                	<?php
	                		$recuperation_tout = $bdd->prepare('SELECT * FROM pour_combien WHERE id_joueur1=? OR id_joueur2=?');
							$recuperation_tout -> execute(array($_SESSION['id'],$_SESSION['id']));
							while ($recup_tout = $recuperation_tout->fetch()){?>
								<hr>
								<form method="post" action="" id="formulaire_mes_parties">
									<?php //Affichage de l'adversaire
									if($recup_tout['id_joueur1'] != $_SESSION['id']){
										$adversaire = $bdd->prepare('SELECT pseudo FROM membres WHERE id=?');
										$adversaire -> execute(array($recup_tout['id_joueur1']));
										$adv = $adversaire->fetch();
										echo '<p>'.$adv['pseudo'].' te défie : ';
										$isMyGame = '0';
									}
									else{
										$adversaire = $bdd->prepare('SELECT pseudo FROM membres WHERE id=?');
										$adversaire -> execute(array($recup_tout['id_joueur2']));
										$adv = $adversaire->fetch();
										echo '<p>Tu as défié '.$adv['pseudo'].' : ';
										$isMyGame = '1';
									}
									echo 'Pour combien tu '.$recup_tout['titre'].'</p>';
									if(empty($recup_tout['limite']) && $isMyGame == '0' && empty($recup_tout['reponse2'])){ //Si c'est pas ma partie et que j'ai pas encore joué 
										if(empty($_POST['Envoyer'])){?>
											<p>Pour : <input type="number" name="limite" id="limite" value="1" min="2" max="50" required="required"></p>
											<p>Mon nombre : <input type="number" name="mon_nombre" id="mon_nombre" value="1" min="1" max="50" required="required"></p>
											<p><input type="submit" name="Envoyer" value="Envoyer" class="bouton_commencer"></p>
											<?php 
											if((isset($_POST['mon_nombre'])) && ($_POST['mon_nombre'] < '1' ||  $_POST['mon_nombre'] > $_POST['limite'])){
												?>Arrête de tricher ! Choisis un nombre entre 1 et ta limite !<?php
											}
										}
										elseif(!empty($_POST['Envoyer'])){
											$req = $bdd->prepare('UPDATE pour_combien SET limite=:limite,reponse2=:reponse2 WHERE id_partie=:id');
											$req->execute(array(
												'limite' => htmlentities($_POST['limite']),
												'reponse2' => htmlentities($_POST['mon_nombre']),
												'id' => $recup_tout['id_partie']));
										    $req->closeCursor();
										    header('Location: index.php');
    										exit();
										}
									} 
									elseif(empty($recup_tout['limite']) && $isMyGame == '1'){ //Si c'est ma partie et que l'autre n'a pas encore joué?>
										<p>En attente du choix de <?php echo $adv['pseudo'];?>...</p>
									<?php }

									elseif(!empty($recup_tout['limite']) && $isMyGame == '0' && !empty($recup_tout['reponse2']) && $recup_tout['reponse1'] == '0'){ //Si c'est pas ma partie et que j'ai joué (limite+nb) ?>
										<p>En attente du choix de <?php echo $adv['pseudo'];?>...</p>
									<?php }

									elseif(!empty($recup_tout['limite']) && $isMyGame == '1' && !empty($recup_tout['reponse2']) && empty($recup_tout['reponse1'])){ //Si c'est ma partie et que l'autre joueur a joué 
										if(empty($_POST['Envoyer'])){?>
											<p>Pour <?php echo $recup_tout['limite'];?></p>
											<p>Nombre de <?php echo $adv['pseudo'];?> : ?</p>
											<p>Mon nombre : <input type="number" name="mon_nombre" id="mon_nombre" value="1" min="1" max=<?php echo '"'.$recup_tout['limite'].'"'?> required="required"></p>
											<p><input type="submit" name="Envoyer" value="Envoyer" class="bouton_commencer"></p>
											<?php 
										}
										if(!empty($_POST['Envoyer'])){
											$req = $bdd->prepare('UPDATE pour_combien SET reponse1=:reponse1 WHERE id_partie=:id');
											$req->execute(array(
												'reponse1' => htmlentities($_POST['mon_nombre']),
												'id' => $recup_tout['id_partie']));
										    $req->closeCursor();
										    header('Location: index.php');
    										exit();
										}
									}

									else{ //Fin du jeu
										?><p>Pour : <?php echo $recup_tout['limite'];?></p><?php
										if($isMyGame == '1'){
											?>
											<p>Mon nombre : <?php echo $recup_tout['reponse1'];?></p>
											<p>Nombre de <?php echo $adv['pseudo'].' : '.$recup_tout['reponse2'];?> </p>
											<?php
											if($recup_tout['reponse1'] == $recup_tout['reponse2']){ ?>
												<p>Tu as gagné !</p>
											<?php }
											else{
												?> <p>Dommage !</p> <?php
											}
											?>
											<input type="text" name="id_partie" id="id_partie" hidden="hidden" value=<?php echo '"'.$recup_tout['id_partie'].'"';?> required="required">
											<p><input type="submit" name="Supprimer" value="Supprimer" class="bouton_commencer"></p>
											<?php
										}
										else{ ?>
											<p>Mon nombre : <?php echo $recup_tout['reponse2'];?></p>
											<p>Nombre de <?php echo $adv['pseudo'].' : '.$recup_tout['reponse1'];?> </p>
											<?php
											if($recup_tout['reponse1'] == $recup_tout['reponse2']){ ?>
												<p>Tu as perdu !</p>
											<?php }
											else{
												?> <p>Tu t'en sors bien...</p> <?php
											}
										}
									}
									?>

		                		</form>
						<?php }
	                	?>
	                	
	                </div>
	            </div>
            </section> 
        </div>
        
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    
    </body>
        
</html>

<?php } ?>