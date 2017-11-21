<?php
/* ==================================================
  //Author  : dianarifr
  //Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/periode_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Periode';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
?>
<script type="text/javascript">
    $(document).ready(function({
        
    }))    
</script>

<SCRIPT language="JavaScript" TYPE="text/javascript">
function validasiForm(form)
{
	if(form.cboTahun.value=="0")
    {
        alert("Periode Tahun harus dipilih !");
        form.cboTahun.focus();
        return false;
    }
    if(form.cboBulan.value=="0")
	{
		alert("Periode Bulan harus dipilih !");
		form.cboBulan.focus();
		return false;
	}
    document.getElementById('cboTahun').disabled = false;
    document.getElementById('cboBulan').disabled = false;
	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        PERIODE
        <small>List Periode</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Referensi</li>
        <li class="active">Periode</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/periode_list" method="post" name="frmKotaDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        $disabled = '';
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA PERIODE </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            $disabled = 'disabled';
                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT idperiode, SUBSTRING(idperiode,1,2) as bulan, SUBSTRING(idperiode,3,4) as tahun, status ";
                            $q.= "FROM periode WHERE md5(idperiode)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($data = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='idperiode' value='" . $data["idperiode"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA PERIODE </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="cboTahun">Periode Tahun</label>
                            <select name="cboTahun" id="cboTahun" class="form-control" <?= $disabled ?> >
                                <option value="" >Pilih Tahun Periode...</option>
                                <?php for ($i=(date('Y')-5); $i <= (date('Y')+5) ; $i++) { 
                                    if($data['tahun']==$i)
                                        echo("<option value='".$i."' selected>".$i."</option>");
                                    else
                                        echo("<option value='".$i."'>".$i."</option>");
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="cboBulan">Periode Bulan</label>
                            <select name="cboBulan" id="cboBulan" class="form-control" <?= $disabled ?> >
                                <option value="" >Pilih Bulan Periode...</option>
                                <?php for ($i=1; $i <= 12 ; $i++) { 
                                    $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    if($data['bulan']==$val)
                                        echo("<option value='".$val."' selected>".namaBulan_id($val)."</option>");
                                    else
                                        echo("<option value='".$val."'>".namaBulan_id($val)."</option>");
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="rdoStatus">Status</label>
                            <div class="radio">
                            <label class="radio-inline"><input type="radio" name="rdoStatus" value="aktif" <?= (!isset($data)or(isset($data)&&$data['status']) == 'aktif') ? 'checked' : ''; ?>> Aktif
                            </label>
                            <label class="radio-inline"><input type="radio" name="rdoStatus" value="tutup" <?= isset($data)&&$data['status'] == 'tutup' ? 'checked' : ''; ?>> Tutup
                            </label>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=html/periode_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
