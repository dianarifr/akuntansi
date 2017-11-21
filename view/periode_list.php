<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/periode_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Periode';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_akun.php");
    $tmp=new c_akun();
    //Jika Mode Tambah/Add

    if ($_POST["txtMode"]=="Add")
    {
            $pesan=$tmp->addPeriode($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"]=="Edit")
    {
            $pesan=$tmp->editPeriode($_POST); 
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"]=="Delete")
    {
            $pesan=$tmp->deletePeriode($_GET["idperiode"]);
    }

    //Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
    //Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<section class="content-header">
    <h1>
        PENGATURAN PERIODE
        <small>List Periode</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Referensi</li>
        <li class="active">Periode</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-6">

            <!-- TO DO List -->
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Kriteria Pencarian Periode </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                           

                            <select name="status" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0" >Pilih Status Periode...</option>
                                <option value="aktif" <?= ($_GET["status"]=="aktif"?" selected":"") ?> >Aktif</option>
                                <option value="tutup" <?= ($_GET["status"]=="tutup"?" selected":"") ?> >Tidak Aktif</option>
                            </select>
                        
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                        
                    </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php
                    if ($hakUser == 90) {
                        ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?page=html/periode_detail&mode=add"; ?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <!-- /.box -->
        </section>
        <!-- /.Left col -->
        <!-- right col -->
        <section class="col-lg-6">
            <?php
            //informasi hasil input/update Sukses atau Gagal
            if ($_GET["pesan"] != "") {
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-warning"></i>
                        <h3 class="box-title">Pesan</h3>
                    </div>
                    <div class="box-body">
                        
                        <?php
                            if (substr($_GET["pesan"], 0, 5) == "Gagal") {
                                echo '<div class="callout callout-danger">'. $_GET["pesan"] . '</div>';
                            } else {
                                echo '<div class="callout callout-success">'. $_GET["pesan"] . '</div>';
                            }
                        ?>

                        
                    </div>
                </div>
            <?php } ?>
        </section>
        <!-- /.right col -->

        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <?php
                $status = secureParam($_GET["status"], $dbLink);

                //Set Filter berdasarkan query string
                $filter = "";
                if($status)
                        $filter= $filter." AND status LIKE '%".$status."%'";

                //Query
                $q = "SELECT idperiode, SUBSTRING(idperiode,1,2) as bulan, SUBSTRING(idperiode,3,4) as tahun, status ";
                $q.= "FROM periode  ";
                $q.= "WHERE 1=1 ".$filter;
                $q.= " ORDER BY idperiode";

                //Paging
                $rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?= $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th width="20%" class="sort-alpha">Periode</th>
                                <th width="40%" class="sort-alpha">Status Periode</th>
                                <th colspan="3" width="3%">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . namaBulan_id($query_data['bulan']) . ' ' . $query_data['tahun'] . "</td>";
                                echo "<td>" . $query_data["status"] . "</td>";
                                

                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/periode_detail&mode=edit&kode=" . md5($query_data["idperiode"]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                    echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Periode " . namaBulan_id($query_data['bulan']) . ' ' . $query_data['tahun'] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&idperiode=" . md5($query_data["idperiode"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");
                                    
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>

    </div>
    <!-- /.row -->
</section>

