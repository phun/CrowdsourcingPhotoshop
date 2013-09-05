var page = require('webpage').create();

var videos = [
"http://www.youtube.com/watch?v=ndfm-esLqFs",
"http://www.youtube.com/watch?v=ebt1iec9X2E",
"http://www.youtube.com/watch?v=6regL5X8XZk",
"https://www.youtube.com/watch?v=IouehE5_CRc",
"https://www.youtube.com/watch?v=hdwyzOilyo4",
"https://www.youtube.com/watch?v=yFYxqGIYEDE",
"https://www.youtube.com/watch?v=PAZGPorV66A",
"https://www.youtube.com/watch?v=5WbqQfPWZYI",
"https://www.youtube.com/watch?v=LKI_n5fdWu8",
"https://www.youtube.com/watch?v=Dwun6HgBVQ4",
"https://www.youtube.com/watch?v=uUEG5wI6ke8"
// "http://www.youtube.com/watch?v=t8sTxPBtt8A",
// "http://www.youtube.com/watch?v=lA5e3r6Dj_0",
// "http://www.youtube.com/watch?v=sMRLYxcQJMA",
// "http://www.youtube.com/watch?v=nIZphW1eh2c",
// "http://www.youtube.com/watch?v=UzxuYMR1jHQ",
// "http://www.youtube.com/watch?v=aEPnRWO6Fn0",
// "http://www.youtube.com/watch?v=E3-fKp7sTls",
// "http://www.youtube.com/watch?v=ndfm-esLqFs",
// "http://www.youtube.com/watch?v=jqHpQ9Isv-U",
// "http://www.youtube.com/watch?v=7z5YKi5cizA",
// "http://www.youtube.com/watch?v=Y-xZAxQuJzA",
// "http://www.youtube.com/watch?v=ebt1iec9X2E",
// "http://www.youtube.com/watch?v=qJ70dcSmf-M",
// "http://www.youtube.com/watch?v=ZFtdvFEchug",
// "http://www.youtube.com/watch?v=85WjLBsJVjc",
// "http://www.youtube.com/watch?v=RUUDpU_VQ1U",
// "http://www.youtube.com/watch?v=XjvLF5N-1I8",
// "http://www.youtube.com/watch?v=QHP0ExWZNa4",
// "http://www.youtube.com/watch?v=GGGRLxfhF4A",
// "http://www.youtube.com/watch?v=z6jHHxfgG-o",
// "http://www.youtube.com/watch?v=uPk9L_MhK9k",
// "http://www.youtube.com/watch?v=MYnpg3HmdXA",
// "http://www.youtube.com/watch?v=cDrwzOa6e74",
// "http://www.youtube.com/watch?v=kmq1p0am3dI",
// "http://www.youtube.com/watch?v=orfRida5Bwk",
// "http://www.youtube.com/watch?v=ybYXXZee3kw",
// "http://www.youtube.com/watch?v=Pq-6p85QYB4",
// "http://www.youtube.com/watch?v=tTtQ6k8BS30",
// "http://www.youtube.com/watch?v=TzG7mI4h_yY",
// "http://www.youtube.com/watch?v=C_5t3oH6QdY",
// "http://www.youtube.com/watch?v=CCPRn6b-zdc",
// "http://www.youtube.com/watch?v=yjV7yvnS-wI",
// "http://www.youtube.com/watch?v=bvN-D1MIj7I",
// "http://www.youtube.com/watch?v=pgVTsl7Z8ms",
// "http://www.youtube.com/watch?v=zYtg-6lb9gU",
// "http://www.youtube.com/watch?v=_HfGFc4I6OM",
// "http://www.youtube.com/watch?v=xu9QlcdDbxc",
// "http://www.youtube.com/watch?v=6regL5X8XZk",
// "http://www.youtube.com/watch?v=ggDygKyJL5o",
// "http://www.youtube.com/watch?v=8AAZrL-2ZHQ",
// "http://www.youtube.com/watch?v=LyNsb_9PUFU",
// "http://www.youtube.com/watch?v=k7rLRSNtB7I",
// "http://www.youtube.com/watch?v=-fAugO9Gii0",
// "http://www.youtube.com/watch?v=ceVSg0LWCqY",
// "http://www.youtube.com/watch?v=t5ciQu_Pfgw",
// "http://www.youtube.com/watch?v=KNj0QJSRz0g",
// "http://www.youtube.com/watch?v=1P6ctvQEikw",
// "http://www.youtube.com/watch?v=7wPn3FJhFXs",
// "http://www.youtube.com/watch?v=8qj8dp_i31k",
// "http://www.youtube.com/watch?v=Kr6H3lxY-rg",
// "http://www.youtube.com/watch?v=F_XwQDM-EMo",
// "http://www.youtube.com/watch?v=0VHDFzpwD_g",
// "http://www.youtube.com/watch?v=5m0hEdDG9Z4",
// "https://www.youtube.com/watch?v=C6CzJjnxFQ0",
// "https://www.youtube.com/watch?v=G-TmVtU1Fx8",
// "https://www.youtube.com/watch?v=-84zTwumB84",
// "https://www.youtube.com/watch?v=yEyQQaFyhxI",
// "https://www.youtube.com/watch?v=vmTP3k22f0I",
// "https://www.youtube.com/watch?v=coYqrXsDPdU",
// "https://www.youtube.com/watch?v=6IN_mupBjh8",
// "https://www.youtube.com/watch?v=b0yoIOX8Bk0",
// "https://www.youtube.com/watch?v=Kkhvy9rQHaQ",
// "https://www.youtube.com/watch?v=IouehE5_CRc",
// "https://www.youtube.com/watch?v=GP2LKHRb3w0",
// "https://www.youtube.com/watch?v=PWqEpqEBcRk",
// "https://www.youtube.com/watch?v=dhqa3jTxYUU",
// "https://www.youtube.com/watch?v=i7z3zFuac5g",
// "https://www.youtube.com/watch?v=jV8m5GO0xuk",
// "https://www.youtube.com/watch?v=a1sUPZIB62g",
// "https://www.youtube.com/watch?v=oqzS0VqnWnw",
// "https://www.youtube.com/watch?v=Jrh_aympGlk",
// "https://www.youtube.com/watch?v=lsT2lffh-AQ",
// "https://www.youtube.com/watch?v=hdwyzOilyo4",
// "https://www.youtube.com/watch?v=lODEoZSwb4I",
// "https://www.youtube.com/watch?v=qQtch33dbrM",
// "https://www.youtube.com/watch?v=5m01DL60_4Y",
// "https://www.youtube.com/watch?v=yFYxqGIYEDE",
// "https://www.youtube.com/watch?v=GtVEasGxGpM",
// "https://www.youtube.com/watch?v=iTXnpGe7a1A",
// "https://www.youtube.com/watch?v=PAZGPorV66A",
// "https://www.youtube.com/watch?v=UMHG85jNjgs",
// "https://www.youtube.com/watch?v=qic7WMpSw1s",
// "https://www.youtube.com/watch?v=mfjPP4nXPuo",
// "https://www.youtube.com/watch?v=Qu5MwxHtkt8",
// "https://www.youtube.com/watch?v=Lw-cl1zibEc",
// "https://www.youtube.com/watch?v=BHsWPX8GRaw",
// "https://www.youtube.com/watch?v=XQVKpO0Ms5k",
// "https://www.youtube.com/watch?v=5WbqQfPWZYI",
// "https://www.youtube.com/watch?v=6OrZZOccrxs",
// "https://www.youtube.com/watch?v=JbrGHF52YSg",
// "https://www.youtube.com/watch?v=Eo8c2sZ2eOY",
// "https://www.youtube.com/watch?v=-Nbo5M72qdQ",
// "https://www.youtube.com/watch?v=ZJnaTr4z8d8",
// "https://www.youtube.com/watch?v=byjGdX3fn1k",
// "https://www.youtube.com/watch?v=Of4jTzm20d4",
// "https://www.youtube.com/watch?v=LKI_n5fdWu8",
// "https://www.youtube.com/watch?v=dLvS5IZ-iS8",
// "https://www.youtube.com/watch?v=mkS9kLrsqfc",
// "https://www.youtube.com/watch?v=BV9HB7UJqGY",
// "https://www.youtube.com/watch?v=Dwun6HgBVQ4",
// "https://www.youtube.com/watch?v=uUEG5wI6ke8",
// "https://www.youtube.com/watch?v=YScWIPI1cfw",
// "https://www.youtube.com/watch?v=tlOVSm39jL8"
];

processPage(0);

function processPage(index){
	if (index == videos.length)
		phantom.exit();
	console.log("===", index, videos[index]);
	page.open(videos[index], function () {
	    // page.render('example.png');
	    var result = page.evaluate(getStoryboard);
	    var i;
	    for (i in result){
	    	console.log(result[i]);	
	    }
		processPage(index + 1);
	});
}

function getStoryboard(){
	var a = ytplayer.config.args.storyboard_spec;
 
// If the Storyboard parameter is missing, no thumbnails would be generated
	if (!a) {
		console.log("storyboard not available");
	    return [];
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
	var urls = [];
	// var imgs = "";
	var url = "";
	for (i = 0; i < n; i++) {
		url = base + i + ".jpg?sigh=" + sigh;
		// imgs += "<img src='" + url + "'><br />";
		// urls += url + "<br />";
		urls.push(url);
	}
 	return urls;

}