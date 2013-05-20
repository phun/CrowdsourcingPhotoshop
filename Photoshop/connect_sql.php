 <?php 
 $address = "localhost";
 $user = "inputter";
 $pass = "admin";
 $db_name = "photoshop";
 mysql_connect($address, $user, $pass) or die(mysql_error()); 
 mysql_select_db($db_name) or die(mysql_error()); 
 ?> 