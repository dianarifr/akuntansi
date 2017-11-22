<?php
/* ==================================================
  //Author  : dianarifr
  //Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/akun_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Akun';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
?>

<!-- Include script date di bawah jika ada field tanggal -->
<link rel="stylesheet" href="dist/iCheck/all.css">
<script src="dist/iCheck/icheck.min.js"></script>
<script src="dist/others/angka.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function () { 
    $(".autoselect").select2();
});
</script>
<!-- End of Script Tanggal -->


<SCRIPT language="JavaScript" TYPE="text/javascript">
function ambilPeriode(counter)
{
    var bulan = $("#cbobulan_"+counter).val();
    var tahun = $("#cbotahun_"+counter).val();
    var idperiode = bulan + tahun;
    $("#txtIdPeriode_"+counter).val(idperiode);
}

function addSaldo() 
{    
    scounter = $("#jumAddSaldo").val();
    $("#jumAddSaldo").val(parseInt($("#jumAddSaldo").val())+1);
    
    <?php 
        $qq = "SELECT SUBSTRING(idperiode,1,2) as bulan, SUBSTRING(idperiode,3,4) as tahun FROM periode ORDER BY idperiode DESC";
        $rss = mysql_query($qq, $dbLink);
        while ($roww = mysql_fetch_array($rss)) {
            $arrBulan[] = $roww['bulan'];
            $arrTahun[] = $roww['tahun'];
        }
        $arTahun = array_unique($arrTahun);
    ?>
    var ttable = document.getElementById("saldo");
    var trow = document.createElement("TR");
                
    //Kolom 1 Checkbox
    var td = document.createElement("TD");
    td.setAttribute("align","center");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input type="checkbox" class="minimal" name="chkAddSaldo_'+scounter+'" id="chkAddSaldo_'+scounter+'" value="1" checked /></div>';
    trow.appendChild(td);
    
    //Kolom 2 Nama bulan
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><select name="cbobulan_'+scounter+'" id="cbobulan_'+scounter+'" class="form-control autoselect" onChange="ambilPeriode('+scounter+')"><?php echo "<option value=\'0\'>-- Pilih Bulan --</option>";foreach ($arrBulan as $key){ $b = str_pad($value["bulan"],2,"0",STR_PAD_LEFT);
                                    echo "              <option value=\'".$key."\'>".namaBulan_id($key)."</option>";
                                                }
                                    echo "      </select></div>"; ?> ';
    trow.appendChild(td);
    
    //Kolom 3 Urutan Biaya
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><select name="cbotahun_'+scounter+'" id="cbotahun_'+scounter+'" class="form-control autoselect" onChange="ambilPeriode('+scounter+')"><?php echo "<option value=\'0\'>-- Pilih Tahun --</option>"; foreach ($arTahun as $key){ $t = $value['tahun'];
                                    echo "              <option value=\'".$key."\'>".$key."</option>";
                                                }
                                    echo "      </select></div>"; ?> ';
    trow.appendChild(td);

    //Kolom 4 Keterangan
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input name="txtNominal_'+scounter+'" id="txtNominal_'+scounter+'" class="form-control" onkeydown="return numbersonly(this, event);" /></div>';
    trow.appendChild(td);

    //Kolom 5 Hidden
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input type="hidden" name="txtIdPeriode_'+scounter+'" id="txtIdPeriode_'+scounter+'" class="form-control" onKeyPress="return handleEnter(this, event);" ></div>';
    trow.appendChild(td);
    
    ttable.appendChild(trow);
}

function validasiForm(form)
{
    //validasi data saldo
    var i=0;
    var tmax=document.getElementById('jumAddSaldo').value;

    for (i=0;i<tmax;i++){
        if(document.getElementById('chkAddSaldo_'+i).checked==true){
            if(document.getElementById('cbobulan_'+i).value=="0"){
                alert("Bulan harus dipilih !");
                document.getElementById("cbobulan_"+i).focus();
                return false;
            }
            if(document.getElementById('cbotahun_'+i).value=="0"){
                alert("Tahun harus dipilih !");
                document.getElementById("cbotahun_"+i).focus();
                return false;
            }
            if(document.getElementById('txtNominal_'+i).value==""){
                document.getElementById("txtNominal_"+i).value=0;
            }
        }
    }

    var i=0;
    var tmax=document.getElementById('jumEditSaldo').value;

    for (i=0;i<tmax;i++){
        if(document.getElementById('chkEdit_'+i).checked==true){
            document.getElementById('cbobulanE_'+i).disabled = false;
            document.getElementById('cbotahunE_'+i).disabled = false;
            if(document.getElementById('cbobulanE_'+i).value=="0"){
                alert("Bulan harus dipilih !");
                document.getElementById("cbobulanE_"+i).focus();
                return false;
            }
            if(document.getElementById('cbotahunE_'+i).value=="0"){
                alert("Tahun harus dipilih !");
                document.getElementById("cbotahunE_"+i).focus();
                return false;
            }
            if(document.getElementById('txtNominalE_'+i).value==""){
                document.getElementById("txtNominalE_"+i).value=0;
            }
        }
    }

    if(form.txtKodeAkun.value=="")
    {
        alert("Kode Akun harus diisi !");
        form.txtKodeAkun.focus();
        return false;
    }
    if(form.txtNama.value=="")
    {
        alert("Nama Akun harus diisi !");
        form.txtNama.focus();
        return false;
    }
    if(form.rdoPosisi.value=="")
    {
        alert("Posisi harus diisi !");
        form.rdoPosisi.focus();
        return false;
    }
    if(form.rdoNormal.value=="")
    {
        alert("Saldo Normal harus diisi !");
        form.rdoNormal.focus();
        return false;
    }
	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        AKUN
        <small>List Akun</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Referensi</li>
        <li class="active">Akun</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <form action="index2.php?page=view/akun_list" method="post" name="frmKotaDetail" onSubmit="return validasiForm(this);"> 
        <div class="row">
            <section class="col-lg-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA AKUN</h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT kodeAkun, nama, normal, parentKodeAkun, posisi ";
                            $q.= "FROM akun WHERE md5(kodeAkun)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($data = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeAkun' value='" . $data["kodeAkun"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA AKUN </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="KodeAkun">Kode Akun</label>
                            
                            <?php if($_GET["mode"]=="edit")
                                    {
                                            echo '<input name="txtKodeAkun" id="txtKodeAkun" size="5" class="form-control text-uppercase" 
                                   placeholder="auto" readonly value="'.$data["kodeAkun"].'" onKeyPress="return handleEnter(this, event)">';
                                    }
                                    else
                                    {
                            ?>                            
                            <input name="txtKodeAkun" id="txtKodeAkun" size="5" class="form-control" 
                                   placeholder="Wajib isi..." onKeyPress="return handleEnter(this, event)">
                            <script language="javascript">
                            $(document).ready(function(){
                                $("#txtKodeAkun").blur(function()
                                {
                                    $("#msgbox").text('Checking...');

                                    $.post("function/ajax_function.php",{ fungsi: "checkKodeAkun", kodeAkun:$("#txtKodeAkun").val() } ,function(data)
                                    {
                                        if(data=='yes') 
                                        {
                                                $("#msgbox").removeClass().addClass('messageboxerror').text('Kode Akun telah ada. Gunakan Kode lain.').fadeIn("slow");
                                        }
                                        else if (data=='no') 
                                        {
                                                $("#msgbox").removeClass().addClass('messageboxok').text('Kode Akun belum tercatat - data baru.').fadeIn("slow");
                                        }
                                        else if (data=='none') 
                                        {
                                            $("#msgbox").removeClass().addClass('messageboxok').text('Kode Akun harus diisi.').fadeIn("slow");
                                        }
                                        else {
                                            $("#msgbox").removeClass().addClass('messageboxerror').text('Maaf, terjadi error pada System').fadeIn("slow");
                                        }
                                    });
                                });
                            });

                            </script>
                            <style type="text/css">
                            .messageboxok{
                                font-weight:bold;
                                color:#008000;
                            }
                            .messageboxerror{
                                font-weight:bold;
                                color:#CC0000;
                            }
                            </style>
                            <span id="msgbox"></span>
                            <?php } ?>

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtNama">Nama Akun</label>
                            <input name="txtNama" id="txtNama" size="35" class="form-control " 
                                   value="<?php echo $data["nama"]; ?>" placeholder="Wajib Isi..." 
                                   onKeyPress="return handleEnter(this, event)">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cboKodeParent">Parent Kode Akun</label>
                            <select name="cboKodeParent" class="form-control autoselect" onKeyPress="return handleEnter(this, event)">
                                <option value="0" >Pilih Parent Kode Akun...</option>
                                <?php
                                $q = "SELECT kodeAkun, nama FROM akun ";
                                if($_GET["mode"]=='edit'){
                                    $q .= " WHERE kodeAkun!='".$data["kodeAkun"]."'";
                                }
                                $q .= " ORDER BY kodeAkun";
                                $rsTemp = mysql_query($q, $dbLink);
                                while($query_data=mysql_fetch_array($rsTemp))
                                {
                                    if($data["parentKodeAkun"]==$query_data["kodeAkun"])
                                            echo("<option value=".$query_data["kodeAkun"]." selected>".$query_data["kodeAkun"]." - ".$query_data["nama"]."</option>");
                                    else
                                            echo("<option value=".$query_data["kodeAkun"].">".$query_data["kodeAkun"]." - ".$query_data["nama"]."</option>");
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="rdoPosisi">Posisi</label>
                            <div class="radio">
                            <label class="radio-inline"><input type="radio" name="rdoPosisi" value="neraca" <?= (!isset($data) or (isset($data) && $data['posisi']) == 'neraca') ? 'checked' : ''; ?>> Neraca
                            </label>
                            <label class="radio-inline"><input type="radio" name="rdoPosisi" value="rugilaba" <?= isset($data)&&$data['posisi'] == 'rugilaba' ? 'checked' : ''; ?>> Rugi Laba
                            </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="rdoNormal">Saldo Normal</label>
                            <div class="radio">
                            <label class="radio-inline"><input type="radio" name="rdoNormal" value="debet" <?= (!isset($data) or (isset($data) && $data['normal']) == 'debet') ? 'checked' : ''; ?>> Debet
                            </label>
                            <label class="radio-inline"><input type="radio" name="rdoNormal" value="kredit" <?= isset($data)&&$data['normal'] == 'kredit' ? 'checked' : ''; ?>> Kredit
                            </label>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">
                        <a href="index.php?page=html/akun_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </div>    
            </section>
            <!-- Data Saldo Awal -->
            <?php 
                $q = "SELECT a.*, CASE WHEN b.parentKodeAkun IS NULL THEN 0 ELSE 1 END as isParent FROM akun a LEFT JOIN akun b on b.parentKodeAkun=a.kodeAkun WHERE a.kodeAkun='".$data["kodeAkun"]."'";
                $dataAkun = mysql_fetch_array(mysql_query($q, $dbLink));
                if(!$dataAkun["isParent"])
                {
            ?>
            <section class="col-lg-6"> 
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">DATA SALDO AWAL </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped table-hover" id="saldo" >
                            <thead>
                                <tr>
                                    <th style="width: 2%"><i class='fa fa-edit'></i></th>
                                    <th style="width: 70%" colspan="2">Periode</th>
                                    <th style="width: 25%">Saldo Awal</th>
                                    <!-- <th style=" width: 2%"><i class='fa fa-trash'></i></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    //ambil data saldo awal
                                    $hide = 0;
                                    $q = "SELECT p.idperiode, pa.KodeAkun, pa.saldoAwal, p.status FROM periodeakun pa JOIN periode p ON pa.idperiode = p.idperiode WHERE pa.KodeAkun = '".$data["kodeAkun"]."' ORDER BY idperiode DESC";
                                    $rs = mysql_query($q, $dbLink);
                                    //ambil data periode
                                    $qq = "SELECT SUBSTRING(idperiode,1,2) as bulan, SUBSTRING(idperiode,3,4) as tahun FROM periode ORDER BY idperiode DESC";
                                    $rss = mysql_query($qq, $dbLink);
                                    while ($roww = mysql_fetch_array($rss)) {
                                        $arrPeriode[] = $roww;
                                    }
                                    $iSaldo = 0;
                                    while ($row = mysql_fetch_array($rs)) {
                                        if($row['status'] == 'tutup')
                                            $hide = 1;
                                        $bulan = str_pad(substr($row["idperiode"],0,2),2,"0",STR_PAD_LEFT);
                                        $tahun = substr($row['idperiode'],2,4);
                                        echo '<tr>';
                                        if(!$hide){
                                        echo '<td>
                                                <div class="form-group">
                                                    <input type="checkbox" class="minimal"  name="chkEdit_' . $iSaldo . '" id="chkEdit_' . $iSaldo . '" value="' . $row["idperiode"] . '" />
                                                </div>
                                            </td>';
                                        }else{
                                            echo '<td>&nbsp;</td>';
                                        }
                                        
                                        echo '<td >
                                                <div class="form-group">
                                                    <select name="cbobulanE_' . $iSaldo . '" id="cbobulanE_' . $iSaldo . '" disabled class="form-control autoselect">';
                                                    foreach ($arrPeriode as $key => $value) {
                                                        $b = str_pad($value['bulan'],2,"0",STR_PAD_LEFT);
                                                        if($bulan == $b)
                                        echo '              <option value="'.$b.'" selected>'.namaBulan_id($value['bulan']).'</option>';
                                                        else
                                        echo '              <option value="'.$b.'">'.namaBulan_id($value['bulan']).'</option>';
                                                    }
                                        echo '      </select>
                                                </div>
                                            </td>';

                                        echo '<td>
                                                <div class="form-group">
                                                    <select name="cbotahunE_' . $iSaldo . '" id="cbotahunE_' . $iSaldo . '" disabled class="form-control autoselect">';
                                                    foreach ($arrPeriode as $key => $value) {
                                                        $t = $value['tahun'];
                                                        if($tahun == $t)
                                        echo '              <option value="'.$t.'" selected>'.$value['tahun'].'</option>';
                                                        else
                                        echo '              <option value="'.$t.'">'.$value['tahun'].'</option>';
                                                    }
                                        echo '      </select>
                                                </div>
                                            </td>';
                                        
                                        echo '<td align="center" valign="top" width=><div class="form-group">
                                            <input type="text" class="form-control"  name="txtNominalE_' . $iSaldo . '" id="txtNominalE_' . $iSaldo . '" value="' . number_format($row["saldoAwal"], 0, ",", ".") . '" '.($hide?"readonly":"").' onkeydown="return numbersonly(this, event);"/></div></td>';

                                        // echo '<td align="center" valign="top"><div class="form-group">
                                        //     <input type="checkbox" class="minimal"  name="chkDel_' . $iSaldo . '" id="chkDel_' . $iSaldo . '" value="' . $row["idperiode"] . '" /></div></td>';
                                        echo '</tr>';
                                        $iSaldo++;
                                    }
                                ?>
                            </tbody>
                        </table>

                        <input type="hidden" value="0" id="jumAddSaldo" name="jumAddSaldo"/>
                        <input type="hidden" value="<?= $iSaldo; ?>" id="jumEditSaldo" name="jumEditSaldo"/>
                        <br />
                        <center>
                            <button type="button" class="btn btn-info" onclick="javascript:addSaldo()">Tambah Saldo Awal</button>
                        </center>
                    </div>
                </div>
            </section>
            <?php } ?>
            <!-- Data Saldo Awal End -->
        </div>
    </form>
</section>
