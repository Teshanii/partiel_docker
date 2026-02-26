<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$host = "database";
$db   = getenv('MARIADB_DATABASE');
$user = getenv('MARIADB_USER');
$pass = trim(file_get_contents('/run/secrets/db_password'));

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $stmt = $pdo->query("SELECT * FROM livres");
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($livres);
} catch (Exception $e) {
    echo json_encode(["erreur" => "Impossible de se connecter à la bibliothèque"]);
}