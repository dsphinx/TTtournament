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
 *    main.php :  13/4/25  15:45   -   dsphinx
 *
 */

// Phase A
require_once __DIR__ . "/../../App/TTennis.php";
require_once __DIR__ . "/../../App/TTRanking.php";

const PlayersGroupNumberBestView = 4;

//          ?page=main&reGENERATEAllGaMES=1
//                                                                  /?debug=1&reGENERATEAllGaMES=1
if (miniMVController::param("reGENERATEAllGaMES")) {
//// debug ... TRUCATE
    echo " regenerating all games ---- DEBUG ";
    require_once __DIR__ . '/../admin/initialise.php';
}

TTennis::showMessage("Τουρνουά ", TTennis::$gameTitle);

//  Πάρε όλους του αθλητές που έχουν εγγραφεί στον αγώνα τον τελευταίο - πίνακας athletesGames
$PlayersRegisteredLastGame = miniMVController::$db->fetchAll("SELECT * FROM athletesRegisterGame order by scorePOFEPA desc"); //  πίνακας athletesGames
$PlayersRegisteredLastGameVet = count(miniMVController::$db->fetchAll("SELECT * FROM athletesRegisterGame WHERE veteran=1"));
$PlayersGroupNumber = miniMVController::param("groupMax") ?? 3;                               // group 2 -6 αθλητές - default 3
$PlayersGroup = $PlayersGroupID = array();                               // group 2 -$PlayersGroupNumber αθλητές
$total = 1;                                                             // counter
$phaseTournament = 1;  // phase 1 - Ομιλοις
/**
 *    Κριτηρια - Υπλοογισμοι
 */
foreach ($PlayersRegisteredLastGame as $player) {           // Groups
    $player['scorePOFEPA'] = str_replace('.', '', $player['scorePOFEPA']); // Αντικαθιστά το κόμμα με τελεία
    $player['scorePOFEPA'] = ceil(floatval($player['scorePOFEPA']));
    $newPlayer = array(
        'fullname' => $player['surname'] . " " . $player['name'],
        'id' => $player['id'],
        'scorePOFEPA' => $player['scorePOFEPA'],
        'groupNO' => NULL
    );
    TTennis::TournamentAthletes($newPlayer);
}


// TODO: πως γίνεται η κλήρωση με τι κριτήρια ?????
// τοπ σχορε = 1ow και oi αλλοι τυχαίοι


$debugShow = miniMVController::param("debug") ?? false;                               // group 2 -6 αθλητές - default 3
//$debugShow = true; // testing

$tmp = miniMVController::$db->get_row("SELECT * FROM gameGroups WHERE groupNO=1 AND  `gameSESSION`= '" . TTennis::getSessionGameTitleMD5() . "'", true);

$displayInfo = array(
    'ΦΑΣΗ' => "Α - Όμιλοι",
    'Συμμετέχοντες Αθλητές' => count(TTennis::$tournament['athletes']),
    'Βετεράνοι' => $PlayersRegisteredLastGameVet,
    'Ανεξάρτητοι' => count(TTennis::$tournament['athletes']) - $PlayersRegisteredLastGameVet,
    'Αθλητές ανα Όμιλο' => $PlayersGroupNumber,
);

if ($tmp) {  // hdh
    $done = TTennis::TournamentAthletesLoadGroups();
    $displayInfo = array_merge($displayInfo, array('Ημερ. Κλήρωσης ' => $done));
} else {        //  nea dhmioyrgia omafcn
    TTennis::TournamentAthletesGenerateGroups($debugShow, $PlayersGroupNumber);
}

$displayInfo = array_merge($displayInfo, array('Όμιλοι ' => count(TTennis::$tournament['groups'])));

//miniMVController::object(TTennis::$tournament['athletes']); //miniMVController::object(TTennis::$tournament['groups']);  //$PlayersRegisteredLastGame= TTennis::$tournament['athletes'];

TTennis::showMessage("Στατιστικά ", $displayInfo);


foreach (TTennis::$tournament['groups'] as $player) {
    foreach ($player as $pp) {
        TTRanking::$athletesFullnames[$pp['id']] = $pp['fullname'];
    }
}

foreach (TTennis::$tournament['groups'] as $player) {

//    miniMVController::object(TTRanking::$athletesFullnames);
    $dataGames = array(
        $player[0]['id'],
        $player[1]['id']
    );

    $PlayersGroup["athlete1"] = $player[0]['fullname'];
    $PlayersGroupID[0] = $player[0]['id'];                                      // ID Αθλητή σε group
    $PlayersGroup["athlete2"] = $player[1]['fullname'];
    $PlayersGroupID[1] = $player[1]['id'];                                      // ID Αθλητή σε group

    if (isset($player[2])) {
        $dataGames[] = $player[2]['id'];
        $PlayersGroup["athlete3"] = $player[2]['fullname'];
        $PlayersGroupID[2] = $player[2]['id'];                                      // ID Αθλητή σε group

    }

    if (isset($player[3])) {
        $dataGames[] = $player[3]['id'];
        $PlayersGroup["athlete4"] = $player[3]['fullname'];
        $PlayersGroupID[3] = $player[3]['id'];                                      // ID Αθλητή σε group

    }

    $athleteBIS = $PlayersGroupID[3] ?? NULL;
    $data = array(
        'groupNO' => TTennis::$groupLetter,
        'gameSESSION' => TTennis::getSessionGameTitleMD5(),
        'phase' => '1',                                         // 1η φάση - ομιλοι
        'athleteA' => $PlayersGroupID[2],
        'athleteB' => $athleteBIS,
    );

    if (isset($PlayersGroupID[4])) {
        $data['athleteC'] = $PlayersGroupID[4];
        $dataGames[] = $PlayersGroupID[4];
    }

    if (isset($PlayersGroupID[5])) {
        $data['athleteD'] = $PlayersGroupID[5];
        $dataGames[] = $PlayersGroupID[5];
    }

    if (isset($PlayersGroupID[6])) {
        $data['athleteE'] = $PlayersGroupID[6];
        $dataGames[] = $PlayersGroupID[6];
    }

    if (isset($PlayersGroupID[7])) {
        $data['athleteF'] = $PlayersGroupID[7];
        $dataGames[] = $PlayersGroupID[7];
    }

    $tmp = miniMVController::$db->get_row("SELECT * FROM gameGroups WHERE gameSESSION = '" . TTennis::getSessionGameTitleMD5() . "' AND groupNO = " . intval(TTennis::$groupLetter), true);

    if (!$tmp) {
        $insertIdΧ = miniMVController::$db->insertRow('gameGroups', $data);

        // game :: δημιοτυργία τψων παιχνιδιων για όλο τον ομιλο
        for ($i = 0; $i < count($dataGames); $i++) {
            for ($t = $i; $t < count($dataGames); $t++) {
                if ($i != $t) {
                    $data = array(
                        'athleteA' => $dataGames[$i],
                        'athleteB' => $dataGames[$t],
                        'gameGroupsID' => $insertIdΧ,
                        'gameSetttingID' => TTennis::$gameTitleMD5ID,
                        'phase' => $phaseTournament                  // phase 1 - όμιλοι
                    );


                    if (!miniMVController::$db->get_row("SELECT * FROM game WHERE gameGroupsID = '" . $insertId . "' AND athleteA = " . $dataGames[$i] . " AND athleteB =" . $dataGames[$t], true)) {
                        $insertId = miniMVController::$db->insertRow('game', $data);
                        $PlayersGroup["athlete" . ($t + 1) . ($i + 1) . "Link"] = '?page=match/main&id=' . $insertId;           // Όνομα Αθλητή σε group
                        $PlayersGroup["athlete" . ($i + 1) . ($t + 1) . "Link"] = '?page=match/main&id=' . $insertId;           // Όνομα Αθλητή σε group
                        if (!$insertId) {
                            unset($PlayersGroup["athlete" . ($t + 1) . ($i + 1) . "Link"]);
                            unset($PlayersGroup["athlete" . ($i + 1) . ($t + 1) . "Link"]);
                        }
                    }
                }
            }
        }
    }


    // game :: εμφανιση τψων παιχνιδιων για Καταχώρηση
    for ($i = 0; $i < count($dataGames); $i++) {
        for ($t = $i; $t < count($dataGames); $t++) {
            if ($i != $t) {

                $insertId = miniMVController::$db->get_row("SELECT * FROM `game` WHERE cancel=0 AND gameSetttingID=" . TTennis::$gameTitleMD5ID . " AND phase=" . $phaseTournament . " AND athleteA = " . $dataGames[$i] . " AND athleteB =" . $dataGames[$t], true);

                $finalScore = $finalScore2 = $insertId['final'] ?? "";
                $PlayersGroup["athlete" . ($t + 1) . ($i + 1) . "Link"] = $PlayersGroup["athlete" . ($i + 1) . ($t + 1) . "Link"] = "";           // Όνομα Αθλητή σε group

                if ($finalScore) {

                    if ($insertId['winner'] == $PlayersGroupID[0]) {
                        $finalScore = TTRanking::revertScore($finalScore);
                    } else {
                        $finalScore = TTRanking::revertScore($finalScore);
                    }

                } else {
                    // TO DO να μην εμφανιζεται για εισαγωγη ξανα οταν ειναι  φιναλ ???
                    $PlayersGroup["athlete" . ($t + 1) . ($i + 1) . "Link"] = '?page=match/main&id=' . $insertId['id'];           // Όνομα Αθλητή σε group
                    $PlayersGroup["athlete" . ($i + 1) . ($t + 1) . "Link"] = '?page=match/main&id=' . $insertId['id'];           // Όνομα Αθλητή σε group
                }


//                echo "<br/> - $i $t ";
                $PlayersGroup["athlete" . ($t + 1) . ($i + 1)] = $finalScore;
                $PlayersGroup["athlete" . ($i + 1) . ($t + 1)] = $finalScore2;


                if (!$insertId) {
                    unset($PlayersGroup["athlete" . ($t + 1) . ($i + 1) . "Link"]);
                    unset($PlayersGroup["athlete" . ($i + 1) . ($t + 1) . "Link"]);
                }

            }
        }
    }


    $groupStats = TTRanking::getGroupRankingStatistics(intval(TTennis::$groupLetter + 1));


    // HTML generate tables
    $data = array_merge(array(                                                   // athletes
        'groupName' => TTennis::getGroupsLetters(),
        'grooupNO' => $total++,
    ), $PlayersGroup, $groupStats);

//    echo __FILE__. ":".__LINE__;         miniMVController::object($data);

    TTennis::showGroupPhaseA(__DIR__ . "/phaseA.html", $data);              // display HTML groups colored

    echo "<br/>";
}