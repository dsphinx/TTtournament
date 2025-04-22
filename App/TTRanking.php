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
 *    TTRanking.php :  17/4/25  08:00   -   dsphinx
 *
 */


class TTRanking
{
    static $db;
    static $gameID;

    static $pointGame = 1;                                  // 1 Βαθμός για κάθε συμμετοχή που έπαιξε
    static $athletesFullnames = array();

    public static function dbOpen(): void
    {
        if (!self::$db) {
            self::$db = new sqliteDb($_SESSION['PATHS']['DB']);

            $tempRow = self::$db->get_row("SELECT * FROM gameSetting order by id desc", TRUE);
            self::$gameID = $tempRow['id'];
        }
    }


    /**
     * @param array $src
     * @return int
     *
     *
     *
     *    ΥΠΟΛΟΓΙΣΜΟΣ ΒΑΘΜΟΩ ΓΙΑ ΚΑΘΕ ΠΑΙΧΝΙΔΙ ΠΟΥ ΕΓΟΝΕ
     */
    public static function completeGamePoint(array $src, int $id): bool
    {

        try {

            self::dbOpen();

            // TODO:  finished  έλεγοχς να μην ξκανα γινει update
//            $gameID = self::$gameID;
//            $rankingID = self::$db->get_row("SELECT * FROM `game` WHERE  `athleteID`=$id AND `gameID`=$gameID ", true);


            $newPlayer = [
                'winner' => $src['winner'],
                'set1' => $src['set1'],
                'set2' => $src['set2'],
                'set3' => $src['set3'],
                'set4' => $src['set4'],
                'set5' => $src['set5'],
                'athleteAPoints' => $src['athleteAPoints'],
                'athleteBPoints' => $src['athleteBPoints'],
                'final' => $src['final'],
                'matchDate' => date('Y-m-d H:i:s'),
                'finished' => 1     // complete
            ];


            $success = self::$db->updateRow('game', $newPlayer, 'id', $id);             //  Καταχώρησε τα αποτελέσματα για το παιχνίδι

            //**
            //      Καταχώρηση ΒΑΘΜΟΛΟΓΙΑΣ
            //
            $points = TTRanking::computeGamePoints($src);               // Πόντους στον ΝΙΚΗΤΗ - όσα τα σετ
            self::setRanking($points['winner'], $points['points']);              //  βάλε Πόντους στον ΝΙΚΗΤΗ - όσα τα σετ

            //  συμμετοχές
            self::setRanking($src['athleteA'], self::$pointGame);              //  πρόσθεση self::$pointGame 1 βαθμός για κάθε συμμετοχή
            self::setRanking($src['athleteB'], self::$pointGame);              //  πρόσθεση self::$pointGame 1 βαθμός για κάθε συμμετοχή
//            echo " Συνολικοι ποα σ amtch ";
//            var_dump($src);

        } catch (Exception $e) {
            echo json_encode(array('status' => '0', 'message' => $e->getMessage()));
            return false;
        }

        return true;
    }


    /**
     * @param array $rec
     * @return array
     *
     *     1 Βαθμός για ΣΕΤ στον νικητή
     */
    public static function computeGamePoints(array $rec): array
    {
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

        $winnerPoints = ($setsWonA > $setsWonB) ? $setsWonA : $setsWonB;
        $loserPoints = ($setsWonA > $setsWonB) ? $setsWonB : $setsWonA;
        $loser = ($rec['winner'] == $rec['athleteA']) ? $rec['athleteB'] : $rec['athleteA'];

//        echo "loser get $loser  poong  ";

        return array(
            'winner' => $rec['winner'],
            'loser' => $loser,
            'points' => $winnerPoints,
            'loserpoints' => $loserPoints);
    }


    public static function revertScore(string $score): string
    {
        $ret = explode("-", $score);
        return $ret[1] . "-" . $ret[0];
    }

    /**
     * @param int $gameGroupsID
     * @param int $phase
     * @return array
     *
     */
    public static function getGroupRankingStatistics(int $gameGroupsID, int $phase = 1): array
    {

        $ret = $info = $htmlArray = [];
        $cx = 1;
        self::dbOpen();
        $gameID = self::$gameID;

        $players = self::$db->get_rows("SELECT * FROM `game` WHERE `finished`=1 AND `gameSetttingID`=$gameID AND `phase`=$phase AND `gameGroupsID`=$gameGroupsID", true);

        if ($players) {
            foreach ($players as $player) {
                $pointGame = TTRanking::computeGamePoints($player);

                $winner = $player['winner'];
                $loser = $pointGame['loser'] ?? null;

                $ret[$winner] = intval($ret[$winner] ?? 0) + intval($pointGame['points'] ?? 0);

                foreach (['athleteA', 'athleteB'] as $athleteKey) {
                    $id = $player[$athleteKey];
                    $ret[$id] = ($ret[$id] ?? 0) + self::$pointGame;
//                    $ret['points'] = ($player['points'] ?? 0) + 1;

                }

                $info[$winner]['wins'] = ($info[$winner]['wins'] ?? 0) + 1;

                if ($loser !== null) {
                    $info[$loser]['losses'] = ($info[$loser]['losses'] ?? 0) + 1;
                }
            }
        }

        // Φτιάχνουμε προσωρινό array για sort
        $rankingData = [];

        foreach ($ret as $athleteID => $score) {
            $rankingData[] = [
                'id' => $athleteID,
                'name' => TTRanking::$athletesFullnames[$athleteID] ?? "Αθλητής $athleteID",
                'score' => $score,
                'wins' => $info[$athleteID]['wins'] ?? 0,
                'loss' => $info[$athleteID]['losses'] ?? 0,
                'points' => $info[$athleteID]['points'] ?? 0,
            ];



        }

        // Sort κατά score, μετά wins
        usort($rankingData, function ($a, $b) {
            // Sort descending by score, then wins
            if ($b['score'] === $a['score']) {
                return $b['wins'] <=> $a['wins'];
            }
            return $b['score'] <=> $a['score'];
        });



        // db
        self::phase_ranking($phase, $gameGroupsID, $rankingData);

        // Δημιουργία του htmlArray με rank
        $rank = 1;
        foreach ($rankingData as $data) {
            $tmp = "athlete" . $cx++;
            $htmlArray[$tmp . "_stats"] = $data['name'];
            $htmlArray[$tmp . "rank_stats"] = $rank++;
            $htmlArray[$tmp . "score_stats"] = $data['score'];
            $htmlArray[$tmp . "wins_stats"] = $data['wins'];
            $htmlArray[$tmp . "lost_stats"] = $data['loss'];
            $htmlArray[$tmp . "points"] = $data['points'];
        }

        return $htmlArray;
    }

    public static function getPlayerPhase_ranking(int $phase, int $gameGroupsID, int $gameID, int $athleteID, int $finished = 0): int
    {
        $ret = 0;
        $table = "ranking_per_phase";
//        try {
//            echo "SELECT id FROM `$table` WHERE `finished`=$finished AND `gameID`=$gameID AND `phase`=$phase AND `athleteID`=$athleteID" ;

            $player = self::$db->get_row("SELECT id FROM `$table` WHERE `groupID`=$gameGroupsID   AND `finished`=$finished AND `gameID`=$gameID AND `phase`=$phase AND `athleteID`=$athleteID", true);

            if ($player) {
                $ret = $player['id'];
            }
//        } catch (Exception $e) {
//            $ret = 0;
//        }

        return $ret;
    }


    public static function phase_ranking(int $phase, int $gameGroupsID, array $data): bool
    {
        $ret = false;
        self::dbOpen();
        $gameID = self::$gameID;

        $rank =1;
        foreach ($data as $player) {

            $playerPhase = self::getPlayerPhase_ranking($phase, $gameGroupsID, $gameID,  $player['id'] , 1);

            if (!$playerPhase) {


                $playerPhaseRank = [
                    'athleteID' => $player['id'],
                    'gameID' => $gameID,
                    'groupID' => $gameGroupsID,
                    'phase' => $phase,
                    'finished' => 1,  // $src['set3'],
                    'phase_score' => $player['score'],
                    'phase_rank' => $rank++,
                    'phase_wins' => $player['wins'],
                    'phase_losses' => $player['loss'],
                ];

//                miniMVController::object($playerPhaseRank);
                $insertId = self::$db->insertRow('ranking_per_phase', $playerPhaseRank);

            }


        }


        return $ret;
    }


    public static function setRanking(int $id, int $score): int
    {
        $ret = 0;

        if ($id > 0) {

            try {
                self::dbOpen();
                $gameID = self::$gameID;
                $rankingID = self::$db->get_row("SELECT * FROM `ranking` WHERE  `athleteID`=$id AND `gameID`=$gameID ", true);

                if ($rankingID) {

                    $scoreIS = $rankingID['score'] + 0;
                    $scoreISA = $scoreIS + $score;
                    $newPlayer = [
                        'score' => $scoreISA,
                    ];
                    $success = self::$db->updateRow('ranking', $newPlayer, 'athleteID', $id);

                } else {

                    $newPlayer = [
                        'athleteID' => $id,
                        'gameID' => $gameID,
                        'score' => $score
                    ];
                    $insertId = self::$db->insertRow('ranking', $newPlayer);

                }
                $ret = $score;

            } catch (Exception $e) {
                echo json_encode(array('status' => '0', 'message' => $e->getMessage()));
            }
        }

        return $ret;
    }


}