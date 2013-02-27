<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Phuster: Homepage of Phu Nguyen</title>
<link rel="stylesheet" href="css/960.css" />
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" href="css/reveal.css" />
<script src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.reveal.js"></script>
<script>
	var RecaptchaOptions = {
    	theme : 'clean'
 	};
	$(document).ready(function() {
		$(".lastName").hide();
		var scrollSpeed = 500;
		$(".homeBtn").each(function() {
			$(this).click(function() {
				$('html, body').animate({
					scrollTop: $("#header").offset().top
			 	}, scrollSpeed);
		 	});
		});
		$(".portfolioBtn").each(function() {
			$(this).click(function() {
				$('html, body').animate({
					scrollTop: $("#portfolio").offset().top
			 	}, scrollSpeed);
		 	});
		});
		$(".aboutBtn").each(function() {
			$(this).click(function() {
				$('html, body').animate({
					scrollTop: $("#about").offset().top
			 	}, scrollSpeed);
		 	});
		});
		$(".contactBtn").each(function() {
			$(this).click(function() {
				$('html, body').animate({
					scrollTop: $("#contact").offset().top
			 	}, scrollSpeed);
		 	});
		});
		$("#resume").click(function() {
			window.location = "https://dl.dropbox.com/u/17249876/resume.pdf";
		});
		
		// Portfolio Bindings
		$('#planshare').click(function(e) {
        		e.preventDefault();
	  		$('#planshareModal').reveal();
     		});
     	$('#nlightn').click(function(e) {
        		e.preventDefault();
	  		$('#nlightnModal').reveal();
     	});
		$('#iMafia').click(function(e) {
        		e.preventDefault();
	  		$('#iMafiaModal').reveal();
     	});
	});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28236626-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<div class="fullBackground black">
    <div id="planshareModal" class="reveal-modal">
    	<div class="modalTop">
        	<div class="left">
            <img src="projects/planshare/image-1.png" />
            </div>
            <div class="right">
            	<h1> planshare </h1>
                <h2> Spring 2012 </h2>
                
                <div class="info">
                	<strong> Purpose </strong> 21W.785
                </div>
                
                <div class="tags">
                    <span class="tag"> HTML </span>
                    <span class="tag"> CSS </span>
                    <span class="tag"> Django </span>
                    <span class="tag"> Photoshop </span>
                    <span class="tag"> Javascript </span>
                    <span class="tag"> Jquery </span>
                </div>
            </div>
        </div>
        <div class="modalBottom">
            <p>Planshare was a web application I created with three of my friends to simplify the process of adding our class schedules to our Google Calendar. What started out as a project for our class 21W.785 turned into a semester long commitment to building something that will save time for all of MIT students. </p>
        </div>
        <a class="close-reveal-modal">&#215;</a>
    </div>
     <div id="nlightnModal" class="reveal-modal">
    	<div class="modalTop">
        	<div class="left">
            <img src="img/nlightn-300x180.png" />
            </div>
            <div class="right">
            	<h1> nlightn </h1>
                <h2> January 2012 </h2>
                
                <div class="info">
                	<strong> Purpose </strong> Web Competition
                </div>
                
                <div class="tags">
                    <span class="tag"> HTML </span>
                    <span class="tag"> CSS </span>
                    <span class="tag"> Ruby on Rails </span>
                    <span class="tag"> Photoshop </span>
                    <span class="tag"> Javascript </span>
                    <span class="tag"> Jquery </span>
                    <span class="tag"> Bootstrap </span>
                </div>
            </div>
        </div>
        <div class="modalBottom">
            <p> One of my friends and I wanted to see what the hype is about Ruby on Rails, so we decided to hack out a small web application for a web competition at MIT. Nlightn served as a community where users can ask questions, answer questions, and tutor other users. </p>
        </div>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div id="iMafiaModal" class="reveal-modal">
    	<div class="modalTop">
        	<div class="left">
            <img src="img/imafia-300x180.png" />
            </div>
            <div class="right">
            	<h1> iMafia </h1>
                <h2> Fall 2012 </h2>
                
                <div class="info">
                	<strong> Purpose </strong> Class Project
                </div>
                
                <div class="tags">
                    <span class="tag"> Java </span>
                </div>
            </div>
        </div>
        <div class="modalBottom">
            <p> iMafia is an online version of the popular card game Mafia. Users talk to each other using the chatbox and make their decision with the graphic user interface. All characters are randomly assigned and the narrator is played by a computer. At the end of the game, a story based on the finished game is generated for users to read. </p>
        </div>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div class="container_12" id="header">
        <div id="title" class="grid_4"><div class="logo"></div> Phuster </div>
        <div id="nav" class="grid_8"> 
            <ul id="navList">
                <li class="homeBtn"> Home </li>
                <li class="portfolioBtn"> Portfolio </li>
                <li class="aboutBtn"> About </li>
                <li class="contactBtn"> Contact </li>
            </ul>
        </div>
        <div id="intro">
        	<div id="welcome" class="grid_6">
            	<h1>Hi, I'm Phu.</h1>
                <p>
                	I'm an undergraduate studying Computer Science at MIT. I have a passion for making great user experiences . . . and having fun.
                </p>
                <div id="resume"> Here's My Resume </div>
            </div>
           <div id="recentWork" class="grid_6">
           		<h2>Most Recent Work</h2>
 				<div class="imgContainer"><img src='img/planshare-460x240.png' />
                	<div class="infoBox">
                		<h3> Planshare </h3>
                    	<p> A wep application developed for MIT students to create their semester calendars </p>
                	</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="portfolio" class="container_12">
	<div class="grid_12">
    	<div class="homeBtn toTop"> Back To Top </div>
   		<h1> Portfolio </h1>
    </div>
    <div class="project grid_4">
    	 <div id="planshare" class="infoBox">
         	<h3> Planshare </h3>
            	<p> A wep application developed for MIT students to create their semester calendars using Django </p>
        </div>
    	<img src="img/planshare-300x180.png" />
    </div>
        <div class="project grid_4">
    	 <div id="nlightn" class="infoBox">
         	<h3> nlighn </h3>
            	<p> A website that promotes sharing knowledge through questions using Ruby on Rails </p>
        </div>        
    	<img src="img/nlightn-300x180.png" />
    </div>
    <div class="project grid_4">
    	 <div id="iMafia" class="infoBox">
         	<h3> iMafia </h3>
            	<p> An online version of the popular card game, Mafia, written in Java </p>
        </div>
    	<img src="img/imafia-300x180.png" />
    </div>
    <div class="clear" style="height: 20px"></div>
    <div class="project grid_4">
        <div class="infoBox">
         	<h3> TBA </h3>
            	<p> Concept design for a mobile application I plan to make </p>
        </div>
    	<img src="img/tba-300x180.png" />
    </div>
    <div class="project grid_4">
        <div id="thriveHive" class="infoBox">
         	<h3> ThriveHive </h3>
            	<p> Worked on a Google maps and Flot charts project during my internship with ThriveHive </p>
        </div>
    	<img src="img/thrivehive-300x180.png" />
    </div>
    <div class="project grid_4">
    	<div id="goodGame" class="infoBox">
         	<h3> GoodGame </h3>
            	<p> A web application I created with former roomates for a competition that suggested games for you </p>
        </div>
    	<img src="img/goodgame-300x180.png" />
    </div>
</div>
<div class="fullBackground black">
<div id="about" class="container_12">
	<div class="grid_12">
        <div class="homeBtn toTop"> Back To Top </div>
   		<h1> About Me </h1>
    </div>
    <div class="grid_8">
    	<h3>I am a web designer.</h3>
		<p>
			I created my first webpage on Neopets.com in the fifth grade following the site's step-by-step HTML tutorial. I created my first graphic in Microsoft Paint making banners for my website.
       	</p>

		<h3>I am a programmer.</h3>
		<p>
		I learned my first language, C/C++, taking a class at Tufts University during my Junior year in high school. The program I am most proud of in my early works is my text-based version of Battleship. Who knew creating artificial intelligence for a computer was so difficult? Update: I am now very familiar with programming in Java (much more than I am with C/C++ I would say).
		</p>

		<h3>I am a student.</h3>
		<p>
I want to further my education with the most knowledge and preparation that the Massachusetts Institute of Technology can give me to help me succeed in my prospective career.
		</p>
        <h3>I am the oldest son.</h3>
		<p>
I am of three children, born in a Vietnamese family that immigrated in search of the American Dream. I am the first in the family to go to college, and I am proud of how far I have come. I couldn't have done without the support of my loved ones.
		</p>
    </div>
    <div class="grid_4">
		<h2> Quick Bio </h2> 
		<ul>
        	<li> 20 years old </li>
            <li> Born and raised in Boston, MA </li>
            <li> MIT Class of 2014 </li>
            <li> Vietnamese-American </li>
        </ul>	
		<h2> Things I Love </h2>
        <ul>
        	<li> A relaxing longboarding session </li>
            <li> Fresh clothes and all things viral </li>
            <li> Dancing with MIT Dancetroupe, Mocha Moves, and Ridonkulous </li>
            <li> Being a percussionist and a drummer </li>
            <li> Listening, making, and remixing all genres of music </li>
            <li> Attempting to sing in the shower </li>
       	</ul>
    </div>
</div>
</div>
<div id="contact" class="container_12">
	<div class="grid_12">
        <div class="homeBtn toTop"> Back To Top </div>
    	<h1> Contact </h1>
    </div>
    <div class="grid_8">
    	<h2> Send A Message </h2>
        <form method="post" action="testsend.php" enctype='multipart/form-data'>
            <label> Name </label><input id="name" type="text" name="name" size="35" />
            <label class="lastName"> Last Name </label>
            <input id="lastName" class="lastName" name="lastName" />
            <label> Email </label><input id="email" type="text" name="email" size="35" />
            <label> Message </label><textarea id="message" name="message" rows="4" cols="40"></textarea>
            <input id="sendMail" class="submit" type="submit" value="Send" />
        </form>
    </div>
    <div class="grid_4">
    	<h2> Reach Me </h2>
		<ul id="contactList">	
            <li><a href="mailto:me@phuster.com"><img src="css/images/email-30.png" /> me@phuster.com </a></li>
            <li><a href="tel:8576006843"><img src="css/images/phone-30.png" /> 857.600.6843 </a></li>
        	<li><a href="http://twitter.com/#!/WhosPhu"><img src="css/images/twitter-30.png" /> @WhosPhu </a></li>
            <li><a href="http://www.linkedin.com/profile/view?id=109539234"><img src="css/images/linkedin-30.png" /> Phu Nguyen </a> </li>
            <li><a href="http://phunomenon.tumblr.com/"><img src="css/images/tumblr-30.png" /> phunomenon </a></li>
        </ul>
    </div>
</div>
</body>
</html>