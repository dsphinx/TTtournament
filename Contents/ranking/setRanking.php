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
 *    copyAthleteLocal.php :  16/4/25  06:55   -   dsphinx
 *
 */



// TODO:  να τα βαλω στο ΤΤΡανκινγ yparxei hdh ?


//  completeGamePoint(array $src, int $id): bool

die();

const AJAX = true;
require_once __DIR__ . '/../../App/configuration.php'; //   return   $db
require_once __DIR__ . '/../../App/TTRanking.php'; //   return   $db


$dataSrc = isset($_SESSION) ? [$_GET, $_POST, $_SESSION, $_COOKIE] : [$_GET, $_POST, $_COOKIE];
$src = $dataSrc[0];
header('Content-Type: application/json');


$id = ($src['id']) ?? $src['id'];
$gameID = ($src['gameID']) ?? $src['gameID'];
$dai = ($src['DAI']) ?? $src['DAI'];
$score = ($src['score']) ?? $src['score'];
$scoreOperator = isset($src['operator']) ? $src['operator'] : "+";
$rank = ($src['rank']) ?? $src['rank'];

//
//   Contents/ranking/setRanking.php?id=1&gameID=1&DAI=2073&score=22
//



if ($id && $gameID && $dai && $score) {

    try {
        $db = new sqliteDb($_SESSION['PATHS']['DB']);

        $rankingID = $db->get_row("SELECT * FROM `ranking` WHERE  `DAI`=$dai AND `athleteID`=$id AND `gameID`=$gameID ", true);


        // TODO:  να τα βαλω στο ΤΤΡανκινγ yparxei hdh ?

        if ($rankingID) {

            $scoreIS = $rankingID['score'];
            var_dump($scoreOperator);

            $scoreISA = ($scoreOperator == "+")  ? $scoreIS + $score :  $scoreIS - $score;
            $newPlayer = ['score' => $scoreISA,
                'rank' => $rank];

            echo "<h3>Ο Αθλητής με ΔΑΙ ( $dai  ) έχει ηδη score $scoreIS >> ΝΕΟ ΣΚΟΡ   $scoreISA  </h3>";
            $success = $db->updateRow('ranking', $newPlayer, 'id', $rankingID['id']);

        } else {
            $newPlayer = [
                'athleteID' => $id,
                'DAI' => $dai,
                'rank' => $rank,
                'gameID' => $gameID,
//                'gameSESSION' => $_SESSION[''],
                'score' => $score
            ];

            $insertId = $db->insertRow('ranking', $newPlayer);
        }

    } catch (Exception $e) {
        echo json_encode(array('status' => '0', 'message' => $e->getMessage()));
    }

}
