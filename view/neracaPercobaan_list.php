<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/neracaPercobaan_list";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Neraca Percobaan';
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
        NERACA PERCOBAAN
        <small>Neraca Percobaan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Laporan</li>
        <li class="active">Neraca Percobaan</li>
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
                    <h3 class="box-title">Kriteria Pencarian Necara Percobaan</h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                            <select name="idperiode" id="idperiode" class="form-control input-sm autoselect"  onKeyPress="return handleEnter(this, event)">
                                <option value="" >Pilih Periode...</option>
                                <?php 
                                $idperiode = isset($_GET["idperiode"])?secureParam($_GET["idperiode"],$dbLink):"";
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
                if($idperiode){
                    $tahun = substr($idperiode, 2, 4);
                    $bulan = substr($idperiode, 0, 2);
                    $arr = $tmp->NeracaPercobaan($idperiode);
                    $arrNeracaPercobaan = $arr[0];
                    $arrTotalNP = $arr[1];
                }
                ?>
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <?php if($idperiode){ ?>
                        <thead>
                            <tr>
                                <th class='text-center' colspan="8">
                                    <h3>NERACA PERCOBAAN</h3><h4>Periode: <?= namaBulan_id($bulan)." ".$tahun;?></h4>
                                </th>
                            </tr>
                            <tr>
                                <th class='text-center' rowspan="2">Kode Akun</th>
                                <th class='text-center' rowspan="2">Nama Akun</th>
                                <th class='text-center' colspan="2">Saldo Awal</th>
                                <th class='text-center' colspan="2">Mutasi</th>
                                <th class='text-center' colspan="2">Saldo Akhir</th>
                            </tr>
                            <tr>
                                <th class='text-center'>Debet</th>
                                <th class='text-center'>Kredit</th>
                                <th class='text-center'>Debet</th>
                                <th class='text-center'>Kredit</th>
                                <th class='text-center'>Debet</th>
                                <th class='text-center'>Kredit</th>
                            </tr>
                        </thead>
                        <?php } ?>
                        <tbody>
                            <?php 
                            if(isset($arrNeracaPercobaan)){
                                foreach ($arrNeracaPercobaan as $key => $nc) {
                                    echo "<tr>";
                                    echo "<td>" . $key . "</td>";
                                    echo "<td>" . $nc["nama"] . "</td>";
                                    echo "<td class='text-right'>" . $tmp->formatNumber($nc["saldoAwalD"]) . "</td>";
                                    echo "<td class='text-right'>" . $tmp->formatNumber($nc["saldoAwalK"]) . "</td>";
                                    echo "<td class='text-right'>" . $tmp->formatNumber($nc["mutasiD"]) . "</td>";
                                    echo "<td class='text-right'>" . $tmp->formatNumber($nc["mutasiK"]) . "</td>";
                                    echo "<td class='text-right'>" . $tmp->formatNumber($nc["saldoAkhirD"]) . "</td>";
                                    echo "<td class='text-right'>" . $tmp->formatNumber($nc["saldoAkhirK"]) . "</td>";
                                    echo("</tr>");
                                }
                                echo "<tr>";
                                echo "<td colspan='2'>&nbsp;</td>";
                                echo "<td class='text-bold text-right'>" . $tmp->formatNumber($arrTotalNP["saldoAwalD"]) . "</td>";
                                echo "<td class='text-bold text-right'>" . $tmp->formatNumber($arrTotalNP["saldoAwalK"]) . "</td>";
                                echo "<td class='text-bold text-right'>" . $tmp->formatNumber($arrTotalNP["mutasiD"]) . "</td>";
                                echo "<td class='text-bold text-right'>" . $tmp->formatNumber($arrTotalNP["mutasiK"]) . "</td>";
                                echo "<td class='text-bold text-right'>" . $tmp->formatNumber($arrTotalNP["saldoAkhirD"]) . "</td>";
                                echo "<td class='text-bold text-right'>" . $tmp->formatNumber($arrTotalNP["saldoAkhirK"]) . "</td>";
                                echo("</tr>");
                            }else{
                                echo("<tr class='even'>");
                                echo ("<td colspan='3' align='center'>Silahkan pilih periode terlebih dahulu.</td>");
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


