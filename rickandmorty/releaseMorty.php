<?php
  include("DBUtils.php");

  $results = releaseMorty($_POST['mortyid'], strtolower($_POST['releasePhrase']));

  echo $results;
?>
