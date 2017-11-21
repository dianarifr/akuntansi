<?php
/* ==================================================
//Author  : dianarifr
//Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/ubahPassword_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Ubah Password';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
?>

<!-- Include script date di bawah jika ada field tanggal -->
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<script type="text/javascript" charset="utf-8">
    $(function()
    {
        $('.date-pick').datePicker({startDate:'01/01/1970'});
    });
</script>
<!-- End of Script Tanggal -->

<!-- Include script di bawah jika ada field yang Huruf Besar semua -->
<script src="js/jquery.bestupper.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".bestupper").bestupper();
    });
</script>

<SCRIPT language="JavaScript" TYPE="text/javascript">
function validasiForm(form)
{	
	if($("#txtKodeUser").val()=="")
	{
		alert("Kode User tidak valid!");
		$("#txtKodeUser").focus();
		return false;
	}
	
	if($("#txtPasswordBaru").val()=="")
	{
		alert("Password Baru harus diisi !");
		$("#txtPasswordBaru").focus();
		return false;
	}
	if($("#txtConfirmPassword").val()=="")
	{
		alert("Konfirmasi Password Baru harus diisi !");
		$("#txtConfirmPassword").focus();
		return false;
	}
	if($("#txtConfirmPassword").val()!=$("#txtPasswordBaru").val())
	{
		alert("Password baru tidak sesuai dengan konfirmasi. Silakan ulangi !");
		$("#txtPasswordBaru").val("");
		$("#txtConfirmPassword").val("");
		$("#txtPasswordBaru").focus();
		return false;
	}
	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        PENGATURAN USER
        <small>Detail Ubah Password</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">User</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/user_list" method="post" name="frmUbahPassword" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH PASSWORD USER </h3>';
                            echo "<input type='hidden' name='txtMode' value='".md5("ChangePassword")."'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT kodeUser, nama, aktif, password ";
                            $q.= "FROM user WHERE md5(kodeUser)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataUser = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeUser' value='" . $dataUser["kodeUser"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } 
                        ?>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="txtKodeUser">Kode User</label>
                            <input name="txtKodeUser" id="txtKodeUser" maxlength="15" class="form-control" 
                                   value="<?= $dataUser["kodeUser"]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtNama">Nama</label>

                            <input name="txtNama" id="txtNama" maxlength="20" class="form-control" 
                                   value="<?= $dataUser["nama"]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtPassword">Password Baru</label>

                            <input type="password" name="txtPasswordBaru" id="txtPasswordBaru" maxlength="50" class="form-control" 
                                   value="<?= $dataUser["password"]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtConfirmPassword">Konfirmasi Password Baru</label>

                            <input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" 
                                   value="" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        
                        
                        
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=html/user_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
