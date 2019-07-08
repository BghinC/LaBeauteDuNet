<?php // Redirection si déjà connecté
if(!empty($_SESSION['id'])){
header('Location: http://www.labeautedunet.fr');
exit();
}
else{?>

<!DOCTYPE html>
<html>

<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>

    <head>
        <link rel="stylesheet" href="res/css/inscription.css" />
        <script src='https://www.google.com/recaptcha/api.js'></script> <!-- Pour Captcha -->
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
            
            <?php
            if(!empty($_POST['connexion'])) {// Si user a cliqué sur connexion --> vérifie le pseudo et le mdp puis connecte
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
            }

                //Vérification afin de savoir si le pseudo n'est pas déjà pris
            if(!empty(($_POST['pseudo']))){
                $req=$bdd->prepare('SELECT pseudo,email FROM membres WHERE pseudo=? OR email=?');
                $req->execute(array(htmlentities($_POST['pseudo']),htmlentities($_POST['email'])));
                $pseudo_email_non_dispo = $req->fetch();
                $req->closeCursor();
            }

        	if (empty(($_POST['connexion'])) || (!empty(($_POST['connexion'])) && ($_POST['password']) != ($_POST['password2'])) || !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", ($_POST['password'])) || !preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", ($_POST['email'])) || $pseudo_email_non_dispo || (!empty($_POST['connexion']) && $decode['success'] != true)) {

                if(!empty($pseudo_email_non_dispo)){
                	if(!empty($pseudo_email_non_dispo['pseudo']) && $pseudo_email_non_dispo['pseudo'] == htmlentities($_POST['pseudo'])){
                    	echo '<p class="id_incorrect">Pseudo non disponnible</p>';
                    }
                    elseif(!empty($pseudo_email_non_dispo['email']) && $pseudo_email_non_dispo['email'] == htmlentities($_POST['email'])){
                    	echo '<p class="id_incorrect">L\'adresse email est déjà utilisé, connectez-vous <a href="/connexion">ici</a>, si vous avez oublié votre mot de passe vous pouvez le réinitialiser <a href="/reinitialiser_mdp">ici</a></p>';
                    }
                }
                
        		if(!empty(($_POST['password'])) && !empty(($_POST['password2'])) && (($_POST['password']) != ($_POST['password2']))){
        			//Si les deux mots de passe ne correspondent pas on affiche la phrase et le formulaire à nouveau
        			echo '<p class="id_incorrect">Les mots de passe ne correspondent pas</p>';
        		}

        		if (!empty(($_POST['password'])) && !empty(($_POST['password2'])) && !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", ($_POST['password']))){
        			echo '<p class="id_incorrect">Le format du mot de passe est incorrect</p>';
        		}

        		if (!empty(($_POST['email'])) && !preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", ($_POST['email']))){
        			echo '<p class="id_incorrect">Adresse email incorrect</p>';
        		}

                if(!empty($_POST['connexion']) && $decode['success'] != true){
                    echo '<p class="id_incorrect">Captcha incorrect</p>';
                }

                if (!empty($_SESSION['id'])) {//Si l'user rentre se rend sur inscription alors qu'il est déjà connecté, on affiche qu'il est déjà connecté et on n'affiche pas le formulaire
                echo '<h1>Vous êtes déjà connecté !</h1>';
                }
                    
                else{
                    ?>
                	<div class="reseaux">
                        <div class="element">
                            <?php include($_SERVER['DOCUMENT_ROOT']."/Facebook_connexion/index_connexion.php");?>
                        </div>
                        <div class="element">
                            <img src="/res/img/icone_info.png" width="20px" height="20px">
                            <div class="infobulle">
                                Si vous vous inscrivez avec Facebook alors que vous aviez déjà créé un compte via le formulaire, alors ceux-ci seront fusionnés à condition que les adresses email soient identiques.
                            </div>  
                        </div>
                    </div>
                    <?php
        		echo '
    	            <h1>Inscription</h1>
    	            <article>
    		            <form method="post" action="" id="formulaire_inscription">
    		                <p class="paragraphe_formulaire">Pseudo* :</p>
    		                <input type="text" name="pseudo" id="pseudo" required="required">

    		                <p class="paragraphe_formulaire">Mot de passe* :</p>
                            <label class="info_supplementaire">6 caractères minimum dont 1 majuscule et 1 minuscule</label></br>
    		                <input type="password" name="password" class="password" minlength="6" required="required">

    		                <p class="paragraphe_formulaire">Saisissez à nouveau votre mot de passe* :</p>
    		                <input type="password" name="password2" class="password" minlength="6" required="required">

    		                <p class="paragraphe_formulaire">Adresse email* :</p>
                            <label class="info_supplementaire">Un mail de confirmation vous sera envoyé</label></br>
    		                <input type="text" name="email" id="email" required="required" placeholder="exemple@gmail.com">                
    		                
                            <p class="paragraphe_formulaire"></p>
                            <p id="paragraphe_checkbox"><input name="condition_utilisation" id="condition_utilisation" type="checkbox" required="required">J\'accepte les <a href="condition-generale-dutilisation" target="_blank">conditions générales d\'utilisation</a>*</input></p>

                            <p class="paragraphe_formulaire"></p>
                            <div class="g-recaptcha" data-sitekey="6LcQNVMUAAAAACtb4VNzSJgI6eDoOknfMphjksue"></div>

    		                <input type="submit" name="connexion" value="Valider" id="bouton_inscription">
                           
    		            </form>
    	            <p class="paragraphe_champs_obligatoire">* : Champs obligatoires</p>
                    </article>';
                }
            }

        	elseif (!empty(($_POST['pseudo'])) && !empty(($_POST['password'])) && !empty(($_POST['password2'])) && preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", ($_POST['password'])) && !empty(($_POST['email'])) && preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", ($_POST['email'])) && (($_POST['password']) == ($_POST['password2'])) && !$pseudo_email_non_dispo && $decode['success'] == true){

                //=====Insertion des infos dans la bdd
                
        		$pass_hache = password_hash(htmlentities($_POST['password']), PASSWORD_DEFAULT);
        		$req = $bdd->prepare('INSERT INTO membres(oauth_provider,pseudo,pass,isPasswordModified,codePasswordForgotten,email,isEmailValidated,codeEmailValidated,date_inscription,date_derniere_connexion,niveau_privilege) VALUES ("labeautedunet",:pseudo,:pass,"1",:codePasswordForgotten,:email,"0",:codeEmailValidated,CURDATE(),CURDATE(),"utilisateur")');

                $date = date("Y-m-d H:i:s");
                $code=md5(htmlentities($_POST['pseudo']).htmlentities($_POST['email'].$date));
                $code_email = md5($date.htmlentities($_POST['pseudo']).$date);

        		$req->execute(array(
        			'pseudo' => htmlentities($_POST['pseudo']),
        			'pass' => $pass_hache,
                    'codePasswordForgotten' => $code,
        			'email' => htmlentities($_POST['email']),
        			'codeEmailValidated' => $code_email));
                $req->closeCursor();
                //==========

                //=====Déclaration du destinataire
                $destinataire = htmlentities($_POST['email']);
                //==========

                //=====Infos expéditeur
                $nom_expediteur = "LaBeauteDuNet";
                $mail_expediteur = "contact@labeautedunet.fr";
                //==========

				//=====Déclaration des messages au format texte et au format HTML.
				$message_txt = "Tu viens de rejoindre LaBeauteDuNet ! - Dernière étape avant de pouvoir te connecter : tu dois valider ton adresse mail. - Tu as juste à cliquer sur ce lien : http://www.labeautedunet.fr/validation_email?code=".$code_email." - A tout de suite ! - Vous recevez cet email car vous vous êtes inscrit sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.";

				$message_html = "<html><head><link rel=\"stylesheet\" href=\"http://www.labeautedunet.fr/res/css/mail/mail_inscription.css\" /></head><body><div><h2><b>Tu viens de rejoindre LaBeauteDuNet !</b></h2><p>Dernière étape avant de pouvoir te connecter : tu dois valider ton adresse mail.</p><p>Tu as juste à cliquer sur ce lien :</p><p>http://www.labeautedunet.fr/validation_email?code=".$code_email." <img src=\"http://www.labeautedunet.fr/res/img/smiley/welcome.png\" height=\"20\" width=\"20\"/></p><p>A tout de suite !</p><p><img src=\"http://www.labeautedunet.fr/res/img/logo_labeautedunet.png\" height=\"125\" width=\"125\"/></p><p class=\"info_sup\">Vous recevez cet email car vous vous êtes inscrit sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.</p></div></body></html>";
				//==========
				 
				//=====Définition de l'objet.
				$objet = "Bienvenue sur LaBeauteDuNet !";
				//=========

                if(envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet)){
                	?>
                	<article>
                		<p class="mail_envoye">
                			Un mail de confirmation vient de vous être envoyé, vérifiez vos spams et courriers indésirables. Si vous le ne recevez pas, merci de nous contacter <a href="mailto:contact@labeautedunet.fr" class="lien_mail_envoye">par mail</a>.
                		</p>
                	</article>
                	<?php
                }
                else{
                	?>
                		<p class="mail_non_envoye">
                			Un problème est survenu lors de l'envoi du mail de confirmation. Merci de nous contacter <a href="mailto:contact@labeautedunet.fr">par mail</a>.
                		</p>
                	<?php
                }
        	}
            ?>
            </section>   
        </div>

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>

<?php } ?>