<?php
function checkData($log, $pass, $email) {
    return preg_match("/^[a-z0-9-_]{2,20}$/i", $log) && preg_match("/^[a-z0-9-_]{2,20}$/i", $pass) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

if (isset($_GET['key'])) {
    $dat = openssl_decrypt(str_replace(' ', '+', $_GET['key']), 'aes256', 'ebb6da4f35d5c1cc51b0a3b66567bcb5');
    $inf = explode(";;;5", $dat);
    $db = mysqli_connect("localhost", "login", "password", "database");
    $data = mysqli_query($db, "SELECT * FROM `DB` WHERE `Username` = '{$inf[0]}' AND `Email` = '{$inf[1]}'");
    if ($data->num_rows > 0 && checkData($inf[0], $inf[2], $inf[1])) {
        $row = mysqli_fetch_array($data);
        mysqli_query($db, "UPDATE `DB` SET `Password` = '{$inf[2]}' WHERE `Username` = '{$inf[0]}' AND `Email` = '{$inf[1]}'");
        echo 'OK';
    } else
        echo 'INCORRECT'; 
    mysqli_close($db);
}
?>