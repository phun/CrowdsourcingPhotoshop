<?php
include 'connect_sql.php'; 

$table_name = "i1_t2";

$ip = $_SERVER['REMOTE_ADDR']; 
$video = $_GET["video"];
$region = $_GET["region"];
$tool = $_GET["tool"];

$insert_q = "INSERT INTO " . $table_name . " (ip, video, region, tool)
	VALUES ('" . $ip . "', '" . $video . "', '" . $region . "', '" . $tool . "')";
mysql_query($insert_q);
?>