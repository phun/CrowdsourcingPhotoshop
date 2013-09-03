<html>

<head>

function actOnEachLine(textarea, func) {
    var lines = textarea.value.replace(/\r\n/g, "\n").split("\n");
    var newLines, newValue, i;

    // Use the map() method of Array where available 
    if (typeof lines.map != "undefined") {
        newLines = lines.map(func);
    } else {
        newLines = [];
        i = lines.length;
        while (i--) {
            newLines[i] = func(lines[i]);
        }
    }
    textarea.value = newLines.join("\r\n");
}

var script = function(indicesString, start, end) {
	var indices = indicesString.split(',');
	var buffer = 0.5;
	var labels = [{"id":"109","video_id":"6","user_id":"1","tm":"4","type":"image","tool":"","comment":"#initial","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_4.png_crop.png?rand=0.9339688242989722","added_at":"2012-08-10 08:55:40"},{"id":"110","video_id":"6","user_id":"1","tm":"27","type":"menu","tool":"Layer > Duplicate Layer","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_27.png","added_at":"2012-08-10 08:56:09"},{"id":"112","video_id":"6","user_id":"1","tm":"35","type":"tool","tool":"Polygonal Lasso","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_35.png","added_at":"2012-08-10 08:56:36"},{"id":"113","video_id":"6","user_id":"1","tm":"140","type":"image","tool":"","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_140.png_crop.png?rand=0.15781982449142307","added_at":"2012-08-10 08:57:56"},{"id":"114","video_id":"6","user_id":"1","tm":"149","type":"menu","tool":"Select > Modify > Feather","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_149.png","added_at":"2012-08-10 08:58:21"},{"id":"115","video_id":"6","user_id":"1","tm":"162","type":"image","tool":"","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_162.png","added_at":"2012-08-10 08:59:28"},{"id":"117","video_id":"6","user_id":"1","tm":"178","type":"menu","tool":"Select > Inverse","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_178.png","added_at":"2012-08-10 09:00:16"},{"id":"118","video_id":"6","user_id":"1","tm":"189","type":"menu","tool":"Filter > Blur > Motion Blur","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_189.png","added_at":"2012-08-10 09:00:34"},{"id":"119","video_id":"6","user_id":"1","tm":"196","type":"image","tool":"","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_196.png_crop.png?rand=0.4704284855005245","added_at":"2012-08-10 09:00:59"},{"id":"120","video_id":"6","user_id":"1","tm":"198","type":"menu","tool":"Select > Deselect","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_198.png","added_at":"2012-08-10 09:01:10"},{"id":"121","video_id":"6","user_id":"1","tm":"201","type":"image","tool":"","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_201.png_crop.png?rand=0.5876000526203446","added_at":"2012-08-10 09:01:20"},{"id":"122","video_id":"6","user_id":"1","tm":"208","type":"tool","tool":"Lasso","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_208.png","added_at":"2012-08-10 09:01:38"},{"id":"123","video_id":"6","user_id":"1","tm":"221","type":"image","tool":"","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_221.png_crop.png?rand=0.9516728345784475","added_at":"2012-08-10 09:02:03"},{"id":"124","video_id":"6","user_id":"1","tm":"225","type":"menu","tool":"Filter > Blur > Radial Blur","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_225.png","added_at":"2012-08-10 09:02:14"},{"id":"125","video_id":"6","user_id":"1","tm":"242","type":"image","tool":"","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_242.png_crop.png?rand=0.3724911452704007","added_at":"2012-08-10 09:02:39"},{"id":"126","video_id":"6","user_id":"1","tm":"244","type":"menu","tool":"Select > Deselect","comment":"","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_244.png","added_at":"2012-08-10 09:02:47"},{"id":"127","video_id":"6","user_id":"1","tm":"265","type":"image","tool":"","comment":"#final","thumbnail":"img\/thumbnails\/aEPnRWO6Fn0_265.png_crop.png?rand=0.7359064382432623","added_at":"2012-08-10 09:03:24"}];
	var toolTimes = [];
	var correct = 0;
	var indicesNotAdded = 0;
	var falseIndices = 0;
	for (i = 0; i < labels.length; i++) {
		var label = labels[i];
		if (label["type"] != "image" && label["tm"] > start && label["tm"] < end) {
			toolTimes.push({"time": label["tm"] - start, "hit": false});
		}
	}
	for (i = 0; i < indices.length; i++) {
		var index = indices[i];
		for (j = 0; j < toolTimes.length; j++) {
			var toolTime = toolTimes[j];
			var before = toolTime["time"] - buffer;
			var after = toolTime["time"] + buffer;
			if (index > before && index < after && toolTime["hit"] == false) {
				correct += 1;
				toolTimes[j]["hit"] = true;
				break;
			}
			if (j == toolTimes.length - 1) {
				falseIndices += 1;
			}
		}
	}
	for (j = 0; j < toolTimes.length; j++) {
		if (toolTimes[j]["hit"] == false) {
			indicesNotAdded += 1;
		}
	}
	console.log(toolTimes);
	wrong = indicesNotAdded + falseIndices;
	return [correct, indicesNotAdded, falseIndices];
}