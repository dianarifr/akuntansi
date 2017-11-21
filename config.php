<?php 
/*********** Database Settings ***********/
error_reporting(0);
// require_once('./function/mysql.php');

$dbHost = 'localhost';
$dbName = 'akuntansi'; //untuk di server

$dbUser = 'root';
$dbPass = '';

$passSalt = 'UFqPNrZENKSQc5yc';

//Default database link
$dbLink = mysql_connect($dbHost,$dbUser,$dbPass, true)or die('Could not connect: ' . mysql_error());
mysql_query("SET NAMES 'UTF8'");

if(!mysql_select_db($dbName,$dbLink))
{
    die('Database Connection Failed!');
}

/*********** Email Settings ***********/
$mailFrom = 'dian.arifrachman@gmail.com';

$mailSupport = 'dian.arifrachman@gmail.com';

/*********** Display Settings ***********/
$siteTitle = 'PT. LANA GLOBAL INDOTAMA';
$recordPerPage = 10;

$wajibIsiKeterangan ='<font style="color:#FF0000; font-weight:bold">Field Bertanda * Wajib Diisi</font>';
$wajibIsiSimbol = '<font style="color:#FF0000; font-weight:bold">&nbsp;&nbsp;*</font>';
?>