<?php
require_once __DIR__ . "/../../App/TTennis.php";
require_once __DIR__ . "/../../App/TTRanking.php";

TTennis::showMessage(" Test data   ", ' Ψευδοτυχαία αποτελέσματα  ..');

//      ?page=admin/autoGenerateScores

$db = miniMVController::$db;

$table = "ranking";
$db->query("DELETE FROM '$table'" );
$db->query("DELETE FROM sqlite_sequence WHERE name = '$table' " );


function generateSetScore(): string
{
    $scoreA = 11 + rand(0, 5); // Κερδισμένοι πόντοι
    $scoreB = $scoreA - 2;

// Για 50% πιθανότητα να κερδίσει ο αντίπαλος
    if (rand(0, 1)) {
        [$scoreA, $scoreB] = [$scoreB, $scoreA];
    }

    return "$scoreA-$scoreB";
}

function simulateMatch(int $athleteA, int $athleteB): array
{
    $sets = [];
    $winsA = 0;
    $winsB = 0;

    while (count($sets) < 5 && $winsA < 3 && $winsB < 3) {
        $score = generateSetScore();
        [$a, $b] = explode('-', $score);

        if ($a > $b) $winsA++;
        else $winsB++;

        $sets[] = $score;
    }

    $winner = $winsA > $winsB ? $athleteA : $athleteB;
    $finalScore = $winsA . '-' . $winsB;

    return [
        'athleteA' => $athleteA,
        'athleteB' => $athleteB,
        'winner' => $winner,
        'sets' => $sets,
        'final' => $finalScore
    ];
}

// --- DB Part (SQLite)

$groupID = 1;
$gameSettingID = 1;
$phase = 1;

$maxSimulateGames = 30;
// Παράδειγμα: Τυχαία 5 παιχνίδια


$tmp = miniMVController::$db->get_rows("SELECT * FROM `game` WHERE cancel=0 AND gameSetttingID=" . TTennis::$gameTitleMD5ID , true);


foreach ($tmp as $rec) {

    $a = $rec['athleteA'];
    $b = $rec['athleteB'];

    $match = simulateMatch($a, $b);

// Πάρε όλες τις ατελείωτες εγγραφές (π.χ. από συγκεκριμένο group αν θες)
//    $results = $db->query("SELECT id, athleteA, athleteB FROM game WHERE   athleteA IS NOT NULL AND athleteB IS NOT NULL");


    $newPlayer = [
        'id' =>  $rec['id'],
        'athleteA' =>  $a,
        'athleteB' =>  $b,
        'winner' => $match['winner'],
        'set1' => $match['sets'][0] ?? 0,
        'set2' => $match['sets'][1] ?? 0,
        'set3' =>$match['sets'][2] ?? 0,
        'set4' => $match['sets'][3] ?? "0-0",
        'set5' =>$match['sets'][4] ?? "0-0",
        'final' => $match['final'],
        'finished' => 1     // complete
    ];

    miniMVController::object($newPlayer);

//    var_dump($newPlayer);
    $r =   TTRanking::completeGamePoint($newPlayer, $rec['id']);



}
echo "Έγιναν   παιχνίδια με τυχαία σετ!\n";
