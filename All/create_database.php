<?php

include 'datalogin.php'; 

// Create database
if (mysql_query("CREATE DATABASE " . $db_name, $con))
  {
  echo "Database created";
  }
else
  {
  echo "Error creating database: " . mysql_error();
  }

?>