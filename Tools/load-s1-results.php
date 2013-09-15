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
$domain = "p"; // "c", "m", "p"
if ($domain == "c")
	$entries = array_merge(file("real/s1_" . $domain . "_11/external_hit.results"), file("real/s1_" . $domain . "_1/external_hit.results"), file("real/s1_" . $domain . "_2/external_hit.results"), file("real/s1_" . $domain . "_3/external_hit.results"), file("real/s1_" . $domain . "_4/external_hit.results"), file("real/s1_" . $domain . "_5/external_hit.results"), file("real/s1_" . $domain . "_6/external_hit.results"));
else if ($domain == "m")
	$entries = array_merge(file("real/s1_" . $domain . "_11/external_hit.results"), file("real/s1_" . $domain . "_1/external_hit.results"), file("real/s1_" . $domain . "_2/external_hit.results"), file("real/s1_" . $domain . "_3/external_hit.results"), file("real/s1_" . $domain . "_4/external_hit.results"), file("real/s1_" . $domain . "_5/external_hit.results"), file("real/s1_" . $domain . "_6/external_hit.results"));
else if ($domain == "p")
	$entries = array_merge(file("real/s1_" . $domain . "_11/external_hit.results"), file("real/s1_" . $domain . "_1/external_hit.results"), file("real/s1_" . $domain . "_2/external_hit.results"), file("real/s1_" . $domain . "_3/external_hit.results"), file("real/s1_" . $domain . "_4/external_hit.results"), file("real/s1_" . $domain . "_5/external_hit.results"), file("real/s1_" . $domain . "_6/external_hit.results"), file("real/s1_" . $domain . "_7/external_hit.results"), file("real/s1_" . $domain . "_8/external_hit.results"));

//$entries = file("real/s1_" . $domain . "_5/external_hit.results");

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
			<th>Video</th>
			<th>Response</th>
		</tr>
<?php

$blackList = ["", "\"A3GEI91OQIB2I6\"", "\"A30P92SBY851NI\"", "\"A3CIIRJLV6MAPF\"", "\"A2KYQHSSAR531E\"", "\"A3AYR3H2AYYT5G\"", "\"A3IKU2UUVMXBCQ\"", "\"A1AB408CLFAC5D\"", "\"A1IPZBG9BOHYZ6\"", "\"A2EKPA2DR5VSA8\""];
//$blackList = ["", "\"A3GEI91OQIB2I6\"", "\"A30P92SBY851NI\"", "\"A3CIIRJLV6MAPF\"", "\"A2KYQHSSAR531E\"", "\"A3AYR3H2AYYT5G\"", "\"A3IKU2UUVMXBCQ\"", "\"A1AB408CLFAC5D\"", "\"A1IPZBG9BOHYZ6\""];
// , "\"A2EKPA2DR5VSA8\""

$count = 0;
foreach($entries as $i => $entry) {
	$output_string = "";
	$data = explode("\t", $entry);
	// filter wrong results. skip if rere
	if ($data[19] == "\"workerid\"" || $data[28] == "\"y\"" || $data[19] == "" || in_array($data[19], $blackList))
		continue;
	$count = $count + 1;
	$labels = explode(",", substr($data[30], 1, -2)); // getting rid of quotes and split
	$labels_result = "";
	$labels_result_file = "";
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
			$entry_array[$vid][$cur_thing] = 0;
		
		if ($label != "")
			$entry_array[$vid][$cur_thing] = $entry_array[$vid][$cur_thing] + 1;	

		//echo $entry_array[$vid][$cur_thing];
		if ($j == 0 || strpos($label, "@") == FALSE){
			$labels_result .= $label;
			$labels_result_file .= $label;
		} else {
			$labels_result .= "<br/>" . $label;
			$labels_result_file .= "\t" . $label;
		}
	}
	echo "<tr><td>{$count}</td><td>{$data[19]}</td><td>" . htmlspecialchars($vid) . "</td><td>" . $labels_result . "</td></tr>";
	$output_string = $count . "\t\t" . $data[19] . "_" . $count . "\t\t" . $vid . "\t\t" . $labels_result_file . "\n";
	file_put_contents("s1_" . $domain . ".data", $output_string, FILE_APPEND);
}

function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}
ksort($entry_array);

function showCounts($entry_array, $val){
	$count = 0;
	foreach($entry_array as $i => $entry){
		if ($entry["count"] == $val){
			$count = $count + 1;
			echo $i . " " . $entry["count"] . " || "; 
			for($j=1; $j<=$val; $j++){
				echo $entry[$j] . " ";
			}
			echo "<br/>";
		}
	}
	echo "<b>{$count} {$val}/3 completed.</b><br>";

}

showCounts($entry_array, 6);
showCounts($entry_array, 5);
showCounts($entry_array, 4);
showCounts($entry_array, 3);
showCounts($entry_array, 2);
showCounts($entry_array, 1);
showCounts($entry_array, 0);

$count = 0;
foreach($entry_array as $i => $entry){
		$count = $count + 1;
		echo $i . " " . $entry["count"] . " === " . $entry[1] . " : " . $entry[2] . " : " . $entry[3] . "<br/>";
}
echo "<b>{$count} with something.</b><br>";

?>
	</table>
</body>
</html>