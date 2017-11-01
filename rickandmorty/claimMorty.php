<?php
  include("DBUtils.php");

  $results = claimMorty($_POST['mortyid'], $_POST['name'], $_POST['number'], strtolower($_POST['releasePhrase']));

  echo $results;
?>
