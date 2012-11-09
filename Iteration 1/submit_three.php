<?php
include 'connect_sql.php'; 

$table_name = "i1_t3";

$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']); 
$video = mysql_real_escape_string($_GET["video"]);
$beforeIndice = mysql_real_escape_string($_GET["beforeIndice"]);
$afterIndice = mysql_real_escape_string($_GET["afterIndice"]);
$allBeforeIndices = mysql_real_escape_string($_GET["allBeforeIndices"]);
$allAfterIndices = mysql_real_escape_string($_GET["allAfterIndices"]);

echo "Thank you for completing this task!";

$insert_q = "INSERT INTO " . $table_name . " (ip, video, beforeIndice, afterIndice, allBeforeIndices, allAfterIndices)
	VALUES ('" . $ip . "', '" . $video . "', '" . $beforeIndice . "', '" . $afterIndice . "', '" . $allBeforeIndices . "', '" . $allAfterIndices . "')";
mysql_query($insert_q);
?>