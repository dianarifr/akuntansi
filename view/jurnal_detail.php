<?php
/* ==================================================
  //Author  : dianarifr
  //Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/jurnal_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Jurnal';
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
<script src="dist/others/isNumeric.js"></script>
<script src="dist/others/date.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function () { 
    $("#txtTanggal").datepicker({ format: 'dd-mm-yyyy', autoclose:true, endDate: '0d' }); 
    $(".autoselect").select2();
});
</script>
<!-- End of Script Tanggal -->


<SCRIPT language="JavaScript" TYPE="text/javascript">
function hitungTotal(){
    var n = parseInt($('#jumEditJurnal').val());
    var m = parseInt($('#jumAddJurnal').val());
    var totalD = 0;
    var totalK = 0;
    for(i=0;i<n;i++){
        if($("#cboAkunE_"+i).length){
            if($('#txtDebetE_'+i).val()!='')
                totalD += parseInt(removeCommas($('#txtDebetE_'+i).val()));
            if($('#txtKreditE_'+i).val()!='')
                totalK += parseInt(removeCommas($('#txtKreditE_'+i).val()));
        }
    }
    for(i=0;i<m;i++){
        if($("#cboAkun_"+i).length){
            if($('#txtDebet_'+i).val()!='')
                totalD += parseInt(removeCommas($('#txtDebet_'+i).val()));
            if($('#txtKredit_'+i).val()!='')
                totalK += parseInt(removeCommas($('#txtKredit_'+i).val()));
        }
    }


    $('#txtTotalDebit').val(addCommas(totalD));
    $('#txtTotalKredit').val(addCommas(totalK));
}

function removeCommas(nStr) {
    return nStr.replace(/\D/g,'',"");
}

function disableK(n){
    if($('#txtDebetE_'+n).val()!=''){
        $('#txtKreditE_'+n).attr('readonly',true);
        $('#txtKreditE_'+n).val(0);
        var val = $('#txtDebetE_'+n).val();
        var newVal = addCommas(removeCommas(val));
        $('#txtDebetE_'+n).val(newVal);
    }else
        $('#txtKreditE_'+n).attr('readonly',false);
    if(n!='add')
        hitungTotal();
}

function disableD(n){
    if($('#txtKreditE_'+n).val()!=''){
        $('#txtDebetE_'+n).attr('readonly',true);
        $('#txtDebetE_'+n).val(0);
        var val = $('#txtKreditE_'+n).val();
        var newVal = addCommas(removeCommas(val));
        $('#txtKreditE_'+n).val(newVal);
    }else
        $('#txtDebetE_'+n).attr('readonly',false);
    if(n!='add')
        hitungTotal();
}

function AdddisableK(n){
    if($('#txtDebet_'+n).val()!=''){
        $('#txtKredit_'+n).attr('readonly',true);
        $('#txtKredit_'+n).val(0);
        var val = $('#txtDebet_'+n).val();
        var newVal = addCommas(removeCommas(val));
        $('#txtDebet_'+n).val(newVal);
    }else
        $('#txtKredit_'+n).attr('readonly',false);
    if(n!='add')
        hitungTotal();
}

function AdddisableD(n){
    if($('#txtKredit_'+n).val()!=''){
        $('#txtDebet_'+n).attr('readonly',true);
        $('#txtDebet_'+n).val(0);
        var val = $('#txtKredit_'+n).val();
        var newVal = addCommas(removeCommas(val));
        $('#txtKredit_'+n).val(newVal);
    }else
        $('#txtDebet_'+n).attr('readonly',false);
    if(n!='add')
        hitungTotal();
}

function addJurnal() 
{    
    jcounter = $("#jumAddJurnal").val();
    $("#jumAddJurnal").val(parseInt($("#jumAddJurnal").val())+1);
    
    <?php 
        $qq = "SELECT a.nama, a.kodeAkun FROM akun a LEFT JOIN akun b ON a.kodeAkun = b.parentKodeAkun WHERE b.kodeAkun IS NULL order by a.kodeAkun";
        $rsa = mysql_query($qq, $dbLink);
        while ($rowa = mysql_fetch_array($rsa)) {
            $arrAkun[] = $rowa;
        }
    ?>
    var ttable = document.getElementById("jurnal");
    var trow = document.createElement("TR");
                
    //Kolom 1 Checkbox
    var td = document.createElement("TD");
    td.setAttribute("align","center");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_'+jcounter+'" id="chkAddJurnal_'+jcounter+'" value="1" checked /></div>';
    trow.appendChild(td);
    
    //Kolom 2 Nama Akun
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><select name="cboAkun_'+jcounter+'" id="cboAkun_'+jcounter+'" class="form-control autoselect"><?php echo "<option value=\'0\'>Pilih Kode Akun...</option>";foreach ($arrAkun as $key => $val){
                                    echo "              <option value=\'".$val['kodeAkun']."\'>".$val['kodeAkun']." - ".$val['nama']."</option>";
                                                }
                                    echo "      </select></div>"; ?> ';
    trow.appendChild(td);
 
    //Kolom 3 Debet
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input name="txtDebet_'+jcounter+'" id="txtDebet_'+jcounter+'" class="form-control text-right" onkeydown="return numbersonly(this, event);" onKeyUp="AdddisableK('+jcounter+')" /></div>';
    trow.appendChild(td);

    //Kolom 4 Kredit
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input name="txtKredit_'+jcounter+'" id="txtKredit_'+jcounter+'" class="form-control text-right" onkeydown="return numbersonly(this, event);" onKeyUp="AdddisableD('+jcounter+')" /></div>';
    trow.appendChild(td);

    //Kolom 5 Hidden
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input type="hidden" name="txtKodeAkun_'+jcounter+'" id="txtKodeAkun_'+jcounter+'" class="form-control" onKeyPress="return handleEnter(this, event);" ></div>';
    trow.appendChild(td);
    
    ttable.appendChild(trow);
    $('.autoselect').select2();
    hitungTotal()
}

function validasiForm(form)
{
    if(form.txtTanggal.value=="")
    {
        alert("Tanggal harus diisi !");
        form.txtTanggal.focus();
        return false;
    }
    if(form.txtKeterangan.value=="")
    {
        alert("Keterangan harus diisi !");
        form.txtKeterangan.focus();
        return false;
    }

    var totalD = 0;
    var totalK = 0;
    var nominalD = 0;
    var nominalK = 0;
    //validasi data detail jurnal Baru
    var i=0;
    var tmax=document.getElementById('jumAddJurnal').value;

    for (i=0;i<tmax;i++){
        if(document.getElementById('chkAddJurnal_'+i).checked==true){
            if(document.getElementById('cboAkun_'+i).value=="0"){
                alert("Nama Akun harus dipilih !");
                document.getElementById("cboAkun_"+i).focus();
                return false;
            }
            if(document.getElementById('txtDebet_'+i).value==""){
                alert("Nominal Debit harus diisi!");
                document.getElementById("txtDebet_"+i).focus();
                return false;
            }
            if(document.getElementById('txtKredit_'+i).value==""){
                alert("Nominal Kredit harus diisi!");
                document.getElementById("txtKredit_"+i).focus();
                return false;
            }
            nominalD = parseInt(removeCommas($("#txtDebet_"+i).val()));
            nominalK = parseInt(removeCommas($("#txtKredit_"+i).val()));
            totalD += nominalD;
            totalK += nominalK;
        }
    }

    //validasi data detail jurnal Lama
    var i=0;
    var tmax=document.getElementById('jumEditJurnal').value;

    for (i=0;i<tmax;i++){
        if(document.getElementById('chkEdit_'+i).checked==true){
            if(document.getElementById('cboAkunE_'+i).value=="0"){
                alert("Nama Akun harus dipilih !");
                document.getElementById("cboAkunE_"+i).focus();
                return false;
            }
            if(document.getElementById('txtDebetE_'+i).value==""){
                alert("Nominal Debit harus diisi!");
                document.getElementById("txtDebetE_"+i).focus();
                return false;
            }
            if(document.getElementById('txtKreditE_'+i).value==""){
                alert("Nominal Kredit harus diisi!");
                document.getElementById("txtKreditE_"+i).focus();
                return false;
            }
            nominalD = parseInt(removeCommas($("#txtDebetE_"+i).val()));
            nominalK = parseInt(removeCommas($("#txtKreditE_"+i).val()));
            totalD += nominalD;
            totalK += nominalK;
        }
    }

    if(document.getElementById('jumAddJurnal').value==0 || (totalD==0 && totalK==0)){
        alert("Detail Jurnal harus diisi !");
        return false;
    }

    if(totalD!=totalK){
        alert("Total Debit dan Kredit harus sama !");
        return false;
    }

	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        JURNAL
        <small>List Jurnal</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Referensi</li>
        <li class="active">Jurnal</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <form action="index2.php?page=view/jurnal_list" method="post" name="frmKotaDetail" onSubmit="return validasiForm(this);"> 
        <div class="row">
            <section class="col-lg-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA JURNAL</h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT j.noJurnal, j.keterangan, DATE_FORMAT(j.tgl,'%d-%m-%Y') AS tglJurnal
                                    FROM jurnal j ";
                            $q.= " WHERE md5(j.noJurnal)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($data = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='noJurnal' value='" . $data["noJurnal"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA JURNAL </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="txtKodeJurnal">Kode Jurnal</label>
                            
                            <?php if($_GET["mode"]=="edit")
                                    {
                                            echo '<input name="txtKodeJurnal" id="txtKodeJurnal" size="5" class="form-control text-uppercase" 
                                   placeholder="auto" readonly value="'.$data["noJurnal"].'" onKeyPress="return handleEnter(this, event)">';
                                    }
                                    else
                                    {
                            ?>                            
                            <input name="txtKodeJurnal" id="txtKodeJurnal" size="5" class="form-control" 
                                   placeholder="AUTO" onKeyPress="return handleEnter(this, event)" readonly>
                            <?php } ?>

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtTanggal">Tanggal</label>
                            <input name="txtTanggal" id="txtTanggal" size="35" class="form-control " 
                                   value="<?= ($data["tgl"]==''?date("d-m-Y"):datetoind($data["tgl"])) ?>" placeholder="Wajib Isi..." 
                                   onKeyPress="return handleEnter(this, event)">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="txtKeterangan">Parent Kode Jurnal</label>
                            <textarea placeholder="Wajib Isi..." class="form-control" name="txtKeterangan"><?= $data['keterangan'] ?></textarea>
                        </div>

                    </div>
                    
                </div>    
            </section>
            
        </div>
        <div class="row">
            <!-- Data Saldo Awal -->
            <section class="col-lg-12"> 
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">DATA DETAIL JURNAL </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped table-hover" id="jurnal">
                            <thead>
                                <tr>
                                    <th style="width: 2%"><i class='fa fa-edit'></i></th>
                                    <th style="width: 55%">Nama Akun</th>
                                    <th style="width: 20%">Debit</th>
                                    <th style="width: 20%">Kredit</th>
                                    <th style="width: 2%"><i class='fa fa-trash'></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    //ambil data jurnal detail
                                    $iJurnal = $totalD = $totalK = 0;
                                    $q = "SELECT kodeAkun, normal, nominal, nourut FROM detailjurnal WHERE noJurnal = '".$data["noJurnal"]."' ORDER BY noUrut";
                                    $rs = mysql_query($q, $dbLink);
                                    while ($row = mysql_fetch_array($rs)) {
                                        $totalD += ($row["normal"]=="debet"?$row["nominal"]:0);
                                        $totalK += ($row["normal"]=="kredit"?$row["nominal"]:0);
                                        $arrDetJurnal[] = $row;
                                    }
                                    //ambil data akun
                                    $qq = "SELECT a.nama, a.kodeAkun FROM akun a LEFT JOIN akun b ON a.kodeAkun = b.parentKodeAkun WHERE b.kodeAkun IS NULL order by a.kodeAkun";
                                    $rsa = mysql_query($qq, $dbLink);
                                    while ($rowa = mysql_fetch_array($rsa)) {
                                        $arrAkunE[] = $rowa;
                                    }
                                ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td class="text-uppercase text-right"><h4><strong>Total</strong></h4></td>
                                    <td><input type="text" class="form-control text-right" name="txtTotalDebit" id="txtTotalDebit" value="<?= number_format($totalD, 0, ",", ".") ?>" readonly></td>
                                    <td><input type="text" class="form-control text-right" name="txtTotalKredit" id="txtTotalKredit" value="<?= number_format($totalK, 0, ",", ".") ?>" readonly></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php
                                    foreach ($arrDetJurnal as $key => $detJurnal) {
                                        $nominalD = ($detJurnal["normal"]=="debet"?$detJurnal["nominal"]:0);
                                        $nominalK = ($detJurnal["normal"]=="kredit"?$detJurnal["nominal"]:0);
                                        // $totalD += $nominalD;
                                        // $totalK += $nominalK;
                                        echo '<tr>';
                                        echo '<td>
                                                <div class="form-group">
                                                    <input type="checkbox" class="minimal"  name="chkEdit_' . $iJurnal . '" id="chkEdit_' . $iJurnal . '" value="' . $detJurnal["kodeAkun"] . '" />
                                                </div>
                                            </td>';
                                        echo '<td >
                                                <div class="form-group">
                                                    <select name="cboAkunE_' . $iJurnal . '" id="cboAkunE_' . $iJurnal . '" class="form-control autoselect"> <option value="0" >Pilih Kode Akun...</option>"';
                                                    foreach($arrAkunE as $key => $val) {
                                                        if($detJurnal['kodeAkun'] == $val['kodeAkun'])
                                        echo '              <option value="'.$val['kodeAkun'].'" selected>'.$val['kodeAkun'] . ' - '. $val['nama'].'</option>';
                                                        else
                                        echo '              <option value="'.$val['kodeAkun'].'">'.$val['kodeAkun'] . ' - '. $val['nama'].'</option>';
                                                    }
                                        echo '      </select>
                                                </div>
                                            </td>';

                                        echo '<td align="center" valign="top" width=><div class="form-group">
                                            <input type="text" class="form-control text-right" name="txtDebetE_' . $iJurnal . '" id="txtDebetE_' . $iJurnal . '" value="' . number_format($nominalD, 0, ",", ".") . '" '.($nominalD>0?"":"readonly").' onkeydown="return numbersonly(this, event);" onKeyUp="disableK('.$iJurnal.')" /></div></td>';
                                        
                                        echo '<td align="center" valign="top" width=><div class="form-group">
                                            <input type="text" class="form-control text-right"  name="txtKreditE_' . $iJurnal . '" id="txtKreditE_' . $iJurnal . '" value="' . number_format($nominalK, 0, ",", ".") . '" '.($nominalK>0?"":"readonly").' onkeydown="return numbersonly(this, event);" onKeyUp="disableD('.$iJurnal.')" /></div></td>';

                                        echo '<td align="center" valign="top"><div class="form-group">
                                            <input type="checkbox" class="minimal"  name="chkDel_' . $iJurnal . '" id="chkDel_' . $iJurnal . '" value="' . $row["kodeAkun"] . '" /></div></td>';
                                        echo '</tr>';
                                        $iJurnal++;
                                    }
                                ?>
                            </tbody>
                        </table>

                        <input type="hidden" value="0" id="jumAddJurnal" name="jumAddJurnal"/>
                        <input type="hidden" value="<?= $iJurnal; ?>" id="jumEditJurnal" name="jumEditJurnal"/>
                        <br />
                        <center>
                            <button type="button" class="btn btn-info" onclick="javascript:addJurnal()">Tambah Saldo Awal</button>
                        </center>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">
                        <a href="index.php?page=html/jurnal_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </div>
            </section>
            <!-- Data Saldo Awal End -->
        </div>
    </form>
</section>
