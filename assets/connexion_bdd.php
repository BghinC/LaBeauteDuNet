<?php //Connexion à la base de donnée
try{ $bdd = new PDO('mysql:host=XXXXXX;dbname=XXXXXX;charset=utf8', 'XXXXXXXX', 'XXXXXXXX');} catch (Exception $e){ die('Erreur : ' . $e->getMessage());}?>