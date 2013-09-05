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


$entries = file("real/s2_c_1/external_hit.results");

echo "total: " . sizeof($entries) . " entries<br>";
?>
<html>
<head>
<link rel="stylesheet" href="js/bootstrap.min.css" type="text/css" />
<script src="js/jquery-1.7.2.min.js"></script>	
<script src="js/sorttable.js"></script>
	<style type="text/css">
		table, td {
			border: 1px solid black;
		}
	</style>
<script>
	sorttable.makeSortable(document.getElementById('dataTable'));
</script>
</head>
<body>
	<table class="sortable" id="dataTable">
		<tr>
			<th class="sorttable_nosort">ID</th>
			<th>Worker ID</th>
			<th>Cluster ID</th>
			<th>Instruction</th>
			<th>Order</th>
			<th>Video ID</th>
			<th>Labels</th>
		</tr>
<?php

$count = 0;
foreach($entries as $i => $entry) {	
	if ($i == 0) // ignore the header
		continue;
	// $data[29]: video ID
	// $data[31]: answer
	// $data[32]: order of options presented
	$output_string = "";
	$data = explode("\t", $entry);
	// filter wrong results. skip if rere
	// if ($data[19] == "\"workerid\"" || $data[28] == "\"y\"" || $data[19] == "" || in_array($data[19], $blackList))
	// 	continue;
	$data[29] = substr($data[29], 1, -1);
	// $labels = explode(",", substr($data[30], 1, -2)); // getting rid of quotes and split
	// $labels_result = "";
	// $labels_result_file = "";
	// $vid = substr($data[29], 8, -5);
	// if (!isset ($entry_array[$vid])){
	// 	$entry_array[$vid] = array();
	// 	$entry_array[$vid]["count"] = 1;
	// } else
	// 	$entry_array[$vid]["count"] = $entry_array[$vid]["count"] + 1;	

	echo "<tr><td>{$i}</td><td>{$data[19]}</td><td>" . intval($data[29]) . "</td><td>{$data[31]}</td><td>{$data[32]}</td>";

	$result = $mysqli->query("SELECT * FROM stage2_3 WHERE id=$data[29]");
	while($responses = $result->fetch_assoc()){
		echo "<td>" . $responses["video_id"] . "</td>";
		echo "<td>" . implode("====", unserialize($responses["all_labels"])) . "</td>";
	}
	echo "</tr>";
	// $output_string = $count . "\t\t" . $data[19] . "\t\t" . $vid . "\t\t" . $labels_result_file . "\n";
	// file_put_contents("s2_.data", $output_string, FILE_APPEND);
}

?>
	</table>
</body>
</html>