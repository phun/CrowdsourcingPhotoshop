<?php 

define("FFMPEG_PATH", "/opt/local/bin/ffmpeg");	// path of ffmpeg

define("DB_HOST", "50.116.6.114");	// MySQL host name
define("DB_USERNAME", "annotation-user");	// MySQL username
define("DB_PASSWD", "3APGj4vGmdWcQ6fy");	// MySQL password
define("DB_NAME", "HowtoAnnotation");	// MySQL database name. vt.sql uses the default video_learning name. So be careful.

// Set the file to use.
$data_file = "s2.data.1.final.json";
$success_count = 0;

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$id_list = array();

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function storeInDB($mysqli, $video_id, $cluster_id, $det_label_index, $det_label){
	global $id_list;
	$video_id = $mysqli->escape_string($video_id);
	$cluster_id = $mysqli->escape_string($cluster_id);
    $det_label_index = $mysqli->escape_string($det_label_index);
	$det_label = $mysqli->escape_string($det_label);
	$success = true;
    
    // echo $cluster_id . " " . $video_id . " ". $det_label_index . " " . $det_label . "<br>";
	if (!$mysqli->query("UPDATE stage2_3 SET det_label='$det_label' WHERE id='$cluster_id' AND det_label_index='$det_label_index'"));
	  	$success = false;
	// if ($mysqli->affected_rows != 1)
	//   	$success = false;
    if ($success == false)
        echo "error" . " " . $cluster_id . " " . $video_id . " ". $det_label_index . " " . $det_label . "<br>";
    if ($success == true)
        $success_count += 1;
}

function get_video_name($video_id, $e_count){
	if ($e_count < 10){
		return $video_id . "_e0$e_count";
	} 
	return $video_id . "_e$e_count";
}


$string = file_get_contents($data_file);
$json = json_decode($string, TRUE);

// $jsonIterator = new RecursiveIteratorIterator(
//     new RecursiveArrayIterator($json),
//     RecursiveIteratorIterator::SELF_FIRST);

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
    				if ($field == "time")
    					$det_label_index = $field_val;
                    else if ($field == "cluster_id")
                        $cluster_id = $field_val;
                    else if ($field == "label")
                        $det_label = $field_val;
    			}
    		}
    		$new_video_id = get_video_name($video_id, $e_count);
    		$new_video_id[1] = "2";
    		// echo $new_video_id . "\n\n";
    		// echo $cluster_id . "\n\n";
    		// echo $det_label_index . "\n\n";
    		// echo print_r($time_array) . "\n\n";
    		// echo print_r($desc_array) . "\n\n<br>";
    		storeInDB($mysqli, $new_video_id, $cluster_id, $det_label_index, $det_label);

    	}
    }
}

$mysqli->close();
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
	<ul>
<?php 
	echo $success_count . " items successfully updated.";
?>
	</ul>
</body>
</html>