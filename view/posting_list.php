<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/posting_list";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Posting';
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

    if ($_POST["btn"]=="posting" || $_POST["btn"]=="postingsemua")
    {
            $pesan=$tmp->savePosting($_POST);
    }

    if ($_POST["btn"]=="batalposting" || $_POST["btn"]=="batalpostingsemua")
    {
            $pesan=$tmp->saveBatalPosting($_POST);
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
        <small>List Posting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Transaksi</li>
        <li class="active">Posting</li>
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
                    <h3 class="box-title">Kriteria Pencarian Posting </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                            <?php
                            $_GET['statusPosting'] = (!isset($_GET['statusPosting'])?0:$_GET['statusPosting']);
                            if(!isset($_GET['idperiode'])){
                                $periode = $tmp->getPeriodeAktif();
                            }else{
                                $periode['idperiode'] = secureParam($_GET['idperiode'],$dbLink);
                                $periode['tahun'] = substr($periode['idperiode'], 2, 4);
                                $periode['bulan'] = substr($periode['idperiode'], 0, 2);
                            }
                            ?>

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
                        
                        <div class="input-group input-group-sm">
                            <select name="statusPosting" class="form-control">
                                <option value="0" <?= ($_GET["statusPosting"]=="0"?" selected":"") ?> >Belum Posting</option>
                                <option value="1" <?= ($_GET["statusPosting"]=="1"?" selected":"") ?> >Sudah Posting</option>
                            </select>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                        
                    </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <form name="frmPosting" id="frmPosting" method="POST" action="index2.php?page=view/posting_list">
                        <input type="hidden" name="idperiode" id="idperiode2" value="<?=$periode['idperiode']?>">
                        <input type="hidden" name="status" id="status2" value="<?=$_GET['status']?>">
                        <input type="hidden" name="namaPeriode" id="namaPeriode" value="<?=namaBulan_id($periode['bulan'])." ".$periode['tahun']?>">
                        <input type="hidden" name="noJurnal" id="noJurnal">
                        <input type="hidden" name="btn" id="btn">
                    <?php
                    if ($hakUser == 90) {
                        if(!isset($_GET["statusPosting"]) || $_GET['statusPosting']=='0'){
                    ?>
                    <div class="pull-right">
                        <button type="button" id="posting" class="btn btn-primary"><i class="fa fa-save"></i> Posting</button>&nbsp;
                        <button type="button" id="postingsemua" class="btn btn-success"><i class="fa fa-save"></i> Posting Semua</button>
                    </div>
                    <?php
                        }elseif (isset($_GET["statusPosting"]) && $_GET['statusPosting']=='1') {
                    ?>
                    <div class="pull-right">
                        <button type="button" id="batalposting" class="btn btn-warning"><i class="fa fa-ban"></i> Batal Posting</button>&nbsp;
                        <button type="button" id="batalpostingsemua" class="btn btn-danger"><i class="fa fa-ban"></i> Batal Posting Semua</button>
                    </div>
                    <?php
                        }
                    }
                    ?>
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
                $statusPosting = secureParam($_GET["statusPosting"], $dbLink);

                //Set Filter berdasarkan query string
                $filter = "";
                $filter= $filter." AND statusPosting ='".$statusPosting."'";
                if($periode['idperiode'])
                        $filter=$filter." AND idperiode = '".$periode['idperiode']."'";

                //Query
                $q = "SELECT j.noJurnal, j.keterangan, j.statusPosting, DATE_FORMAT(j.tgl,'%d-%m-%Y') AS tglPosting, d.total 
                        FROM jurnal j 
                        LEFT JOIN (SELECT noJurnal,SUM(nominal) as total FROM detailjurnal GROUP BY noJurnal) d ON d.noJurnal=j.noJurnal 
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
                                <th style="width: 3%">
                                    <?php if ($hakUser == 90) {
                                        echo '<input type="checkbox" name="chkAll" id="chkAll" onClick="checkAll()">';
                                    }else{
                                        echo '#';
                                    } ?>
                                </th>
                                <th style="width: 15%">Tgl</th>
                                <th style="width: 15%">No Posting</th>
                                <th style="width: 40%">Keterangan</th>
                                <th style="width: 20%">Nominal</th>
                                <th colspan="2" width="7%">Status</th>
                                
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
                                if ($hakUser == 90) {
                                    echo "<td><input type='checkbox' name='chk[]' id='chk_".$rowCounter."' value='".$query_data["noJurnal"]."' onClick='addNoJurnal()'></td>";
                                }else{
                                    echo "<td>".$rowCounter."</td>";
                                }
                                echo "<td>" . $query_data['tglPosting'] . "</td>";
                                echo "<td>" . $query_data['noJurnal'] . "</td>";
                                echo "<td>" . $query_data['keterangan'] . "</td>";
                                echo "<td align='right'>" . number_format($query_data['total'],'0',',','.') . "</td>";
                                if($query_data["statusPosting"]==1){
                                    echo "<td><span class='label label-success'>Telah Diposting</span></td>";
                                }else{
                                    echo "<td><span class='label label-danger'>Belum Diposting</span></td>";
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
        function checkAll(){
        if($('#chkAll').is(":checked")){
            $('input[id^="chk_"]').prop('checked',true);
        }else{
            $('input[id^="chk_"]').prop('checked',false);
        }
        addNoJurnal();
    }

    function addNoJurnal(){
        var str = '';
        $('input[id^="chk_"]').each(function(){
            if($(this).is(":checked")){
                str += $(this).val()+',';
            }
        });
        str = str.substring(0, str.length - 1);
        $("#noJurnal").val(str);
    }

    $("#posting, #postingsemua, #batalposting, #batalpostingsemua").click(function(e) {
        var id = $(this).prop('id');
        var r = false;
        if(id=='posting' || id=='batalposting'){
            if($("#noJurnal").val()==''){
                alert("Pilih data jurnal terlebih dahulu!")
            }else{
                r = true;
            }
        }else{
            // if($("#bulan").val()=='' || $("#tahun").val()==''){
            if($("#idperiode").val()==''){
                alert("Pilih periode terlebih dahulu!")
            }else{
                if(id=='postingsemua'){
                    r = confirm("Apakah Anda yakin akan memposting semua jurnal periode "+$("#namaPeriode").val()+"?");
                }else{
                    r = confirm("Apakah Anda yakin akan membatalkan posting semua jurnal periode "+$("#namaPeriode").val()+"?");
                }
            }           
        }
        if(r){
            $("#btn").val(id);
            $("#frmPosting").submit();
        }        
    });
</script>

