<?php
//Author  : Kristoforus H. Abadi
//Created : 10 Nopember 2016
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Dashboard
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <?php
                    $q_inisiasi = mysql_query("SELECT count(id) FROM inisiasi WHERE status='Inisiasi'", $dbLink);
                    $inisiasi = mysql_fetch_row($q_inisiasi);
                    ?>
                    <h3><?php echo $inisiasi[0]; ?></h3>

                    <p>Info 1</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clipboard"></i>
                </div>
                <a href="index.php?page=view/inisiasi_list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <?php
                    $q_progres = mysql_query("SELECT count(id) FROM progreswacana ", $dbLink);
                    $progres = mysql_fetch_row($q_progres);
                    ?>
                    <h3><?php echo $progres[0]; ?></h3>

                    <p>Info 2</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-timer"></i>
                </div>
                <a href="index.php?page=view/progres_list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <?php
                    $q_kerjasama = mysql_query("SELECT count(id) FROM inisiasi WHERE status='Kerjasama'", $dbLink);
                    $kerjasama = mysql_fetch_row($q_kerjasama);
                    ?>
                    <h3><?php echo $kerjasama[0]; ?></h3>

                    <p>Info 3</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-cog"></i>
                </div>
                <a href="index.php?page=view/kerjasama_list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <?php
                    $q_review = mysql_query("SELECT count(id) FROM review ", $dbLink);
                    $review = mysql_fetch_row($q_review);
                    ?>
                    <h3><?php echo $review[0]; ?></h3>

                    <p>Info 4</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-analytics"></i>
                </div>
                <a href="index.php?page=view/review_list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-6 connectedSortable">

            <!-- TO DO List -->
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>

                    <h3 class="box-title">List 1 </h3>

                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <ul class="todo-list">
                        <?php
                        $q = "SELECT id, waktuPermintaan, judulKerjasama, namaMitra FROM inisiasi 
                                                WHERE status='Inisiasi' order by waktuPermintaan DESC limit 6 ";
                        $q_inisiasi = mysql_query($q, $dbLink);
                        while ($inisiasi = mysql_fetch_array($q_inisiasi)) {
                            ?>
                            <li>
                                <!-- drag handle -->
                                <span class="handle">
                                    <i class="fa fa-ellipsis-v"></i>
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                                <!-- checkbox -->
    <!--                                                <input type="checkbox" value="">-->
                                <!-- todo text -->
                                <span class="text"><?= $inisiasi['judulKerjasama'] . ", " . $inisiasi['namaMitra']; ?></span>
                                <!-- Emphasis label -->
                                <small class="label label-info"><i class="fa fa-clock-o"></i> <?= datetoind($inisiasi['waktuPermintaan']); ?></small>
                                <!-- General tools such as edit or delete-->
                                <!--                                                <div class="tools">
                                                                                    <i class="fa fa-edit"></i>
                                                                                    <i class="fa fa-trash-o"></i>
                                                                                </div>-->
                            </li>
                            <?php
                        }
                        ?>

                    </ul>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix no-border">
<!--                                        <button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>-->
                </div>
            </div>
            <!-- /.box -->
        </section>
        <!-- /.Left col -->

        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-6 connectedSortable">

            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>

                    <h3 class="box-title">List 2 </h3>

                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <ul class="todo-list">
                        <?php
                        $q = "SELECT id, waktuPermintaan, judulKerjasama, namaMitra, tglAkhirKerjasama FROM inisiasi 
                                                WHERE status='Kerjasama' and tglAkhirKerjasama > now() order by tglAkhirKerjasama limit 6 ";
                        $q_kerjasama = mysql_query($q, $dbLink);
                        while ($kerjasama = mysql_fetch_array($q_kerjasama)) {
                            ?>
                            <li>
                                <!-- drag handle -->
                                <span class="handle">
                                    <i class="fa fa-ellipsis-v"></i>
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                               
                                <!-- todo text -->
                                <span class="text"><?= $kerjasama['judulKerjasama'] . ", " . $kerjasama['namaMitra']; ?></span>
                                <!-- Emphasis label -->
                                <small class="label label-danger"><i class="fa fa-clock-o"></i> <?= datetoind($kerjasama['tglAkhirKerjasama']); ?></small>
                                
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix no-border">

                </div>
            </div>
        </section>
        <!-- right col -->
    </div>
    <!-- /.box -->
</section>


