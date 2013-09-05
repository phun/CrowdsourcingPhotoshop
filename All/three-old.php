<?php

define("FFMPEG_PATH", "/opt/local/bin/ffmpeg");    // path of ffmpeg

define("DB_HOST", "50.116.6.114");    // MySQL host name
define("DB_USERNAME", "annotation-user");    // MySQL username
define("DB_PASSWD", "3APGj4vGmdWcQ6fy");    // MySQL password
define("DB_NAME", "HowtoAnnotation");    // MySQL database name. vt.sql uses the default video_learning name. So be careful.

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$video_id = $_GET["id"];
$result = $mysqli->query("SELECT * FROM stage2_3 WHERE video_id = '$video_id'");
while($responses = $result->fetch_assoc()){
		$det_label = $responses['det_label'];
}

?>

<html>
<head>
	<title>Amazon Turk Step 3</title>
	<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.22.custom.css" type="text/css" />
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="js/libs/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/libs/jquery-ui-1.8.22.custom.min.js"></script> 
	<script type="text/javascript" src="js/libs/jwplayer/jwplayer.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery.qtip.min.css" />
	<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
	<script type="text/javascript">

		var pauseOnSeek = true;

		// Get Parameters
		var prmstr = window.location.search.substr(1);
		var prmarr = prmstr.split ("&");
		var params = {};
		for ( var i = 0; i < prmarr.length; i++) {
			var tmparr = prmarr[i].split("=");
			params[tmparr[0]] = tmparr[1];
		}

		$(document).ready(function() {

			var addToolTick = function() {
	    		var id = "toolTick";          
	    		var duration = 100;
	   			var offset = 49.2;
	    		var html = "<span class='marker btn-inverse toolTick' id='" + id + "' style='left:" + offset + "%;'></span>";  
	    		var i = $(html);

	    		// On hover add "Click to remove"
	    		i.qtip({
					content: {
						text: 'Instruction Given Here'
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

			var addBeforeTick = function(index) {
	    		var id = "beforeTick";  
	    		$("#" + id).remove();         
	    		var duration = jwplayer().getDuration();
	   			var offset = index / duration * 100;
	    		var html = "<span class='marker btn-info beforeTick' id='" + id + "' style='left:" + offset + "%;'></span>";  
	    		var i = $(html);

	    		// On hover add "Click to remove"
	    		i.hover(function() {
	    			var html = "<span class='removeHelper' style='top:" + this.offsetHeight + "; left: " + this.offsetLeft + "'> Double-click to Remove </span>";
	    			$("#timelineHolder").append(html);
	    		}, function() {
	    			$("#timelineHolder .removeHelper").each(function() { $(this).remove(); });
	    		});

	    		i.dblclick(function() {
	    			$(this).hide();
	    			allBeforeIndices.pop();
	    			beforeIndice = null;
	    		})
	    		$("#timelineHolder").append(i);      
	    	};

	    	var addAfterTick = function(index) {
	    		var id = "afterTick";  
	    		$("#" + id).remove();         
	    		var duration = jwplayer().getDuration();
	   			var offset = index / duration * 100;
	    		var html = "<span class='marker btn-danger afterTick' id='" + id + "' style='left:" + offset + "%;'></span>";  
	    		var i = $(html);

	    		// On hover add "Click to remove"
	    		i.hover(function() {
	    			var html = "<span class='removeHelper' style='top:" + this.offsetHeight + "; left: " + this.offsetLeft + "'> Double-click to Remove </span>";
	    			$("#timelineHolder").append(html);
	    		}, function() {
	    			$("#timelineHolder .removeHelper").each(function() { $(this).remove(); });
	    		});

	    		i.dblclick(function() {
	    			$(this).hide();
	    			allAfterIndices.pop();
	    			afterIndice = null;
	    		})
	    		$("#timelineHolder").append(i);      
	    	};

			var allBeforeIndices = [];
			var beforeIndice;
			var allAfterIndices = [];
			var afterIndice;

			var vid = <?php echo json_encode($video_id); ?>,
				tname = <?php echo json_encode($det_label); ?>,
				genre = vid.split('_')[1][0],	// c = Cooking, p = Photoshop, m = Makeup
				video = null;

			if (params['assignmentId'])
			{
				$("#assignmentId").val(params['assignmentId']);
			}

			switch(genre) {
				case 'c':
					video = 'http://juhokim.com/annotation/Cooking/videos/' + vid + '.mp4';
					$(".genreText").each(function() { $(this).text("cooking") });
					$(".canvasText").each(function() { $(this).text("food") });
					break;
				case 'm':
					video = 'http://juhokim.com/annotation/Makeup/videos/' + vid + '.mp4';
					$(".genreText").each(function() { $(this).text("makeup") });
					$(".canvasText").each(function() { $(this).text("person") });
					break;
				case 'p':
					video = 'http://juhokim.com/annotation/Photoshop/videos/' + vid + '.mp4';
					$(".genreText").each(function() { $(this).text("Photoshop") });
					$(".canvasText").each(function() { $(this).text("canvas") });
					break;
				default:
					console.log('ERROR: Genre type not found.')
			}

			$(".tname").text(tname);

			$("#videoURL").val(video);

			var toggleBtns = function(pos, dur) {
				if (pos < dur / 2) {
					$('#beforeButton').removeClass('disabled').removeAttr('disabled');
					$('#afterButton').addClass('disabled').attr('disabled', 'disabled');
				} else {
					$('#afterButton').removeClass('disabled').removeAttr('disabled');
					$('#beforeButton').addClass('disabled').attr('disabled', 'disabled');
				}
			}


			jwplayer("mediaplayer").setup({
			  flashplayer: "js/libs/jwplayer/player.swf",
			  controlbar: "bottom",
			  file: video,
			  events: {
					onTime: function(event) {
						toggleBtns(event.position, jwplayer().getDuration());
						$("#timeline").slider( "option", "max", jwplayer().getDuration());
            			$("#timeline").slider('value', event.position);
          			},
          			onPlay: function(event) {
          				var s = $('#submitBtn');
          				if (s.attr('disabled') == 'disabled') {
          					setTimeout( function() { s.removeAttr('disabled').removeClass('disabled') } , 20000);
          				}
          			}
			  }
			});

			$("#beforeButton").click(function() {
				var index = jwplayer().getPosition();
				allBeforeIndices.push(index);
				beforeIndice = index;
				addBeforeTick(index);
				$("#allBeforeIndices").val(allBeforeIndices);
				$("#beforeIndice").val(index);

			});

			$("#afterButton").click(function() {
				var index = jwplayer().getPosition();
				allAfterIndices.push(index);
				afterIndice = index;
				addAfterTick(index);
				$("#allAfterIndices").val(allAfterIndices);
				$("#afterIndice").val(index);

			});			

			$(".submitHIT").click(function() {
				alert(indices);
			});

	    	$( "#timeline" ).slider({
				range: "min",
				min: 0,
				max: 279,
				step: 0.1,
				animate: true,
				slide: function(event, ui){
					jwplayer().seek(ui.value);
				}
      		});

      		//$('#readBtn').addClass('disabled').attr('disabled', 'disabled');
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
	<h1> Find Before and After Images in How-to Video </h1>
</div>

<div class="section">
	<br />
	In this <span class="genreText"></span> video tutorial, the instructor is going to give the instruction: "<span class="tname"></span>". 
	<br />We would like the before and after images of this <span class="canvasText"></span>.

<!-- 	</br/><h4>For example:</h4> 
	<img src="gaussian_blur_before.png" style="width: 150px;"> <img src="gaussian_blur_after.png" style="width: 150px;">
 -->
	<h3>HIT Information</h3>
	<ol>
		<li> <font color="red"><strong> Please have your audio on! </strong> </font></li>
		<li> Watch the <span class="genreText"></span> video tutorial. </li>
		<li><strong>Before Image:</strong> Click the <button class="btn btn-info btn-mini disabled">Before Image</button> when you see the <span class="canvasText"></span> during the <strong> first ten seconds before the instruction is given. </strong></li>
		<li> Wait until the instructor is done with the instruction: "<span class="tname"></span>". </li>
		<li><strong>After Image:</strong> Click the <button class="btn btn-danger btn-mini disabled">After Image</button> when you see the <span class="canvasText"></span> after the instructor is finished with the instruction.</li>

	</ol>
	<ul><li><i>Tip</i>: Click on the buttons multiple times to override the current selection.</li></ul>

	<button id='readBtn' class="btn btn-large btn-primary disabled" disabled='disabled'> I have read the information. </button> 

	<div class='cleaner'>&nbsp;</div>
</div>
<div id='task' style='display: none'>
	<h2> Task </h2>
	<div class="section" style="text-align: center;">
		<div id="mediaplayer" width="100%" height="400">JW Player goes here</div>
		<br/><button class="btn btn-info" id="beforeButton">Before Image</button><button class="btn btn-danger" id="afterButton">After Image</button>
		<br/><div id="timelineHolder">
			<div id="timeline"></div>
			</div>
	</div>
	<div class='cleaner'>&nbsp;</div>
	<h2> Finish </h2>
	<div class="section">
		<!-- <form action="http://www.mturk.com/mturk/externalSubmit"> -->
		<form id="submitForm" action="https://www.mturk.com/mturk/externalSubmit" method="POST">
			<input type="hidden" name="assignmentId" id="assignmentId" value="">
			<input type="text" name="beforeIndice" id="beforeIndice">
			<input type="text" name="allBeforeIndices" id="allBeforeIndices">
			<input type="text" name="afterIndice" id="afterIndice">
			<input type="text" name="allAfterIndices" id="allAfterIndices">
			<input type="text" name="video" id="videoURL">
			<button id="submitBtn" type="submit" class="btn btn-large btn-primary disabled" style="float:right" disabled='disabled'>Submit</button>
			When you are done hit the submit button.
		</form>
	</div>
</div>
</body>

</html>