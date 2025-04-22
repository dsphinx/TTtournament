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
 *    initialise.php :  15/4/25  06:33   -   dsphinx
 *
 */

require_once __DIR__.'/../../App/TTennis.php';
require_once __DIR__.'/../../App/TTennisCalls.php';

TTennis::showMessage("Αρχικοποίηση εφαρμογής  ", ' σβήνονται όλοι οι πινακες - όμιλοι - αγωνές - ranking ..');



//exit;

// emtpy TABLE - TRUNCATE

 $emptyTables = array(
     'athletesGames',
     'gameGroups',
     'game',
     'ranking',
     );

echo " <h3> Μηδενισμός  </h3>";

foreach ($emptyTables as $tbl) {
     echo "Άδειασμα πίνακα  $tbl <br>";
     TTennisCalls::truncateSQLTable($tbl);
 }

 //      init

$init = new TTennis();
$gameID = TTennis::getSessionGameTitleMD5();
$players = TTennisCalls::$db->fetchAll("SELECT * FROM athletesAlphabetical ");



echo " <h3> Εισαγωγή τυχαιών αθλητών στο παιχνίδι  </h3>";

foreach ($players as $player) {
//    echo $player['id'] . "<br/>";
    $putAthletesinGameRegister = array(
        'gameID' => TTennis::$gameTitleMD5ID,
        'gameSESSION' => TTennis::getSessionGameTitleMD5(),
        'athleteID' => $player['id']
    );
    $insertId = TTennisCalls::$db->insertRow('athletesGames', $putAthletesinGameRegister);

}


echo " <h3> Εισαγωγή τυχαίων αποτελεσμάτων   </h3> <a target='_vlanks' href='?page=admin/autoGenerateScores' class='btn btn-primary'>Εισαγωγή τυχαίων αποτελεσμάτων στπους αγώνες </a> ";




//var_dump($players);