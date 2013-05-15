<?php
include 'connect_sql.php'; 

$table_name = "i1_t1";

$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']); 
$indices = mysql_real_escape_string($_GET["indices"]);
$video = mysql_real_escape_string($_GET["video"]);

echo "Thank you for completing this task!";

$insert_q = "INSERT INTO " . $table_name . " (ip, indices, video)
	VALUES ('" . $ip . "', '" . $indices . "', '" . $video . "')";
mysql_query($insert_q);
?>