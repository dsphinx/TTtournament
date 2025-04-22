/**
 * Created by dsphinx on 17/9/17.
 */

var timer, pre, clipboardData, scrolling = true;

// Check every 200msec for fresh data
var interval = 1500;
// var interval = 200;
var scriptIS = "App/FilesTail.php";
// var logFiles;
var logFiles = new Array();
var logFilesLength = new Array();


// Function that asks the API for new data
function reloadData() {
    clearTimeout(timer);

    for (  i in logFilesLength) {

        if (tailFileIS == i) {
            console.log("IN ", i, ' @ ',offset);

        } else {
            console.log("--> ", i, logFilesLength[i] + " <-- ", tailFileIS, ' - ',offset);
        }

        if (logFilesLength[i] !== undefined && logFilesLength[i] !== offset) {
     //        offset = logFilesLength[i];
   //          tailFileIS = i;
        }
        clipboardData.innerHTML = "";

         $.getJSON(scriptIS + "?log=" + tailFileIS + "&offset=" + offset, function (data) {
            offset = data["offset"];
            var arr = data["lines"];

            if (arr === undefined) {

            } else if (arr !== NaN && arr.length > 1) {
                for (var i = 0; i < arr.length; i++) {
                    pre.append(arr[i] + '<br/>');
                    clipboardData.append(arr[i] + "<br>");

                }

                if (!scrolling) {
                    scrolling = true;
                    show.log(' scrolling:  auto -> new data from ' + tailFileIS);
                    $("#scroll").prop('checked', true);
                }
                //  pre.append(arr);
                //      copyToClipboard(clipboardData);
            }
        });
    }

    if (scrolling) {
        $("#tailOutput").animate({scrollTop: $('#tailOutput').prop("scrollHeight")}, 1000);
        //   worker.postMessage({'cmd': 'status'});

    }

    if (logFiles.indexOf(tailFileIS) === -1) {
        logFiles.push(tailFileIS)
        logFilesLength[tailFileIS] = offset;
        // worker.postMessage({'cmd': 'start', 'msg': 'Hi', 'logfile': tailFileIS, 'logfileOffset': offset});

    } else {
      //  logFiles.splice(logFiles.indexOf(tailFileIS) ,1);
        logFilesLength[tailFileIS] = offset;

      //  console.log("This log already exists");
    }

    timer = setTimeout(reloadData, interval);
}

$(document).ready(function () {

     // Get offset at first loading of page
    $.getJSON(scriptIS + "?log=" + tailFileIS, function (data) {
        offset = data["offset"];
    });
    pre = $("#tailOutput");
    clipboardData = $("#tailClipboard");
    // Trigger function that looks for new data
    reloadData();

    $("#scroll").click(function () {
        scrolling = this.checked;
        show.log(' scrolling: ' + scrolling);

    });

    $("#scroll").prop('checked', true);
});