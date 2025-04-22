<?php

setlocale(LC_ALL, 'el_GR.UTF8');
date_default_timezone_set('Europe/Athens');



require __DIR__ . '/../../vendor/autoload.php';
const AJAX = true;
require_once __DIR__ . "/../../App/configuration.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

setlocale(LC_TIME, 'el_GR.UTF-8');
$header = file_get_contents('header.html'); // ή βάλε το $html σαν string


$greekMonths = array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου');
$greekDate = date('j') . ' ' . $greekMonths[intval(date('m'))-1] . ' ' . date('Y');

$phaseID= $_GET['phase'] ?? NULL;
$groupID= $_GET['group'] ?? NULL;
$name= $_GET['name'] ?? NULL;



$db = new sqliteDb($_SESSION['PATHS']['DB']);
$dataALL = "";
$tmp = $db->get_rows("SELECT * FROM gameGroups WHERE  `phase`=" .$phaseID. " ORDER BY gameDate,id asc" , true);


$ret =  $tmp[0]['gameDate'];
foreach ($tmp as $group) {
    $grpID= $group['id'];
    $games = $db->get_rows("SELECT * FROM game WHERE gameGroupsID=". $grpID  , true);

    $ath =array();
    foreach ($games as $game) {

        $tmp=            $db->get_row("SELECT * FROM athletesRegisterGame WHERE `id`=". $game['athleteA'] , true);
        $tmp1=           $db->get_row("SELECT * FROM athletesRegisterGame WHERE `id`=". $game['athleteB'] , true);

//        trigger_error(print_r($tmp,true));
//        trigger_error(print_r($tmp1,true));
        $groupLetter = mb_chr(mb_ord('Α') + $grpID-1);
        $data = array(                                                   // athletes
            'game' => "7ο Ανοιχτό Τουρνουά Βορείου Ελλάδος",
            'gameB' => " 40+",
            'date' => $greekDate,
            'titleA' => $groupLetter,
            'titleAsmall' => "ΟΜΙΛΟΣ",
            'gameType' => "ΦΑΣΗ ΟΜΙΛΩΝ",
            'athleteA' => $tmp['surname']." ".$tmp['name'],
            'athleteB' => $tmp1['surname']." ".$tmp1['name'],
        );

        $html = file_get_contents('matchPrint.html'); // ή βάλε το $html σαν string
        foreach ($data as $tag => $val) {
            $html = str_replace('[@' . $tag . ']', $val, $html);
        }
        $html = preg_replace('/\[@[^\]]*\]/', ' ', $html);
        $dataALL .= $html;

    }

}

$dompdf->loadHtml($header . $dataALL . "</div> </body> </html>", 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("7ο_Τουρνουά_ΦΑ_no".$groupID.".pdf", ["Attachment" => false]); // false = preview, true = download

