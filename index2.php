<?php

//Untuk memastikan bahwa setiap sesi web dimulai dari halaman ini
define('validSession', 1);
//Periksa keberadaan file config.php. Jika ada, load file tersebut untuk memasukkan variable konfigurasi umum
if (!file_exists('config.php')) {
    exit();
}

require_once( 'config.php' );
require_once('./function/strip_html_tags.php');
require_once('./function/secureParam.php');
require_once('./class/c_user.php');

session_name("tempSiska");
session_start();

require_once('./function/getUserPrivilege.php');

//Load module yang bersesuaian
if (isset($_GET["page"])) {
    if ($_GET["page"] == 'login_detail') {
        require_once('login_detail.php');
    } else {
        require_once('view/' . substr($_GET["page"] . '.php', 5, strlen($_GET["page"] . '.php') - 5));
    }
}
?>
