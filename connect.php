<?php> 
$pdo = Database::connect();
$sql = “SELECT * ‘iss_persons’ WHERE 1”;           (or limit 1)
$data = $pdo ->query($sql);
print_r($data);
?>
