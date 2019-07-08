<?php

if (isset($_SESSION['pseudo'])){}//Dejà connecté
elseif (!isset($_SESSION['id']) && (isset($_COOKIE['pseudo']) && $_COOKIE['pseudo']!='')  && (isset($_COOKIE['pass']) && $_COOKIE['pass']!='')) { //Si pas connecté mais cookie présent
	//Connexion à la base de donnée
	include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");

	$req = $bdd->prepare('SELECT id,pseudo,pass,email,picture,niveau_privilege FROM membres WHERE pseudo=?');
	$req -> execute(array(htmlentities($_COOKIE['pseudo'])));
	$resultat = $req->fetch();

	if($_COOKIE['pass'] == $resultat['pass']){//Si le mdp du cookie correspond au mdp de la bdd on connecte
		$_SESSION['id'] = $resultat['id'];
		$_SESSION['pseudo'] = $resultat['pseudo'];
		$_SESSION['email'] = $resultat['email'];
		if($resultat['picture']!=''){
			$_SESSION['picture']==$resultat['picture'];
		}
		$_SESSION['niveau_privilege'] = $resultat['niveau_privilege'];

		$requete=$bdd->prepare('UPDATE membres SET date_derniere_connexion=CURDATE() WHERE id=?');
        $requete->execute(array($resultat['id']));
        $requete->closeCursor();
	}
	else{}
}