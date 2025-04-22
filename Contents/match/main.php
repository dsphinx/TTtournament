<?php
/**
 *  Copyright (c) 2025, dsphinx@plug.gr
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
 *      This product includes software developed by the dsphinx@plug.gr.
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
 *  Created
 *    main.php :  13/4/25  21:36   -   dsphinx
 *
 */


// Phase A


require_once __DIR__ . "/../../App/TTennis.php";

TTennis::showMessage("Συμμετέχοντες Αθλητές ",   array('Τουρνουά' =>TTennis::$gameTitle,
    'Καταχώρηση Αποτελεσμάτων'=> ' Αγώνας'));

$matchID = miniMVController::param('id');

//echo $matchID;
//var_dump($_SESSION);
if ($matchID > 0) {

    $db = new sqliteDb($_SESSION['PATHS']['DB']);

    $ret = $db->get_row("SELECT * FROM `game` WHERE cancel =0 AND id = " . intval($matchID), true);

    if (isset($ret['id'])) {

        $athleteA= $db->get_row("SELECT * FROM `athletes` WHERE  id = " . intval($ret['athleteA']), true);
        $athleteB= $db->get_row("SELECT * FROM `athletes` WHERE  id = " . intval($ret['athleteB']), true);
//        var_dump($ret);
//        var_dump($athleteA);

        $data= array(
            'titleSide' => 'A',
            'titleSub' => 'Αγώνας',
            'athleteA' =>    $athleteA['surname']. " ". $athleteA['name'] ,
            'athleteB' =>  $athleteB['surname']. " ". $athleteB['name'] ,
            'athleteAid' =>   $ret['athleteA'] ,
            'athleteBid' =>  $ret['athleteB'] ,
            'titleKind' => '',
            'title' => '',
            'titleMen' => '',
            'titleDate' => '',

        );

        TTennis::showGroupPhaseA(__DIR__ . "/matchGame.html", $data);


// den ;exv akomh to gameSession ID
//just test for pirnt


    }

} else {

    echo "<br> <h3> no game selected  !! </h3>";
}