<?php 
header("Content-type: text/javascript");

include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
include($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_usuelles.php");
?>

<?php 
if ($_GET['id_membre'] == "not_connected"){?>
	var sMessage = "Veuillez vous connecter pour signaler un lien mort"
<?php }
else{
	if ($_GET['source']=='video'){
		$message_txt = "Un signalement de lien mort a été effectué sur une video : ".$_GET['vid_titre']." (ID : ".$_GET['id_video'].")";
        $lien_vid_titre = str_replace(' ', '+', $_GET['vid_titre']);
        $message_txt.= "  --> https://www.labeautedunet.fr/menu_videos?recherche_saisie=".$lien_vid_titre;

        $message_html = "<html><head></head><body><h3>Un signalement de lien mort a été effectué sur une video</h3><p>Titre : ".$_GET['vid_titre']."</p><p> ID : ".$_GET['id_video']."</p><p>Lien : <a href=\"https://www.labeautedunet.fr/menu_videos?recherche_saisie=".$_GET['vid_titre']."\">https://www.labeautedunet.fr/menu_videos?recherche_saisie=".$lien_vid_titre."</a></p></body></html>";
	}
	else{
		$message_txt='Un problème à été signalé sur une image : "'.$_GET['img_titre'].'" (ID : '.$_GET['id_image'].')';
		$lien_img_titre = str_replace(' ', '+', $_GET['img_titre']);
		$message.= '  --> https://www.labeautedunet.fr/menu_images?recherche_saisie='.$lien_img_titre.'';

        $message_html = "<html><head></head><body><h3>Un problème à été signalé sur une image</h3><p>Titre : ".$_GET['img_titre']."</p><p> ID : ".$_GET['id_image']."</p><p>Lien : <a href=\"https://www.labeautedunet.fr/menu_images?recherche_saisie=".$_GET['img_titre']."\">https://www.labeautedunet.fr/menu_images?recherche_saisie=".$lien_img_titre."</a></p></body></html>";
	}

    //=====Déclaration du destinataire
    $destinataire = "contact@labeautedunet.fr";
    //==========

    //=====Infos expéditeur
    $nom_expediteur = $_GET['pseudo_membre'];
    $mail_expediteur = $_GET['email_membre'];
    //==========
     
    //=====Définition de l'objet.
    $objet="Signalement d'un probleme !";
    //=========

    if (envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet)){ // Envoi de l'email
        ?>var sMessage="Le signalement a été effectué. Merci !"<?php
    }
    else{ // Non envoyé
        ?>var sMessage="Le signalement n'a pas pu être effectué. Merci de nous signaler le problème via le formulaire de contact."<?php
    }

}?>

callback(sMessage);