<?php // Redirection si déjà connecté
if(!empty($_SESSION['id'])){
header('Location: http://www.labeautedunet.fr');
exit();
}
else{?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="res/css/connexion.css" />
        <script src='https://www.google.com/recaptcha/api.js'></script> <!-- Pour Captcha -->
        <?php
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
        
    </head>

    <body>


        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>

        <div class="corps">

	        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php"); ?>
     
	        <section>
	            <?php //Connexion à la base de donnée
				include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");

	            if(!empty($_POST['connexion'])) {

	                // Ma clé privée
					$secret = "6LcQNVMUAAAAABhB3cSPTefkmaejRWe33OLnU5kS";
					// Paramètre renvoyé par le recaptcha
					$response = $_POST['g-recaptcha-response'];
					// On récupère l'IP de l'utilisateur
					$remoteip = $_SERVER['REMOTE_ADDR'];
					
					$api_url = "https://www.google.com/recaptcha/api/siteverify?secret=" 
					    . $secret
					    . "&response=" . $response
					    . "&remoteip=" . $remoteip ;
					
					$decode = json_decode(file_get_contents($api_url), true);

					if($decode['success'] == true){

		                $req = $bdd->prepare('SELECT id,pseudo,pass,email,isEmailValidated,niveau_privilege FROM membres WHERE pseudo=? OR email=?');
		                $req -> execute(array(htmlentities($_POST['pseudo_email']),htmlentities($_POST['pseudo_email'])));
		                $resultat = $req->fetch();

		                if (!$resultat){
		                    echo '<p class="id_incorrect">Pseudo/Email ou mot de passe incorrect</p>';
		                }

		                elseif(!empty($_POST['pass']) && !empty($resultat['pass']) && !empty($_POST['g-recaptcha-response'])){
		                    $is_password_correct = password_verify(htmlentities($_POST['pass']), $resultat['pass']);//Check si le mdp correspond
							
		                    if($is_password_correct)//S'il correspond on connecte
		                    {
		                    	if($resultat['isEmailValidated'] == "0"){
		                			echo '<p class="id_incorrect">Veuillez valider votre adresse email pour pouvoir vous connecter</p>';
		                		}
		                    	else{
		                    		if(!empty($_POST['connexion_automatique'])){
			                    		setcookie('pseudo',$resultat['pseudo'],time()+60*60*24*30,'/','labeautedunet.fr',true,true);
			                    		setcookie('pass',$resultat['pass'],time()+60*60*24*30,'/','labeautedunet.fr',true,true);
		                    		}
		                    		$_SESSION['id'] = $resultat['id'];
			                        $_SESSION['pseudo'] = $resultat['pseudo'];
			                        $_SESSION['email'] = $resultat['email'];
			                        $_SESSION['niveau_privilege'] = $resultat['niveau_privilege'];

			                        $req=$bdd->prepare('UPDATE membres SET date_derniere_connexion=CURDATE() WHERE id=?');
					                $req->execute(array($_SESSION['id']));
					                $req->closeCursor();

					                header('Location: http://www.labeautedunet.fr');
				                    exit();
		                    	}
		                        //session_start();//Pas de code html avant session_start()   !!!!!!!!!!!! //session_start() se trouve dans header.php
		                    }

		                    else{
		                        echo '<p class="id_incorrect">Pseudo/Email ou mot de passe incorrect</p>';
		                    }
		                }
		            	$req->closeCursor();
	            	}
	            	else{
	            		echo '<p class="id_incorrect">Captcha incorrect</p>';
	            	}
				}

	            if(empty($_SESSION['id']) && (empty(($_POST['connexion'])) || !$resultat || !$is_password_correct)) {//Affiche le formulaire de connexion
	                ?>

	                <div class="reseaux">
						<div class="element">
							<?php include($_SERVER['DOCUMENT_ROOT']."/Facebook_connexion/index_connexion.php");?>
						</div>
						<div class="element">
							<img src="/res/img/icone_info.png" width="20px" height="20px"/>
							<div class="infobulle">
								Si vous vous connectez avec Facebook alors que vous aviez déjà créé un compte via le formulaire, alors ceux-ci seront fusionnés à condition que les adresses email soient identiques.
							</div>	
						</div>
					</div>
	                
	                <h1>Connexion</h1>
	                <form method="post" action="" id="formulaire_connexion">

	                    <p class="paragraphe_formulaire">Pseudo ou Email :</p>
	                    <input type="text" name="pseudo_email" id="pseudo_email" required="required">

	                    <p class="paragraphe_formulaire">Mot de passe :</p>
	                    <input type="password" name="pass" id="pass" required="required">

	                    <p class="paragraphe_formulaire"></p>
                        <p id="paragraphe_checkbox"><input name="connexion_automatique" id="connexion_automatique" type="checkbox">Se souvenir de moi</input></p>

						<p class="paragraphe_formulaire"></p>
						<div class="g-recaptcha" data-sitekey="6LcQNVMUAAAAACtb4VNzSJgI6eDoOknfMphjksue"></div>

	                    <p class="paragraphe_formulaire"></p>
	                    <input type="submit" name="connexion" value="Connexion" id="bouton_connexion">
	                </form>
	                <p class="reset_login"><a href="/reinitialiser_mdp" class="reset_login_lien">Mot de passe perdu ?</a></p>
	                </article>
	                <?php
	            }?>

        	</section>
    	</div>

    	<?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>

<?php } ?>