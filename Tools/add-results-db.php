<?php 

define("FFMPEG_PATH", "/opt/local/bin/ffmpeg");	// path of ffmpeg

define("DB_HOST", "50.116.6.114");	// MySQL host name
define("DB_USERNAME", "annotation-user");	// MySQL username
define("DB_PASSWD", "3APGj4vGmdWcQ6fy");	// MySQL password
define("DB_NAME", "HowtoAnnotation");	// MySQL database name. vt.sql uses the default video_learning name. So be careful.

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// $result = $mysqli->query("SELECT * FROM stage2_3");
// while($responses = $result->fetch_assoc()){
// 	//var_dump($responses);
// }

$entry_array = array();
$entries = file("real/s1_p_2/external_hit.results");
?>
<html>
<head>
<link rel="stylesheet" href="js/bootstrap.min.css" type="text/css" />
<script src="js/jquery-1.7.2.min.js"></script>	
	<style type="text/css">
		table, td {
			border: 1px solid black;
		}
	</style>
</head>
<body>
	<table>
		<tr>
			<td>ID</td>
			<td>Worker ID</td>
			<td>Video</td>
			<td>Response</td>
		</tr>
<?php


foreach($entries as $i => $entry) {
	$data = explode("\t", $entry);
	$labels = explode(",", substr($data[30], 1, -2)); // getting rid of quotes and split
	$labels_result = "";
	$vid = substr($data[29], 8, -5);
	if (!isset ($entry_array[$vid])){
		$entry_array[$vid] = array();
		$entry_array[$vid]["count"] = 1;
	} else
		$entry_array[$vid]["count"] = $entry_array[$vid]["count"] + 1;	

	foreach($labels as $j => $label){
		$cur_thing = $entry_array[$vid]["count"];
		//echo $cur_thing;
		if (!isset ($entry_array[$vid][$cur_thing]))
			$entry_array[$vid][$cur_thing] = 1;
		else
			$entry_array[$vid][$cur_thing] = $entry_array[$vid][$cur_thing] + 1;	

		//echo $entry_array[$vid][$cur_thing];
		if ($j == 0 || strpos($label, "@") == FALSE)
			$labels_result .= $label;
		else
			$labels_result .= "<br/>" . $label;
	}
	echo "<tr><td>Line #<b>{$i}</b></td><td>" . $data[19] . "</td><td>" . htmlspecialchars($data[29]) . "</td><td>" . $labels_result . "</td></tr>";
}

foreach($entry_array as $i => $entry){
	if ($entry["count"] == 3)
		echo $i . " " . $entry["count"] . " === " . $entry[1] . " : " . $entry[2] . " : " . $entry[3] . "<br/>";
}

?>
	</table>
</body>
</html>