<?php
require_once("../Auth/Application.php");
function checkData($log, $pass) {
    return (preg_match("/^[a-z0-9-_]{2,20}$/i", $log) || filter_var($log, FILTER_VALIDATE_EMAIL) !== false) && preg_match("/^[a-z0-9-_]{2,20}$/i", $pass);
}

function swap(&$x,&$y) { extract(array('x' => $y, 'y' => $x)); }

if (isset($_GET['login']) && isset($_GET['password']) && isset($_GET['secretKey']) && isset($_GET['newPosition'])) {
    $db = mysqli_connect("localhost", "login", "password", "database");
    $log = $_GET['login'];
    $pass = $_GET['password'];
    $data = mysqli_query($db, "SELECT * FROM `DB` WHERE `Username` = '{$log}' AND `Password` = '{$pass}'");
    if (filter_var($log, FILTER_VALIDATE_EMAIL) !== false && $data->num_rows <= 0)
        $data = mysqli_query($db, "SELECT * FROM `DB` WHERE `Email` = '{$log}' AND `Password` = '{$pass}'");
    if ($data->num_rows > 0 && checkData($log, $pass)) {
        $row = mysqli_fetch_array($data);
        $new = "null";
        if ($row['SecretKeys'] !== "null") {
            $array = json_decode($row['SecretKeys']);
            $newArray = array();
            $i = 0;
            $exists = false;
            foreach ($array as &$app) {
                if ($app->Secret == $_GET['secretKey']) {
                    if ($exists === false) {
                        $exists = $app;
                        $i1 = 0;
                        foreach ($array as &$app1) {
                            if ($i1 == $_GET['newPosition']) {
                                array_push($newArray, $app1);
                                break;
                            }
                            $i1++;
                        }
                    } else
                        array_push($newArray, $exists);
                } else if ($i == $_GET['newPosition']) {
                    if ($exists === false) {
                        $exists = $app;
                        foreach ($array as &$app1) {
                            if ($app1->Secret == $_GET['secretKey']) {
                                array_push($newArray, $app1);
                                break;
                            }
                            $i1++;
                        }
                    } else
                        array_push($newArray, $exists);
                } else
                    array_push($newArray, $app);
                $i++;
            }
            $new = json_encode($newArray);
        }
        echo mysqli_query($db, "UPDATE `DB` SET `SecretKeys` = '{$new}' WHERE `Username` = '{$log}' OR `Email` = '{$log}'") ? "OK" : "ERROR";
    } else
        echo 'INCORRECT';
    mysqli_close($db);
}
?>