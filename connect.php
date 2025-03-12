<?php 
require "../database/database.php"; 
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT * FROM iss_persons where id = ? LIMIT 1";
$q = $pdo->prepare($sql);
$id = 1;
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);
print_r($data);
?>