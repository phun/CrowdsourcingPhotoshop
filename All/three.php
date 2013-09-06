<?php

define("DB_HOST", "50.116.6.114");    // MySQL host name
define("DB_USERNAME", "annotation-user");    // MySQL username
define("DB_PASSWD", "3APGj4vGmdWcQ6fy");    // MySQL password
define("DB_NAME", "HowtoAnnotation");    // MySQL database name. vt.sql uses the default video_learning name. So be careful.

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$id = $_GET["id"];
$result = $mysqli->query("SELECT * FROM stage2_3 WHERE id = '$id'");
while($responses = $result->fetch_assoc()){
	$video_id = $responses['video_id'];
	$raw_labels = $responses['all_labels'];
	$time_index = $responses['det_label_index'];
}
$all_labels = unserialize($raw_labels);

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
if ($video_id[3] == "c")
	$list = $cid_list;
else if ($video_id[3] == "m")
	$list = $mid_list;
else if ($video_id[3] == "p")
	$list = $pid_list;

$key = array_search(substr($video_id, 3, 7), $list);
// echo $key;

$mysqli = new mysqli(DB_HOST, "toolscape-user", "G8hsDe5r4jDtFAYa", "video_learning");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$result = $mysqli->query("SELECT * FROM videos WHERE id='$key'");
if ($result->num_rows != 1)
	echo "query error";
$video = $result->fetch_assoc();

// echo print_r($video);
?>

<html>
<head>
	<title>Amazon Turk Stage 3</title>
	<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.22.custom.css" type="text/css" />
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="js/libs/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/libs/jquery-ui-1.8.22.custom.min.js"></script> 
	<script type="text/javascript" src="js/libs/jwplayer/jwplayer.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery.qtip.min.css" />
	<style>
	.sb-option{
		width: 150px;
		height: 150px;
		float: left;
	}
	.sb{
		width: 120px;
		height: 90px;
	}
	</style>
	<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	<script type="text/javascript">

	// for the given video and time (in second), 
	// get the storyboard image file.
	function getStoryBoard(slug, index){
		var sheet_index = Math.floor(index / 25);
		var url = "http://juhokim.com/annotation/videos/storyboard/" + 
			slug + "/M" + sheet_index + ".jpg";
		return url;
	}

	// from the 5x5 storyboard, find the position of the given item
	function getThumbnailPosition(img, index, image_url, second){
		var thumb_index = index % 25;
		// var $img = $(img[0]);
		// var thumb_width = Math.floor($img.width() / 5);
		// var thumb_height = Math.floor($img.height() / 5);
		var thumb_width = 120; //Math.floor(img.width / 5);
		var thumb_height = 90; //Math.floor(img.height / 5);
		var position = [];
		position[0] = (-1) * thumb_width * (thumb_index % 5) + "px";
		position[1] = (-1) * thumb_height * Math.floor(thumb_index / 5) + "px";
		var $div = $("<div/>").addClass("sb sb-" + second)
				// .attr("src", image_url)
				.css("background", "transparent url(" + image_url + ") " + position[0] + " " + position[1])
				// .css("background-size", thumb_width + "px " + thumb_height + "px ")
				.css("background-size", "600px 450px")
				.css("background-repeat", "no-repeat");
		$(".sb").css("width", thumb_width).css("height", thumb_height);
		console.log(index, img, thumb_width, thumb_height, position[0], position[1]);
		return $div;
	}

	// controlling storyboard display
	function displayChoices(start_int, second){
		var second;
		var slug = "<?php echo $video['slug']; ?>";
		var position = []; // [x, y]
		var image_url = "";
		var index;
		var $choice;
		var $img;
		var dummy_img; // placeholder to compute image width and height
		// var start_int = start);
		// for (second = start_int; second <= start_int + 20; second++){
			index = Math.floor(second / 5); // because storyboards are sampled every 2 second
			console.log(start_int, second, index);
			image_url = getStoryBoard(slug, index);
			dummy_img = new Image();
			dummy_img.onload = function(){
				$img = getThumbnailPosition(dummy_img, index, image_url, second);
				$choice = $("<div/>").addClass("sb-option").append($img);
				// once image successfully loaded,
				if (second <= start_int + 10)
					$("#choices-before").append($choice);
				else
					$("#choices-after").append($choice);
				if (second <= start_int + 20)
					displayChoices(start_int, second + 1);
			}
			dummy_img.src = image_url;
		// }

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

		// starting time retrieved from database. (target time - 10)
		var start = <?php echo json_encode($time_index); ?> - 10;
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
    		swfobject.embedSWF("http://www.youtube.com/v/<?php echo $video['slug']; ?>?enablejsapi=1&playerapiid=ytplayer&version=3",
                       "ytplayer", "100%", "360", "8", null, null, vidParams, atts);

			function onYouTubePlayerReady(playerId) {
			      player = document.getElementById("ytplayer");
			      console.log("onPlayerReady", start, start+20);
			      player.addEventListener("onStateChange", "onPlayerStateChange");
			      player.loadVideoById({'videoId': '<?php echo $video['slug']; ?>', 'startSeconds': start, 'endSeconds': start+20});
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
				    setTimeout( function() { 
						 videoPlayed = true;
						 if ($("#instruction").val() != "") {
						 	$("#taskSub").removeClass('disabled').removeAttr('disabled');
						 }
					}, 20000);
		            done = true;
		        }
		      }

		      function stopVideo() {
		        player.stopVideo();
		      }   

		$(document).ready(function() {

			// var tasks = [];

		    var addToolTick = function() {
	    		var id = "toolTick";          
	    		var duration = 100;
	   			var offset = 49.2;
	    		var html = "<span class='marker btn-inverse toolTick' id='" + id + "' style='left:" + offset + "%;'></span>";  
	    		var i = $(html);

	    		// On hover add "Click to remove"
	    		i.qtip({
					content: {
						text: 'Instruction Here<br/>(10 sec mark)'
					},
					position: {
						my: 'top center', // Use the corner...
						at: 'bottom center' // ...and opposite corner
					},
					show: {
						event: false, // Don't specify a show event...
						ready: true // ... but show the tooltip when ready
					},
					hide: false, // Don't specify a hide event either!
					style: {
						classes: 'qtip-shadow qtip-' + 'dark'
					}
				});
	    		$("#timelineHolder").append(i);      
	    	};

      		var makeTask = function(video, labels, genre) {
      			var infoDes;
      			var ht = '<div class="section task">' +
      					// '<h2> Video </h2>' +
						// '<div class="video">' +
						// 	'<div id="mediaplayer" width="100%" height="400">JW Player goes here</div>' + 
						// '</div>' +
						'<div id="errorMsg"></div>' +						
						'<br/><div id="timerDisplay">0:00</div></span><div id="timelineHolder" style="position:relative">' +
						'<div id="timeline"></div>' + 
						'</div>' + 
						'<div class="info"><div>' +
							'<h3> Which best shows the <span class="canvasText"/> before &quot;<span class="tname"/>&quot;?</h3>' +
							'<div id="tipLabel"><strong>Tip: Pick the most visible and clear image.</strong></div>' +
							// '<div>Select multiple ONLY if there are more than one instructions in this video.</div>' +
							// '<div><strong>GOOD: concrete and actionable.</strong> <span class="good-examples"></span></div>' +
							// '<div><strong>BAD: too generic and not actionable.</strong> <span class="bad-examples"></span></div>' +
							'<div id="choices-before"></div>' +
							'<p style="clear:both"></p>' +
							'<h3> Which best shows the <span class="canvasText"/> after &quot;<span class="tname"/>&quot;?</h3>' +
							'<div id="choices-after"></div>' +
							'<p style="clear:both"></p>' +
							// '<input type="radio" name="labelRadios" value="@">' + '<i>None of these </i>' +
							// '<div id="otherLabel" style="display:none"><h4>Please write an alternative label: </h4>' +
							// 	'<input type="text" id="otherLabelText"></div>' +
						'</div>' +
					'</div>';

				$("#tasks").append(ht);
				switch(genre) {
					case 'c':
						$(".good-examples").html("(e.g., add olive oil, put dough in flour)");
						$(".bad-examples").html("(e.g., make a pizza, it is important not to mix)");
						break;
					case 'm':
						$(".good-examples").html("(e.g., apply eye shadow, use damp brush)");
						$(".bad-examples").html("(e.g., make pretty, lips)");
						break;
					case 'p':
						$(".good-examples").html("(e.g., select Gaussian Blur, duplicate a layer)");
						$(".bad-examples").html("(e.g., make bright, finish up)");

					// case 'c':
					// 	$("#selection-good-examples").html("<strong>GOOD</strong>: cut tomatoes in slices AND add basil (different instructions, pick both)");
					// 	$("#selection-bad-examples").html("<strong>BAD</strong> : thinly slice tomatoes AND cut tomatoes in thin slices (too similar, pick only one)");
					// 	break;
					// case 'm':
					// 	$("#selection-good-examples").html("<strong>GOOD</strong>: apply concealer AND blend with fingertips (different instrutions, pick both)");
					// 	$("#selection-bad-examples").html("<strong>BAD</strong> : apply concealer AND use concealer (too similar, pick only one)");
					// 	break;
					// case 'p':
					// 	$("#selection-good-examples").html("<strong>GOOD</strong>: add new layer AND click color dodge (different instrutions, pick both)");
					// 	$("#selection-bad-examples").html("<strong>BAD</strong> : add new layer AND insert new layer (too similar, pick only one)");
					// 	break;
					default:
						console.log('ERROR: Genre type not found.')
				}

				// randomize array
				var labelIndex, key;
				var labels_obj = {};
				var keys = [];
				// turn into an object to store the randomize order,
				// which helps track which item was selected by the user.
				for (labelIndex in labels){
					labels_obj[labelIndex] = labels[labelIndex];
				}
			    for(key in labels_obj){
			        if(labels_obj.hasOwnProperty(key)){
			            keys.push(key);
			        }
			    }			
				keys.sort(function () { if (Math.random()<.5) return -1; else return 1; });
				// console.log("after:", keys);
				// labels.sort(function () { if (Math.random()<.5) return -1; else return 1; });
				$("#order").val(keys);
				for (labelIndex in keys) {
					var key = keys[labelIndex];
					var label = labels_obj[key];
					var inputString = '<input type="radio" name="labelRadios" value="' + key + '">&quot;' + label.toLowerCase() + '&quot;<br>';
					$("#labelSelection").append(inputString);					
				}
				// for (labelIndex in labels) {
				// 	var label = labels[labelIndex];
				// 	var inputString = '<input type="radio" name="labelRadios" value="' + label.toLowerCase() + '">&quot;' + label.toLowerCase() + '&quot;<br>';
				// 	$("#labelSelection").append(inputString);					
				// }

      			displayChoices(parseInt(start), parseInt(start));

				$("input[type=radio][name=labelRadios]").change(function() {
					var labelVal = $(this).val();
					if (labelVal == '@') {
						$('#otherLabel').show();
					} else {
						$('#otherLabel').hide();
						$('#instruction').val(labelVal);
						if (videoPlayed) {
							$("#taskSub").removeClass('disabled').removeAttr('disabled');
						}
					}
				});

				// $("input[type=checkbox][name=labelRadios]").change(function() {
				// 	var labelVal = $(this).val();
				// 	if (labelVal == '@') {
				// 		if ($("input[type=checkbox][value='@']").is(':checked')){
				// 			$("input[type=checkbox][value!='@']").each(function(){
				// 				if ($(this).is(':checked')){
				// 					$(this).trigger('click');
				// 				}	
				// 			});						
				// 		}
				// 		$('#otherLabel').toggle();
				// 	} else {
				// 		if ($(this).is(':checked')){
				// 			if ($("input[type=checkbox][value='@']").is(':checked')){
				// 				$("input[type=checkbox][value='@']").trigger('click');
				// 			}							
				// 		}
						
				// 		$('#instruction').val(labelVal);
				// 		if (videoPlayed) {
				// 			$("#taskSub").removeClass('disabled').removeAttr('disabled');
				// 		}
				// 	}
				// });

				$('#otherLabelText').keyup(function() {
					$("#instruction").val($(this).val());
					if ($(this).val() != "" && videoPlayed) {
						$("#taskSub").removeClass('disabled').removeAttr('disabled');
					} else {
						$("#taskSub").addClass('disabled').attr('disabled', 'disabled');
					}
				})

				// jwplayer("mediaplayer").setup({
				// 	flashplayer: "js/libs/jwplayer/player.swf",
				// 	controlbar: "bottom",
				// 	file: video,
				// 	// file: "http://www.youtube.com/watch?v=iTXnpGe7a1A",
				// 	start: start,					
				// 	events: {
				// 		onReady: function(event) {
				// 			jwplayer().seek(start);
				// 		},
				// 		onTime: function(event) {
				// 			// console.log(event.position);
				// 			var offset = parseInt(event.position - start);
				// 			if (offset < 10)
				// 				$("#timerDisplay").text("0:0" + offset);
				// 			else
				// 				$("#timerDisplay").text("0:" + offset);
				// 			// if (event.position > 120)
				// 			// 	jwplayer().pause();
				// 			//$("#timeline").slider( "option", "max", jwplayer().getDuration());
				// 			//$("#timeline").slider('value', event.position);
				// 			$("#timeline").slider( "option", "max", 20);
	   //          			$("#timeline").slider('value', event.position - start);
				// 		}, 
				// 		onPlay: function(event) {
				// 			setTimeout( function() { 
				// 				videoPlayed = true;
				// 				if ($("#instruction").val() != "") {
				// 				 	$("#taskSub").removeClass('disabled').removeAttr('disabled');
				// 				}
				// 			}, 20000);
    //       				}
				// 	}
				// });


				$( "#timeline").slider({
					range: "min",
					min: 0,
					max: 279,
					step: 0.1,
					animate: true,
					slide: function(event, ui){
						// jwplayer().seek(ui.value + start);
						if (player)
							player.seekTo(ui.value + start);
					}
	      		});
      		};

			if (params['assignmentId'])
				$("#assignmentId").val(params['assignmentId']);
			if (params['id'])
				$("#video").val(params['id']);

			var vid = <?php echo json_encode($video_id); ?>,
				allLabels = <?php echo json_encode($all_labels); ?>,
				genre = vid.split('_')[1][0],	// c = Cooking, p = Photoshop, m = Makeup
				// video = null;
				video = "<?php echo urldecode(stripslashes($video['url'])); ?>";
			
			//allLabels = allLabels.replace(/\"/g, "").split(',');
			console.log(allLabels);
			switch(genre) {
				case 'c':
					// video = 'http://juhokim.com/annotation/Cooking/videos/' + vid + '.mp4';
					// video = "http://people.csail.mit.edu/juhokim/annotation-videos/<?=$video['task_id'];?>/<?=$video['filename'];?>";
					$(".genreText").each(function() { $(this).text("cooking") });
					$(".canvasText").each(function() { $(this).text("food") });
					$(".good-examples").html("(e.g., add olive oil, put dough in flour)");
					$(".bad-examples").html("(e.g., make a pizza, it is important not to mix)");
					break;
				case 'm':
					// video = 'http://juhokim.com/annotation/Makeup/videos/' + vid + '.mp4';
					// video = "http://people.csail.mit.edu/juhokim/annotation-videos/<?=$video['task_id'];?>/<?=$video['filename'];?>";
					$(".genreText").each(function() { $(this).text("makeup") });
					$(".canvasText").each(function() { $(this).text("person") });
					$(".good-examples").html("(e.g., apply eye shadow, use damp brush)");
					$(".bad-examples").html("(e.g., make pretty, lips)");
					break;
				case 'p':
					// video = 'http://juhokim.com/annotation/Photoshop/videos/' + vid + '.mp4';
					// video = "http://juhokim.com/toolscape/photoshop-video/video/1P6ctvQEikw.flv";
					// video = "http://juhokim.com/toolscape/photoshop-video/video/<?=$video['filename'];?>";
					$(".genreText").each(function() { $(this).text("Photoshop") });
					$(".canvasText").each(function() { $(this).text("canvas") });
					$(".good-examples").html("(e.g., select Gaussian Blur, duplicate a layer)");
					$(".bad-examples").html("(e.g., make bright, finish up)");
					break;
				default:
					console.log('ERROR: Genre type not found.')
			}
			console.log(genre, video);
      		makeTask(video, allLabels, genre);

      		$('#readBtn').click(function() {
      				$('#task').show();
      				addToolTick();
      		});

      		var enableRead = function() {
				$('#readBtn').removeClass('disabled').removeAttr('disabled');
      		}
      		setTimeout(enableRead, 5000);
   		
		});
	</script>
</head>
<body>
<div id="title">
	<h1>Find Before and After Images in How-to Video</h1>
</div>

<div class="section">
<br />
	In this 20-second <span class="genreText"></span> how-to video, the instructor is going to give the instruction: "<span class="tname"></span>" around the <strong>10 second time mark</strong>. 
	<br />We would like the before and after images of this <span class="canvasText"></span>.

	<h3>HIT Information</h3>
	<ol>
		<li><font color="red"><strong> Please have your audio on! </strong> </font></li>
		<li>Watch the video clip and focus on the instruction around the <strong>10 second mark</strong>. </li>
		<li>Choose before and after images that best show the effect of the instruction: "<span class="tname"></span>". </li>
		<li>Click <button class="btn btn-info btn-mini disabled">Before Image</button> when you see the <span class="canvasText"></span> during the first ten seconds. </strong></li>
		<li>Click <button class="btn btn-danger btn-mini disabled">After Image</button> when you see the <span class="canvasText"></span> .</li>


		<li> GOOD: <strong>clear difference, visible <span class="canvasText"></span></strong>. <span class="good-examples"></span></li>
		<li> BAD: <strong>no <span class="canvasText"></span> captured, no visible difference</strong>. <span class="bad-examples"></span></li>
	</ol>

	<button id='readBtn' class="btn btn-large btn-primary disabled" disabled='disabled'> I have read the information </button> 

	<div class='cleaner'>&nbsp;</div>
</div>
<div id="task" style='display: none'>
<form id="submitForm" action="https://www.mturk.com/mturk/externalSubmit" method="POST">
		<div id="ytplayer">You need Flash player 8+ and JavaScript enabled to view this video.</div>
		<!-- <iframe id="ytplayer" type="text/html" width="640" height="390"
	  src="https://www.youtube.com/v/<?php echo $video['slug']; ?>?enablejsapi=1&version=3"
	  frameborder="0"></iframe> -->
<div id="tasks" >

</div>
<div class='cleaner'>&nbsp;</div>
<h2> Finish </h2>
<div class="section">
	<!-- <form action="http://www.mturk.com/mturk/externalSubmit"> -->
	<input type="hidden" name="assignmentId" id="assignmentId" value="">
	<input type="text" name="beforeIndice" id="beforeIndice">
	<input type="text" name="allBeforeIndices" id="allBeforeIndices">
	<input type="text" name="afterIndice" id="afterIndice">
	<input type="text" name="allAfterIndices" id="allAfterIndices">
	<input type="text" name="video" id="video">
	<button id='taskSub' type="submit" class="btn btn-large btn-primary disabled" style="float:right" disabled="disabled">Submit</button>
	When you are done, hit the submit button.
</div>
</form>
</div>
</body>

</html>
