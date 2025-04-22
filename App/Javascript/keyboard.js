/**
 *  Copyright (c) 11/6/16, dsphinx@plug.gr
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   1. Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *   2. Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *   3. All advertising materials mentioning features or use of this software
 *      must display the following acknowledgement:
 *      This product includes software developed by the dsphinx.
 *   4. Neither the name of the dsphinx nor the
 *      names of its contributors may be used to endorse or promote products
 *     derived from this software without specific prior written permission.
 *
 **/

// https://www.w3.org/TR/gamepad/
var keysControl = {
    /*
     *      Variables
     */
    flag: 1,
    defaultDOMElement: window,  // affected element
    /*
     *       Methods
     *
     *       handle keys on DOMElemenmt, default is window - global
     */
    init: function (DOMElement) {
        var object = DOMElement ? DOMElement : this.defaultDOMElement;
        // press
        object.addEventListener('keydown', this.handleKeydown, false);
        // released keyd
        object.addEventListener('keyup', this.handleKeyup, false);
        return false;
    },
    /**
     *   When a key pressed
     *
     * @param e
     * @returns {boolean}
     */
    handleKeydown: function (e) {
        show.log('keycode pressed: ' + e.keyCode);

        switch (e.keyCode) {
            //
            // up array
            case 38:
                break;
            // down array
            case 40:
                break;
            // left array
            case 37:
                break;
            // right array
            case 39:
                break;
            //
            //   ? keys - for HELP
            //
            case 191:
                show.log(" HELP ***. <1..9> logfiles,  <c> copy selected text, <enter,space> red line, <s> start/stop scrolling ");
                break;
            /**
             *     normal keys
             */
            //
            //
            case 49:
            case 50:
            case 51:
            case 52:
            case 53:
            case 54:
            case 55:
            case 56:
            case 57:
                var x = "#log" + (e.keyCode - 48);
                $(x).trigger('click');
                break;


            case 32:
            case 13:
                $("#tailOutput").append("<hr/> ");
                break;


            case 67:
                show.log(" copy to clipboard ");
                copy2Clipboard();
                break;


            case 83:
                $("#scroll").trigger('click');
                break;
            //
            // a

            //
            //  default actions
            default:
                break;
        }


        return false;
    },
    /**
     *
     * when key is released
     *
     * @param e
     * @returns {boolean}
     */
    handleKeyup: function (e) {
        //  show.log('                   ' );
        //  show.log('keycode released pressed: ' + e.keyCode);

        return false;
    }
};


/**
 *    simple call
 *
 *
 * @param DOMElement
 */
function initKeys(DOMElement) {
    // keysControl.init();
    keysControl.init(DOMElement);
    //  keysControl.init(window);
    //  keysControl.init(canvas);
    // keysControl.init(divID);


}

// init
window.onload = initKeys();