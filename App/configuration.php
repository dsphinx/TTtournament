<?php
/**
 *  Copyright (c) 2025, dsphinx by chatGPT
 *  Copyright (c) 2016, dsphinx
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
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
 *  THIS SOFTWARE IS PROVIDED BY dsphinx ''AS IS'' AND ANY
 *  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL dsphinx BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *  Filename: configuration.php
 *  Created : 17/9/16 10:20
 */

// config at sqlite file ... settings

/**
 *  Cookies
 */
const APP_Version = 4.0;
//    DEFINE ("__AJAX", TRUE);  //   Do not use mysqli Session

setlocale(LC_ALL, 'el_GR.UTF8');
date_default_timezone_set('Europe/Athens');

$_path = __DIR__;
$_path = substr($_path, 0, -3); // App/config

error_reporting(E_ALL);
//error_reporting(0);

/*
 *          Session Management   Simple
 *           need secure ?
 */
if (!defined("AJAX") && file_exists('App/sessions.php')) {
    require_once($_path . 'App/sessions.php');
}

/*
 *          Paths to include
 */
$_SESSION['PATHS'] = array("__ROOT__" => $_path,
    "CONFIG" => __FILE__,
    "JAVASCRIPT" => "App/Javascript/",
    "UPLOADS" => "App/Media/uploads/",
    "IMAGES" => "App/Media/images/",
    "DB" =>  __DIR__ . "/Data/dataInfo.sqlite",
    "INCLUDE_DIRS" => array( // Loading pages from ...
        'Contents/',
        'App/',
        'Developer/',
    )
);
$_SESSION['LOGS'] = array();
$_SESSION['GAME'] = array(
    'gameTitleMD5' => NULL
);



if (!defined("AJAX")) {
// DB Sqlite3 :  Settings table
    $tempRow = miniMVController::$db->get_rows("SELECT * FROM Settings", TRUE);

    miniMVController::$config['HTMLBlock'] = $tempRow[0];
//miniMVController::object(miniMVController::$config['HTMLBlock']);

    miniMVController::$config['HTMLBlock']['title'] .= "@" . php_uname("n");

// DB Sqlite3 :  menus table
    $tempRow = miniMVController::$db->get_rows("SELECT title,uri FROM menus WHERE hide=0", TRUE);
//miniMVController::object($tempRow);

    foreach ($tempRow as $show) {

        $active = ($show['uri'] == '?page=' . miniMVController::$config['page']) ? " active " : '';
        miniMVController::$config['menus'] .= "<li  class=\"nav-item\"><a  class='nav-link $active' href='$show[uri]'>$show[title]</a></li>";
    }

    unset($tempRow);
    unset($_path);

} else {
    // AJAx
     require_once 'sqliteDb.php';
}