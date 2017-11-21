<?php

require_once('../config.php' );
require_once('../function/secureParam.php');
switch ($_POST['fungsi']) {
    case "checkKodeMenu":
        $result = mysql_query("select kodeMenu FROM menu WHERE kodeMenu ='" . secureParamAjax($_POST['kodeMenu'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;

    case "checkKodeGroup":
        $result = mysql_query("select kodeGroup FROM groups WHERE kodeGroup ='" . secureParamAjax($_POST['kodeGroup'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;

    case "checkKodeUser":
        $result = mysql_query("select kodeUser FROM user WHERE kodeUser ='" . secureParamAjax($_POST['kodeUser'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;

    case 'checkKodeAkun':
        if ($_POST['kodeAkun'] != "") {
            $result = mysql_query("select kodeAkun from akun where kodeAkun = '". secureParamAjax($_POST['kodeAkun'], $dbLink) ."' ", $dbLink);
            if (mysql_num_rows($result)) {
                echo "yes";
            } else {
                echo "no";
            }
        } else {
            echo "none";
        }
        break;

}
?>
