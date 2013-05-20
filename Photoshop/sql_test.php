<?php

include 'datalogin.php'; 

// Create database
// if (mysql_query("CREATE DATABASE " . $db_name, $con))
//   {
//   echo "Database created";
//   }
// else
//   {
//   echo "Error creating database: " . mysql_error();
//   }

// Generate Table
// mysql_select_db($db_name, $con);
$sql_t1 = "CREATE TABLE i1_t1 (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip varchar(30),
	date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	indices varchar(1000),
 video varchar(50), 
	)";

// $sql_t2 = "CREATE TABLE i1_t2 (
// 	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
// 	ip varchar(30),
// 	date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
// 	video varchar(50),
// 	region INT,
// 	tool varchar(50) 
// 	)";

$sql_t2 = "CREATE TABLE i1_t3 (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip varchar(30),
	date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	video varchar(50),
	beforeIndice varchar(50),
	afterIndice varchar(50),
	allBeforeIndices varchar(2000),
	allAfterIndices varchar(2000)
	)";

// mysql_query($sql,$con);
//

// Test Insertion
// mysql_select_db($db_name, $con);
// $insert_q = "INSERT INTO i1_t1 (ip, indices)
// VALUES ('localhost', '0, 1.0, 24.5')";
// mysql_query($insert_q,$con);

?>