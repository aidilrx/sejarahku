<?php

/**
 * Display quiz result and some user information
 * @author Aidil
 * @version 0.1-development_phase
 */

//import some file
require_once('../assets/php/actions.inc.php');
require_once('../assets/php/connection.inc.php');

//check if param IDSkor exist
if (!isset($_Get['IDSkor']) && empty($_GET['IDSkor'])) {
    //failed test
    die(alert_user('AKSES TANPA KEBENARAN.') . return_to_prev());
}
//pass

$IDSkor = $_GET['IDSkor'];

//check if param IDSkor exists in database
if ($stmt = $condb->prepare("SELECT * FROM skor_murid WHERE IDSkor = ?")) {
    $stmt->bind_param('i', $IDSkor); //bind param to query
    $stmt->execute(); //execute query
    $stmt->store_result(); //store result query
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($IDSkor, $IDMurid, $IDKuiz, $Skor, $Gred); // store result if any
        $stmt->fetch();
        //store all result into single var
        $Skor = [
            'IDSkor'  => $IDSkor,
            'IDMuird' => $IDMurid,
            'IDKuiz'  => $IDKuiz,
            'Skor'    => $Skor,
            'Gred'    => $Gred
        ];
    } else {
        die(alert_user('Tiada keputusan ditemui. Sila cuba lagi.') . return_to_prev());
    }
} else {
    die('RALAT! Tidak dapat memaparkan keputusan. Sila cuba lagi.');
}

/**
 * Retrieve any other Data
 */
$Data_query = "SELECT sm.*, m.IDMurid, m.NamaMurid, k.*, COUNT(s.IDSoalan) AS JumlahSoalan FROM skor_murid AS sm, murid
                 as M, kuiz AS k, soalan AS s WHERE sm.IDSkor = '$IDSkor' AND sm.IDMurid = m.IDMurid AND sm.IDKuiz = k.IDKuiz 
                 AND s.IDKuiz = k.IDKuiz";
//store all result into $Data
$Data = $condb->query($Data_query)->fetch_assoc();
//formula for correct question
/**
 * p(%) = c(int) / t(int) * 100;
 */
// echo ($Data['JumlahSoalan'] * 100) / $Data['Skor'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Keputusan: <?= $Data['NamaMurid'] ?>(<?= $Data['NamaKuiz'] ?>) :SejarahKu</title>
    <link rel="stylesheet" href="../assets/css/style2.css">
</head>

<body>
    <div class="result">
        <h1 class='main-title'>Keputusan</h1>
        <h2 class="quiz-title"><?= $Data['NamaKuiz'] ?><sub><?= $Data['IDKuiz'] ?></sub></h2>
        <h3 class="namamurid"><?= $Data['NamaMurid'] ?></h3>

        <div class="specific-result">
            <span class="result-row">
                <b>Nama Kuiz: </b>
                <?= $Data['NamaKuiz'] ?>
            </span>
            <span class="result-row">
                <b>Nama Murid:</b>
                <?= $Data['NamaMurid'] ?>
            </span>
            <span class="result-row">
                <b>Gred: </b>
                <?= $Data['Gred'] ?>
            </span>
            <span class="result-row">
                <b>Peratusan: </b>
                <?= $Data['Skor'] ?>%
            </span>
            <span class="result-row">
                <b>Soalan Betul: </b>
                <?= $Data['Skor'] / 100 * $Data['JumlahSoalan'] ?>
                /
                <?= $Data['JumlahSoalan'] ?>
            </span>
        </div>
    </div>
</body>

</html>