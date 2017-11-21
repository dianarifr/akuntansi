<?php
//Untuk memastikan bahwa setiap sesi web dimulai dari halaman ini

//ini_set('display_errors', 1);
define('validSession', 1);

//Periksa keberadaan file config.php. Jika ada, load file tersebut untuk memasukkan variable konfigurasi umum
if (!file_exists('config.php')) {
    exit();
}

require_once('config.php' );
require_once('./class/c_user.php');

session_name("tempSiska");
session_start();

require_once('./function/fungsi_menu.php');
require_once('./function/getUserPrivilege.php');
require_once('./function/pagedresults.php');
require_once('./function/secureParam.php');
require_once('./function/fungsi_formatdate.php');
?>


<!DOCTYPE html>
<html>
    <head>
        <!-- <link rel="shortcut icon" href="http://fkg.unair.ac.id/templates/unair-joomla/favicon.gif" type="image/gif" />  -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>PT. LANA GLOBAL INDOTAMA</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="dist/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
        <link rel="stylesheet" href="dist/font-awesome/css/font-awesome.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="dist/select2/select2.min.css">
        <!-- Ionicons -->
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
        <link rel="stylesheet" href="dist/ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/adminlte/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="dist/adminlte/css/skins/_all-skins.min.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="dist/iCheck/all.css">
        <!-- iCheck -->
<!--        <link rel="stylesheet" href="dist/iCheck/flat/blue.css">-->
        <!-- Morris chart -->
        <link rel="stylesheet" href="dist/morris/morris.css">
        <!-- jvectormap -->
        <link rel="stylesheet" href="dist/jvectormap/jquery-jvectormap-1.2.2.css">
        <!-- Date Picker -->
        <link rel="stylesheet" href="dist/datepicker/datepicker3.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="dist/daterangepicker/daterangepicker.css">
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="dist/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

        <!-- jQuery 2.2.3 -->
        <script src="dist/jQuery/jquery-2.2.3.min.js"></script>
        <!-- jQuery UI 1.11.4 -->
<!--        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>-->
        <script src="dist/adminlte/js/jquery-ui.min.js"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button);
        </script>
        <!-- Bootstrap 3.3.6 -->
        <script src="dist/bootstrap/js/bootstrap.min.js"></script>
        <!-- Select2 -->
        <script src="dist/select2/select2.full.min.js"></script>
        <!-- Morris.js charts -->
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>-->
        <!-- <script src="dist/js/raphael-min.js"></script> -->
        <script src="dist/morris/morris.min.js"></script>
        <!-- Sparkline -->
        <script src="dist/sparkline/jquery.sparkline.min.js"></script>
        <!-- jvectormap -->
        <script src="dist/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="dist/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <!-- jQuery Knob Chart -->
        <script src="dist/knob/jquery.knob.js"></script>
        <!-- daterangepicker -->
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
        <script src="dist/adminlte/js/moment.min.js"></script>
        <script src="dist/daterangepicker/daterangepicker.js"></script>
        <!-- datepicker -->
        <script src="dist/datepicker/bootstrap-datepicker.js"></script>
        <script src="dist/datepicker/locales/bootstrap-datepicker.id.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="dist/iCheck/icheck.min.js"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="dist/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
        <!-- Slimscroll -->
        <script src="dist/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="dist/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/adminlte/js/app.min.js"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <!-- <script src="dist/js/pages/dashboard.js"></script> -->
        <!-- AdminLTE for demo purposes -->
        <script src="dist/adminlte/js/demo.js"></script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <?php
    if (isset($_SESSION["my"]) == false || $_GET["page"] == 'login_detail') {
        echo '<body class="hold-transition skin-blue sidebar-mini login-page">';
    } else {
        ?>

        <body class="hold-transition skin-blue sidebar-mini">
            
        
            <?php
        }
        if (isset($_SESSION["my"]) == false || $_GET["page"] == 'login_detail') {
        ?>

        <div class="wrapper" style="background-color:#ECF0F5">
        <?php } else {?>
            <div class="wrapper">
        <?php } ?>
            <header class="main-header">
                <!-- Logo -->
                <?php
                if (isset($_SESSION["my"]) != false && $_GET["page"] != 'login_detail') {
                    ?>
                    <a href="index.php" class="logo">
                        <!-- mini logo for sidebar mini 50x50 pixels -->
                        <!--<span class="logo-mini"><img src="/dist/img/logorcfkg_mini.png" width="100%" align="center"></span>-->
                        <!-- logo for regular state and mobile devices -->
                        <!--<span class="logo-lg"><img src="/dist/img/logorcfkg.png" width="100%" align="center"></span>-->
                    </a>

                    <!-- Header Navbar: style can be found in header.less -->
                    <nav class="navbar navbar-static-top">
                        <!-- Sidebar toggle button-->
                        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                            <span class="sr-only">Toggle navigation</span>
                        </a>

                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <!-- Notifications: style can be found in dropdown.less -->
                                <li class="dropdown notifications-menu">
                                    <?php
                                    //cari data inisiasi, progres, kerjasama dan review terbaru dalam 1 bulan dr tanggal skr
                                    $q_in="SELECT count( `id` ) FROM `inisiasi` WHERE status='Inisiasi' AND datediff( current_date() , `waktuPermintaan` ) <=30 ";
                                    $rs_in = mysql_query($q_in, $dbLink);
                                    $in = mysql_fetch_row($rs_in);
                                    if($in[0]>0){
                                        $dt_in = $in[0];
                                    }else{
                                        $dt_in=0;
                                    }
                                    
                                    $q_pr="SELECT count( `id` ) AS jmlprog FROM `progreswacana` WHERE datediff( current_date() , `waktuProgres` ) <=30 ";
                                    $rs_pr = mysql_query($q_pr, $dbLink);
                                    $pr = mysql_fetch_row($rs_pr);
                                    if($pr[0]>0){
                                        $dt_pr = $pr[0];
                                    }else{
                                        $dt_pr=0;
                                    }
                                    
                                    $q_mou="SELECT count( `id` ) AS jmlmou FROM `penyusunan` WHERE datediff( current_date() , `waktuProgres` ) <=30 
                                        AND jenisDokumen='MOU'";
                                    $rs_mou = mysql_query($q_mou, $dbLink);
                                    $mou = mysql_fetch_row($rs_mou);
                                    if($mou[0]>0){
                                        $dt_mou = $mou[0];
                                    }else{
                                        $dt_mou=0;
                                    }
                                    
                                    $q_loa="SELECT count( `id` ) AS jmlpeny FROM `penyusunan` WHERE datediff( current_date() , `waktuProgres` ) <=30 
                                        AND jenisDokumen='LOA'";
                                    $rs_loa = mysql_query($q_loa, $dbLink);
                                    $loa = mysql_fetch_row($rs_loa);
                                    if($loa[0]>0){
                                        $dt_loa = $loa[0];
                                    }else{
                                        $dt_loa=0;
                                    }
                                    
                                    $q_rev="SELECT count( `id` ) FROM `review` WHERE datediff( current_date() , `waktuBuat` ) <=30 ";
                                    $rs_rev = mysql_query($q_rev, $dbLink);
                                    $rev = mysql_fetch_row($rs_rev);
                                    if($rev[0]>0){
                                        $dt_rev = $rev[0];
                                    }else{
                                        $dt_rev=0;
                                    }
                                    $notif = $dt_in+ $dt_pr + $dt_mou + $dt_loa + $dt_rev;
                                    ?>
                                    
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bell-o" title="Notifikasi"></i>
                                        <span class="label label-warning"><?=$notif; ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="header">Anda memiliki <?=$notif; ?> notifikasi</li>
                                        <li>
                                            <!-- inner menu: contains the actual data -->
                                            <ul class="menu">
                                                <?php if ($dt_in>0) { ?>
                                                <li>
                                                    <a href="index2.php?page=view/inisiasi_list">
                                                        <i class="fa fa-clipboard text-aqua"></i> <?=$dt_in;?> Inisiasi baru
                                                    </a>
                                                </li>
                                                <?php 
                                                }
                                                if ($dt_pr>0) {
                                                ?>
                                                <li>
                                                    <a href="index2.php?page=view/progres_list">
                                                        <i class="fa fa-clock-o text-yellow"></i> <?=$dt_pr;?> Progres Inisiasi baru
                                                    </a>
                                                </li>
                                                <?php 
                                                }
                                                if ($dt_mou>0) {
                                                ?>
                                                <li>
                                                    <a href="index2.php?page=view/kerjasama_list">
                                                        <i class="fa fa-shield text-red"></i> <?=$dt_mou;?> MOU baru
                                                    </a>
                                                </li>
                                                <?php
                                                }
                                                if ($dt_loa>0) {
                                                ?>
                                                <li>
                                                    <a href="index2.php?page=view/review_list">
                                                        <i class="fa fa-shield text-green"></i> <?=$dt_loa;?> LOA baru
                                                    </a>
                                                </li>
                                                <?php
                                                }
                                                if ($dt_rev>0) {
                                                ?>
                                                <li>
                                                    <a href="#">
                                                        <i class="fa fa-balance-scale text-red"></i> <?=$dt_rev;?> Review baru
                                                    </a>
                                                </li>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </li>
<!--                                        <li class="footer"><a href="#">View all</a></li>-->
                                    </ul>
                                </li>
                                
                                <!-- Tasks: style can be found in dropdown.less -->
                                
                                
                                
                                <li class="dropdown tasks-menu">
                                    <?php
                                    $jmlTask="SELECT id, judulKerjasama FROM `inisiasi` WHERE status NOT IN ('Kerjasama', 'Batal')";
                                    $rsTask = mysql_query($jmlTask);
                                    $task = mysql_fetch_row($rsTask);
                                    ?>
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-flag-o" title="Tugas"></i>
                                        <span class="label label-danger"><?= $task[0]; ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="header">Ada <?=$task[0]; ?> tugas</li>
                                        <li>
                                            <ul class="menu">
                                    <?php
                                    //cari data inisiasi
                                    
                                    
                                    $q_in="SELECT id, judulKerjasama, status FROM `inisiasi` WHERE status NOT IN ('Kerjasama', 'Batal') 
                                        ORDER BY waktuPermintaan desc";
                                    $rs_in = mysql_query($q_in, $dbLink);
                                    
                                    while ($in = mysql_fetch_array($rs_in)){
                                        
                                    ?>
                                        <!-- inner menu: contains the actual data -->
                                            <?php
                                            if ($in['status']=='Inisiasi'){
                                                $nilai = "20";
                                                $persen= "20%";
                                                $class = "progress-bar progress-bar-aqua";
                                            }
                                            if ($in['status']=='Draft'){
                                                $nilai = "40";
                                                $persen= "40%";
                                                $class = "progress-bar progress-bar-green";
                                            }
                                            if ($in['status']=='Review Draft'){
                                                $nilai = "60";
                                                $persen= "60%";
                                                $class = "progress-bar progress-bar-red";
                                            }
                                            if ($in['status']=='Revisi Draft'){
                                                $nilai = "80";
                                                $persen= "80%";
                                                $class = "progress-bar progress-bar-yellow";
                                            }
                                            ?>
                                                
                                                <li><!-- Task item -->
                                                    <a href="#">
                                                        <h3>
                                                            <?=substr($in['judulKerjasama'],0,30)."..."; ?>
                                                            <small class="pull-right"><?=$persen; ?></small>
                                                        </h3>
                                                        <div class="progress xs">
                                                            <div class="<?=$class; ?>" style="width: <?=$persen; ?>" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                                <span class="sr-only"><?=$persen; ?> Complete</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <!-- end task item -->
                                                
                                    <?php
                                    }//while tugas
                                ?>
                                                </ul>
                                        </li>
<!--                                        <li class="footer">
                                            <a href="#">View all tasks</a>
                                        </li>-->
                                    </ul>
                                </li>
                                
                                <!-- User Account: style can be found in dropdown.less -->
                                <li class="dropdown user user-menu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <!--            tampilkan nama user di   -->

                                    </a>
                                </li>
                                <!-- Control Sidebar Toggle Button -->
                                <li>
                                    <a href="index.php?page=login_detail&eventCode=20"><i class="fa fa-sign-out" aria-hidden="true" title="Logout"></i></a>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </nav>
            </header>

            <?php
            /* Periksa session $my, jika belum teregistrasi load modul login */

            if (isset($_SESSION["my"]) == false || $_GET["page"] == 'login_detail') {
                require_once('login_detail.php' );
            } else {
//                if (isset($_SESSION["my"]) == false || $_GET["page"] == 'daftar_detail') {
//                    require_once('daftar_detail.php' );
//                }else{
                ?>   

                <!-- Left side column. contains the logo and sidebar -->
                <aside class="main-sidebar">
                    <!-- sidebar: style can be found in sidebar.less -->
                    <section class="sidebar">
                        <!-- Sidebar user panel -->
                        <div class="user-panel">
                            <div class="pull-left image">
                                <!-- <img src="dist/img/<?php //echo $_SESSION["my"]->id; ?>.jpg" class="img-circle" alt="User Image"> -->
                                <img src="dist/adminlte/img/avatar04.png" class="img-circle" alt="User Image">
                            </div>
                            <div class="pull-left info">
                                <p><?php echo $_SESSION["my"]->name; ?></p>
                                <a href="index.php?page=view/ubahProfil_list"><i class="fa fa-circle text-success"></i> Ubah Profil</a>
                            </div>
                        </div>
                        <!-- search form -->
                        <!--      <form action="#" method="get" class="sidebar-form">
                                <div class="input-group">
                                  <input type="text" name="q" class="form-control" placeholder="Search...">
                                      <span class="input-group-btn">
                                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                        </button>
                                      </span>
                                </div>
                              </form>-->
                        <!-- /.search form -->
                        <!-- sidebar menu: : style can be found in sidebar.less -->
                        <ul class="sidebar-menu">
                            <li class="header">MAIN NAVIGATION</li>
                            <li class="treeview">
                                <?php echo menu(); ?>
                            </li>
                        </ul>
                    </section>
                    <!-- /.sidebar -->
                </aside>

                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Pages Content take in here -->
                    <?php
                    //Load module yang bersesuaian
                    if (isset($_GET["page"])) {
                        require_once('view/' . substr($_GET["page"] . ".php", 5, strlen($_GET["page"] . '.php') - 5));
                    } else {
                        // require_once ('view/dashboard.php');
                    }
                    ?>
                    <!-- End Pages Content -->
                </div>
                <!-- content wrapper/.row (main row) -->

                <?php
            }
            ?>
            <!-- /.content-wrapper -->
            <?php
            if (isset($_SESSION["my"]) != false && $_GET["page"] != 'login_detail') {
                ?>
                <footer class="main-footer">

                    <div class="pull-right hidden-xs">
                        <b>V</b> 1.0.0
                    </div>
                    <strong>Copyright &copy; <?= date('Y')." ".$siteTitle; ?></strong> All rights
                    reserved.

                </footer>
                <?php
            }
            ?>

        </div>
        <!-- ./wrapper -->
        
        
    </body>
    
    
</html>
