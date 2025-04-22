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


const AJAX = true;
require_once __DIR__ . '/../../App/configuration.php'; //   return   $db


header('Content-Type: application/json');


try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $db = new sqliteDb($_SESSION['PATHS']['DB']);


        if (isset($_GET['id']) ) {

            $db = new sqliteDb($_SESSION['PATHS']['DB']);

            $Athlete= $db->get_row("SELECT * FROM `POFEPA_Ranking` WHERE  id = " . intval($_GET['id']), true);

//            var_dump($Athlete);
            $newPlayer = [
                'yearDOB' => $Athlete['yearDOB'],
                'arithmoisdeltiou' => $Athlete['DAI'],
//                'email' => $Athlete['email'],
                'notes' => $Athlete['notes'],
                'veteran' => $Athlete['veteran'],
                'surname' => $Athlete['surname'],
                'name' => $Athlete['name']
            ];

            if ($db->get_row("SELECT * FROM `athletes` WHERE  arithmoisdeltiou = " .  $Athlete['DAI'], true)) {
                echo  "<h3>Ο Αθλητής ".  $Athlete['surname'] ." υπάρχει   </h3>";

            } else {
                $insertId = $db->insertRow('athletes', $newPlayer);

            }



            if ($insertId) {
                echo  "<h3>Ο Αθλητής ".  $Athlete['surname'] ." έχει εισαχθεί ...  </h3>";

            }



        }
    }
} catch (Exception $e) {
    echo json_encode(array('status' => '0', 'message' => $e->getMessage()));
}
