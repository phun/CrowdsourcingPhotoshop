

    function _getSecondsDisplay(seconds){
      return (seconds < 10) ? "0" + seconds : seconds; 
    }

    function getTimeDisplay(seconds){
      var text = "";  
      text = Math.floor(seconds / 60) + ":" + _getSecondsDisplay(seconds % 60);
      return text;
    }

/*
    - video_id
    - user_id
    - interface_id
    - task_id
    - action (hover, click, ...)
    - object type: label, video, user, ...
    - object id: (object type)_id to refer to the corresponding database
*/
function formatListLog(interface_id, task_id, user_id, action, target, obj){
  var json = {
    "iid": interface_id,
    "tid": task_id,
    "uid": user_id,
    "action": action,
    "target": target,
    "obj": obj
  }
  //console.log(JSON.stringify(json));
  return JSON.stringify(json);
}

function formatBrowseLog(video_id, interface_id, task_id, user_id, action, target, obj){
  var json = {
    "vid": video_id,
    "iid": interface_id,
    "tid": task_id,
    "uid": user_id,
    "action": action,
    "target": target,
    "obj": obj
  }
  //console.log(JSON.stringify(json));
  return JSON.stringify(json);
}

// input: "level1 > level2 > level3"
function formatTool(str, className) {
  var split = str.split(" > ");
  split[split.length-1] = "<span class='" + className + "'>"+split[split.length-1]+"</span>";
  //console.log(split);
  
  return split.join(" > ");
}

function formatDate(str) {
    // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
  
  var date = new Date(str.substr(0,4), str.substr(4,2)-1, str.substr(-2)); // months are 0-based
  return _prettyDate(date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate());
}

function _prettyDate(date_str){
  var time_formats = [
  [60, 'just now', 1], // 60
  [120, '1 minute ago', '1 minute from now'], // 60*2
  [3600, 'minutes', 60], // 60*60, 60
  [7200, '1 hour ago', '1 hour from now'], // 60*60*2
  [86400, 'hours', 3600], // 60*60*24, 60*60
  [172800, 'yesterday', 'tomorrow'], // 60*60*24*2
  [604800, 'days', 86400], // 60*60*24*7, 60*60*24
  [1209600, 'last week', 'next week'], // 60*60*24*7*4*2
  [2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
  [4838400, 'last month', 'next month'], // 60*60*24*7*4*2
  [29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
  [58060800, 'last year', 'next year'], // 60*60*24*7*4*12*2
  [2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
  [5806080000, 'last century', 'next century'], // 60*60*24*7*4*12*100*2
  [58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
  ];
  var time = ('' + date_str).replace(/-/g,"/").replace(/[TZ]/g," ").replace(/^\s\s*/, '').replace(/\s\s*$/, '');
  if(time.substr(time.length-4,1)==".") time =time.substr(0,time.length-4);
  var seconds = (new Date - new Date(time)) / 1000;
  var token = 'ago', list_choice = 1;
  if (seconds < 0) {
    seconds = Math.abs(seconds);
    token = 'from now';
    list_choice = 2;
  }
  var i = 0, format;
  while (format = time_formats[i++])
    if (seconds < format[0]) {
      if (typeof format[2] == 'string')
        return format[list_choice];
      else
        return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
    }
  return time;
};

var likert = {
  vsVideoQuestions: [
  "This video is relevant to my task.", 
  "This video contains skills I'd like to learn.", 
  "This video is suited to my skill level."
  //"I want to learn skills in this video",
  //"I have the skills to understand and learn from this tutorial"
  ],
 
  vsVideoAfterQuestions: [
  "This video was relevant to my task.", 
  "This video contained skills I'd like to learn.", 
  "This video was suited to my skill level."
  //"I want to learn skills in this video",
  //"I have the skills to understand and learn from this tutorial"
  ],

  vsPostQuestions: [
  "It was easy to use.",
	"It was easy to understand.",
	"It was enjoyable.",
	"It was informative.",
	"It represented the video well.",
	"It aided in deciding whether the video is relevant.",
	"It aided in deciding whether the video contains new skills to learn.",
	"It aided in deciding whether the video is advanced.",
	"It replaced watching the full video.",
	"It would be useful in browsing video tutorials."
  ],

  dsConfidenceQuestions: [
  "with solving graphic design problems?",
	"at understanding graphic design problems?",
	"with applying design skills in practice?",
	"with incorporating skills from video tutorials in your design?"
  ],

  dsPostOverallQuestions: [
  	"The task was difficult to perform.",
  	"The quality of my final image is high."
  ],

  dsPostInterfaceQuestions: [
    "It was easy to use.",
    "It was easy to understand.",
    "It was enjoyable.",
    "It was informative."
  ],

  dsYoutubePostQuestions: [
  	"1. Thumbnail",
  	"2. Title",
  	"3. Description",
//  	"View Count",
  	"4. Video Length",
  	"5. Uploader",
  	"6. Upload date",
    "7. Thumbnail preview in the video player"
  ],

  dsOursPostQuestions: [
    "1. Title",
    "2. Description",
    "3. The number of steps",
//    "View Count",
    "4. Video Length",
    "5. Uploader",
    "6. Upload date",

    "7. Top tools",
    "8. Filtering on tools",
    "9. Seeing the workflow in sequence",   
    "10. Work-in-progress images",
    "11. List of tools used",
    "12. Before image",
    "13. After image",

    "14. Thumbnail preview in the video player",
    "15. Timeline visualization",
    "16. Clicking to jump to a specific point in a video",
    "17. Visualization of actual start and end"

  ],

  _getLikertScaleHTML: function (scale){
    /*             <tr>
              <td class="ss-scalenumbers"></td>
              <td class="ss-scalenumbers"><label class="ss-scalenumber">1</label></td>
              <td class="ss-scalenumbers"><label class="ss-scalenumber">2</label></td>
              <td class="ss-scalenumbers"><label class="ss-scalenumber">3</label></td>
              <td class="ss-scalenumbers"><label class="ss-scalenumber">4</label></td>
              <td class="ss-scalenumbers"><label class="ss-scalenumber">5</label></td>
              <td class="ss-scalenumbers"></td>
            </tr>
    */
    var i=1;
    var html = "<tr><td class='ss-scalenumbers'></td>";
    for (i=1; i<=scale; i++){
      html = html + "<td class='ss-scalenumbers'><label class='ss-scalenumber'>" + i + "</label></td>";
    }
    html = html + "<td class='ss-scalenumbers'></td></tr>"
    return html;
  },
  
  _getLikertRadioHTML: function (scale, v_index, q_index){
/* 
            <tr class="ss-scalerow">
              <td class="ss-scalerow ss-leftlabel">not really</td>
              <td class="ss-scalerow"><input type="radio" value="1" id="1" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="2" id="2" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="3" id="3" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="4" id="4" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="5" id="5" name="entry.7.group"></td>
              <td class="ss-scalerow ss-rightlabel">very much</td>
            </tr>
*/    
    var i=1;
    var html = "<tr class='ss-scalerow'><td class='ss-scalerow ss-leftlabel'>strongly disagree</td>";
    for (i=1; i<=scale; i++){
      html = html + "<td class='ss-scalerow'><input type='radio' value='" + i + "' id='" + i + "' name='entry." + v_index + "." + q_index + ".group'></td>";
    }
    html = html + "<td class='ss-scalerow ss-rightlabel'>strongly agree</td></tr>"
    return html;
  },

  _getConfidenceLikertRadioHTML: function (scale, v_index, q_index){
/* 
            <tr class="ss-scalerow">
              <td class="ss-scalerow ss-leftlabel">not really</td>
              <td class="ss-scalerow"><input type="radio" value="1" id="1" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="2" id="2" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="3" id="3" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="4" id="4" name="entry.7.group"></td>
              <td class="ss-scalerow"><input type="radio" value="5" id="5" name="entry.7.group"></td>
              <td class="ss-scalerow ss-rightlabel">very much</td>
            </tr>
*/    
    var i=1;
    var html = "<tr class='ss-scalerow'><td class='ss-scalerow ss-leftlabel'>not confident at all</td>";
    for (i=1; i<=scale; i++){
      html = html + "<td class='ss-scalerow'><input type='radio' value='" + i + "' id='" + i + "' name='entry." + v_index + "." + q_index + ".group'></td>";
    }
    html = html + "<td class='ss-scalerow ss-rightlabel'>very confident</td></tr>"
    return html;
  },

  getVSVideoHTML: function (v_index){
    //return _getGenericHTML(v_index, this.vsVideoQuestions);
    // span3 is here!
    var i=0;
    var html = '';
    for (i=0; i<this.vsVideoQuestions.length; i++) {
    html = html + '<div class="ss-formwidget-container span3">'
      + '<div class="ss-form-entry" style="text-align: left;" dir="ltr">'
        + '<span class="ss-q-title">' + this.vsVideoQuestions[i] + '</span>' // <span class="ss-required-asterisk">*</span>
        //+ '<span class="ss-q-help">Gaussian blur, motion blur, radial blur, etc.</span>'
        + '<table cellspacing="0" cellpadding="5" border="0" class="ss-q-table">'
          + '<tbody>'
          + this._getLikertScaleHTML(7)
          + this._getLikertRadioHTML(7, v_index, i)
          + '</tbody></table></div></div>';
    }
    return html;
    
  },

  getVSAfterVideoHTML: function (v_index){
    //return _getGenericHTML(v_index, this.vsVideoQuestions);
    // span3 is here!
    var i=0;
    var html = '';
    for (i=0; i<this.vsVideoQuestions.length; i++) {
    html = html + '<div class="ss-formwidget-container span3">'
      + '<div class="ss-form-entry" style="text-align: left;" dir="ltr">'
        + '<span class="ss-q-title">' + this.vsVideoAfterQuestions[i] + '</span>' // <span class="ss-required-asterisk">*</span>
        //+ '<span class="ss-q-help">Gaussian blur, motion blur, radial blur, etc.</span>'
        + '<table cellspacing="0" cellpadding="5" border="0" class="ss-q-table">'
          + '<tbody>'
          + this._getLikertScaleHTML(7)
          + this._getLikertRadioHTML(7, v_index, i)
          + '</tbody></table></div></div>';
    }
    return html;
    
  },

  getVSPostHTML: function (v_index){
    return this._getGenericHTML(v_index, this.vsPostQuestions);
    /*    
    var i=0;
    var html = '';
    for (i=0; i<this.vsPostQuestions.length; i++) {
    html = html + '<div class="ss-formwidget-container span12">'
      + '<div class="ss-form-entry" style="text-align: left;" dir="ltr">'
        + '<h4 class="ss-q-title" style="text-align: left;">' + this.vsPostQuestions[i] + '</h4>' // <span class="ss-required-asterisk">*</span>
        //+ '<span class="ss-q-help">Gaussian blur, motion blur, radial blur, etc.</span>'
        + '<table cellspacing="0" cellpadding="5" border="0" class="ss-q-table">'
          + '<tbody>'
          + this._getLikertScaleHTML(7)
          + this._getLikertRadioHTML(7, v_index, i)
          + '</tbody></table></div><br><br></div>';
    }
    return html;
    */
  },

  getDSConfidenceHTML: function (v_index){
    var i=0;
    var html = '';
    for (i=0; i<this.dsConfidenceQuestions.length; i++) {
    html = html + '<div class="ss-formwidget-container span12">'
      + '<div class="ss-form-entry" style="text-align: left;" dir="ltr">'
        + '<h4 class="ss-q-title" style="text-align: left;">' + this.dsConfidenceQuestions[i] + '</h4>' // <span class="ss-required-asterisk">*</span>
        //+ '<span class="ss-q-help">Gaussian blur, motion blur, radial blur, etc.</span>'
        + '<table cellspacing="0" cellpadding="5" border="0" class="ss-q-table">'
          + '<tbody>'
          + this._getLikertScaleHTML(7)
          + this._getConfidenceLikertRadioHTML(7, v_index, i)
          + '</tbody></table></div><br><br></div>';
    }
    return html;

  },

  _getGenericHTML: function(v_index, question_set){
      var i=0;
      var html = '';
      for (i=0; i<question_set.length; i++) {
      html = html + '<div class="ss-formwidget-container span12">'
        + '<div class="ss-form-entry" style="text-align: left;" dir="ltr">'
          + '<h4 class="ss-q-title" style="text-align: left;">' + question_set[i] + '</h4>' // <span class="ss-required-asterisk">*</span>
          //+ '<span class="ss-q-help">Gaussian blur, motion blur, radial blur, etc.</span>'
          + '<table cellspacing="0" cellpadding="5" border="0" class="ss-q-table">'
            + '<tbody>'
            + this._getLikertScaleHTML(7)
            + this._getLikertRadioHTML(7, v_index, i)
            + '</tbody></table></div><br><br></div>';
      }
      return html;
  },

  getDSPostOverallHTML: function (v_index){
    return this._getGenericHTML(v_index, this.dsPostOverallQuestions);
    //dsPostInterfaceQuestions
  },  

  getDSPostInterfaceHTML: function (v_index){
    return this._getGenericHTML(v_index, this.dsPostInterfaceQuestions);
    //dsPostInterfaceQuestions
  },  

  getDSPostSpecificInterfaceHTML: function (v_index, isYouTube){
    //var i=0;
    //var html = '';
    var source = null;

    if (isYouTube)
    	source = this.dsYoutubePostQuestions;
    else
    	source = this.dsOursPostQuestions;

    return this._getGenericHTML(v_index, source);

    /*
    for (i=0; i<source.length; i++) {
    html = html + '<div class="ss-formwidget-container span12">'
      + '<div class="ss-form-entry" style="text-align: left;" dir="ltr">'
        + '<h4 class="ss-q-title" style="text-align: left;">' + source[i] + '</h4>' // <span class="ss-required-asterisk">*</span>
        //+ '<span class="ss-q-help">Gaussian blur, motion blur, radial blur, etc.</span>'
        + '<table cellspacing="0" cellpadding="5" border="0" class="ss-q-table">'
          + '<tbody>'
          + this._getLikertScaleHTML(7)
          + this._getLikertRadioHTML(7, v_index, i)
          + '</tbody></table></div><br><br></div>';
    }

    return html;
    */
  }  
  
}    