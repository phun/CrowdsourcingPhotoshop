<?php

define("DB_HOST", "50.116.6.114");    // MySQL host name
define("DB_USERNAME", "annotation-user");    // MySQL username
define("DB_PASSWD", "3APGj4vGmdWcQ6fy");    // MySQL password
define("DB_NAME", "HowtoAnnotation");    // MySQL database name. vt.sql uses the default video_learning name. So be careful.

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$video_id = $_GET["vid"];
// $result = $mysqli->query("SELECT * FROM stage2_3 WHERE id = '$id'");
// while($responses = $result->fetch_assoc()){
// 	$video_id = $responses['video_id'];
// 	$det_label = $responses['det_label'];
// 	$raw_labels = $responses['all_labels'];
// 	$time_index = $responses['det_label_index'];
// }
// $all_labels = unserialize($raw_labels);


$query_vid = "s2_" . $video_id . "%";
$s23_data = array();
$result = $mysqli->query("SELECT * FROM stage2_3 WHERE video_id LIKE '$query_vid'");
while($responses = $result->fetch_assoc()){
	$s23_data[$responses["id"]] = array(
		"video_id" => $responses["video_id"],
		"label" => $responses["det_label"],
		"time" => $responses["det_label_index"],
		"all_before_indices" => $responses["all_before_indices"],
		"all_after_indices" => $responses["all_after_indices"],
		"before_index" => $responses["det_before_index"],
		"after_index" => $responses["det_after_index"],
		);
	// $vid = $responses["video_id"];
	// echo "<td>" . $vid . "</td>";
	// echo "<td>" . $labels . "</td>";
}

// $video_id = $vids[$id]["video_id"];

$entries = array_merge(file("real/s3_1/external_hit.results"), file("real/s3_2/external_hit.results"));

$count = 0;
$turk_data = array();
foreach($entries as $i => $entry) {	
	// 30: video ID
	// 31 Answer.allAfterIndices	NOT USED
	// 33 Answer.allBeforeIndices	NOT USED
	// 35 Answer.before-noop	
	// 32 Answer.beforeIndex	
	// 29 Answer.afterIndex	
	// 34 Answer.after-noop

	// deprecated!!! 
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
	$cluster_id = substr($data[30], 1, -1);
	$item = array(
		"cluster_id" => $cluster_id,
		"worker_id" => substr($data[19], 1, -1),
		"before_index" => intval(substr($data[32], 1, -1)),
		"after_index" => intval(substr($data[29], 1, -1)),
		"before_noop" => substr($data[35], 1, -1),
		"after_noop" => substr($data[34], 1, -2)
	);
	if (!isset($turk_data[$cluster_id]))
		$turk_data[$cluster_id] = array();
	array_push($turk_data[$cluster_id], $item);
}

// echo print_r($all_labels);

// define("DB_HOST", "50.116.6.114");	// MySQL host name
// define("DB_USERNAME", "toolscape-user");	// MySQL username
// define("DB_PASSWD", "G8hsDe5r4jDtFAYa");	// MySQL password
// define("DB_NAME", "video_learning");	// MySQL database name. vt.sql uses the default video_learning name. So be careful.

// from labeler/cscw-get-true-label.php
$cid_list = array(56 => "c01_v01",57 => "c01_v02",58 => "c01_v03",59 => "c01_v04",60 => "c01_v05",
				  61 => "c02_v01",62 => "c02_v02",63 => "c02_v03",64 => "c02_v04",65 => "c02_v05",
				  66 => "c03_v01",67 => "c03_v02",68 => "c03_v03",69 => "c03_v04",70 => "c03_v05",
				  71 => "c04_v01",72 => "c04_v02",73 => "c04_v03",74 => "c04_v04",75 => "c04_v05",
				  76 => "c05_v01",77 => "c05_v02",78 => "c05_v03",79 => "c05_v04",80 => "c05_v05");
$mid_list = array(81 => "m01_v01",82 => "m01_v02",83 => "m01_v03",84 => "m01_v04",85 => "m01_v05",
				  86 => "m02_v01",87 => "m02_v02",88 => "m02_v03",89 => "m02_v04",90 => "m02_v05",
				  91 => "m03_v01",92 => "m03_v02",93 => "m03_v03",94 => "m03_v04",95 => "m03_v05",
				  96 => "m04_v01",97 => "m04_v02",98 => "m04_v03",99 => "m04_v04",100 => "m04_v05",
				  101 => "m05_v01",102 => "m05_v02",103 => "m05_v03",104 => "m05_v04",105 => "m05_v05");
$pid_list = array(1  => "p01_v01",2  => "p01_v02",3  => "p01_v03",4  => "p01_v04",5  => "p01_v05",
				  22 => "p02_v01",23 => "p02_v02",24 => "p02_v03",25 => "p02_v04",26 => "p02_v05",
				  32 => "p03_v01",33 => "p03_v02",34 => "p03_v03",35 => "p03_v04",36 => "p03_v05",
				  11 => "p04_v01",12 => "p04_v02",13 => "p04_v03",14 => "p04_v04",15 => "p04_v05",
				  42 => "p05_v01",43 => "p05_v02",44 => "p05_v03",45 => "p05_v04",46 => "p05_v05");

// echo substr($video_id, 3, 7);
if ($video_id[0] == "c")
	$list = $cid_list;
else if ($video_id[0] == "m")
	$list = $mid_list;
else if ($video_id[0] == "p")
	$list = $pid_list;

// $key = array_search(substr($video_id, 3, 7), $list);
// echo $key;

$mysqli = new mysqli(DB_HOST, "toolscape-user", "G8hsDe5r4jDtFAYa", "video_learning");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$video_data = array();
$result = $mysqli->query("SELECT * FROM videos WHERE in_chi2014_set = '$video_id'");
// if ($result->num_rows != 1)
// 	echo "query error";
// $video = $result->fetch_assoc();
while($responses = $result->fetch_assoc()){
	$video_data[$responses["in_chi2014_set"]] = array(
		"video_id" => $responses["in_chi2014_set"],
		"slug" => $responses["slug"],
		"duration" => $responses["duration"],
		"title" => $responses["title"],
		"url" => $responses["url"]
	);
	// $vid = $responses["video_id"];
	// echo "<td>" . $vid . "</td>";
	// echo "<td>" . $labels . "</td>";
}

// echo print_r($video);
?>

<html>
<head>
	<title>Verify Stage 3</title>
	<link rel="stylesheet" href="../All/css/ui-lightness/jquery-ui-1.8.22.custom.css" type="text/css" />
	<link rel="stylesheet" href="../All/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="../All/style.css" />
	<script src="../All/js/libs/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../All/js/libs/jquery-ui-1.8.22.custom.min.js"></script> 
	<script type="text/javascript" src="../All/js/libs/jwplayer/jwplayer.js"></script>
	<link rel="stylesheet" type="text/css" href="../All/js/jquery.qtip.min.css" />
	<style>
	#header{
		position: fixed;
		z-index: 100;
		height:430px;
		top: 0;
		background-color: #ddd;
		padding-left: 15px;
		padding-right: 15px;
		width: 100%;
	}
	#task{
		margin-top: 430px;
	}
	.sb-option{
		width: 250px;
		height: 180px;
		float: left;
		padding: 10px;
		position: relative;
	}
	.sb{
		/*width: 120px;
		height: 90px;*/
		cursor: pointer;
		border: 10px solid white;
		-webkit-touch-callout: none;
		-webkit-user-select: none;
		-khtml-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;		
	}
	.sb-label{
		text-align: center;
	}
	.sb.selected{
		border: 10px solid #0E6870;
	}
	.sb:hover{
		border: 10px solid #0E6870;
	}
	.s3-turker-input, .s3-final-input{
		width: 100%;
		height: 250px;
	}
	.before-image, .after-image{
		float: left;
		width: 250px;
		height: 180px;
		margin: 0 30px 0 30px;
	}
	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	<script type="text/javascript">

	Number.prototype.pad = function(n) {
	    return ('0000000000' + this).slice((n || 2) * -1);
	}

	// for the given video and time (in second), 
	// get the storyboard image file.
	function getStoryBoard(slug, index){
		var sheet_index = Math.floor(index / 25);
		var processed = sheet_index >= 10 ? ("" + sheet_index) : ("0" + sheet_index);
		var url = "http://juhokim.com/annotation/videos/sb/" + 
			slug + "00" + processed + ".jpg";
		return url;
	}

	// // from the 5x5 storyboard, find the position of the given item
	// function getThumbnailPosition(img, index, image_url, second){
	// 	var thumb_index = index % 25;
	// 	// var $img = $(img[0]);
	// 	// var thumb_width = Math.floor($img.width() / 5);
	// 	// var thumb_height = Math.floor($img.height() / 5);
	// 	var thumb_width = 108; //Math.floor(img.width / 5);
	// 	var thumb_height = 60; //Math.floor(img.height / 5);
	// 	var position = [];
	// 	position[0] = (-1) * thumb_width * (thumb_index % 5) + "px";
	// 	position[1] = (-1) * thumb_height * Math.floor(thumb_index / 5) + "px";
	// 	var $div = $("<div/>").addClass("sb")
	// 			.attr("data-index", second)
	// 			// .attr("src", image_url)
	// 			.css("background", "transparent url(" + image_url + ") " + position[0] + " " + position[1])
	// 			// .css("background-size", thumb_width + "px " + thumb_height + "px ")
	// 			// .css("background-size", "720px 300px") // 540:300 is the default (108:60 per image, 9:5)
	// 			.css("background-repeat", "no-repeat");
	// 	$(".sb").css("width", thumb_width).css("height", thumb_height);
	// 	console.log(index, img, position[0], position[1]);
	// 	return $div;
	// }

	// // controlling storyboard display
	// function displayChoices_storyboard(start_int, second){
	// 	var second;
	// 	var slug = "<?php echo $video['slug']; ?>";
	// 	var position = []; // [x, y]
	// 	var image_url = "";
	// 	var index;
	// 	var $choice;
	// 	var $img;
	// 	var dummy_img; // placeholder to compute image width and height
	// 	// var start_int = start);
	// 	// for (second = start_int; second <= start_int + 20; second++){
	// 		index = Math.floor(second); // divide by the sampling rate if not 1
	// 		console.log(start_int, second, index);
	// 		image_url = getStoryBoard(slug, index);
	// 		dummy_img = new Image();
	// 		dummy_img.onload = function(){
	// 			$img = getThumbnailPosition(dummy_img, index, image_url, second);
	// 			$choice = $("<div/>").addClass("sb-option").append($img);
	// 			// once image successfully loaded,
	// 			if (second < start_int + 10) {
	// 				console.log("c1", second, start_int);
	// 				$("#choices-before").append($choice);
	// 			} else if (second == start_int + 10) { // add to both
	// 				console.log("c2", second, start_int);
	// 				$("#choices-before").append($choice);
	// 				$("#choices-after").append($choice.clone());
	// 			} else {
	// 				console.log("c3", second, start_int);
	// 				$("#choices-after").append($choice);
	// 			}
	// 			if (second <= start_int + 18)
	// 				displayChoices(start_int, second + 2);
	// 		}
	// 		dummy_img.src = image_url;
	// 	// }

	// }


	// // controlling storyboard display
	// function displayChoices(start_int, second){
	// 	var second;
	// 	var slug = "<?php echo $video['slug']; ?>";
	// 	var position = []; // [x, y]
	// 	var image_url = "";
	// 	var index;
	// 	var $choice;
	// 	var $img;
	// 	var dummy_img; // placeholder to compute image width and height
	// 	// var start_int = start);
	// 	// for (second = start_int; second <= start_int + 20; second++){
	// 		index = Math.floor(second); // divide by the sampling rate if not 1
	// 		console.log(start_int, second, index);
	// 		image_url = "http://juhokim.com/annotation/videos/thumbs/v_" + slug + "_" + second.pad(3) + ".jpg";
	// 		dummy_img = new Image();
	// 		dummy_img.onload = function(){
	// 			$img = $("<img/>").addClass("sb").attr("data-index", index).attr("src", image_url);
	// 			$choice = $("<div/>").addClass("sb-option").append($img);
	// 			// once image successfully loaded,
	// 			if (second < start_int + 10) {
	// 				console.log("c1", second, start_int);
	// 				$("#choices-before").append($choice);
	// 			} else if (second == start_int + 10) { // add to both
	// 				console.log("c2", second, start_int);
	// 				$("#choices-before").append($choice);
	// 				$("#choices-after").append($choice.clone());
	// 			} else {
	// 				console.log("c3", second, start_int);
	// 				$("#choices-after").append($choice);
	// 			}
	// 			if (second <= start_int + 18)
	// 				displayChoices(start_int, second + 2);
	// 		}
	// 		dummy_img.src = image_url;
	// 	// }

	// }


	// controlling storyboard display
	function displaySingleChoice(slug, second, $el, mode){
		var position = []; // [x, y]
		var image_url = "";
		var index;
		var $choice;
		var $img;
		var $label;
		var dummy_img; // placeholder to compute image width and height
		index = Math.floor(second); // divide by the sampling rate if not 1
		// console.log(second, index, index.pad(3), parseInt(second).pad(3), typeof second, typeof index);
		image_url = "http://juhokim.com/annotation/videos/thumbs/v_" + slug + "_" + index.pad(3) + ".jpg";
		console.log(image_url);
		dummy_img = new Image();
		dummy_img.onload = function(){
			$label = $("<div/>").addClass("sb-label").text(mode);
			$img = $("<img/>").addClass("sb").attr("data-index", index).attr("src", image_url);
			$choice = $("<div/>").addClass("sb-option").append($label).append($img);
			$el.append($choice);
		}
		dummy_img.src = image_url;
	}

		var videoPlayed = false;

		// Get Parameters
		var prmstr = window.location.search.substr(1);
		var prmarr = prmstr.split ("&");
		var params = {};
		for ( var i = 0; i < prmarr.length; i++) {
			var tmparr = prmarr[i].split("=");
			params[tmparr[0]] = tmparr[1];
		}
		var user = params["user"];
		// starting time retrieved from database. (target time - 10)
		var start = 0;
		console.log(start);		
		      // 2. This code loads the IFrame Player API code asynchronously.
		      // var tag = document.createElement('script');
		      // tag.src = "https://www.youtube.com/iframe_api";
		      // var firstScriptTag = document.getElementsByTagName('script')[0];
		      // firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		      // 3. This function creates an <iframe> (and YouTube player)
		      //    after the API code downloads.
		      // var player;
		      // function onYouTubeIframeAPIReady() {
		      //   player = new YT.Player('ytplayer', {
		      //     height: '390',
		      //     width: '640',
		      //     videoId: "<?php echo $video['slug']; ?>",
		      //     events: {
		      //       'onReady': onPlayerReady,
		      //       'onStateChange': onPlayerStateChange
		      //     }
		      //   });
		      // }
		    var player;
    		var vidParams = { allowScriptAccess: "always" };
    		var atts = { id: "ytplayer" };
    		swfobject.embedSWF("http://www.youtube.com/v/<?php echo $video_data[$video_id]['slug']; ?>?enablejsapi=1&playerapiid=ytplayer&version=3",
                       "ytplayer", "560", "315", "8", null, null, vidParams, atts);

			function onYouTubePlayerReady(playerId) {
			      player = document.getElementById("ytplayer");
			      console.log("onPlayerReady");
			      player.addEventListener("onStateChange", "onPlayerStateChange");
			      player.loadVideoById({'videoId': '<?php echo $video_data[$video_id]["slug"]; ?>'});
			      setInterval(updateytplayerInfo, 600);
			      updateytplayerInfo();
			}		      

			function updateytplayerInfo(){
				if (player) {
				    var position = player.getCurrentTime();
					var offset = parseInt(position - start);
					if (offset < 10)
						$("#timerDisplay").text("0:0" + offset);
					else
						$("#timerDisplay").text("0:" + offset);		
					$("#timeline").slider( "option", "max", 20);
	            	$("#timeline").slider('value', offset);		    
				}							
			}
			/*
		      // 4. The API will call this function when the video player is ready.
		      function onPlayerReady(event) {
			    console.log("onPlayerReady", start, start+20);
			    //event.target.playVideo();
				player.cueVideoById({'videoId': '<?php echo $video['slug']; ?>', 'startSeconds': start, 'endSeconds': start+20});
				//player.cueVideoById({'videoId': 'bHQqvYy5KYo', 'startSeconds': 50, 'endSeconds': 60});

		      }
		    */
		      // 5. The API calls this function when the player's state changes.
		      //    The function indicates that when playing a video (state=1),
		      //    the player should play for six seconds and then stop.
		      var done = false;
		      function onPlayerStateChange(state) {
		      	console.log("CHANGE", state);
		      	if (state == -1){
				    setTimeout( function() { 
				  		if (player.getPlayerState() == -1){
							$("#errorMsg").show()
								.html("Cannot see the video? Please open <a target='_blank' href='<?php echo urldecode(stripslashes($video['url'])); ?>&t=" +
									parseInt(start) + "s'>this link</a>, watch the video for 20 seconds, and answer the question below.");
							setTimeout( function() { 
								 videoPlayed = true;
								 if ($("#instruction").val() != "") {
								 	$("#taskSub").removeClass('disabled').removeAttr('disabled');
								 }
							}, 20000);
						}
					}, 5000);		      		
		      	} else if (state == 1 && !done) {
		      		stopVideo();
		      		done = true;
				 //    setTimeout( function() { 
					// 	 videoPlayed = true;
					// 	 if ($("#instruction").val() != "") {
					// 	 	$("#taskSub").removeClass('disabled').removeAttr('disabled');
					// 	 }
					// }, 20000);
		   //          done = true;
		        }
		      }

		      function stopVideo() {
		        player.stopVideo();
		      }  


		var vid = <?php echo json_encode($video_id); ?>,
			s23_data = <?php echo json_encode($s23_data); ?>,
			video_data = <?php echo json_encode($video_data); ?>,
			tname = <?php echo json_encode($s23_data[$video_id]["label"]); ?>,
			turk_data = <?php echo json_encode($turk_data); ?>,
			// cluster_id = params['id'],
			// allLabels = <?php echo json_encode($all_labels); ?>,
			genre = vid[0],	// c = Cooking, p = Photoshop, m = Makeup
			// video = null;
			video = "<?php echo urldecode(stripslashes($video_data[$video_id]['url'])); ?>";


		$(document).ready(function() {

			$(document).on("click", "#submit-button", function(){
				console.log("collect all clicks and submit");
				var results = [];
				$(".s3-final-input").each(function(){
					var b_selected = $(this).find(".before-image .sb").hasClass("selected");
					var a_selected = $(this).find(".after-image .sb").hasClass("selected")
					console.log($(this).attr("id"), b_selected, a_selected);
					results.push({"cid": $(this).attr("id"), "before": b_selected, "after": a_selected});
				});
				console.log(user, vid, results);
				$.ajax({
				  type: "POST",
				  url: "s3-verify-ajax-record.php",
				  data: { 
				  	"user": user,
				  	"vid": vid,
				  	"results": JSON.stringify(results) }
				}).done(function( msg ) {
				  alert( "Data Saved");
				  window.location.href = "s3-verify-list.html?user=" + user;
				});
			});
      		var makeTask = function(video, tname, genre) {
    //   			var infoDes;
      			// var ht = '<div class="section task">' +
      					// '<h2> Video </h2>' +
						// '<div class="video">' +
						// 	'<div id="mediaplayer" width="100%" height="400">JW Player goes here</div>' + 
						// '</div>' +
						// '<div id="errorMsg"></div>' +						
						// '<div class="info"><div>' +
						// 	'<h3>For each image, click if it correctly represents before/after effects of the given step.</h3>' +
							// '<div id="tipLabel"><strong>Click the most visible and clear image.</strong> ' +
							// '<span class="pull-right"><input type="checkbox" name="before-noop">There is no good image available.</input></span>' + 
							// '</div>' +
							// '<div id="choices-before" class="choices"></div>' +
							// '<p style="clear:both"></p>' +
							// '<h3> Which best shows the <span class="canvasText"/> <u>after</u> &quot;<span class="tname"/>&quot;?</h3>' +
							// '<div id="tipLabel"><strong>Click the most visible and clear image.</strong> ' +
							// '<span class="pull-right"><input type="checkbox" name="after-noop">There is no good image available.</input></span>' + 
							// '</div>' +
							// '<div id="choices-after" class="choices"></div>' +
							// '<p style="clear:both"></p>' +
							// '<input type="radio" name="labelRadios" value="@">' + '<i>None of these </i>' +
							// '<div id="otherLabel" style="display:none"><h4>Please write an alternative label: </h4>' +
							// 	'<input type="text" id="otherLabelText"></div>' +
				// 		'</div>' +
				// 	'</div>';

				// $("#tasks").append(ht);

      			// displayChoices(parseInt(start), parseInt(start));


				$(document).on("hover", ".sb", 
					function(){
						$(this).addClass("mouse-over");
					}, 
					function(){
						$(this).removeClass("mouse-over");
					});
	      		$(document).on("click", ".sb", function(event, mode){
	      			// $(this).closest(".s3-final-input").find(".selected");
	      			$(this).toggleClass("selected");
	      			// $(this).addClass("selected");
	      			// do nothing for manual trigger. only used to remove the selection marker
	      			// if (mode == "manual")
	      			// 	return;
	      			// var $prevSelection = $(this).closest(".choices").find(".selected");
	      			// var curState = $(this).hasClass("selected");
	      			// $(this).closest(".choices").find(".sb").removeClass("selected");
	      			// if (curState == false){
	      			// 	// trigger click on the previously selected one
	      			// 	if ($prevSelection.length > 0)
	      			// 		$prevSelection.trigger("click", ["manual"]);
	      			// 	$(this).addClass("selected");
	      			// 	if ($(this).closest(".choices").attr("id") == "choices-before"){
	      			// 		$("#beforeIndex").val($(this).attr("data-index"));
	      			// 		addSelectionMarker($(this).closest(".sb"), true);
	      			// 	} else if ($(this).closest(".choices").attr("id") == "choices-after"){
	      			// 		$("#afterIndex").val($(this).attr("data-index"));
	      			// 		addSelectionMarker($(this).closest(".sb"), false);
	      			// 	}
	      			// } else {
	      			// 	if ($(this).closest(".choices").attr("id") == "choices-before"){
	      			// 		$("#beforeIndex").val("");
	      			// 		// removeSelectionMarker($(this).closest(".sb-option"), true);
	      			// 	} else if ($(this).closest(".choices").attr("id") == "choices-after"){
	      			// 		$("#afterIndex").val("");	    
	      			// 		// removeSelectionMarker($(this).closest(".sb-option"), false);
	      			// 	}				
	      			// }
	      			// console.log("b_idx", $("#beforeIndex").val(), "a_idx", $("#afterIndex").val())
	      		});
      		};

			if (params['assignmentId'])
				$("#assignmentId").val(params['assignmentId']);
			if (params['id'])
				$("#video").val(params['id']);

			console.log(genre, vid, tname, turk_data, s23_data, video);		
      		makeTask(video, tname, genre);

      	/* Version that displays all final S3 images */
      		// for (var cid in s23_data){
      		// 	console.log(cid, turk_data[cid], s23_data[cid]);
      		// 	// var short_vid = s23_data[cid]["video_id"].substr(3, 7);
      		// 	var slug = video_data[vid]["slug"];
      		// 	console.log(vid, slug, s23_data[cid]["label"]);
      		// 	$("#task").append("<h4>[" + cid + "] &quot;" + 
      		// 	s23_data[cid]["label"] + 
      		// "&quot; <span> @ " + parseInt(s23_data[cid]["time"]) + "</span> <small>(" + 
      		// 	s23_data[cid]["video_id"] + 
      		// ")</small>" +
      		// " <span><a href='#' class='play-button' data-index='" + s23_data[cid]["time"] + "'>Play</a></span></h4>");


      		// 		var label = s23_data[cid];
      		// 		console.log(label["before_index"], label["after_index"]);
      		// 		var dom_id = cid;
      		// 		var $el = $("<div/>").attr("id", dom_id).addClass("s3-final-input")
      		// 					.append("<div>" + label["worker_id"] + "</div>")
      		// 					.append("<div class='before-image'>&nbsp;</div>")
      		// 					.append("<div class='after-image'>&nbsp;</div>");

      		// 		if (label["before_index"] != "\"" && label["before_index"] != "")
      		// 			displaySingleChoice(slug, label["before_index"], $el.find('.before-image'), "before");
      		// 		else
      		// 			console.log(label["before_index"], label["before_index"] == "\"");
      		// 		if (label["after_index"] != "\"" && label["after_index"] != "")
      		// 			displaySingleChoice(slug, label["after_index"], $el.find('.after-image'), "after");
      		// 		else
      		// 			console.log(label["after_index"], label["after_index"] == "\"");
      		// 		$el.appendTo("#task");
      			
      		// }

      		$(document).on("click", ".play-button", function(){
      			player.seekTo($(this).attr("data-index") - 10);
      			player.playVideo();
      			return false;
      		});

      	/* Version that displays all Turker input */
      		for (var cid in turk_data){
      			if (!(cid in s23_data)){
      				// console.log(cid, "not for this video");
      				continue;
      			}
      			console.log(cid, turk_data, s23_data[cid]);
      			// var short_vid = s23_data[cid]["video_id"].substr(3, 7);
      			var slug = video_data[vid]["slug"];
      			console.log(vid, slug, s23_data[cid]["label"]);
      			$("#task").append("<h4>[" + cid + "] &quot;" 
      				+ s23_data[cid]["label"] + "&quot; <span> @ " + parseInt(s23_data[cid]["time"]) + "</span> <small>(" 
      				+ s23_data[cid]["video_id"] + ")</small>"
      				+ " <span><a href='#' class='play-button' data-index='" + s23_data[cid]["time"] + "'>Play</a></span></h4>");
      			for (var i in turk_data[cid]){
      				var label = turk_data[cid][i];
      				console.log(label["before_index"], label["after_index"]);
      				var dom_id = cid + "-" + i;
      				var $el = $("<div/>").attr("id", dom_id).addClass("s3-final-input")
      							.append("<div>" + label["worker_id"] + "</div>")
      							.append("<div class='before-image'></div>")
      							.append("<div class='after-image'></div>");

      				if (label["before_index"] != "\"" && label["before_index"] != "")
      					displaySingleChoice(slug, label["before_index"], $el.find('.before-image'), "before");
      				else
      					console.log(label["before_index"], label["before_index"] == "\"");
      				if (label["after_index"] != "\"" && label["after_index"] != "")
      					displaySingleChoice(slug, label["after_index"], $el.find('.after-image'), "after");
      				else
      					console.log(label["after_index"], label["after_index"] == "\"");
      				$el.appendTo("#task");
      			}
      		}

		});
	</script>
</head>
<body>

<div id="header">
	<div id="title">
	<h4><?php echo $video_data[$video_id]["title"]; ?></h4>
	</div>
		<div id="ytplayer">You need Flash player 8+ and JavaScript enabled to view this video.</div>
		<!-- <iframe id="ytplayer" type="text/html" width="640" height="390"
	  src="https://www.youtube.com/v/<?php echo $video['slug']; ?>?enablejsapi=1&version=3"
	  frameborder="0"></iframe> -->
	<div id="errorMsg"></div>	
	<div class='cleaner'>&nbsp;</div>
	<div class="info">
		<h4>For each image, click if it correctly represents before/after effects of the given step.</h4>
	</div>
</div>
<div id="task">	
</div>
<div>
	<button id="submit-button" class="btn btn-primary btn-xxlarge">Submit Results</button>
</div>
</body>

</html>
