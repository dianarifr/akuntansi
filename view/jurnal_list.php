<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/jurnal_list";
ini_set('display_errors', 1);
//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Jurnal';
$hakUser = getUserPrivilege($curPage);

require_once("./class/c_akun.php");
$tmp=new c_akun();

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    //Jika Mode Tambah/Add

    if ($_POST["txtMode"]=="Add")
    {
            $pesan=$tmp->addJurnal($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"]=="Edit")
    {
            $pesan=$tmp->editJurnal($_POST); 
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"]=="Delete")
    {
            $pesan=$tmp->deleteJurnal($_GET["idperiode"]);
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
        PENGATURAN JURNAL
        <small>List Jurnal</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Transaksi</li>
        <li class="active">Jurnal</li>
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
                    <h3 class="box-title">Kriteria Pencarian Jurnal </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="noJurnal" id="noJurnal" placeholder="No Jurnal..."
                            <?php
                            if ($_GET["noJurnal"]) {
                                echo("value='" . $_GET["noJurnal"] . "'");
                            }
                            if(!isset($_GET['idperiode'])){
                                $periode = $tmp->getPeriodeAktif();
                            }else{
                                $periode['idperiode'] = secureParam($_GET['idperiode'],$dbLink);
                                $periode['tahun'] = substr($periode['idperiode'], 0, 4);
                                $periode['bulan'] = substr($periode['idperiode'], 4, 2);
                            }
                            ?> onKeyPress="return handleEnter(this, event)">

                            <select name="idperiode" id="idperiode" class="form-control input-sm autoselect"  onKeyPress="return handleEnter(this, event)">
                                <option value="" >Pilih Periode...</option>
                                <?php 
                                $rs = $tmp->getPilihanPeriode();
                                while ($row = mysql_fetch_array($rs)) {
                                    if($periode['idperiode']==$row['idperiode'])
                                        echo("<option value='".$row['idperiode']."' selected>".namaBulan_id($row['bulan'])."</option>");
                                    else
                                        echo("<option value='".$row['idperiode']."'>".namaBulan_id($row['bulan'])."</option>");
                                }
                                ?>
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
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?page=html/jurnal_detail&mode=add"; ?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
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
                $noJurnal = secureParam($_GET["noJurnal"], $dbLink);

                //Set Filter berdasarkan query string
                $filter = "";
                if($noJurnal)
                        $filter= $filter." AND noJurnal LIKE '%".$noJurnal."%'";
                if($periode['idperiode'])
                        $filter=$filter." AND idperiode = '".$periode['idperiode']."'";

                //Query
                $q = "SELECT j.noJurnal, j.keterangan, j.statusPosting, DATE_FORMAT(j.tgl,'%d-%m-%Y') AS tglJurnal, d.total 
                        FROM jurnal j 
                        LEFT JOIN (SELECT noJurnal,SUM(nominal) as total FROM detailJurnal GROUP BY noJurnal,normal) d ON d.noJurnal=j.noJurnal 
                        WHERE 1=1 ".$filter." ORDER BY j.idperiode, j.noJurnal";

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
                                <th style="width: 15%">Tgl</th>
                                <th style="width: 15%">No Jurnal</th>
                                <th style="width: 40%">Keterangan</th>
                                <th style="width: 20%">Nominal</th>
                                <th colspan="2" width="7%">Aksi</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter=1;
                            if ($_GET["resultpage"]>1){
                                    $rowCounter = ($_GET["resultpage"] * 10) - 10 + $rowCounter; //per halaman 10 list
                            }
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . $query_data['tglJurnal'] . "</td>";
                                echo "<td>" . $query_data['noJurnal'] . "</td>";
                                echo "<td>" . $query_data['keterangan'] . "</td>";
                                echo "<td align='right'>" . number_format($query_data['total'],'0',',','.') . "</td>";
                                if ($hakUser == 90) {
                                    if($query_data['statusPosting']==0) {
                                        echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/jurnal_detail&mode=edit&kode=" . md5($query_data['noJurnal']) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                        echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data jurnal nomor `" . $query_data['noJurnal'] . "` ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kode=" . md5($query_data['noJurnal']) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");
                                    }else{
                                        echo "<td><span class='label label-info' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/jurnal_detail&mode=edit&kode=" . md5($query_data['noJurnal']) . "'><i class='fa fa-edit'></i>&nbsp;Detail</span></td>";
                                        echo "<td>&nbsp;</td>";
                                    }
//                                    
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='6' align='center'>Maaf, data tidak ditemukan</td>");
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
<script type="text/javascript">
    $(document).ready(function(){
        $(".autoselect").select2();
    }); 
</script>

