#!/usr/bin/php
<?php
require '../vendor/autoload.php';
require 'TTennisCalls.php';

const EXCEL_POFEPA_MONTH_LATEST= __DIR__ . '/Data/pofepa.xlsx';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Σύνδεση με SQLite βάση
// $db = new PDO('sqlite:aitiseis.sqlite'); // προσαρμόζεις το όνομα της βάσης
// $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (!is_file(EXCEL_POFEPA_MONTH_LATEST)) {

    die (" δεν βρεθηκε το αρχειο της ΠΟΦΕΠΑ ... ". EXCEL_POFEPA_MONTH_LATEST);
}


$TT = new TTennisCalls();
$db = new sqliteDb($_SESSION['PATHS']['DB']);

// Διαδρομή στο Excel αρχείο
$inputFileName = EXCEL_POFEPA_MONTH_LATEST; // το Excel που ανέβασες

TTennisCalls::truncateSQLTable('POFEPA_Ranking');
// exit;


// Φόρτωση Excel
$spreadsheet = IOFactory::load($inputFileName);
$sheet = $spreadsheet->getSheetByName('ΓΕΝΙΚΟΣ');
// $sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

// Παράλειψη τίτλων στηλών
array_shift($rows);

// var_dump($rows);
foreach ($rows as $row) {

    $vet= 1;
    if (str_contains($row['4'], 'ΑΝΕ') || str_contains($row['4'], 'ANE')) {
        $vet=0;
    }
    $tmp = explode (" ",$row[2]);
    $e= $tmp[0];
    $o= $tmp[1];

    $newPlayer = [
        'yearDOB' =>  $row[5],
        'DAI' =>  $row[7],
        'score' =>  $row[3],
        'notes' =>  $row[6],
       'veteran' => $vet,
        'surname' => $e,
        'name' => $o,
        'trash' => 0,
        'isPofepaRANK' => 1
    ];

     //   var_dump($newPlayer);
        $insertId = $db->insertRow('POFEPA_Ranking', $newPlayer);

}

// ranking-POFEPA

// // Εντολή εισαγωγής
// $sql = "INSERT INTO aitisi (
//     eponymo, onoma, patronymo, mitronymo,
//     etos_gennisis, dieythinsi, tilefono,
//     email, vathmos
// ) VALUES (
//     :eponymo, :onoma, :patronymo, :mitronymo,
//     :etos_gennisis, :dieythinsi, :tilefono,
//     :email, :vathmos
// )";
// $stmt = $db->prepare($sql);

// // Εισαγωγή δεδομένων
// foreach ($rows as $row) {
//     $stmt->execute([
//         ':eponymo'       => $row[0],
//         ':onoma'         => $row[1],
//         ':patronymo'     => $row[2],
//         ':mitronymo'     => $row[3],
//         ':etos_gennisis' => intval($row[4]),
//         ':dieythinsi'    => $row[5],
//         ':tilefono'      => $row[6],
//         ':email'         => $row[7],
//         ':vathmos'       => floatval($row[8])
//     ]);
// }

echo "✅ Εισαγωγή ολοκληρώθηκε  -- ".count($rows);
