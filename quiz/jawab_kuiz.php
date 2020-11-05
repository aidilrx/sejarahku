<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses...</title>
</head>

<body>

</body>

</html>
<?php

/**
 * Process all the answered quiz questions
 * @author Aidil
 * @version 0.1-development_phase
 * @package sejarahku
 */
//import some file
require_once('../assets/php/actions.inc.php');
require_once('../assets/php/connection.inc.php');

//start session
session_start();

/**
 * Validation
 */

//check if user is logged in
if (!isset($_SESSION['NoKP']))
    //failed test
    die(alert_user('AKSES TANPA KEBENARAN.') . return_to_prev());
//pass

//check if there any IDKuiz
if (!isset($_GET['IDKuiz']))
    //failed test
    die(alert_user('RALAT!') . return_to_prev());
else {
    //pass
    /**
     * Extract data from parameter
     */
    $Kuiz = [
        "IDKuiz"       => $_GET["IDKuiz"],
        "JumlahSoalan" => $_GET['JumlahSoalan']
    ];
    $Soalan = []; //container for IDSoalan
    $NomborSoalan = (int)1;
    //get all param
    for (; $NomborSoalan <= $_GET['JumlahSoalan']; $NomborSoalan++) {
        $IDSoalan = 'IDSoalan-' . $NomborSoalan;

        //if key exists
        if (array_key_exists($IDSoalan, $_GET)) {
            //key found
            //store in $IDSoalan_
            $IDSoalan_ = [
                "IDSoalan" => $_GET[$IDSoalan],
                "IDJawapan" => []
            ];

            //get the answer
            $IDJawapan = 'IDJawapan-' . $NomborSoalan;

            if (array_key_exists($IDJawapan, $_GET)) {
                //key found
                if (gettype($_GET[$IDJawapan]) === 'string')
                    array_push($IDSoalan_['IDJawapan'], $_GET[$IDJawapan]);
                else
                    foreach ($_GET[$IDJawapan] as $jawapan)
                        array_push($IDSoalan_['IDJawapan'], $jawapan);
            }
            array_push($Soalan, $IDSoalan_);
        }
    }
    $Kuiz["Soalan"] = $Soalan;
    //print_r($IDSoalans);
    //print_r($Kuiz);

    /**
     * Calculate user score
     */
    $SoalanBetul = 0;
    foreach ($Kuiz['Soalan'] as $soalan) {
        //get soalan's jenis
        $IDSoalan = $soalan['IDSoalan'];

        $Soalan_query = "SELECT * FROM soalan WHERE IDSoalan = '$IDSoalan'"; //query
        $Soalan = $condb->query($Soalan_query)->fetch_assoc();
        $JenisSoalan = $Soalan['Jenis'];

        //get all corect answer from table jawapan_soalan
        $Jawapan = [];
        $IDJawapan = [];
        $Jawapan_query = "SELECT * FROM jawapan_soalan WHERE IDSoalan = '$IDSoalan'";
        $Jawapan_result = $condb->query($Jawapan_query);

        if ($Jawapan_result->num_rows > 0) {
            //fetchAll
            while ($jawapan = $Jawapan_result->fetch_assoc()) {
                array_push($Jawapan, $jawapan);
                array_push($IDJawapan, $jawapan['IDJawapan']);
            }
        } else {
            //test failed
            die('Tiada jawapan dengan IDSoalan: ' . $IDSoalan);
        }

        //print_r($soalan);
        //check if the $soalan[IDJawapan] length is equal to $jawapan
        if (count($soalan['IDJawapan']) == count($Jawapan)) {
            //pass

            $SoalanJawapan = $soalan['IDJawapan'];
            if ($JenisSoalan == 'mcq') {

                foreach ($SoalanJawapan as $jawapan) {
                    if (!in_array($jawapan, $IDJawapan)) {
                        //echo 'wrong answer:' . $jawapan;
                        continue;
                    }
                }
                $SoalanBetul++;
            } else if ($JenisSoalan == 'scq') {
                if ($IDJawapan[0] == $soalan['IDJawapan'][0]) {
                    //jawapan is equal to correct jawapan's ID
                    $SoalanBetul++;
                }
                continue;
            } else if ($JenisSoalan == 'text') {
                $IDJawapan = $Jawapan[0]['IDJawapan'];
                if ($condb->query("SELECT TeksJawapan FROM jawapan WHERE IDJawapan = '$IDJawapan'")->fetch_assoc()['TeksJawapan'] == $SoalanJawapan[0]) {
                    //echo 'correct';
                    //jawapan is equal to correct jawapan's text
                    $SoalanBetul++;
                }
                continue;
            }
        } else {
            //test failed
            //continue to next question cuz the length already inequal
            continue;
        }
    }


    /**
     * Store results
     */

    $SkorMurid_query = "INSERT INTO skor_murid(IDMurid, IDKuiz, Skor, Gred) VALUE (?,?,?,?)";
    //do some more processing
    if ($stmt = $condb->prepare($SkorMurid_query)) {
        //more processing b
        //Count Percentage
        $Percentage = $SoalanBetul / $Kuiz['JumlahSoalan'] * 100;

        //count gred
        $Gred = 'G'; //default value

        $GredValue = [
            ['A+', 90, 100],
            ['A',  80, 89],
            ['A-', 70, 79],
            ['B+', 65, 69],
            ['B',  60, 64],
            ['C+', 55, 59],
            ['C',  50, 54],
            ['D',  45, 49],
            ['F',  40, 44],
            ['G',  0,  39]
        ];
        foreach ($GredValue as $gred) {
            if (in_array($Percentage, range($gred[1], $gred[2]))) {
                $Gred = $gred[0];
                break;
            }
        }

        //bind data to query
        $stmt->bind_param('iids', $_SESSION['IDMurid'], $Kuiz['IDKuiz'], $Percentage, $Gred);
        $stmt->execute(); // execute query;
        $stmt->store_result(); // store result

        if ($stmt) {
            //succeed
            //redirect user to result page
            echo redirect_to('keputusan.php?IDSkor=' . $stmt->insert_id);
        } else {
            //failed to insert data
            die(alert_user('Gagal memasukkan markah. Sila cuba lagi') . return_to_prev());
        }
    } else {
        //failed to execute query
        die(alert_user('Gagal memasukkan markah. Sila cuba lagi') . return_to_prev());
    }
}
?>