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
 *    insertAthlete.php :  12/4/25  23:37   -   dsphinx
 *
 */


// AJAX calls
const AJAX = true;
require_once  __DIR__. '/../../App/configuration.php'; //   return   $db


header('Content-Type: application/json');
//
//try {
//
//    $stmt = $db->prepare("INSERT INTO athletes (surname, name, yearDOB, arithmoisdeltiou, email, notes, veteran) VALUES (?, ?, ?, ?, ?, ?, ?)");
//
//    $stmt->execute([
//        $_POST['surname'],
//        $_POST['name'],
//        (int)$_POST['yearDOB'],
//        (int)$_POST['arithmoisdeltiou'],
//        $_POST['email'],
//        $_POST['notes'],
//        (int)$_POST['veteran']
//    ]);
//
//    echo json_encode(['success' => true]);
//} catch (Exception $e) {
//    http_response_code(500);
//    echo json_encode(['error' => $e->getMessage()]);
//}


// insert_batch_athletes.php

$athletes_raw = <<<TXT
ΧΡΗΣΤΟΥ ΚΩΝ/ΝΟΣ
ΓΚΟΓΚΙΔΗΣ ΓΕΩΡΓΙΟΣ
ΣΚΛΙΑΣ ΑΛΕΞΑΝΔΡΟΣ
ΓΟΥΔΗΣ ΝΑΟΥΜ
ΝΤΙΝΑΣ ΝΙΚΟΛΑΟΣ
ΜΑΡΚΟΠΟΥΛΟΣ ΑΝΕΣΤΗΣ
ΤΖΙΟΥΦΑΣ ΚΩΝ/ΝΟΣ
ΧΑΤΖΗΚΥΡΙΑΚΙΔΗΣ ΝΙΚΟΛΑΟΣ
ΠΑΝΟΥΚΙΔΗΣ ΓΕΩΡΓΙΟΣ
ΣΙΩΠΗΣ ΧΡΗΣΤΟΣ
ΨΙΑΝΟΣ ΚΩΝ/ΝΟΣ
ΤΟΠΑΛΗΣ ΕΥΑΓΓΕΛΟΣ
ΜΕΛΙΣΙΔΗΣ ΚΩΝ/ΝΟΣ
ΧΑΤΖΗΣΑΒΒΙΔΗΣ ΣΤΕΛΛΙΟΣ
ΑΡΓΥΡΙΑΔΗΣ ΑΘΑΝΑΣΙΟΣ
ΣΟΥΦΙ ΜΙΛΤΟΣ
ΛΙΟΓΑΣ ΚΩΝ/ΝΟΣ
ΔΗΜΤΣΑΣ ΙΩΑΝΝΗΣ
ΚΑΤΣΟΥΠΑΚΗΣ ΑΝΑΣΤΑΣΙΟΣ
ΘΩΜΟΣ ΓΕΩΡΓΙΟΣ
ΑΝΤΩΝΙΑΔΗΣ ΕΛΕΥΘΕΡΙΟΣ
ΖΙΩΓΑΣ ΚΩΝ/ΝΟΣ
ΔΙΔΑΣΚΑΛΟΥ ΚΩΝ/ΝΟΣ
ΚΑΛΥΒΑΣ ΠΕΤΡΟΣ
ΑΝΑΣΤΑΣΙΑΔΗΣ ΒΕΝΙΑΜΙΝ
ΤΡΑΝΤΑΣ ΝΙΚΟΛΑΟΣ
ΦΑΚΑΛΗΣ ΓΕΩΡΓΙΟΣ
ΚΟΥΚΑΡΟΥΔΗΣ ΒΑΙΟΣ
TXT;

try {
//    $db = new PDO('sqlite:dataInfo.sqlite');
//    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     $db = new sqliteDb($_SESSION['PATHS']['DB']);

    $lines = explode("\n", trim($athletes_raw));
    $inserted = 0;






    foreach ($lines as $i => $line) {
        $parts = explode(' ', trim($line), 2);


        $newPlayer = [
        'yearDOB' => '',
        'arithmoisdeltiou' => '',
        'email' => '',
        'notes' => '',
        'veteran' => 0,
        'surname' => $parts[0],
        'name' =>$parts[1],
    ];

        $insertId = $db->insertRow('athletes', $newPlayer);

     var_dump($newPlayer);
     echo "<br>";
         $inserted++;



    }

    echo "✅ Εισήχθησαν $inserted αθλητές.\n";
} catch (Exception $e) {
    echo "❌ Σφάλμα: " . $e->getMessage();
}
