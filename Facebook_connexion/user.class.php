<?php

class User {
    private $dbHost     = "db732637752.db.1and1.com";
    private $dbUsername = "dbo732637752";
    private $dbPassword = "Clement200$";
    private $dbName     = "db732637752";
    private $userTbl    = 'membres';
    
    function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
                $this->db = $conn;
            }
        }
    }
    
    function checkUser($userData = array()){

    	include ($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_usuelles.php");

        if(!empty($userData)){
            $prevQuery_mail = "SELECT * FROM ".$this->userTbl." WHERE email = '".$userData['email']."'";
            $prevResult_mail = $this->db->query($prevQuery_mail);

            // Check whether user data already exists in database
            $prevQuery = "SELECT * FROM ".$this->userTbl." WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
            $prevResult = $this->db->query($prevQuery);

            if($prevResult->num_rows > 0){
                // Update user data if already exists
                $query = "UPDATE ".$this->userTbl." SET first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', locale = '".$userData['locale']."', picture = '".$userData['picture']."', link = '".$userData['link']."', date_derniere_connexion = NOW() WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
                $update = $this->db->query($query);
                $result = $this->db->query($prevQuery);
            }
            elseif($prevResult_mail->num_rows > 0){
            	$query = "UPDATE ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', locale = '".$userData['locale']."', picture = '".$userData['picture']."', link = '".$userData['link']."', date_derniere_connexion = NOW() WHERE email = '".$userData['email']."'";
                $update = $this->db->query($query);
                $result = $this->db->query($prevQuery_mail);


                //=====Mail pour prévenir de la fusion des comptes

                //=====Déclaration du destinataire
                $destinataire = $userData['email'];
                //==========

                //=====Infos expéditeur
                $nom_expediteur = "LaBeauteDuNet";
                $mail_expediteur = "contact@labeautedunet.fr";
                //==========

				//=====Déclaration des messages au format texte et au format HTML.
				$message_txt = "Vous venez de vous connecter à LaBeauteDuNet avec votre compte Facebook. Votre adresse email ".$userData['email']." était déjà associé à un compte. Vos comptes ont donc été fusionnés. Vous pouvez continuer à vous connecter avec Facebook ou alors avec votre Pseudo/Email et votre mot de passe. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.";

				$message_html = "<html><head><link rel=\"stylesheet\" href=\"http://www.labeautedunet.fr/res/css/mail/mail_fusion_compte.css\" /></head><body><div><h2><b>Vous venez de vous connecter à LaBeauteDuNet avec votre compte Facebook.</b></h2><p>Votre adresse email ".$userData['email']." était déjà associé à un compte. Vos comptes ont donc été fusionnés.</p><p>Vous pouvez continuer à vous connecter avec Facebook ou alors avec votre Pseudo/Email et votre mot de passe.</p><p class=\"info_sup\">Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.</p><p><img class=\"logo\" src=\"http://www.labeautedunet.fr/res/img/logo_labeautedunet.png\" height=\"125\" width=\"125\"/></p></div></body></html>";
				//==========
				 
				//=====Définition de l'objet.
				$objet = "Vos comptes ont été fusionnés";
				//=========

                envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet);
            }
            else{
                $pseudo=$userData['first_name'].$userData['oauth_uid'];
                $oauth_uid_hash =password_hash($userData['oauth_uid'], PASSWORD_DEFAULT);
                $isPasswordModified = '0';
                $date = date("Y-m-d H:i:s");
                $code=md5($resultat['pseudo'].$resultat['email'].$date);
                // Insert user data
                $query = "INSERT INTO ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."',pseudo = '".$pseudo."', pass = '".$oauth_uid_hash."', isPasswordModified = '".$isPasswordModified."', codePasswordForgotten ='".$code."',email = '".$userData['email']."', locale = '".$userData['locale']."', picture = '".$userData['picture']."', link = '".$userData['link']."', niveau_privilege ='utilisateur', date_inscription = NOW(), date_derniere_connexion = NOW()";
                $insert = $this->db->query($query);
                $result = $this->db->query($prevQuery);

                //=====Mail de bienvenue

                //=====Déclaration du destinataire
                $destinataire = $userData['email'];
                //==========

                //=====Infos expéditeur
                $nom_expediteur = "LaBeauteDuNet";
                $mail_expediteur = "contact@labeautedunet.fr";
                //==========

				//=====Déclaration des messages au format texte et au format HTML.
				$message_txt = "Bienvenue sur LaBeauteDuNet. Retrouvez les meilleurs vidéos et images cultes qui ont circulé sur Internet ces dernières années. Partagez les vidéos qui vous ont marqué grâce à l'onget \"Soumettre une vidéo\". Vous pouvez créer un mot de passe et changer votre pseudo dans vos paramètres afin de pouvoir vous connecter dans le futur grâce à votre Pseudo/Email et votre mot de passe. Vous pouvez bien sur continuer de vous connecter grâce à Facebook. Merci d'utiliser LaBeauteDuNet. -- Vous recevez cet email car un compte a été créé sur LaBeauteDuNet via Facebook. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.";

				$message_html = "<html><head><link rel=\"stylesheet\" href=\"http://www.labeautedunet.fr/res/css/mail/mail_bienvenue_facebook.css\"/></head><body><div><h2><b>Bienvenue sur LaBeauteDuNet</b></h2><p>Retrouvez les meilleurs vidéos et images cultes qui ont circulé sur Internet ces dernières années. Partagez les vidéos qui vous ont marqué grâce à l'onget \"Soumettre une vidéo\".</p><p>Vous pouvez créer un mot de passe et changer votre pseudo dans vos paramètres afin de pouvoir vous connecter dans le futur grâce à votre Pseudo/Email et votre mot de passe. Vous pouvez bien sur continuer de vous connecter grâce à Facebook.</p><p><b>Merci d'utiliser <a href=\"http://www.labeautedunet.fr\">LaBeauteDuNet</a>.  <img src=\"http://www.labeautedunet.fr/res/img/pouce_bleu.png\" height=\"20\" width=\"20\"/></b></p><p><img class=\"logo\" src=\"http://www.labeautedunet.fr/res/img/logo_labeautedunet.png\" height=\"125\" width=\"125\"/></p><p class=\"info_sup\">Vous recevez cet email car un compte a été créé sur LaBeauteDuNet via Facebook. Si vous n'êtes pas à l'origine de cette opération merci de nous contacter en répondant à cet email.</p></div></body></html>";
				//==========
				 
				//=====Définition de l'objet.
				$objet = "Bienvenue sur LaBeauteDuNet !";
				//=========

                envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet);
            }

            // Get user data from the database
            $userData = $result->fetch_assoc();
        }
        
        // Return user data
        return $userData;
    }
}
?>