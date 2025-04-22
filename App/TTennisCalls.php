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
 *    TTennisCalls.php :  13/4/25  12:11   -   dsphinx
 *
 */

const AJAX = true;
require_once __DIR__ . '/configuration.php'; //   return   $db


class TTennisCalls
{
    static $dataIN = array();
    static $db;
    static $config = array();

    public static function getDataIN(): array
    {
        return self::$dataIN;
    }

    public static function setDataIN(array $dataIN): void
    {
        self::$dataIN = $dataIN;
    }


    public function __construct()
    {
        if (isset($_SESSION)) {
            $sources = [$_GET, $_POST, $_SESSION, $_COOKIE];
        } else {
            $sources = [$_GET, $_POST, $_COOKIE];
        }


        // gameTitleMD5
        foreach ($sources as $source) {
            if (isset($source['id']) || isset($source['gameTitleMD5'])) {
                self::setDataIN($source);
            }
        }

        self::$db = new sqliteDb($_SESSION['PATHS']['DB']);
        self::$config['id'] = self::$dataIN['id'] ?? null;
        self::$config['gameTitleMD5'] = self::$dataIN['gameTitleMD5'] ?? null;
        self::$config['action'] = self::$dataIN['action'] ?? null;
        self::$config['tbl'] = self::$dataIN['tbl'] ?? null;

//        var_dump($sources);

        self::manageCalls();
    }

    public static function getRecord(string $table, int $id): array
    {
        $player = NULL;
        try {
            if ($id >= 0) {
                $player = self::$db->get_row("SELECT * FROM $table WHERE id = " . intval($id), true);
            }
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
        return $player;
    }


    public static function truncateSQLTable(string $table):void
    {
        try {
           self::$db->query("DELETE FROM '$table'" );
           self::$db->query("DELETE FROM sqlite_sequence WHERE name = '$table' " );
        } catch (Exception $e) {
           return;
        }

    }


    public static function manageCalls(): void
    {
        $ret = NULL;

        unset(self::$dataIN['action']);
        unset(self::$dataIN['tbl']);
        unset(self::$dataIN['gameTitleMD5']);

        switch (self::$config['action']) {
            case 'get':                     // ανάκληση εγγραφής απο  tbl
                if (self::$config['id'] > 0) {
                    $ret = self::getRecord(self::$config['tbl'], self::$config['id']);
                }
                if ($ret){
                    echo json_encode($ret);
                    exit();
                }
                break;
            case 'update':
                if (self::$config['id'] > 0) {
                    $success = self::$db->updateRow(self::$config['tbl'], self::$dataIN, 'id', self::$config['id']);
                }
                if ($success){
                    echo json_encode(array('status' => '1', 'message' => " Επιτυχής ενημέρωση ID: ".self::$config['id']));
                    exit();
                }
                break;
//            case 'insert':
//                if (self::$config['id'] > 0) {
//                    $success = self::$db->insertRow(self::$config['tbl'], self::$dataIN);
//                }
//                if ($success){
//                    echo json_encode(array('status' => '1', 'message' => " Επιτυχής εισαγωγη ID: ".self::$config['id']));
//                    exit();
//                }
//                break;


            default:
                break;

        }

    }


}

// Json
header('Content-Type: application/json');
//header('Content-Type: text/plain');


$API = new TTennisCalls();
//trigger_error(  print_r( TTennisCalls::$dataIN, true));

//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//
//    $API = new TTennisCalls();
//    //var_dump(TTennisCalls::$config);
//
//} else {
//    $API = new TTennisCalls();
//
//    echo json_encode(array('status' => '0', 'message' => "No form post"));
//}