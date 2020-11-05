<?php

/**
 * Pilih kuiz
 * @author Aidil
 * @version 0.1-development_phase
 * @package sejarahku
 */

//import some files
require_once('../assets/php/actions.inc.php');
require_once('../assets/php/connection.inc.php');

$Kuiz        = []; //container for all quiz

$Kuiz_query  = 'SELECT * FROM kuiz';
//execute query
$Kuiz_result = $condb->query($Kuiz_query);

if ($Kuiz_result->num_rows > 0) {
    //fetch all data into $Kuiz
    while ($kuiz = $Kuiz_result->fetch_assoc()) {
        //push $kuiz into $Kuiz
        array_push($Kuiz, $kuiz);
    }
} else {
    //no result found in database
    die('Tiada kuiz dijumpai dalam database');
}

//parse $Kuiz into HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <title>Pilih kuiz: SejrahKu</title>
</head>

<body>
    <div id="pilih-kuiz">
        <h1>Pilih Kuiz</h1>
        <div id="senarai-kuiz">
            <?php
            foreach ($Kuiz as $kuiz) {
            ?>
            <a href="v3.php?IDKuiz=<?= $kuiz['IDKuiz'] ?>" class="kuiz">
                <span class="nama-kuiz"><?= $kuiz['NamaKuiz'] ?></span>
            </a>
            <?php
            }
            ?>
        </div>
    </div>
</body>

</html>