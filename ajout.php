<?php //Permet de rediriger les utilisateur vers l'index s'il veulent accéder à la page d'ajout
if($_SESSION['niveau_privilege'] != "admin"){
    header('Location: http://www.labeautedunet.fr');
    exit();
}
elseif ($_SESSION['niveau_privilege'] == "admin"){?>

<!DOCTYPE html>
<html>
<?php //Connexion à la base de donnée
include($_SERVER['DOCUMENT_ROOT']."/assets/connexion_bdd.php");
?>

	<head>
        <link rel="stylesheet" href="res/css/ajout.css" />
        <?php
        include($_SERVER['DOCUMENT_ROOT']."/assets/fonctions_videos_images.php");
        include($_SERVER['DOCUMENT_ROOT']."/assets/balise_head_generale.php");
        ?>
    </head>
    <body>

        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/header.php"); ?>



        <div class="corps">

            <?php include($_SERVER['DOCUMENT_ROOT']."/assets/menu.php"); ?> 

            <section>
                <article>
                    <h1>Insérer une donnée :</h1>
                	<form method="post" action=""><!Creation du formulaire de recherche>
                            <p><select name="type" id="type">
                                <option>Image</option>
                            </select></p>
                            <p><input type="text" name="nom" id="nom" placeholder="Nom" size="30"/></p>
                            <p><input type="text" name="url" id="url" placeholder="Nom du fichier" size="30"/></p>
                            <p><input type="text" name="categorie" id="categorie" placeholder="Catégorie" size="30"/></p>
                            <!--<p><input type="text" name="source" id="source" placeholder="Source(Seulement pour une vidéo) -> fichier ou youtube" size="30"/></p>-->
                            <p><input type="submit" name="Valider" id="valider_insertion"/></p>
                    </form>

                    <?php
                        if (empty(($_POST['type']))){}
                            ////////////////////////////////////////// Ajout d'une vidéo /////////////////////////////////////////////////////
                        /*elseif(($_POST['type'])=='Vidéo' && !empty(($_POST['nom'])) && !empty(($_POST['url'])) && !empty(($_POST['source']))){
                            $req = $bdd->prepare('INSERT INTO videos(nom,date_ajout,url,categorie,source) VALUES (:nom,NOW(),:url,:categorie,:source)');
                            $req->execute(array(
                                'nom'=>htmlentities($_POST['nom']),
                                'url' =>htmlentities($_POST['url']),
                                'categorie' => htmlentities($_POST['categorie']),
                                'source' => htmlentities($_POST['source'])));
                            $req->closeCursor();
                            echo('<p class="succes">Vidéo insérée dans la base de donnée !</p>');}*/
                            ////////////////////////////////////////// Ajout d'une image /////////////////////////////////////////////////////
                        elseif($_POST['type']=='Image' && !empty(($_POST['nom'])) && !empty(($_POST['url']))){
                            $req = $bdd->prepare('INSERT INTO images(nom,date_ajout,url,categorie) VALUES (:nom,NOW(),:url,:categorie)');
                            $req->execute(array(
                                'nom'=>htmlentities($_POST['nom']),
                                'url' =>htmlentities($_POST['url']),
                                'categorie' => htmlentities($_POST['categorie'])
                        ));
                        $req->closeCursor();
                        echo('<p class="succes">Image insérée dans la base de donnée !</p>');
                        }
                        else{echo '<p class="echec">Formulaire mal rempli</p>';}?>
                </article>
            </section>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT']."/assets/footer.php");?>
    </body>
</html>

<?php } ?>

