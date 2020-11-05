<?php

/**
 * Sign up user into server
 * @author Aidil
 * @version 0.1-development_phase
 * @package sejarahku
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //import some file
    require_once('../../assets/php/actions.inc.php');
    require_once('../../assets/php/connection.inc.php');

    $nokp       = $_POST['nokp'];
    $nama       = htmlspecialchars($_POST['nama']); //escape any html string like ', "
    $katalaluan = password_hash($_POST['katalaluan'], PASSWORD_DEFAULT); //hash the password

    /**
     * Filterig and validating some input field
     */
    //check if $nokp is valid number and length == 12
    if (!filter_var($nokp, FILTER_VALIDATE_INT) || strlen($nokp) !== 12)
        //die the script as alert
        die(alert_user('Ralat pada nokp. Sila masukkan nokp dengan benar') . return_to_prev());
    //pass

    //check if $nokp already exists in database
    if ($stmt = $condb->prepare('SELECT NoKP FROM murid WHERE NoKP = ?')) {
        $stmt->bind_param('s', $nokp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            //data exists
            die(alert_user('Nokp sudah wujud dalam database. Sila cuba lagi') . return_to_prev());
        }
        //pass
    }

    /**
     * Deprecated
     */
    // //check if $nama is valid string and use legal characters
    // if (filter_var($nama, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH))
    //     //die the script with alert
    //     die(alert_user('Ralat pada nama. Nama hendaklah menggunakan huruf-huruf ASCII yang sah') . return_to_prev());

    // //check if $katalaluan is valid string and use legal characters
    // if (filter_var($katalaluan, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH))
    //     //die with alert
    //     die(alert_user('Ralat pada katalalaun. Katalaluan hendaklah menggunakan huruf-huruf ASCII yang sah'));


    /**
     * Register user to server
     */
    $query = "INSERT INTO murid(NoKP, NamaMurid, Katalaluan) VALUES (?, ?, ?)";
    if ($stmt = $condb->prepare($query)) {
        $stmt->bind_param('sss', $nokp, $nama, $katalaluan);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt) {
            //success register new data
            echo alert_user('Pendaftaran Berjaya.') . redirect_to('../signin');
        } else {
            //failed to register new data
            die(alert_user('Pendaftaran Gagal. Sila cuba lagi.') . return_to_prev());
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Daftar Baru : SejarahKu</title>
    <link rel="stylesheet" href="../../assets/css/style2.css">
</head>

<body>
    <form action="" method="POST" class="input-form">
        <h1 class="input-title">Daftar Baru</h1>
        <label for="nokp" id="NOKP" class="input-label input-container">
            <span class="input-text">No KP</span>
            <input id="nokp" class="input-field" type="text" name="nokp" placeholder="Masukkan No. KP anda"
                minlength="12" maxlength="13" required>
        </label>

        <label for="nama" id="NAMA" class="input-label input-container">
            <span class="input-text">Nama</span>
            <input id="nama" class="input-field" type="text" name="nama" placeholder="Masukkan nama anda." minlength="5"
                maxlength="255" required>
        </label>

        <label for="katalaluan" id="KATALALUAN" class="input-label input-container">
            <span class="input-text">Katalaluan</span>
            <input id="katalaluan" class="input-field" type="password" name="katalaluan"
                placeholder="Masukkan katalaluan anda." minlength="10" maxlength="255" required>
        </label>
        <button type="submit" class="form-button">Daftar</button>
    </form>
</body>

</html>