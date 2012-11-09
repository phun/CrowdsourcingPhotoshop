<?php
include 'connect_sql.php'; 

$table_name = "i1_t3";

$ip = $_SERVER['REMOTE_ADDR']; 
$video = $_GET["video"];
$beforeIndice = $_GET["beforeIndice"];
$afterIndice = $_GET["afterIndice"];
$allBeforeIndices = $_GET["allBeforeIndices"];
$allAfterIndices = $_GET["allAfterIndices"];

echo $beforeIndice . $allBeforeIndices . "<br/.>" . $afterIndice . $allAfterIndices;; 

$insert_q = "INSERT INTO " . $table_name . " (ip, video, beforeIndice, afterIndice, allBeforeIndices, allAfterIndices)
	VALUES ('" . $ip . "', '" . $video . "', '" . $beforeIndice . "', '" . $afterIndice . "', '" . $allBeforeIndices . "', '" . $allAfterIndices . "')";
mysql_query($insert_q);
?>