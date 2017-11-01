<?php
  include("DBUtils.php");

  $results = getAllMortys();
  echo json_encode($results);
?>
