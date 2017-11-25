<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/akun_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Akun';
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
            $pesan=$tmp->add($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"]=="Edit")
    {
            $pesan=$tmp->edit($_POST); 
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"]=="Delete")
    {
            $pesan=$tmp->delete($_GET["kodeAkun"]);
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
        PENGATURAN AKUN
        <small>List Akun</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Referensi</li>
        <li class="active">Akun</li>
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
                    <h3 class="box-title">Kriteria Pencarian Akun </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                            <input type="text" class="form-control" name="namaAkun" id="namaAkun" placeholder="Nama Akun..."
                            <?php
                            if ($_GET["namaAkun"]) {
                                echo("value='" . $_GET["namaAkun"] . "'");
                            }
                            ?> onKeyPress="return handleEnter(this, event)">

                            <select name="normal" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0" >Pilih Saldo Normal...</option>
                                <option value="debet" <?= ($_GET["normal"]=="debet"?" selected":"") ?> >Debet</option>
                                <option value="kredit" <?= ($_GET["normal"]=="kredit"?" selected":"") ?> >Kredit</option>
                            </select>
                        
                        <div class="input-group input-group-sm">
                            <select name="posisi" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0" >Pilih Posisi...</option>
                                <option value="neraca" <?= ($_GET["posisi"]=="neraca"?" selected":"") ?> >Neraca</option>
                                <option value="rugilaba" <?= ($_GET["posisi"]=="rugilaba"?" selected":"") ?> >Rugi-Laba</option>
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
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?page=html/akun_detail&mode=add"; ?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
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
                $namaAkun = secureParam($_GET["namaAkun"], $dbLink);
                $normal = secureParam($_GET["normal"], $dbLink);
                $posisi = secureParam($_GET["posisi"], $dbLink);

                //Set Filter berdasarkan query string
                $filter = "";
                if($namaAkun)
                        $filter= $filter." AND nama LIKE '%".$namaAkun."%'";
                if($normal)
                        $filter= $filter." AND normal LIKE '%".$normal."%'";
                if($posisi)
                        $filter= $filter." AND posisi LIKE '%".$posisi."%'";

                //Query
                $q = "SELECT kodeAkun, nama, normal, parentKodeAkun, posisi ";
                $q.= "FROM akun  ";
                $q.= "WHERE 1=1 ".$filter;
                $q.= " ORDER BY kodeAkun";

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
                                <th width="20%" class="sort-alpha">Kode Akun</th>
                                <th width="40%" class="sort-alpha">Nama</th>
                                <th width="20%" class="sort-alpha">Kode Parent Akun</th>
                                <th width="20%" class="sort-alpha">Saldo Normal</th>
                                <th width="40%" class="sort-alpha">Posisi</th>
                                <th colspan="3" width="3%">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . $query_data["kodeAkun"] . "</td>";
                                echo "<td>" . $query_data["nama"] . "</td>";
                                echo "<td>" . $query_data["parentKodeAkun"] . "</td>";
                                echo "<td>" . ucfirst($query_data["normal"]) . "</td>";
                                echo "<td>" . ucfirst($query_data["posisi"]) . "</td>";
                                

                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/akun_detail&mode=edit&kode=" . md5($query_data["kodeAkun"]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                    echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Akun " . $query_data["nama"] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kodeAkun=" . md5($query_data["kodeAkun"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");
                                    
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

