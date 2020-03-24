var watch = require("watch");
var Rsync = require("rsync");

var rsync = new Rsync()
  .shell("ssh")
  .delete()
  .flags("avz")
  .set("exclude-from", "excluderSync")
  .source('/c/PROJECTS/kMag1/')
  .destination("ced@172.16.9.39:/var/www/html/kMag1/");

// console.log(process.cwd());

watch.createMonitor(process.cwd(), function(monitor) {
  //  monitor.files['*']
  monitor.on("created", function(f, stat) {
    console.log("Created");
    rsync.execute(
      function(error, code, cmd) {
        if (error) console.log("ERROR:", error);
        if (cmd) console.log("All done executing! ", cmd);
      },
      function(data) {
        // do things like parse progress
        // console.log(data.toString('utf-8'));
      },
      function(data) {
        console.log(data);
        // do things like parse error output
      }
    );
  });
  monitor.on("changed", function(f, curr, prev) {
    console.log("Changed");
    rsync.execute(
      function(error, code, cmd) {
        if (error) console.log("ERROR:", error);
        if (cmd) console.log("All done executing! ", cmd);
      },
      function(data) {
        // do things like parse progress
        // console.log(data.toString('utf-8'));
      },
      function(data) {
        console.log(data);
        // do things like parse error output
      }
    );
    // Handle file changes
  });
  monitor.on("removed", function(f, stat) {
    console.log("Removed");
    rsync.execute(
      function(error, code, cmd) {
        if (error) console.log("ERROR:", error);
        if (cmd) console.log("All done executing! ", cmd);
      },
      function(data) {
        // do things like parse progress
        // console.log(data.toString('utf-8'));
      },
      function(data) {
        console.log(data);
        // do things like parse error output
      }
    );
    // Handle removed files
  });
  // process.on("SIGINT", monitor.stop());
  // monitor.stop(); // Stop watching
});
