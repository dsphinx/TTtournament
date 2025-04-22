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
 *    TTennis.php :  13/4/25  09:39   -   dsphinx
 *
 */


class TTennis
{
    static $gameTitleMD5 = ""; // md5
    static $gameTitleMD5ID = ""; // md5
    static $gameTitle = ""; // md5
    static $groupLetter = 0;

    static $tournament = array(
        'athletes' => array(),
        'groups' => array(),                   //************  όμιλοι - φα΄ση Α
    );

    static $groupColor = array(
        '00a2e8',
        '#3AAAE9',
        '#F81700',
        '#C4D79B',
        '#F4F903',
        '#CCC0DA',
        '#DA9694',
        '#1CB950',
        '#f9c001',
        '#B8CCE4',
        '#FDE9D9',
        '#3AAAE9',
        '#F81700',
        '#C4D79B',

    );
    static $groupSettings = array(
        'groupMAXPlayers' => 4,
        'groupPreferPlayers' => 3,
        'colorIndex' => 1
    );

    public static function getSessionGameTitleMD5(): string
    {
        return self::$gameTitleMD5;
    }

    public static function setSessionGameTitleMD5(string $sessionGameTitleMD5): void
    {
        $_SESSION['GAME']['gameTitleMD5'] = $sessionGameTitleMD5;
        self::$gameTitleMD5 = $_SESSION['GAME']['gameTitleMD5'];
    }

    static function getGroupsLetters(int|string $letter = NULL): string
    {
        $ret = NULL;
        if ($letter) {
            if (is_int($letter)) {
                $ret = mb_chr(mb_ord('Α') + $letter--);
            } else {
                $ret = $letter;
            }
        } else {
            $ret = mb_chr(mb_ord('Α') + self::$groupLetter++);
        }

        return $ret;
    }

    public static function TournamentAthletes(array|string $all): void
    {
        if (is_array($all)) {
            self::$tournament['athletes'][] = $all;
        }
    }

    public static function TournamentAthletesSort(): void
    {
        // Κάνουμε sort φθίνοντα (descending) βάσει του scorePOFEPA
        usort(self::$tournament['athletes'], function ($a, $b) {
            return $b['scorePOFEPA'] <=> $a['scorePOFEPA'];
        });
    }


    public static function getDBRow(string $table, int $id): array
    {
        return miniMVController::$db->get_rows("SELECT * FROM $table WHERE `id`=". $id , true);

    }

    public static function TournamentAthletesLoadGroups(): string
    {
        // TODO δεν τρα εφερ ακοαμ η
        $tmp = miniMVController::$db->get_rows("SELECT * FROM gameGroups WHERE  `gameSESSION`= '" . TTennis::getSessionGameTitleMD5() ."'", true);
        $ret =  $tmp[0]['gameDate'];

        foreach ($tmp as $group) {
            $grpID= $group['id'];
            $games = miniMVController::$db->get_rows("SELECT * FROM game WHERE gameGroupsID=". $grpID." AND gameSetttingID='" . self::$gameTitleMD5ID."'" , true);
            $ath =array();
            foreach ($games as $game) {
                $ath[]= $game['athleteA'];
                $ath[]= $game['athleteB'];

                }
            $t = array_unique($ath);

//            miniMVController::object($t);
            $recid=  array() ;
            $ok =0;
            for ($x=0;$x<6;$x++){
                if (isset($t[$x])) {
                $tmp= self::getDBRow('athletesRegisterGame',$t[$x]);
                $grpNO =  $tmp[0]['groupNO'] ?? NULL;
                 $tmp2 =   array(
                        'id' => $tmp[0]['id'],
                        'fullname' => $tmp[0]['surname']." ".$tmp[0]['name'],
                        'scorePOFEPA' => $tmp[0]['scorePOFEPA'],
                        'groupNO' =>$grpNO
                    );
                $recid[$ok++]=$tmp2;
                }
            }


            self::$tournament['groups'][$grpID] = $recid;
        }

        return $ret;
//        miniMVController::object(  self::$tournament['groups']);
    }



    /**
     * @return void
     *
     *    1ος παιχκτης ο καύτερος βαθμολογια και οι υπολοιποi 2 τυχαια
     */
    public static function TournamentAthletesGenerateGroups(bool $debug=false, int $groupMax = 3): void
    {
        self::TournamentAthletesSort();
        // Αντιγραφή του πίνακα για να τραβάμε τυχαίους από τους υπόλοιπους
        $available = self::$tournament['athletes'];// $t['athletes'];
        $groups = [];
        $cx = 0;
        while (count($available) >= $groupMax) {
            // Πάρε τον καλύτερο (πρώτο στοιχείο)
            $top = array_shift($available); // αφαιρεί και επιστρέφει τον πρώτο

            // Πάρε 2 τυχαίους παίκτες από τους υπόλοιπους
            if (count($available) < 2) break;

            $randomKeys = array_rand($available, 2);
            if (!is_array($randomKeys)) {
                $randomKeys = [$randomKeys];
            }

            $randoms = [];
            // Πρέπει να κάνουμε unset και να προσθέσουμε
            foreach ($randomKeys as $key) {
                $randoms[] = $available[$key];
                unset($available[$key]);
            }

            // Αναδιοργάνωσε τα κλειδιά (για array_rand να δουλεύει καλά στον επόμενο γύρο)
            $available = array_values($available);

            // Φτιάξε την ομάδα των 3
            $groups[] = [
                $top,
                $randoms[0],
                $randoms[1]
            ];
            $cx++;
        }


        if ($available) {
            $cx--;
            $top = array_shift($available); // αφαιρεί και επιστρέφει τον πρώτο
            array_push($groups[$cx], $top);
//            echo  "<small>" .__FILE__."  +1 on last game  </small><br/>";
//            var_dump($top);
            if ($available) {  // allo ena
//                echo "<br> add 1 more - ηταν  2 <br/>";
                $cx--;
                $top = array_shift($available); // αφαιρεί και επιστρέφει τον πρώτο
                array_push($groups[$cx], $top);
//                echo  "<small>" .__FILE__."  +1 on pre-last game  </small><br/>";
            }
        }

        if ($available) {
            echo "ERROR: ********** έχει μείνει παίκτης εκτός ομίλων ";
        }

            self::$tournament['groups'] = $groups;          // χωρισμός σε ομαδες

//        miniMVController::object(  self::$tournament['groups']);

        if ($debug) {
            echo "<br/>";
// Εκτύπωση για έλεγχο
            foreach (self::$tournament['groups'] as $i => $group) {
                echo "<br/>ΟΜΑΔΑ " . ($i + 1) . ":\n";
                foreach ($group as $player) {
                    echo "- {$player['fullname']} (score: {$player['scorePOFEPA']})\n";
                }
                echo "\n";
            }
        }
    }


    public function __construct()
    {
        $gameIS = miniMVController::param('game');

        if ($gameIS) {
            $tempRow = miniMVController::$db->get_row("SELECT * FROM gameSetting where `nameSESSION` = '" . $gameIS . "'", TRUE);
//            var_dump($tempRow);
        } else {
            $tempRow = miniMVController::$db->get_row("SELECT * FROM gameSetting order by id desc", TRUE);
            $gameIS = $tempRow['nameSESSION'];
        }

        self::$gameTitle = $tempRow['title'];
        self::$gameTitleMD5ID = $tempRow['id'];
        self::setSessionGameTitleMD5($gameIS);
    }

    public static function showMessage(string $title, string|array $message): void
    {

        if (is_array($message)) {
            $a = $b = "";
            foreach ($message as $t => $p) {
                $a .= "<td> $t </td> ";
                $b .= "<td> $p </td>";
            }
            $htmlData = $a . "</tr> <tr>" . $b . " </tr>";


        } else {
            $htmlData = "   <td>$title</td> <tr> <td>$message</td> </tr>";
        }
        echo $html = '<div class="group-table-container small">
                    <table class="group-table">
                        <tbody><tr class="group-header1">
                     ' . $htmlData . '
                        </tbody> 
                       </table> 
                        </div> <br/>';
    }


    public static function showGroupPhaseA(string $html, array|string $HTMLBlock): void
    {
        $header = file_get_contents($html);
//        $tmp = miniMVController::getBlocks($header);
        $c = self::$groupSettings['colorIndex'];
        $HTMLBlock['groupColor'] = self::$groupColor[$c];
        self::$groupSettings['colorIndex'] = $c + 1;
        foreach ($HTMLBlock as $tag => $val) {
            $header = miniMVController::setBlocks('[@' . $tag . ']', $val, $header);
        }

        // Αφαίρεση όλων των [@...] patterns
        $header = preg_replace('/\[@[^\]]*\]/', ' ', $header);

        miniMVController::echoc($header);
    }
}

$gameSettings = new TTennis();