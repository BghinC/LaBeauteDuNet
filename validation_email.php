<?php // Redirection si déjà connecté
if(!empty($_SESSION['id']) || empty($_GET['code'])){
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
        <link rel="stylesheet" href="res/css/validation_email.css" />
        <script src='https://www.google.com/recaptcha/api.js'></script> <!-- Pour Captcha -->
        <?php
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_usuelles.php");
        ?>
        
    </head>

    <body>
    	<?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>

    	<div class="corps">

    		<?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php");?>

	    	<section>

	            <?php

	            $req = $bdd->prepare('SELECT id,pseudo,email,niveau_privilege FROM membres WHERE codeEmailValidated=?');
	            $req -> execute(array(htmlentities($_GET['code'])));
	            $resultat = $req->fetch();

	            if(!empty($resultat)){
	            	$_SESSION['id'] = $resultat['id'];
	                $_SESSION['pseudo'] = $resultat['pseudo'];
	                $_SESSION['email'] = $resultat['email'];
	                $_SESSION['niveau_privilege'] = $resultat['niveau_privilege'];

	                setcookie('pseudo',$resultat['pseudo'],time()+60*60*24*30,'/','labeautedunet.fr',true,true);
	                setcookie('pass',$resultat['pass'],time()+60*60*24*30,'/','labeautedunet.fr',true,true);

	                $requete = $bdd->prepare('UPDATE membres SET isEmailValidated="1", codeEmailValidated="" WHERE pseudo=?');
	                $requete->execute(array($code_email,$_SESSION['pseudo']));
	                $requete->closeCursor();

	                //=====Déclaration du destinataire
	                $destinataire = $_SESSION['email'];
	                //==========

	                //=====Infos expéditeur
	                $nom_expediteur = "LaBeauteDuNet";
	                $mail_expediteur = "contact@labeautedunet.fr";
	                //==========

					//=====Déclaration des messages au format texte et au format HTML.
					$message_txt = "Tu viens de valider ton adresse email ! - Tu peux maintenant profiter de l'ensemble du site LaBeauteDuNet - Tu peux liker des vidéos/images pour les sauvegarder dans l'onglet \"Mes likes\" - Tu peux soumettre tes vidéos grâce à l'onglet \"Soumettre une vidéo\" - Un classement des membres ayant ajouté le plus de vidéos est aussi disponible ! - Vous recevez cet email car vous venez de valider votre adresse email sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération cela signifie qu'une tiers personne a accès à votre messagerie.";

					$message_html = "<html><head><link rel=\"stylesheet\" href=\"http://www.labeautedunet.fr/res/css/mail/mail_validation.css\" /></head><body><div><p><img src=\"http://www.labeautedunet.fr/res/img/smiley/heureux.png\" height=\"60\" width=\"60\"/></p><h2><b>Tu viens de valider ton adresse email !</b></h2><p>Tu peux maintenant profiter de l'ensemble du site LaBeauteDuNet.</p><p>Tu peux liker des vidéos/images pour les sauvegarder dans l'onglet \"Mes likes\" et tu peux soumettre tes vidéos grâce à l'onglet \"Soumettre une vidéo\"</p><p>Un classement des membres ayant ajouté le plus de vidéos est aussi disponible !</p><p><img src=\"http://www.labeautedunet.fr/res/img/logo_labeautedunet.png\" height=\"125\" width=\"125\"/></p><p class=\"info_sup\">Vous recevez cet email car vous venez de valider votre adresse email sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération cela signifie qu'une tiers personne a accès à votre messagerie.</p></div></body></html>";
					//==========
					 
					//=====Définition de l'objet.
					$objet = "Adresse email validée !";
					//=========

	                envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet);

	                header('Location: http://www.labeautedunet.fr');
	                exit();
	            }
	            else{
	            	?>
	            	<p class="code_non_valide">Le code n'est plus valide</p>
	            	<?php
	            }

	            ?>
	        </section>
        </div>

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>

<?php } ?>