<?php

/**
 * V3 of quiz. I don't know what happen to 2 previous version
 * @author Aidil
 * @version 0.1-development_phase
 * @package sejarahku
 */
//import some files
require_once('../assets/php/actions.inc.php');
require_once('../assets/php/connection.inc.php');

//start session
session_start();

/**
 * Do some validation
 */

//check if user is logged into server
if (!isset($_SESSION['NoKP']))
    //failed test
    die(alert_user('Sila log masuk untuk menjawab kuiz.') . redirect_to('../auth/signin?redir=' . $_SERVER['REQUEST_URI']));
//pass

//check if quiz id is exist
if (!isset($_GET['IDKuiz']))
    //failed test
    die(alert_user('Sila masukkan quiz id.') . return_to_prev());
//pass

//check if `IDKuiz` is valid number
if (!filter_var($_GET['IDKuiz']) && !is_numeric($_GET['IDKuiz']))
    //failed test
    die(alert_user('IDKuiz tidak sah. Sila cuba lagi') . return_to_prev());
//pass

/**
 * Retrieve data from database using param `IDKuiz`
 */

//check if `IDKuiz` is exist in database
if ($stmt = $condb->prepare('SELECT * FROM kuiz WHERE IDKuiz = ?')) {
    $stmt->bind_param('i', $_GET['IDKuiz']);
    $stmt->execute();
    $stmt->store_result();

    //if IDKuiz exist in database, retrieve the data.
    if (!($stmt->num_rows > 0))
        //failed test
        die(alert_user('IDKuiz tidak sah. Sila cuba lagi') . return_to_prev());
    else {
        //pass
        //data exist
        //bind result to var
        $stmt->bind_result($IDKuiz, $NamaKuiz);
        $stmt->fetch();
        $Kuiz = [
            "IDKuiz"   => $IDKuiz,
            "NamaKuiz" => $NamaKuiz
        ];
        /**
         * Get all Soalan and Jawapan from database
         */
        $Soalan        = []; //container for all Soalan

        $Soalan_query  = "SELECT * FROM soalan WHERE IDKuiz = '$IDKuiz' ORDER BY RAND()"; // select all Soalan base on IDKuiz in random order
        //execute query
        $Soalan_result = $condb->query($Soalan_query);

        //check if $Soalan_result result is > 0
        if ($Soalan_result->num_rows > 0) {
            //pass
            //store all results into $Soalan
            while ($soalan = $Soalan_result->fetch_assoc()) {
                $Jawapan  = [];
                $IDSoalan = $soalan['IDSoalan'];

                $Jawapan_query  = "SELECT * FROM jawapan WHERE IDSoalan = '$IDSoalan' ORDER BY RAND()"; // select all Jawapan to correpondle $soalan in random position
                //excute query
                $Jawapan_result = $condb->query($Jawapan_query);

                //check if there any any result in $Jawapan_result
                if ($Jawapan_result->num_rows > 0) {
                    //pass
                    //sotre all result into $Jawapan
                    while ($jawapan = $Jawapan_result->fetch_assoc()) {
                        //push jawapan into $Jawapan
                        array_push($Jawapan, $jawapan);
                    }
                } else {
                    //failed test
                    die("Soalan {$IDSoalan} tidak mempunyai sebarang jawapan");
                }

                //insert $Jawapan in $soalan
                $soalan['Jawapan'] = $Jawapan;
                // print_r($soalan);
                //push processed result into $Soalan
                array_push($Soalan, $soalan);
            }
        } else
            //failed test
            die('Kuiz ini tidak mempunyai sebarang soalan.');
    }
    $Kuiz["Soalan"] = $Soalan;
} else {
    //failed test
    //server error or smt
    die('Kuiz gagal dimuat naik. Sila cuba lagi.');
}

//next parse collected data into html
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>QUIZ: <?= $Kuiz['NamaKuiz'] ?> :SejarahKu</title>
    <link rel="stylesheet" href="../assets/css/style2.css">
</head>

<body>
    <? //print_r($Kuiz); ?>
    <div class="quiz" id="<?= $Kuiz['IDKuiz'] ?>">

        <form action="jawab_kuiz.php" method="GET">

            <input type="hidden" name="IDKuiz" value="<?= $Kuiz['IDKuiz'] ?>">
            <input type="hidden" name="JumlahSoalan" value="<?= count($Kuiz['Soalan']) ?>">

            <h1 class="quiz-title"><?= $Kuiz['NamaKuiz'] ?></h1>
            <p class="quiz-total-question">Jumlah soalan: <?= count($Kuiz['Soalan']) ?></p>

            <?php
            //parse the retrieved value into HTML
            $index = 0;

            foreach ($Kuiz['Soalan'] as $soalan) {
                $index++;
            ?>
            <input type="hidden" name="IDSoalan-<?= $index ?>" value="<?= $soalan['IDSoalan'] ?>">
            <div class="question" id="<?= $soalan['IDSoalan'] ?>">
                <p class="question-text">
                    <b><?= $index ?>.</b>
                    <?= $soalan['TeksSoalan'] ?>
                </p>
                <div class="question-answers">
                    <?php
                        //parse all jawapan into HTML
                        $JenisSoalan = $soalan['Jenis'];

                        foreach ($soalan['Jawapan'] as $jawapan) {
                            $IDJawapan = $jawapan['IDJawapan'];

                            if ($JenisSoalan == 'scq' || $JenisSoalan == 'mcq') {
                                //do smt with single choice question
                                //do smt with jenis multple choice question
                                $type = 'radio'; // for scq
                                if ($JenisSoalan  == 'mcq')
                                    $type = 'checkbox'; // for mcq
                        ?>
                    <label for="answer-<?= $IDJawapan ?>">
                        <input type="<?= $type ?>"
                            name="IDJawapan-<?= $index ?><?= $JenisSoalan == 'mcq' ? '[]' : '' ?>"
                            value="<?= $IDJawapan ?>" id="answer-<?= $IDJawapan ?>" class="answer-<?= $type ?>"
                            <?= $JenisSoalan == 'mcq' ? '' : 'required' ?>>

                        <span class="answer-text"><?= $jawapan['TeksJawapan'] ?></span>
                    </label>
                    <?php
                            } else if ($JenisSoalan == 'text') {
                                //do smt with jenis text
                            ?>
                    <textarea name="IDJawapan-<?= $index ?>" id="answer-<?= $IDJawapan ?>"
                        placeholder="Tuliskan jawapan anda." required></textarea>
                    <?php
                            }
                        }
                        ?>
                </div>
            </div>
            <?php
            }
            ?>
            <button type="submit" class="submit-btn">Hantar</button>
        </form>
    </div>

</body>

</html>