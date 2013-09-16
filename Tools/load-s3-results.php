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

$entries = array_merge(file("real/s3_test/external_hit.results"));
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
			<th>Bef Index</th>
			<th>Aft Index</th>
			<th>Bef noop</th>
			<th>Aft noop</th>
		</tr>
<?php
	$vids = array();
	$labels = array();
	$result = $mysqli->query("SELECT * FROM stage2_3");
	while($responses = $result->fetch_assoc()){
		$vids[$responses["id"]] = array(
			"vid" => $responses["video_id"],
			"label" => $responses["det_label"],
			"time" => $responses["det_label_index"]
			);
		// $vid = $responses["video_id"];
		// echo "<td>" . $vid . "</td>";
		// echo "<td>" . $labels . "</td>";
	}


$count = 0;
foreach($entries as $i => $entry) {	
	// 30: video ID
	// 31 Answer.allAfterIndices	NOT USED
	// 32 Answer.allBeforeIndices	NOT USED
	// 33 Answer.before-noop	
	// 34 Answer.beforeIndex	
	// 29 Answer.afterIndex	
	// 35 Answer.after-noop


	$output_string = "";
	$data = explode("\t", $entry);
	if ($data[19] == "\"workerid\"") // ignore header
		continue;

	$count = $count + 1;
	// filter wrong results. skip if rere
	// if ($data[19] == "\"workerid\"" || $data[28] == "\"y\"" || $data[19] == "" || in_array($data[19], $blackList))
	// 	continue;
	// removing quotes
	$data[30] = substr($data[30], 1, -1);
	$data[34] = substr($data[34], 1, -1);
	$data[29] = substr($data[29], 1, -1);
	$data[33] = substr($data[33], 1, -1);
	$data[35] = substr($data[35], 1, -2);

	// $labels = explode(",", substr($data[30], 1, -2)); // getting rid of quotes and split
	// $labels_result = "";
	// $labels_result_file = "";
	// $vid = substr($data[29], 8, -5);
	// if (!isset ($entry_array[$vid])){
	// 	$entry_array[$vid] = array();
	// 	$entry_array[$vid]["count"] = 1;
	// } else
	// 	$entry_array[$vid]["count"] = $entry_array[$vid]["count"] + 1;	

	echo "<tr><td>{$count}</td><td>{$data[19]}</td>" .
		"<td>{$data[30]}</td>" .
		"<td>" . intval($data[34]) . "</td>" .
		"<td>" . intval($data[29]) . "</td>" .
		"<td>{$data[33]}</td>" .
		"<td>{$data[35]}</td>";	
	echo "</tr>";

	// $data[32] = trim(preg_replace('/\s+/', ' ', $data[32]));
	$output_string = $count . "\t\t" . 
					$vids[$data[30]]["vid"] . "\t\t" . 
					$vids[$data[30]]["time"] . "\t\t" . 
					$vids[$data[30]]["label"] . "\t\t" .
					intval($data[34]) . "\t\t" .
					intval($data[29]) . "\t\t" .
					$data[33] . "\t\t" .
					$data[35] . "\t\t" .
					"\n";
	file_put_contents("s3.temp.data", $output_string, FILE_APPEND);
}

?>
	</table>
</body>
</html>