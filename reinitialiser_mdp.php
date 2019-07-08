<?php // Redirection si pas connecté
if(!empty($_SESSION['id'])){
header('Location: http://www.labeautedunet.fr');
exit();
}
else{?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="res/css/reinitialiser_mdp.css" />
        <script src='https://www.google.com/recaptcha/api.js'></script> <!-- Pour Captcha -->
        <?php
        include ($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_usuelles.php");
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
				?>

                <?php
                if(!empty($_POST['envoyer'])) {// Si user a cliqué sur connexion --> vérifie le pseudo et le mdp puis connecte
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
                        if(!empty($_POST['email']) && preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", ($_POST['email']))){
                            $req = $bdd->prepare('SELECT pseudo,email FROM membres WHERE email=?');
                            $req -> execute(array(htmlentities($_POST['email'])));//$_POST['pseudo'] correspond à l'id que l'user a rentré dans le formulaire
                            $resultat = $req->fetch();

                            if($resultat){

                                $date = date("Y-m-d H:i:s");
                                $code = md5($resultat['pseudo'].$resultat['email'].$date);

                                //=====Déclaration du destinataire
                                $destinataire = $_POST['email'];
                                //==========

                                //=====Infos expéditeur
                                $nom_expediteur = "LaBeauteDuNet";
                                $mail_expediteur = "contact@labeautedunet.fr";
                                //==========

								//=====Déclaration des messages au format texte et au format HTML.
								$message_txt = "Alors, on a oublié son mot de passe ? - Pas de soucis, on est là pour toi !- Voici le lien pour le réinitialiser : http://www.labeautedunet.fr/reinitialiser_mdp?code=".$code." - Clique sur le lien ou copie-le dans ton navigateur. - A bientôt sur LaBeauteDuNet --- Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectué sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.";

								$message_html = "<html><head><link rel=\"stylesheet\" href=\"http://www.labeautedunet.fr/res/css/mail/mail_reinitialiser_mdp.css\" /></head><body><div><h2><b>Alors, on a oublié son mot de passe ?</b></h2><p>Pas de soucis, on est là pour toi !</p><p>Voici le lien pour le réinitialiser :</p><p><a href=\"http://www.labeautedunet.fr/reinitialiser_mdp?code=".$code."\">http://www.labeautedunet.fr/reinitialiser_mdp?code=".$code."</a>  <img src=\"http://www.labeautedunet.fr/res/img/smiley/welcome.png\" height=\"20\" width=\"20\"/></p><p>Clique sur le lien ou copie-le dans ton navigateur.</p><p><b>A bientôt sur <a href=\"labeautedunet.fr\">LaBeauteDuNet</a></b></p><p><img src=\"http://www.labeautedunet.fr/res/img/logo_labeautedunet.png\" height=\"125\" width=\"125\"/></p><p class=\"info_sup\">Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectué sur LaBeauteDuNet. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.</p></div></body></html>";
								//==========
								 
								//=====Définition de l'objet.
								$objet = "Réinitialisation du mot de passe";
								//=========

                                if (envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet)) // Envoi de l'email
                                {
                                    if (!empty($resultat['email'])){
                                        $requete = $bdd->prepare('UPDATE membres SET isPasswordForgotten="1", codePasswordForgotten=? WHERE email=?');
                                        $requete->execute(array($code,$resultat['email']));
                                        $requete->closeCursor();
                                    }
                                }
                                else // Non envoyé
                                {
                                    ?><p class="message_non_envoye">Un problème est survenu lors de l'envoi du mail, merci d'utiliser le formulaire de contact afin de nous prévenir. Ne tenez pas compte du message ci-dessous.</p><?php
                                }
                                $req->closeCursor();
                            }
                            ?>
                            <p class="message_envoye">Si votre adresse email est dans notre base de données alors un mail vient de vous être envoyé afin de réinitialiser votre mot de passe.</p>
                            <?php

                        }
                        else{
                            ?>
                            <p class="id_incorrect">Format de l'adresse mail incorrect</p>
                            <?php
                        }
                    }
                    elseif(!empty($_POST['valider_changement'])){
                        ?>
                        <p class="id_incorrect">Captcha incorrect</p>
                        <?php
                    }
                }
                

                if((!empty($_GET['code']) && (empty($_POST['valider_changement']) || $_POST['password1']!=$_POST['password2'] || !preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", ($_POST['password1']))))) {
                    if(!empty($_POST['valider_changement'])){
                        if($_POST['password1']!=$_POST['password2']){
                            ?>
                            <p class="id_incorrect">Les mots de passe ne sont pas identiques</p>
                            <?php
                        }
                        elseif(!preg_match("#^(?=.*[a-z])(?=.*[A-Z])#", ($_POST['password1']))){
                            ?>
                            <p class="id_incorrect">Le mot de passe ne correspond pas aux critères</p>
                            <?php
                        }
                    }
                    $req = $bdd->prepare('SELECT pseudo,email,isPasswordForgotten,codePasswordForgotten FROM membres WHERE codePasswordForgotten=?');
                    $req -> execute(array(htmlentities($_GET['code'])));
                    $resultat = $req->fetch();
                    if(!empty($resultat) && $resultat['isPasswordForgotten'] == '1'){
                            ?>
                            <form method="post" action="" id="formulaire_reinitialiser">
                                <h1>Réinitialiser mon mot de passe</h1>

                                <?php echo '<input type="hidden" name="pseudo" id="pseudo" value='.$resultat['pseudo'].' required="required">';
                                echo '<input type="hidden" name="code" id="code" value='.$resultat['codePasswordForgotten'].' required="required">';?>

                                <p class="paragraphe_formulaire">Nouveau mot de passe :</p>
                                <label class="info_supplementaire">6 caractères minimum dont 1 majuscule et 1 minuscule</label></br>
                                <input type="password" name="password1" id="password1" minlength="6" required="required">

                                <p class="paragraphe_formulaire">Confirmer le mot de passe :</p>
                                <input type="password" name="password2" id="password2" minlength="6" required="required">

                                <p class="paragraphe_formulaire"></p>
                                <input type="submit" name="valider_changement" value="Changer mon mot de passe" id="valider_changement">
                            </form>
                            <?php
                    }
                    else{
                        ?>
                        <p class="id_incorrect">Le code n'est plus valide</p>
                        <?php
                    }
                }
                elseif(!empty($_POST['valider_changement'])){
                    if($_POST['password1'] == $_POST['password2']){

                        $pass_hache = password_hash(htmlentities($_POST['password2']), PASSWORD_DEFAULT);

                        $requete = $bdd->prepare('UPDATE membres SET pass=?, isPasswordModified="1", isPasswordForgotten="0", codePasswordForgotten="" WHERE pseudo=?');
                        $requete->execute(array($pass_hache,$_POST['pseudo']));
                        $requete->closeCursor();
                        ?>
                        <p class="message_envoye">Votre mot de passe a été changé vous pouvez maintenant vous <a class="message_envoye_lien" href="/connexion">connecter</a>.</p>
                        <?php
                    }
                    else{
                        ?><p>Les mots de passe ne sont pas identiques</p><?php
                        /*header('Location: http://www.labeautedunet.fr/reinitialiser_mdp?code='.$_POST['code']);
                        exit();*/
                    }
                }
                else{
                    ?>
                    <form method="post" action="" id="formulaire_reinitialiser">
                        <h1>Réinitialiser mon mot de passe</h1>

                        <p class="paragraphe_formulaire">Email :</p>
                        <input type="text" name="email" id="email" required="required">

                        <p class="paragraphe_formulaire"></p>
                        <div class="g-recaptcha" data-sitekey="6LcQNVMUAAAAACtb4VNzSJgI6eDoOknfMphjksue"></div>

                        <p class="paragraphe_formulaire"></p>
                        <input type="submit" name="envoyer" value="Envoyer" id="bouton_envoyer">
                    </form>
                    <?php
                } ?>
            </section>

        </div>

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>

    </body>
</html>
<?php
}
?>