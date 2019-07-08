<footer>
	<img src="/res/img/logo_labeautedunet.png" id="logo_footer_left" contextmenu="return false;" oncontextmenu="return false;">
	<nav id="nav_footer">
		<ul>
			<li><a href="http://www.labeautedunet.fr">Acceuil</a></li>
			<li><a href="http://www.labeautedunet.fr/menu_images">Images</a></li>
			<li><a href="http://www.labeautedunet.fr/menu_videos">Vidéos</a></li>
		</ul>
	</nav>
	<nav id="infos_sup_footer">
		<ul>
			<?php if (!empty($_SESSION['id'])){
				?><li><a href="http://www.labeautedunet.fr/contact">Contact</a></li><?php 
			}
			else{
				?><li><a href="http://www.labeautedunet.fr/connexion">Connexion</a></li>
				<li><a href="http://www.labeautedunet.fr/inscription">Inscription</a></li>
				<?php 
			}?>
			<li><a href="http://www.labeautedunet.fr/condition-generale-dutilisation">Conditions générales d'utilisation</a></li>
		</ul>
	</nav>
	<img src="/res/img/logo_labeautedunet.png" id="logo_footer_right" contextmenu="return false;" oncontextmenu="return false;">
</footer>