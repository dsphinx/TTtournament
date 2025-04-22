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
 *    results.php :  16/4/25  23:57   -   dsphinx
 *
 */

require_once __DIR__ . "/../../App/TTennis.php";

//TTennis::showMessage("Τουρνουά ", TTennis::$gameTitle);



$db = new sqliteDb($_SESSION['PATHS']['DB']);

$playGames = miniMVController::$db->fetchAll("SELECT * FROM game where  cancel=0 AND winner Is not null"); //  πίνακας athletesGames


//miniMVController::object($playGames);
echo '    <style>     table, th, td {        border: 1px solid black;        text-align: center;      }      th, td {        padding: 6px;      }  </style>';


foreach ($playGames as $rec) {

    $playerA = $db->get_row("SELECT surname,name FROM athletes WHERE id =".$rec['athleteA'], true);
    $playerB = $db->get_row("SELECT surname,name FROM athletes WHERE id =".$rec['athleteB'], true);

    $aw = $bw="";
    if  (  $rec['athleteA'] == $rec['winner'] )  {
        $aw='👉 ';
    }else {
        $bw='👉 ';

    }


    // Υπολογισμός συνολικών σετ
    $setsWonA = 0;
    $setsWonB = 0;
    $sets = ['set1', 'set2', 'set3', 'set4', 'set5'];
    foreach ($sets as $set) {
        if (!empty($rec[$set]) && strpos($rec[$set], '-') !== false) {
            [$a, $b] = explode('-', $rec[$set]);
            if ($a > $b) $setsWonA++;
            elseif ($b > $a) $setsWonB++;
        }
    }
    TTennis::$groupLetter = 0;
    $rec['phase'] = "ΦΑΣΗ ".$rec['phase'];
    $rec['gameGroupsID'] = "Όμιλος ".TTennis::getGroupsLetters($rec['gameGroupsID']-1);
    $data = array(
        'phaseA' => $rec['phase'],
        'gameGroups' => $rec['gameGroupsID'],
        'player1' =>  $playerA['surname'] . " " .$playerA['name'],
        'player2' =>  $playerB['surname'] . " " .$playerB['name'],
        'player1Win' =>  $aw,
        'player2Win' =>  $bw,
        'set1' => $rec['set1'],
        'set2' => $rec['set2'],
        'set3' => $rec['set3'],
        'set4' => $rec['set4'],
        'set5' => $rec['set5'],
        'date' => $rec['matchDate'],
        'setA' => $setsWonA,
        'setB' => $setsWonB,
        'player1Points' =>  $rec['athleteAPoints'] ?? 0,
        'player2Points' =>  $rec['athleteBPoints'] ?? 0,


    );

//    miniMVController::object($data);


    TTennis::showGroupPhaseA(__DIR__ . "/game.html", $data);

}


