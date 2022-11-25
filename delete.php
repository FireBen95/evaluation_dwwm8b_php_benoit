<?php
session_start();


if ( !isset($_GET['livre_id']) || empty($_GET['livre_id']) )
{
    return header("Location: index.php");
}

$livre_id = strip_tags($_GET['livre_id']);

$livre_id_converted = (int) $livre_id;

//connexion avec la base  
require __DIR__ . "/db/connexion.php";

$req = $db->prepare("SELECT * FROM livre WHERE id = :id");
$req->bindValue(":id", $livre_id_converted);
$req->execute();
$count = $req->rowCount();

if ($count != 1) 
{
    return header("Location: index.php");
}


$livre = $req->fetch();


$req->closeCursor();


$delete_req = $db->prepare("DELETE FROM livre WHERE id = :id");
$delete_req->bindValue(":id", $livre['id']);
$delete_req->execute();
$delete_req->closeCursor();


return header("Location: index.php");

?>