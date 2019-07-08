<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>
    
<div class="menu">
	<?php
	if(isset($_SESSION['id'])) {?>
		<div class="div_mon_compte">
			<?php
				echo '<h2>'.$_SESSION['pseudo'].'</h2>';
				$picture = $bdd->prepare('SELECT picture FROM membres WHERE id=?');
				$picture->execute(array($_SESSION['id']));
				$pic = $picture->fetch();
    			if ($pic['picture'] != ''){
    				echo '<img id="profile_picture" src="'.$pic['picture'].'">';
    			}
    			$picture->closeCursor();
    		?>
			<p class="menu_compte"><a href="/mes_likes" class="lien_mon_compte">Mes likes</a></p>
			<?php if (!empty ($_SESSION['id']) && $_SESSION['niveau_privilege'] == "admin"){
				echo '<p class="menu_compte"><a href="/ajout" class="lien_mon_compte">Ajout BDD</a></p>';
				echo '<p class="menu_compte"><a href="/moderation_video" class="lien_mon_compte">Modération nouvelles vidéos</a></p>';
			}?>
			<p class="menu_compte"><a href="/soumettre_une_video" class="lien_mon_compte">Soumettre une vidéo</a></p>
			<p class="menu_compte"><a href="/classement" class="lien_mon_compte">Classement</a></p>
			<p class="menu_compte"><a href="/parametres" class="lien_mon_compte">Paramètres</a></p>
			<p class="menu_compte"><a href="/deconnexion" class="lien_mon_compte">Déconnexion</a></p>
		</div>
    <?php }?>
    <hr>
    <form id="form_vid" method="get" action="/menu_videos">
    	<div class="video">
	    	<h2>Vidéos</h2>
	    		<p class="cat"><input class="input_menu" type="submit" name="categorie" value="Toutes les catégories"></p>
	    		<?php
	    		$categories = $bdd->query('SELECT DISTINCT categorie FROM videos ORDER BY categorie');
	    		while($cat = $categories->fetch()){?>
	    			<p class="cat"><input class="input_menu" type="submit" name="categorie" value="<?php echo $cat['categorie'];?>"></p>
	    		<?php } $categories->closeCursor()?>
	    </div>
	</form>
	<hr>
	<form id="form_img" method="get" action="/menu_images">
		<div class="image">
	    	<h2>Images</h2>
	    		<p class="cat"><input class="input_menu" type="submit" name="categorie" value="Toutes les catégories"></p>
	    		<?php
	    		$categories = $bdd->query('SELECT DISTINCT categorie FROM images ORDER BY categorie');
	    		while($cat = $categories->fetch()){?>
	    			<p class="cat"><input class="input_menu" type="submit" name="categorie" value="<?php echo $cat['categorie'];?>"></p>
	    		<?php } $categories->closeCursor()?>
	    </div>
	</form>
	<hr>
</div>