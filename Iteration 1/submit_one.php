<?php
include 'connect_sql.php'; 

$table_name = "i1_t1";

$ip = $_SERVER['REMOTE_ADDR']; 
$indices = $_GET["indices"];
$video = $_GET["video"];

$insert_q = "INSERT INTO " . $table_name . " (ip, indices, video)
	VALUES ('" . $ip . "', '" . $indices . "', '" . $video . "')";
mysql_query($insert_q);
?>