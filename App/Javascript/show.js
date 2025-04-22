/**
 * Created by dsphinx on 18/9/16.
 */
'use strict';


/**
 *   display message via console or showLogs Div
 *
 * @type {{divOutput: string, log: show.log}}
 */
var show = {
    divOutput: '#debugLine', // div id="showLogs"
    preMessage: ' > ',
    message: '',
    log: function (pmessage) {
        var tmp = document.querySelector(this.divOutput);
        this.message = pmessage;

        if (typeof tmp == "undefined") {
            console.log(this.message);
        } else {
            if (tmp != null) {      // DOM element is not readey
                tmp.innerText = this.preMessage + this.message;
            } else {
                console.log(this.message);
            }
        }
    }
};

