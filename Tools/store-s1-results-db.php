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


function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function storeInDB($mysqli, $video_id, $cluster_id, $det_label_index, $time_array, $desc_array){
	$video_id = $mysqli->escape_string($video_id);
	$cluster_id = $mysqli->escape_string($cluster_id);
	$det_label_index = $mysqli->escape_string($det_label_index);
	$all_label_indices = implode(",", $time_array);
	$all_labels = serialize($desc_array);
	$all_label_indices = $mysqli->escape_string($all_label_indices);
	$all_labels = $mysqli->escape_string($all_labels);

	// $success = true;
	// if (!$mysqli->query("INSERT INTO stage2_3(video_id, cluster_id, all_label_indices, det_label_index, all_labels) " . 
	//   "VALUES('$video_id', '$cluster_id', '$all_label_indices', '$det_label_index', '$all_labels')"))
	//   	$success = false;
	// if ($mysqli->affected_rows != 1)
	//   	$success = false;
	// echo "=== $success<br><br>";
}

function get_video_name($video_id, $e_count){
	if ($e_count < 10){
		return $video_id . "_e0$e_count";
	} 
	return $video_id . "_e$e_count";
}


$string = file_get_contents("s1_c.data.0.07.final.json");
$json = json_decode($string, TRUE);

$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator($json),
    RecursiveIteratorIterator::SELF_FIRST);

// foreach ($jsonIterator as $key => $val) {
//     if(is_array($val)) {
//     	if (startsWith($key, "s1_")) {
//     		$video_id = $key;
//         	echo "[[[VIDEO]]] $key:\n\n";	
//         }
//     } else if (is_object($val)){
//     	echo "object" . $val;
//     } else {
//     	if ($key == "cluster_id")
//     		$cluster_id = $val;
//         echo "$key => $val\n\n";
//     }
// }

foreach ($json as $video_id => $video_val) {
    // echo $video_id . "\n\n";
    $e_count = 0;
    if (is_array($video_val)){
    	foreach($video_val as $cluster_id => $cluster_val){
    		$e_count += 1;
    		// echo $cluster_id . "\n\n";
    		if (is_array($cluster_val)){
    			foreach($cluster_val as $field => $field_val){
    				// echo $field . " " . $field_val;
    				if ($field == "time"){
    					$det_label_index = $field_val;
    				} else if ($field == "points_turk" && is_array($field_val)){
    					$time_array = array();
    					$desc_array = array();
    					foreach($field_val as $label){
    						// echo $label["desc"];
    						array_push($time_array, $label["time"]);
    						array_push($desc_array, $label["desc"]);
    					}
    				}
    			}
    		}
    		$new_video_id = get_video_name($video_id, $e_count);
    		$new_video_id[1] = "2";
    		echo $new_video_id . "\n\n";
    		// echo $cluster_id . "\n\n";
    		// echo $det_label_index . "\n\n";
    		// echo print_r($time_array) . "\n\n";
    		// echo print_r($desc_array) . "\n\n<br>";
    		storeInDB($mysqli, $new_video_id, $cluster_id, $det_label_index, $time_array, $desc_array);
    	}
    }
}


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



?>
	</table>
</body>
</html>