/**
 * Created by dsphinx on 6/12/15.
 */

'use strict';

var AppPath = 'App/Javascript/';
var loadJavascript = ['show.js', "keyboard.js" ]; // 'GAnalytics.js'];
var loadScriptSum = loadJavascript.length;
var mainPageContainer = 'mainPageContents';
// var logFiles;


/**
 *
 * load via ajax our javascript file - located on AppPath
 *
 * @param JSFile
 */
function loadJSfile(JSFile) {
    $.getScript(AppPath + JSFile)
        .done(function (script, textStatus) {
            //     show.log(textStatus + " JS loading ");
        })
        .fail(function (jqxhr, settings, exception) {
            show.log(" file not loaded - FAILED " + JSFile);
        });
}


// Load Files , with given order
for (var i = 0; i < loadScriptSum; i++) {
    loadJSfile(loadJavascript[i]);
}


/**
 *    load into DOM without refresh - jquery parser
 */
function loadLinksDOM() {
    $(document).ready(function () {

        // last Action - parse all
        $(".contentParser").pageParser({
            container: $("#" + mainPageContainer), //required
            setTitle: true,
            dynamicUrl: false,
        });

    });
}


function getSelectionText(){
    var selectedText = ""
    if (window.getSelection){ // all modern browsers and IE9+
        selectedText = window.getSelection().toString()
    }
    return selectedText
}


function copyToClipboard(element) {
    var $temp = $("<input>");
    var brRegex = /<br\s*[\/]?>/gi;
    $("body").append($temp);
    // alert($(element).html().replace(brRegex, "\r\n"));
    $temp.val($(element).html().replace(brRegex, "\r\n")).select();
    document.execCommand("copy");
    $temp.remove();
}


function copy2Clipboard() {

    var sel = getSelectionText();

    try {
        if (sel !== "") {
         //   alert(sel);
            document.execCommand("copy");

        } else {
            copyToClipboard($("#tailClipboard"));
        }
     //   console.log('Copying text command was ' + msg);
    } catch (err) {
        console.log('Oops, unable to copy');
    }

 }



//
// function copySelectionText(){
//     var copysuccess // var to check whether execCommand successfully executed
//     try{
//         copysuccess = document.execCommand("copy") // run command to copy selected text to clipboard
//     } catch(e){
//         copysuccess = false
//     }
//     return copysuccess
// }
//
//
// document.body.addEventListener('mouseup', function(){
//     var copysuccess = copySelectionText() // copy user selected text to clipboard
//     show.log(" copied to clipboard " );
//
// }, false)