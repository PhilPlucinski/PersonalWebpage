<?php
include("config.php");

function getAllMortys() {
  global $conn;

  $query = "SELECT mortyid, name, imgsrc, claimedby, claimedbyroom, claimedon FROM mortys";

  $stmt = $conn->prepare($query);
  $stmt->execute($args);
  $results = $stmt->fetchAll();

  return $results;
}

function claimMorty($mortyid, $name, $room, $phrase) {
  global $conn;

  $query = "UPDATE mortys SET claimedby = ?, claimedbyroom = ?, claimreleasephrase = ? WHERE mortyid = ? AND claimedby IS NULL";

  $stmt = $conn->prepare($query);
  $stmt->execute([$name, $room, $phrase, $mortyid]);

  return $stmt->rowCount();
}

function releaseMorty($mortyid, $phrase) {
  global $conn;

  $query = "UPDATE `mortys` SET `claimedby` = NULL, `claimedbyroom` = NULL, `claimedon` = NULL, `claimreleasephrase` = NULL WHERE `mortyid` = ? AND `claimreleasephrase` = ?";

  $stmt = $conn->prepare($query);
  $stmt->execute([$mortyid, $phrase]);

  return $stmt->rowCount();
}


?>
