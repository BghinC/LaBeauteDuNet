<?php header("Content-type: text/javascript");

//Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");


if($_GET['choix'] == "valide"){
	?>var sMessage = "Video acceptée"; <?php

	$id_video=$_GET['id_video'];
	$video = $bdd->prepare('SELECT * FROM videos_en_attente_de_validation WHERE id=?');
	$video->execute(array($id_video));
	$vid = $video->fetch();
	
	$source = "youtube";

    $req = $bdd->prepare('INSERT INTO videos(url,nom,date_ajout,categorie,source,id_membre_ajout) VALUES(:url,:nom, NOW(), :categorie, :source, :id_membre_ajout)');
	$req->execute(array(
		'url' => $vid['url'],
		'nom' => $vid['nom'],
		'categorie' => $vid['categorie'],
		'source' => $source,
		'id_membre_ajout' => $vid['id_membre']
	));

	$supp_vid = $bdd->prepare('DELETE FROM videos_en_attente_de_validation WHERE id=?');
	$supp_vid->execute(array($id_video));
}

elseif($_GET['choix'] == "refuse"){
	$id_video=$_GET['id_video'];
	?>var sMessage = "Video refusée";<?php
	$supp_vid = $bdd->prepare('DELETE FROM videos_en_attente_de_validation WHERE id=?');
	$supp_vid->execute(array($id_video));
	$supp_vid->closeCursor();
}
?>

callback(sMessage);