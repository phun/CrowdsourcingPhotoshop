/*
    YouTube Storyboard Frames
    -------------------------
    Written by Amit Agarwal (amit @labnol.org)
    See video demo at http://youtu.be/Y6yfGGxXyHw
    http://www.labnol.org/internet/youtube-image-frames/24608/
    http://ctrlq.org/code/19236-youtube-storyboard-bookmarklet
*/
javascript:(function(){ 
// Extract the Storyboard URL from the HTML Source
 a = ytplayer.config.args.storyboard_spec;
 
// If the Storyboard parameter is missing, no thumbnails would be generated
 if (!a) {
    alert("Sorry we had trouble creating a storyboard for this YouTube video.");
    exit();
 }
 
// Parse the Storyboard URL to get the base and unique ID of thumbnail images
 b = a.split("|");
 base = b[0].split("$")[0] + "2/M";
 c = b[3].split("#");
 sigh = c[c.length - 1];
 
// Get the length of the YouTube video 
// If the video is longer than 20 minutes, the storyboard has more image frames
 t = ytplayer.config.args.length_seconds;
 n = (t < 60 * 20) ? 5 : t / (60 * 4);
 
// Queue all the thumbnail images
 var urls = "";
 var imgs = "";
 var url = "";
 for (i = 0; i < n; i++) {
   url = base + i + ".jpg?sigh=" + sigh;
   imgs += "<img src='" + url + "'><br />";
   urls += url + "<br />";
 }
 
// Extract the YouTube video title
 var title = ytplayer.config.args.title;
 msg  = "<body style='margin:30px auto;width:800px;'>"
        + "<h2>TITLE</h2><div>LINKS</div><div>IMAGES</div></body>";
 
 msg = msg.replace("TITLE", title);
 msg = msg.replace("LINKS", urls);
 msg = msg.replace("IMAGES", imgs); 
 
// Put everything in an HTML page and open it in a new window
 var labnol = window.open();
 labnol.document.open();
 labnol.document.write(msg);
 labnol.document.close();
 
// When you click the bookmarklet, all the 
// available storyboard frames will open in a new window
})();

