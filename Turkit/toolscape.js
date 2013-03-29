
setTrace(2);

// URL containing the page you want turkers to work on.
var url = "http://people.csail.mit.edu/phun/photoshop/i2/one_v.html?id=1";

var gettask_url = "http://people.csail.mit.edu/juhokim/sinch2/php/gettask.php";

var title = "Gather Steps in Photoshop Video Tutorial";
var description = "Gather Steps in Photoshop Video Tutorial";
var keywords = "video, tutorial, annotate, photoshop";

// Try to keep under this rate per hour.
var maxPerHour = 3.00;

// From 0 to 1.
var aggressiveNess = 0.80;

// Maximum number of seconds until retiring the HIT (seconds).
var maxTimeTillDeath = 60*15;

// Maximum time to complete one task in a HIT (seconds).
var maxTimePerTask = 60;

// Number of answers really desired for each question.
// Actual number be this much or
var numAnswersDesired = 3;

// Multiplier on extra HITs.
// At most you should expect to receive (and pay for)
// overShootMultipler * numAnswersDesired answers.
var overShootMultiplier = 3;

// Number of HITs that should always be posted.
var steadyStateNum = 0;

// Minimum time between adding HITs.
// Number of seconds between deleting and readding HITs -
// setting this too low can cause thrashing, where turkers
// try to accept a HIT but quikturkit has already deleted it.
var minTimeBetweenHITs = 15;

// Number of HITs added at once.
// Maximum number of HITs to add a one time.
var numHITsAtOnce = 2;



// The reward for each HIT.
var reward = 0.01;

// Number of assignments offered in each HIT posted.
var assignments = 1;

//
var numhits = 10;



// Array of HIT ids.
var currentHITs = database.query("return currentHITs;");
if(!currentHITs) currentHITs = [];

print("Old HITs: " + json(currentHITs));

//
// Start things over before we get going by resetting all HITs.
//
for(var j=currentHITs.length-1; j>=0; j--) {
  retireHIT(currentHITs.splice(j, 1));
}

// Current number of active assignments.
var activeAssignments = 0;

//
// The main (infinite) loop.
// Be careful, the only thing stopping you from losing a bundle is the
// global safety values (money spent, number of HITs)
//
for(var i=0; i<1; i++) {

  print("\n\nITERATION " + i + " (" + currentHITs.length + " hits):");

  // Fetch the number of answers provided for the least-answered item in the database.
  var curr = answersForLowest();

  var lowAnswer = curr[0];
  var diff = curr[1];

  // If we already have enough answers, then don't worry about creating more.
  if(lowAnswer > numAnswersDesired - steadyStateNum) {
    lowAnswer = numAnswersDesired - steadyStateNum;
  }

  // If someone's interacting with the application and we don't
  // already have some active HITs, seek 1 answer optimistically.
  if(diff < 60*5 && lowAnswer > (numAnswersDesired -1)) {
      lowAnswer = numAnswersDesired - 1;
  }

  // Track the youngest HIT.
  var youngestHIT = 0;


  // Refresh the number of current HITs periodically unless there's
  // been activity on the phone.
  if(diff < 60*5 || lowAnswer < numAnswersDesired || (i%10==0 && activeAssignments>0)) {
      // Reset activeAssignments for counting next.
      activeAssignments = 0;

      //
      // Review the current HITs to see how many have completed.
      //
      for(var j=currentHITs.length-1; j>=0; j--) {
        var hit = mturk.getHIT(currentHITs[j]);

        var secs = (time() - hit.creationTime) / 1000;

        if(secs < youngestHIT || youngestHIT == 0) {
          youngestHIT = secs;
    }

    // Count active assignments, delete finished HITs.
    if(hit.done || secs > maxTimeTillDeath) {
        // Remove this HIT from our list.
        retireHIT(currentHITs.splice(j, 1));
    } else {
        print("Adding: " + (hit.maxAssignments - hit.assignments.length));
        activeAssignments += (hit.maxAssignments - hit.assignments.length);
    }
   }
  }

  print("active: " + activeAssignments + ", low: " + lowAnswer + "->" + numAnswersDesired + ", steady@: " + steadyStateNum);

  //
  // Add or delete HITs as needed.
  //
  var hitsToAdd = overShootMultiplier*(numAnswersDesired - lowAnswer) - activeAssignments;


  // How many tasks should new HITs have?
  // The default is 12, but this goes down to 5 or 2 based on how long until we expect to what answers.
  var tasksForNewHits = 12;
  if(diff<0) {
    tasksForNewHits = 2;
  } else if(diff < 4*60) {
    tasksForNewHits = 5;
  }

  // Number of HITs added at once.
  var numHITsAtOnce = 2;

  // Adjust for HIT creation rate.
  if(time() - youngestHIT < minTimeBetweenHITs) {
    hitsToAdd = 0;
  } else if(hitsToAdd > numHITsAtOnce) {
    hitsToAdd = (hitsToAdd - numHITsAtOnce > numHITsAtOnce) ? (hitsToAdd-numHITsAtOnce) : numHITsAtOnce;
  }


  //  print("ADD: " + hitsToAdd + ", DIFF: " + diff = ", low: " + lowAnswer + ", active: " + activeAssignments + ", add: " + hitsToAdd);

  hitsToAdd = 1; 
  if(hitsToAdd > 0) {
    for(var j=0; j<hitsToAdd;) {
      var hits = createNewHIT(reward, assignments, numhits, tasksForNewHits);
      currentHITs = currentHITs.concat(hits); 

      // Final number of jobs actually created.
      j+=numhits*assignments;

      print(currentHITs.length + " current HITs, " + hits.length + " new ones created.");
    }
  } else if(hitsToAdd < 0) {
    for(var j=hitsToAdd; j<0; j++) {
      if(currentHITs.length > 0) {
        print("retiring here");
        retireHIT(currentHITs.splice(0, 1));
      }
    }
  } else {
      // We're already in a good state, so do nothing.
  }

  // Store current currentHITs.
  database.query("currentHITs = " + json(currentHITs));

  // Wait for a little bit before polling again.
  Packages.java.lang.Thread.currentThread().sleep(2000);
}


function createNewHIT(reward, assignments, numhits, tasks) {
  var hitsCreated = [];

  // Generate a random number 0-1000;
  salt = Math.floor(Math.random()*1001);

  if(typeof tasks == 'undefined') {
    tasks = 3;
  }

  var mytitle = title.replace(/%%n%%/g, tasks);
  var mydescription = description.replace(/%%n%%/g, tasks);
  var myurl = url.replace(/%%n%%/g, tasks);
  var myKeywords = keywords.replace(/%%n%%/g, tasks);

  for(var i=0; i<numhits; i++) {
    // create a HIT on MTurk using the webpage
    var hitId = mturk.createHITRaw({
      title : mytitle,
      desc : mydescription + " " + salt,
      url : myurl,
      height : 1200,
      reward : reward,
      keywords: myKeywords,
      assignmentDurationInSeconds: maxTimeTillDeath,
      maxAssignments: assignments
    });

    hitsCreated.push(hitId);
  }

  return hitsCreated;
}


function answersForLowest() {
  var ret = [999,999];

  try {
    var content = eval(slurp(gettask_url));
    ret = [content.cnt, content.time];
  } catch(e) {
    ret = [999,999];
  }

  return ret;
}


/**
 * Function for retiring a HIT.
 **/
function retireHIT(hit) {
  mturk.approveAssignments(hit.assignments);
  mturk.deleteHITs([hit]);
}


function printHITs(hits) {
  foreach(hits, printHIT);
}

function printHIT(hit) {
  var h = mturk.getHIT(hit);

  var vals = [h.done, h.assignments.length];

  print(vals.join(','));
}

