<?php
function checkData($log, $pass) {
    return (preg_match("/^[a-z0-9-_]{2,20}$/i", $log) || filter_var($log, FILTER_VALIDATE_EMAIL) !== false);
}

if (isset($_GET['login'])) {
    $db = mysqli_connect("localhost", "login", "password", "database");
    $log = $_GET['login'];
    $data = mysqli_query($db, "SELECT * FROM `DB` WHERE `Username` = '{$log}'");
    if (filter_var($log, FILTER_VALIDATE_EMAIL) !== false && $data->num_rows <= 0)
        $data = mysqli_query($db, "SELECT * FROM `DB` WHERE `Email` = '{$log}'");
    if ($data->num_rows > 0 && checkData($log, $pass)) {
        $row = mysqli_fetch_array($data);
        echo $row['Email'] == 'null' ? 'NO' : 'YES';
    } else
        echo 'INCORRECT';
    mysqli_close($db);
}
?>