<html>
<head>


</head>

<body>
<p>Votre recherche : </p>

<?php

require 'C:\Programmes\wamp64\bin\php\php7.2.18\vendor\autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");

$tag = $_POST["search"];
echo "Votre recherche : ".$tag;

$api_key = "e552deb7d7c2e6231aed3b7acbae9baf";
$str = file_get_contents('https://www.flickr.com/services/rest/?method=flickr.photos.search&api_key='.$api_key.'&tags='.$tag.'&safe_search=3&per_page=5&format=json&nojsoncallback=1');
$json = json_decode($str, true);

echo "<ul>";
foreach ($json["photos"]["photo"] as $photo) {
    $farm = $photo["farm"];
    $server = $photo["server"];
    $id = $photo["id"];
    $secret = $photo["secret"];
    $link = "https://farm".$farm.".staticflickr.com/".$server."/".$id."_".$secret.".jpg";

    $str1 = file_get_contents("https://www.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=".$api_key."&photo_id=".$id."&format=json&nojsoncallback=1");
    $jsonPhoto = json_decode($str1, true);

    $dateUpload= $jsonPhoto["photo"]["dateuploaded"];
    $nom_auteur = $jsonPhoto["photo"]["owner"]["realname"];
    $nsid_auteur = $jsonPhoto["photo"]{"owner"}["nsid"];
    $nbCommentaires = $jsonPhoto["photo"]["comments"]["_content"];
    $lieuDeNaissance = $jsonPhoto["photo"]["owner"]["location"];

    echo "<li>
        <form id = \"details\" action=\"details.php\" method=\"post\">
        <input type=\"hidden\" name=\"id_photo\" value=$id>
        </form>
        <a href='#' onclick='document.getElementById(\"details\").submit()'>
        <img src = $link ></a>
    </li>";

    $collection = $client->flickr->photos;
    $doc_auteur = $client->flickr->auteurs;

    $photoExistante = $collection->findOne(array('id' => $id));
    if ($photoExistante == null){
        $result = $collection->insertOne( [ 'id' => $id, 'recherche' => $tag, 'nsid_auteur' => $nsid_auteur, 'farm' => $farm, 'server' => $server, 'secret' => $secret, 'dateUpload' => $dateUpload, 'nbCommentaires' => $nbCommentaires, 'link' => $link ] );
    }

    $auteurExistant = $doc_auteur->findOne(array('nsid' => $nsid_auteur));
    if ($auteurExistant == null){
        $auteur = $doc_auteur->insertone(['nsid' => $nsid_auteur, 'nom' => $nom_auteur, "lieuDeNaissance"=> $lieuDeNaissance]);
    }
}

echo "</ul>";

$collection = $client->flickr->photos;
$indexName = $collection->createIndex(['recherche' => 1]);

?>

</body>
</html>