<?php
$password = "apple";  // User input password

// Hash the password directly without salt
$computed_hash = md5($password);

// Output the computed hash for verification
echo "Computed Hash: " . $computed_hash . "<br>";
?>
