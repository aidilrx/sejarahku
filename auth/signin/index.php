<?php

/**
 * Interface and log in file for user
 * @author Aidil
 * @version 0.1-development_phase
 * @package sejarahku
 */

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //include some file
    require_once('../../assets/php/connection.inc.php');
    require_once('../../assets/php/actions.inc.php');

    //inputs value
    $nokp       = $_POST['nokp'];
    $katalaluan = $_POST['katalaluan'];


    /**
     * Filter? and validating inputs value
     */
    //checks if $nokp is valid number and length ==  12
    if (!filter_var($nokp, FILTER_VALIDATE_INT) || strlen($nokp) !== 12)
        //failed test
        die(alert_user('Ralat pada No. KP. Sila masukkan No. KP dengan benar.') . return_to_prev());
    //pass

    //checks if data is exists in database
    if ($stmt = $condb->prepare('SELECT * FROM murid WHERE NoKP = ?')) {
        $stmt->bind_param('s', $nokp);
        $stmt->execute();
        $stmt->store_result();

        if (!($stmt->num_rows > 0)) {
            //failed test
            die(alert_user('Pengguna tidak wujud. Sila cuba lagi.') . return_to_prev());
        } else {
            //pass

            //store result into vars if succeed
            $stmt->bind_result($IDMurid, $NoKP, $NamaMurid, $Katalaluan);
            $stmt->fetch();

            // echo $Katalaluan;
            //check if $katalaluan is correct with the correspondle $nokp's Katalaluan
            if (!password_verify($katalaluan, $Katalaluan)) {
                //failed test
                // echo password_verify($katalaluan, $Katalaluan);
                die(alert_user('Katalaluan tidak sah. Sila cuba lagi.') . return_to_prev());
            } else {
                //pass
                /**
                 * Log user into server
                 */
                session_regenerate_id();
                $_SESSION['IDMurid'] = $IDMurid;
                $_SESSION['NoKP'] = $NoKP;
                $_SESSION['NamaMurid'] = $NamaMurid;
                //print_r($_SESSION);
                /**
                 * IF param 'redir' exist in $_GET[]
                 */
                if (isset($_GET['redir']))
                    echo redirect_to($_GET['redir']);

                echo redirect_to('../../');
            }
        }
    } else {
        //failed for some server issues
        die(alert_user('Log Masuk Gagal. Sila cuba lagi.') . return_to_prev());
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Log Masuk</title>
    <link rel="stylesheet" href="../../assets/css/style2.css">
</head>

<body>
    <form action="" method="POST" class="input-form">
        <h1 class="input-title">Log Masuk</h1>
        <label for="nokp" id="NOKP" class="input-label input-container">
            <span class="input-text">No KP</span>
            <input type="text" name="nokp" id="nokp" class="input-field" placeholder="Masukkan No. KP anda"
                minlength="12" maxlength="12" required>
        </label>

        <label for="katalaluan" id="KATALALUAN" class="input-label input-container">
            <span class="input-text">Katalaluan</span>
            <input type="password" name="katalaluan" id="katalaluan" class="input-field"
                placeholder="Masukkan katalaluan anda" minlength="12" maxlength="12" required>
        </label>

        <button type="submit" class="form-button">Log Masuk</button>
    </form>
</body>

</html>