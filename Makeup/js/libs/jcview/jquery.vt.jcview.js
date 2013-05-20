/******************************************
 * jQuery Plugin: Compact View for Video Clips
 * *
 * @author          Juho Kim
 * @copyright       
 * @license         
 * @link            
 * @docs            
 * @version         
 *
 ******************************************/

;(function($) { 
  //"use strict";	
  // The jQuery.vt namespace will automatically be created if it doesn't exist
  $.widget('vt.jcview', {
	// These options will be used as defaults
	options: {
    	labels : new Array(),
    	duration: 100
	},
	// The _create method is where you set up the widget
	_create: function() {
		console.log("_create", this.options.labels.length, "labels");
		if (this.options.labels.length === 0) return;
		var options = this.options;
		var tabs1_html = tabs2_html = tabs3_html = "";
      	var tabs4_html = "<ul class='mycarousel jcarousel-skin-tango'>";
      	var control_html = "<div class='jcarousel-control'>";

		$.each(this.options.labels, function(index, label){
			//console.log(index, label.tm);
	        var tm_formatted = getTimeDisplay(parseInt(label.tm));
	        // Adding tabbed views
	        var url = "browse.php?vid=" + label.video_id + "&uid=" + label.user_id + "&tm=" + label.tm;

	        var thumb_html = "<a class='lightbox' href='" + label.thumbnail + "'><img src='" + label.thumbnail + "?rand=" + Math.random() + "'></a>";
	        var play_html = "<a class='seek btn' id='time" + label.tm + "'href='#'><i class='icon-play'></i></a>";

	        if (label.comment == "#initial")
	          tabs1_html = tabs1_html + "<div class='browse-initial'>Initial Image&nbsp;" + play_html + "<br>" + thumb_html + "</div>";

	        if (label.comment == "#final")
	          tabs1_html = tabs1_html + "<div class='browse-final'>Final Image&nbsp;" + play_html + "<br>" + thumb_html + "</div>";

	        if (label.type == "image") {
	          tabs2_html = tabs2_html + "<div class='browse-wip'>(" + tm_formatted + ") &nbsp;" + play_html + "<br>" + thumb_html + "</div>";
	          tabs3_html = tabs3_html + "<div class='browse-steps-image'>(" + tm_formatted + ") &nbsp;" + play_html + "<br>" + thumb_html + "</div>";
	          tabs4_html = tabs4_html + "<li>" + thumb_html + "</li>";
	          control_html = control_html + "<a href='#' class='marker-image'>" + tm_formatted + "</a> ";
	        } else {
	          tabs3_html = tabs3_html + "<div class='browse-steps-tool'>(" + tm_formatted + ") &nbsp;" + play_html + "<br><b>" + label.tool + "</b></div>";
	          tabs4_html = tabs4_html + "<li><div class='tool-display'>" + formatTool(label.tool, "toolname-emphasize") + "</div></li>";
	          control_html = control_html + "<a href='#' class='marker-tool'>" + tm_formatted + "</a> ";
	        }
        
      	});
	    tabs4_html = tabs4_html + "</ul>";
	    $(this.element).html(tabs4_html);
	},
	_init: function() {
		console.log("_init");
	},
	// Use the _setOption method to respond to changes to options
	_setOption: function(key, value){
		console.log(key, value);
		$.Widget.prototype._setOption.apply(this, arguments);
	},
	// Use the destroy method to reverse everything your plugin has applied
	destroy: function(){
		// Remove any new elements that you created
		// Unbind any events that may still exist
		// Remove any classes, including CSS framework classes, that you applied

		// After you're done, you still need to invoke the "base" destroy method
  		// Does nice things like unbind all namespaced events on the original element
		$.Widget.prototype.destroy.call(this);
	}

  });

})(jQuery);



/*
;(function($) { 
  "use strict";	
  // The jQuery.vt namespace will automatically be created if it doesn't exist
  $.widget("vt.jcview", {
	// These options will be used as defaults
	options: {
    	option1 : "",
    	option2: ""
	},
	// The _create method is where you set up the widget
	_create: function() {
		console.log("_create");
	},
	_init: function() {
		console.log("_init");
	},
	// Use the _setOption method to respond to changes to options
	_setOption: function(key, value){

		$.Widget.prototype._setOption.apply(this, arguments);
	},
	// Use the destroy method to reverse everything your plugin has applied
	destroy: function(){
		$.Widget.prototype.destroy.call(this);
	}

	});
})(jQuery);
*/