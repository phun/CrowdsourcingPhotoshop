<?php
include 'connect_sql.php'; 

$table_name = "i1_t2";

$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']); 
$video = mysql_real_escape_string($_GET["video"]);
$region = mysql_real_escape_string($_GET["region"]);
$tool = mysql_real_escape_string($_GET["tool"]);

echo "Thank you for completing this task!";

$insert_q = "INSERT INTO " . $table_name . " (ip, video, region, tool)
	VALUES ('" . $ip . "', '" . $video . "', '" . $region . "', '" . $tool . "')";
mysql_query($insert_q);
?>