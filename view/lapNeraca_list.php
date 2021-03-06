<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/lapNeraca_list";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Laporan Neraca';
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
    if($_POST["txtMode"]=="dw"){
        downloadLaporanNeraca($_POST['idperiode']);
        exit();
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

function downloadLaporanNeraca($idperiode)
{
    global $tmp;
    if($idperiode){
        $tahun = substr($idperiode, 2, 4);
        $bulan = substr($idperiode, 0, 2);
        $arr = $tmp->LapNeraca($idperiode);
        $arrNeraca = $arr[0];
        $arrTotalN = $arr[1];

        $jumAktiva = count($arrNeraca['a']);
        $jumPasiva = count($arrNeraca['k']);
        $n = $jumPasiva;
        if($jumAktiva > $jumPasiva)
            $n = $jumAktiva;
    }
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan Neraca Periode ".namaBulan_id($bulan)." ".$tahun.".xls");
    ?>
        <table class="table table-bordered table-striped table-hover" >
            <?php if($idperiode){ ?>
            <thead>
                <tr>
                    <th class='text-center' colspan="5"><h3>LAPORAN NERACA</h3><h4>Periode: <?= namaBulan_id($bulan)." ".$tahun;?></h4></th>
                </tr>
            </thead>
            <?php } ?>
            <tbody>
                <?php 
                if(isset($arrNeraca)){
                    for ($i=0; $i < $n; $i++) { 
                        echo "<tr>";
                        if(isset($arrNeraca['a'][$i])){
                            echo "<td width='32%'>" . $arrNeraca['a'][$i]["nama_rekening"] . "</td>";
                            echo "<td width='15%' class='text-right'>" . $tmp->formatNumber($arrNeraca['a'][$i]["nominal"]) . "</td>";
                        }else{
                            echo "<td width='32%'>&nbsp;</td><td width='15%'>&nbsp;</td>";
                        }
                        echo "<td>&nbsp;</td>";
                        if(isset($arrNeraca['k'][$i])){
                            echo "<td width='32%'>" . $arrNeraca['k'][$i]["nama_rekening"] . "</td>";
                            echo "<td width='15%' class='text-right'>" . $tmp->formatNumber($arrNeraca['k'][$i]["nominal"]) . "</td>";
                        }else{
                            echo "<td width='32%'>&nbsp;</td><td width='15%'>&nbsp;</td>";
                        }
                        echo("</tr>");
                    }

                    echo "<tr>";
                    echo "<td class='text-bold'>TOTAL AKTIVA</td>";
                    echo "<td class='text-right text-bold'>" . $tmp->formatNumber($arrTotalN['a']) . "</td>";
                    echo "<td>&nbsp;</td>";
                        echo "<td class='text-bold'>TOTAL PASIVA</td>";
                    echo "<td class='text-right text-bold'>" . $tmp->formatNumber($arrTotalN['k']) . "</td>";
                    echo("</tr>");
                }else{
                    echo("<tr class='even'>");
                    echo ("<td colspan='5' align='center'>Silahkan pilih periode terlebih dahulu.</td>");
                    echo("</tr>");
                }
                ?>                            
            </tbody>
        </table>
<?php
}
?>
<section class="content-header">
    <h1>
        LAPORAN NERACA
        <small>Laporan Necara</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Laporan</li>
        <li class="active">Laporan Necara</li>
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
                    <h3 class="box-title">Kriteria Pencarian Laporan Necara </h3>
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
                <div class="box-footer clearfix">
                    <?php
                        if ($hakUser==90 && $idperiode){
                    ?>
                    <form action="index2.php?page=view/lapNeraca_list" method="post" name="frmLaporan" >
                        <input type="hidden" name="txtMode" value="dw">
                        <input type="hidden" name="idperiode" value="<?= $idperiode ?>">
                        <button type="submit" class="btn btn-success">download!</button>
                    </form>
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
                if($idperiode){
                    $tahun = substr($idperiode, 2, 4);
                    $bulan = substr($idperiode, 0, 2);
                    $arr = $tmp->LapNeraca($idperiode);
                    $arrNeraca = $arr[0];
                    $arrTotalN = $arr[1];

                    $jumAktiva = count($arrNeraca['a']);
                    $jumPasiva = count($arrNeraca['k']);
                    $n = $jumPasiva;
                    if($jumAktiva > $jumPasiva)
                        $n = $jumAktiva;
                }
                ?>
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <?php if($idperiode){ ?>
                        <thead>
                            <tr>
                                <th class='text-center' colspan="5"><h3>LAPORAN NERACA</h3><h4>Periode: <?= namaBulan_id($bulan)." ".$tahun;?></h4></th>
                            </tr>
                        </thead>
                        <?php } ?>
                        <tbody>
                            <?php 
                            if(isset($arrNeraca)){
                                for ($i=0; $i < $n; $i++) { 
                                    echo "<tr>";
                                    if(isset($arrNeraca['a'][$i])){
                                        echo "<td width='32%'>" . $arrNeraca['a'][$i]["nama_rekening"] . "</td>";
                                        echo "<td width='15%' class='text-right'>" . $tmp->formatNumber($arrNeraca['a'][$i]["nominal"]) . "</td>";
                                    }else{
                                        echo "<td width='32%'>&nbsp;</td><td width='15%'>&nbsp;</td>";
                                    }
                                    echo "<td>&nbsp;</td>";
                                    if(isset($arrNeraca['k'][$i])){
                                        echo "<td width='32%'>" . $arrNeraca['k'][$i]["nama_rekening"] . "</td>";
                                        echo "<td width='15%' class='text-right'>" . $tmp->formatNumber($arrNeraca['k'][$i]["nominal"]) . "</td>";
                                    }else{
                                        echo "<td width='32%'>&nbsp;</td><td width='15%'>&nbsp;</td>";
                                    }
                                    echo("</tr>");
                                }

                                echo "<tr>";
                                echo "<td class='text-bold'>TOTAL AKTIVA</td>";
                                echo "<td class='text-right text-bold'>" . $tmp->formatNumber($arrTotalN['a']) . "</td>";
                                echo "<td>&nbsp;</td>";
                                    echo "<td class='text-bold'>TOTAL PASIVA</td>";
                                echo "<td class='text-right text-bold'>" . $tmp->formatNumber($arrTotalN['k']) . "</td>";
                                echo("</tr>");
                            }else{
                                echo("<tr class='even'>");
                                echo ("<td colspan='5' align='center'>Silahkan pilih periode terlebih dahulu.</td>");
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


