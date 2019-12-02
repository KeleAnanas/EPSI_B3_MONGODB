<html>
<head>

</head>
<body>

<?php
require 'C:\Programmes\wamp64\bin\php\php7.2.18\vendor\autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->flickr->photos;

$id = $_POST["id_photo"];

$auteur = $collection->findOne(array('id' => $id), array('nsid_auteur' => true, '_id' => false));
var_dump($auteur);
$nom_auteur = $client->flickr->auteurs->findOne(array('nsid'=> $auteur), array('nom'=> true, '_id' => false));

echo "Details de la photo n°".$id;

echo "<p> Nom de l'auteur : ".$nom_auteur."</p>";


?>

</body>
</html>