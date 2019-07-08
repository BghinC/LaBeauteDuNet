<?php header("Content-type: text/javascript"); ?>
<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>

<?php 
if ($_GET['id_membre'] == "not_connected"){?>
<?php }
elseif($_GET['source']=='video'){
	$test_like = $bdd->prepare('SELECT id_video,id_membre,like_dislike FROM likes_videos WHERE id_video=? AND id_membre=?');
	$test_like -> execute(array($_GET['id_video'],$_GET['id_membre']));
	$test=$test_like->fetch();
	$test_like->closeCursor();
	
	if($test){//Si on a déjà like ou dislike un contenu
		if($test['like_dislike'] == 'like'){
			if($_GET['like_dislike'] == 'like'){
				//Retire le like
				$req=$bdd->prepare('DELETE FROM likes_videos WHERE id_video=? AND id_membre=?');
	            $req->execute(array($_GET['id_video'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de like
	            $req=$bdd->prepare('SELECT nb_like FROM videos WHERE id=?');
	            $req->execute(array($_GET['id_video']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_like=$req2['nb_like']-1;
	            $req=$bdd->prepare('UPDATE videos SET nb_like = ? WHERE id = ?');
	            $req->execute(array($nb_like, $_GET['id_video']));
	            $req->closeCursor();
			}
			if($_GET['like_dislike'] == 'dislike'){
				$req=$bdd->prepare('UPDATE likes_videos SET like_dislike="dislike" WHERE id_video=? AND id_membre=?');
	            $req->execute(array($_GET['id_video'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de like et ajoute 1 à celui des dislike
	            $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM videos WHERE id=?');
	            $req->execute(array($_GET['id_video']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_like=$req2['nb_like']-1;
	            $nb_dislike=$req2['nb_dislike']+1;
	            $req=$bdd->prepare('UPDATE videos SET nb_like = :nblike, nb_dislike = :nbdislike WHERE id = :id');
	            $req->execute(array('nblike'=>$nb_like, 'nbdislike'=>$nb_dislike, 'id'=>$_GET['id_video']));
	            $req->closeCursor();
			}
		}
		elseif($test['like_dislike'] == 'dislike'){
			if($_GET['like_dislike'] == 'dislike'){
				$req=$bdd->prepare('DELETE FROM likes_videos WHERE id_video=? AND id_membre=?');
	            $req->execute(array($_GET['id_video'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de dislike
	            $req=$bdd->prepare('SELECT nb_dislike FROM videos WHERE id=?');
	            $req->execute(array($_GET['id_video']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_dislike=$req2['nb_dislike']-1;
	            $req=$bdd->prepare('UPDATE videos SET nb_dislike = ? WHERE id = ?');
	            $req->execute(array($nb_dislike, $_GET['id_video']));
	            $req->closeCursor();
			}
			if($_GET['like_dislike'] == 'like'){
				$req=$bdd->prepare('UPDATE likes_videos SET like_dislike="like" WHERE id_video=? AND id_membre=?');
	            $req->execute(array($_GET['id_video'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de dislike et ajoute 1 à celui des like
	            $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM videos WHERE id=?');
	            $req->execute(array($_GET['id_video']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_dislike=$req2['nb_dislike']-1;
	            $nb_like=$req2['nb_like']+1;
	            $req=$bdd->prepare('UPDATE videos SET nb_like = :nblike, nb_dislike = :nbdislike WHERE id = :id');
	            $req->execute(array('nblike'=>$nb_like, 'nbdislike'=>$nb_dislike, 'id'=>$_GET['id_video']));
	            $req->closeCursor();
			}
		}
	}
	elseif(!$test){
		if($_GET['like_dislike'] == 'like'){
			$req=$bdd->prepare('INSERT INTO likes_videos(id_video,id_membre,like_dislike,date_like) VALUES (:id_video, :id_membre, "like", NOW())');
	        $req->execute(array(
	        	'id_video' => $_GET['id_video'],
	        	'id_membre' => $_GET['id_membre']));
	        $req->closeCursor();

        	//Ajoute 1 au nombre de like
            $req=$bdd->prepare('SELECT nb_like FROM videos WHERE id=?');
            $req->execute(array($_GET['id_video']));
            $req2=$req->fetch();
            $req->closeCursor();

            $nb_like=$req2['nb_like']+1;
            $req=$bdd->prepare('UPDATE videos SET nb_like = ? WHERE id = ?');
            $req->execute(array($nb_like, $_GET['id_video']));
            $req->closeCursor();
		}
		elseif($_GET['like_dislike'] == 'dislike'){
			$req=$bdd->prepare('INSERT INTO likes_videos(id_video,id_membre,like_dislike,date_like) VALUES (:id_video, :id_membre, "dislike", NOW())');
	        $req->execute(array(
	        	'id_video' => $_GET['id_video'],
	        	'id_membre' => $_GET['id_membre']));
	        $req->closeCursor();

        	//Ajoute 1 au nombre de dislike
            $req=$bdd->prepare('SELECT nb_dislike FROM videos WHERE id=?');
            $req->execute(array($_GET['id_video']));
            $req2=$req->fetch();
            $req->closeCursor();

            $nb_dislike=$req2['nb_dislike']+1;
            $req=$bdd->prepare('UPDATE videos SET nb_dislike = ? WHERE id = ?');
            $req->execute(array($nb_dislike, $_GET['id_video']));
            $req->closeCursor();
		}
	}

	$req=$bdd->prepare('SELECT nb_like,nb_dislike FROM videos WHERE id=?');
    $req->execute(array($_GET['id_video']));
    $req2=$req->fetch();
    $req->closeCursor();
    //$rapport_like_dislike=100*$req2['nb_like']/($req2['nb_like']+$req2['nb_dislike']); Pour avoir un %
    $rapport_like_dislike = $req2['nb_like'] - $req2['nb_dislike']; //Nombre de like - Nombre de dislike
    $req=$bdd->prepare('UPDATE videos SET rapport_like_dislike=:rapport WHERE id = :id');
    $req->execute(array('rapport'=>$rapport_like_dislike, 'id'=>$_GET['id_video'])); 
    $req->closeCursor();
}


elseif($_GET['source']=='image'){
	$test_like = $bdd->prepare('SELECT id_image,id_membre,like_dislike FROM likes_images WHERE id_image=? AND id_membre=?');
	$test_like -> execute(array($_GET['id_image'],$_GET['id_membre']));
	$test=$test_like->fetch();
	$test_like->closeCursor();
	
	if($test){//Si on a déjà like ou dislike un contenu
		if($test['like_dislike'] == 'like'){
			if($_GET['like_dislike'] == 'like'){
				$req=$bdd->prepare('DELETE FROM likes_images WHERE id_image=? AND id_membre=?');
	            $req->execute(array($_GET['id_image'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de like
	            $req=$bdd->prepare('SELECT nb_like FROM images WHERE id=?');
	            $req->execute(array($_GET['id_image']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_like=$req2['nb_like']-1;
	            $req=$bdd->prepare('UPDATE images SET nb_like = ? WHERE id = ?');
	            $req->execute(array($nb_like, $_GET['id_image']));
	            $req->closeCursor();
			}
			if($_GET['like_dislike'] == 'dislike'){
				$req=$bdd->prepare('UPDATE likes_images SET like_dislike="dislike" WHERE id_image=? AND id_membre=?');
	            $req->execute(array($_GET['id_image'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de like et ajoute 1 à celui des dislike
	            $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM images WHERE id=?');
	            $req->execute(array($_GET['id_image']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_like=$req2['nb_like']-1;
	            $nb_dislike=$req2['nb_dislike']+1;
	            $req=$bdd->prepare('UPDATE images SET nb_like = :nblike, nb_dislike = :nbdislike WHERE id = :id');
	            $req->execute(array('nblike'=>$nb_like, 'nbdislike'=>$nb_dislike, 'id'=>$_GET['id_image'])); 
	            $req->closeCursor();
			}
		}
		elseif($test['like_dislike'] == 'dislike'){
			if($_GET['like_dislike'] == 'dislike'){
				$req=$bdd->prepare('DELETE FROM likes_images WHERE id_image=? AND id_membre=?');
	            $req->execute(array($_GET['id_image'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de dislike
	            $req=$bdd->prepare('SELECT nb_dislike FROM images WHERE id=?');
	            $req->execute(array($_GET['id_image']));
	            $req2=$req->fetch();
	            $req->closeCursor();
	            $nb_dislike=$req2['nb_dislike']-1;
	            $req=$bdd->prepare('UPDATE images SET nb_dislike = ? WHERE id = ?');
	            $req->execute(array($nb_dislike, $_GET['id_image']));
	            $req->closeCursor();
			}
			if($_GET['like_dislike'] == 'like'){
				$req=$bdd->prepare('UPDATE likes_images SET like_dislike="like" WHERE id_image=? AND id_membre=?');
	            $req->execute(array($_GET['id_image'],$_GET['id_membre']));
	            $req->closeCursor();

	            //Retire 1 au nombre de dislike et ajoute 1 à celui des like
	            $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM images WHERE id=?');
	            $req->execute(array($_GET['id_image']));
	            $req2=$req->fetch();
	            $req->closeCursor();

	            $nb_dislike=$req2['nb_dislike']-1;
	            $nb_like=$req2['nb_like']+1;
	            $req=$bdd->prepare('UPDATE images SET nb_like = :nblike, nb_dislike = :nbdislike WHERE id = :id');
	            $req->execute(array('nblike'=>$nb_like, 'nbdislike'=>$nb_dislike, 'id'=>$_GET['id_image']));
	            $req->closeCursor();
			}
		}
	}
	elseif(!$test){
		if($_GET['like_dislike'] == 'like'){
			$req=$bdd->prepare('INSERT INTO likes_images(id_image,id_membre,like_dislike,date_like) VALUES (:id_image, :id_membre, "like", NOW())');
	        $req->execute(array(
	        	'id_image' => $_GET['id_image'],
	        	'id_membre' => $_GET['id_membre']));
	        $req->closeCursor();

	        //Ajoute 1 au nombre de like
            $req=$bdd->prepare('SELECT nb_like FROM images WHERE id=?');
            $req->execute(array($_GET['id_image']));
            $req2=$req->fetch();
            $req->closeCursor();

            $nb_like=$req2['nb_like']+1;
            $req=$bdd->prepare('UPDATE images SET nb_like = ? WHERE id = ?');
            $req->execute(array($nb_like, $_GET['id_image']));
            $req->closeCursor();
		}
		elseif($_GET['like_dislike'] == 'dislike'){
			$req=$bdd->prepare('INSERT INTO likes_images(id_image,id_membre,like_dislike,date_like) VALUES (:id_image, :id_membre, "dislike", NOW())');
	        $req->execute(array(
	        	'id_image' => $_GET['id_image'],
	        	'id_membre' => $_GET['id_membre']));
	        $req->closeCursor();

        	//Ajoute 1 au nombre de dislike
            $req=$bdd->prepare('SELECT nb_dislike FROM images WHERE id=?');
            $req->execute(array($_GET['id_image']));
            $req2=$req->fetch();
            $req->closeCursor();

            $nb_dislike=$req2['nb_dislike']+1;
            $req=$bdd->prepare('UPDATE images SET nb_dislike = ? WHERE id = ?');
            $req->execute(array($nb_dislike, $_GET['id_image']));
            $req->closeCursor();
		}
	}

    $req=$bdd->prepare('SELECT nb_like,nb_dislike FROM images WHERE id=?');
    $req->execute(array($_GET['id_image']));
    $req2=$req->fetch();
    $req->closeCursor();
    
    //$rapport_like_dislike=100*$req2['nb_like']/($req2['nb_like']+$req2['nb_dislike']); Pour avoir un %
    $rapport_like_dislike = $req2['nb_like'] - $req2['nb_dislike']; //Nombre de like - Nombre de dislike

    $req=$bdd->prepare('UPDATE images SET rapport_like_dislike=:rapport WHERE id = :id');
    $req->execute(array('rapport'=>$rapport_like_dislike, 'id'=>$_GET['id_image'])); 
    $req->closeCursor();
}?>