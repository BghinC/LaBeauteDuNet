<?php
function envoi_mail($destinataire,$nom_expediteur,$mail_expediteur,$message_txt,$message_html,$objet) {

	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)){ // On filtre les serveurs qui rencontrent des bogues.
		$passage_ligne = "\r\n";
	}
	else{
		$passage_ligne = "\n";
	}

	//=====Déclaration des messages au format texte et au format HTML.
	$message_txt = utf8_decode($message_txt);

	$message_html = utf8_decode($message_html);
	//==========
	 
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	 
	//=====Définition du objet.
	$objet = utf8_decode(utf8_encode($objet));
	//=========
	 
	//=====Création du header de l'e-mail.
	$header = "From: \"".$nom_expediteur."\"<".$mail_expediteur.">".$passage_ligne;
	$header.= "Reply-to: \"".$nom_expediteur."\"<".$mail_expediteur.">".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	//==========
	 
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;

	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//==========

	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========

	return mail($destinataire,$objet,$message,$header); // Envoi de l'email
}
?>