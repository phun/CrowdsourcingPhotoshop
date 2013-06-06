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
		$all_labels = $responses['all_labels'];
}

?>

<html>
<head>
	<title>Amazon Turk Stage 2</title>
	<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.22.custom.css" type="text/css" />
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="js/libs/chosen.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="js/libs/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/libs/jquery-ui-1.8.22.custom.min.js"></script> 
	<script type="text/javascript" src="js/libs/jwplayer/jwplayer.js"></script>
	<script type="text/javascript" src="js/libs/chosen.jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery.qtip.min.css" />
	<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
	<script type="text/javascript">

		var videoPlayed = false;

		// Get Parameters
		var prmstr = window.location.search.substr(1);
		var prmarr = prmstr.split ("&");
		var params = {};
		for ( var i = 0; i < prmarr.length; i++) {
			var tmparr = prmarr[i].split("=");
			params[tmparr[0]] = tmparr[1];
		}

		$(document).ready(function() {

			var tasks = [];

		    var addToolTick = function(num) {
	    		var id = "toolTick";          
	    		var duration = 100;
	   			var offset = 49.2;
	    		var html = "<span class='marker btn-inverse toolTick' id='" + id + "' style='left:" + offset + "%;'></span>";  
	    		var i = $(html);

	    		// On hover add "Click to remove"
	    		i.qtip({
					content: {
						text: 'Instruction Here'
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
	    		$("#timelineHolder-" + num).append(i);      
	    	};

      		var makeTask = function(video, labels, genre) {

      			$("#video").val(video);
      			var infoDes;
      			var num = tasks.length + 1;
      			var start = 100; // TODO: from database. (target time - 10)

      			var ht = '<div class="section task">' +
      					// '<h2> Video </h2>' +
						'<div class="video">' +
							'<div id="mediaplayer-' + num + '" width="100%" height="400">JW Player goes here</div>' + 
						'</div>' +
						'<br/><div id="timerDisplay">0:00</div></span><div id="timelineHolder-' + num + '" style="position:relative">' +
						'<div id="timeline-' + num + '"></div>' + 
						'</div>' + 
						'<div class="info"><div>' +
							'<h3> Which best describes the instruction around the 10 second time mark? </h3>' +
							'<div>Select multiple ONLY if there are more than one instructions in this video.</div>' +
							'<div id="selection-good-examples"></div>' +
							'<div id="selection-bad-examples"></div>' +
							'<div id="labelSelection"></div>' +
							'<input type="checkbox" name="labelRadios" value="@">' + '<i>None of these </i>' +

							'<div id="otherLabel" style="display:none"><h4>Please write an alternative label: </h4>' +
								'<input type="text" id="otherLabelText"></div>' +
						'</div>' +
					'</div>';

				$("#tasks").append(ht);
				switch(genre) {
					case 'c':
						$("#selection-good-examples").html("<strong>GOOD</strong>: cut tomatoes in slices AND add basil (different instructions, pick both)");
						$("#selection-bad-examples").html("<strong>BAD</strong> : thinly slice tomatoes AND cut tomatoes in thin slices (too similar, pick only one)");
						break;
					case 'm':
						$("#selection-good-examples").html("<strong>GOOD</strong>: apply concealer AND blend with fingertips (different instrutions, pick both)");
						$("#selection-bad-examples").html("<strong>BAD</strong> : apply concealer AND use concealer (too similar, pick only one)");
						break;
					case 'p':
						$("#selection-good-examples").html("<strong>GOOD</strong>: add new layer AND click color dodge (different instrutions, pick both)");
						$("#selection-bad-examples").html("<strong>BAD</strong> : add new layer AND insert new layer (too similar, pick only one)");
						break;
					default:
						console.log('ERROR: Genre type not found.')
				}

				// randomize array
				labels.sort(function () { if (Math.random()<.5) return -1; else return 1; });
				for (labelIndex in labels) {
					var label = labels[labelIndex];
					var inputString = '<input type="checkbox" name="labelRadios" value="' + label.toLowerCase() + '">' + label.toLowerCase() + '<br>';
					$("#labelSelection").append(inputString);					
				}

				$("input[type=checkbox][name=labelRadios]").change(function() {
					var labelVal = $(this).val();
					if (labelVal == '@') {
						if ($("input[type=checkbox][value='@']").is(':checked')){
							$("input[type=checkbox][value!='@']").each(function(){
								if ($(this).is(':checked')){
									$(this).trigger('click');
								}	
							});						
						}
						$('#otherLabel').toggle();
					} else {
						if ($(this).is(':checked')){
							if ($("input[type=checkbox][value='@']").is(':checked')){
								$("input[type=checkbox][value='@']").trigger('click');
							}							
						}
						
						$('#instruction').val(labelVal);
						if (videoPlayed) {
							$("#taskSub").removeClass('disabled').removeAttr('disabled');
						}
					}
				});

				$('#otherLabelText').keyup(function() {
					$("#instruction").val($(this).val());
					if ($(this).val() != "" && videoPlayed) {
						$("#taskSub").removeClass('disabled').removeAttr('disabled');
					} else {
						$("#taskSub").addClass('disabled').attr('disabled', 'disabled');
					}
				})

				jwplayer("mediaplayer-" + num).setup({
					flashplayer: "js/libs/jwplayer/player.swf",
					controlbar: "bottom",
					//file: video,
					file: "http://www.youtube.com/watch?v=iTXnpGe7a1A",
					start: start,					
					events: {
						onTime: function(event) {
							//console.log(event.position);
							var offset = parseInt(event.position - start);
							if (offset < 10)
								$("#timerDisplay").text("0:0" + offset);
							else
								$("#timerDisplay").text("0:" + offset);
							if (event.position > 120)
								jwplayer().pause();
							//$("#timeline-" + num).slider( "option", "max", jwplayer().getDuration());
							//$("#timeline-" + num).slider('value', event.position);
							$("#timeline-" + num).slider( "option", "max", 20);
	            			$("#timeline-" + num).slider('value', event.position - start);
						}, 
						onPlay: function(event) {
							setTimeout( function() { 
								 videoPlayed = true;
								 if ($("#instruction").val() != "") {
								 	$("#taskSub").removeClass('disabled').removeAttr('disabled');
								 }
							}, 20000);
          				}
					}
				});

				tasks.push({
					"id": num,
					"video": video,
				});

				$( "#timeline-" + num ).slider({
					range: "min",
					min: 0,
					max: 279,
					step: 0.1,
					animate: true,
					slide: function(event, ui){
						jwplayer().seek(ui.value + start);
					}
	      		});
      		};

			if (params['assignmentId'])
			{
				$("#assignmentId").val(params['assignmentId']);
			}

			var vid = <?php echo json_encode($video_id); ?>,
				allLabels = <?php echo json_encode($all_labels); ?>,
				genre = vid.split('_')[1][0],	// c = Cooking, p = Photoshop, m = Makeup
				video = null;
			
			allLabels = ["a", "b", "c"];
			//allLabels = allLabels.replace(/\"/g, "").split(',');
			console.log(allLabels);

			switch(genre) {
				case 'c':
					video = 'http://juhokim.com/annotation/Cooking/videos/' + vid + '.mp4';
					$(".genreText").each(function() { $(this).text("cooking") });
					$("#good-examples").text("(e.g., add olive oil, put dough in flour)");
					$("#bad-examples").text("(e.g., make a pizza, it is important not to mix)");
					break;
				case 'm':
					video = 'http://juhokim.com/annotation/Makeup/videos/' + vid + '.mp4';
					$(".genreText").each(function() { $(this).text("makeup") });
					$("#good-examples").text("(e.g., apply eye shadow, use damp brush)");
					$("#bad-examples").text("(e.g., make pretty, lips)");
					break;
				case 'p':
					video = 'http://juhokim.com/annotation/Photoshop/videos/' + vid + '.mp4';
					$(".genreText").each(function() { $(this).text("Photoshop") });
					$("#good-examples").text("(e.g., select Gaussian Blur, duplicate a layer)");
					$("#bad-examples").text("(e.g., make bright, finish up)");
					break;
				default:
					console.log('ERROR: Genre type not found.')
			}

      		makeTask(video, allLabels, genre);


      		$('#readBtn').click(function() {
      				$('#task').show();
      				addToolTick(1);
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
	<h1>Choose Best Instruction in How-to Video </h1>
</div>

<div class="section">
<br />
	In this 20-second video clip, the instructor is going to give a <span class="genreText"></span> instruction around the <strong>10 second time mark</strong>.  
	<br/>We would like to know what it is.
	<h3>HIT Information</h3>
	<ol>
		<li> <font color="red"><strong> Please have your audio on! </strong> </font></li>
		<li> Watch the video clip and focus on the instruction around the <strong>10 second mark</strong>. </li>
		<li> From the list of options, choose what you think best describes the instruction. </li>
		<li> Good instructions: <strong>concrete and actionable</strong>. <span id="good-examples"></span></li>
		<li> Bad instructions: <strong>generic and not actionable</strong>. <span id="bad-examples"></span></li>
		<!-- <li> Describe in a short sentence, what the <span class="genreText"></span> instruction was. </li> -->
		<!-- <li> <strong> IGNORE instructions that are <u>NOT</u> happening at the 10 second mark. </strong> </li> -->
	</ol>

	<button id='readBtn' class="btn btn-large btn-primary disabled" disabled='disabled'> I have read the information. </button> 

	<div class='cleaner'>&nbsp;</div>
</div>
<div id="task" style='display: none'>
<form id="submitForm" action="https://www.mturk.com/mturk/externalSubmit" method="POST">
<div id="tasks" ></div>
<div class='cleaner'>&nbsp;</div>
<h2> Finish </h2>
<div class="section">
	<!-- <form action="http://www.mturk.com/mturk/externalSubmit"> -->
        <input type="hidden" name="assignmentId" id="assignmentId" value="">
		<input type="text" name="video" id="video">
		<input type="text" name="instruction" id="instruction">
	<button id='taskSub' type="submit" class="btn btn-large btn-primary disabled" style="float:right" disabled="disabled">Submit</button>
	When you are done, hit the submit button.
</div>
</form>
</div>
</body>

</html>
