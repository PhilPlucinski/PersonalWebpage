<?php
$servername = "127.0.0.1";
$dbUsername = "rickandmorty";
$dbPassword = "wubbalubbadubdub";
$charset = "utf8mb4_bin";

try {
  $conn = new PDO("mysql:host=$servername;dbname=mortys;charset=utf8mb4", $dbUsername, $dbPassword);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>
