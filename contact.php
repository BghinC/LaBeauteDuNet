<?php // Redirection si pas connecté
if(empty($_SESSION['id'])){
header('Location: index?not_connected=true');
exit();
}
else{?>

<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" href="res/css/contact.css" />
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
            <?php
            if(!empty($_POST['objet']) && !empty($_POST['message']) && !empty($_POST['envoyer'])){

                //=====Déclaration du destinataire
                $destinataire = 'contact@labeautedunet.fr';
                //==========

                //=====Infos expéditeur
                $nom_expediteur = $_SESSION['pseudo'];
                $mail_expediteur = $_SESSION['email'];
                //==========

                //=====Déclaration des messages au format texte et au format HTML.
                $message_txt = htmlentities($_POST['message']);

                $message_html = nl2br(htmlentities($_POST['message'])); //nl2br sert à garder la mise en forme(passage à la ligne...)
                //==========
                 
                //=====Définition de l'objet.
                $objet = htmlentities($_POST['objet']);
                //=========

                if (envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet)) // Envoi de l'email
                {
                    ?><p class="message_envoye">Votre message a bien été envoyé.</p>
                    <p class="message_envoye">Nous vous répondrons dès que possible. Merci de vérifier vos spams.</p><?php
                }
                else // Non envoyé
                {
                    ?><p class="message_non_envoye">Votre message n'a pas pu être envoyé</p><?php
                }
            }
                ?>
                <h1>Contact</h1>
                <article>
                    <form method="post" action="" id="formulaire_contact">
                        <p class="paragraphe_formulaire">Pseudo :</p>
                        <?php echo '<input class ="input_formulaire_contact" type="text" name="pseudo" value='.$_SESSION['pseudo'].' disabled="disabled" />';?>

                        <p class="paragraphe_formulaire">Adresse email :</p>
                        <?php echo '<input class ="input_formulaire_contact" type="text" name="mail" value='.$_SESSION['email'].' disabled="disabled" />';?> 

                        <p class="paragraphe_formulaire">Objet :</p>
                        <?php echo '<input class ="input_formulaire_contact" type="text" name="objet"/>';?> 

                        <p class="paragraphe_formulaire">Votre message :</p>
                        <textarea name="message" id="message" rows="10" cols="40"></textarea>

                        <p class="paragraphe_formulaire"></p>
                        <input type="submit" name="envoyer" value="Envoyer" id="envoyer">
                    </form>
                </article>
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>
<?php }?>