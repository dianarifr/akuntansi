<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/ubahProfil_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Ubah Profil';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_user.php");
    $tmpUser=new c_user();
    
    //Jika Mode ChangeProfile untuk file html/ubahProfil_list.php
    if ($_POST["txtMode"]=="ChangeProfile")
    {
             $pesan=$tmpUser->ChangeProfile($_POST);
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

<SCRIPT language="JavaScript" TYPE="text/javascript">
function validasiForm(form)
{
	if(form.txtNama.value=="")
	{
		alert("Nama harus diisi !");
		form.txtNama.focus();
		return false;
	}
	if(form.txtPassword.value=="")
	{
		alert("Password harus diisi !");
		form.txtPassword.focus();
		return false;
	}
	if(form.txtPasswordBaru.value=="")
	{
		alert("Password Baru harus diisi !");
		form.txtPasswordBaru.focus();
		return false;
	}
	if(form.txtConfirmPassword.value=="")
	{
		alert("Konfirmasi Password Baru harus diisi !");
		form.txtConfirmPassword.focus();
		return false;
	}
	if(form.txtConfirmPassword.value!=form.txtPasswordBaru.value)
	{
		alert("Password baru tidak sesuai dengan konfirmasi. Silakan ulangi !");
		form.txtPasswordBaru.value = "";
		form.txtConfirmPassword.value = "";
		form.txtPasswordBaru.focus();
		return false;
	}
	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        UBAH PROFIL USER
        <small>Ubah Profil User</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">User</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6 ">
            <div class="box box-primary">
                <div class="box-body">
                    <form action="index2.php?page=view/ubahProfil_list" method="post" name="frmUserDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        
                            echo '<h3 class="box-title">UBAH PROFIL USER </h3>';
                            echo "<input type='hidden' name='txtMode' value='ChangeProfile'>";

                            //Secure parameter from SQL injection
                            $kode = $_SESSION['my']->id;

                            $q = "SELECT kodeUser, nama, password ";
                            $q.= "FROM user WHERE kodeUser='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataUser = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeUser' value='" . $dataUser[0] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        
                        ?>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="txtKodeUser">Kode User</label>
                            <input name="txtKodeUser" id="txtKodeUser" maxlength="15" class="form-control" 
                                readonly="" value="<?= $dataUser[0]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtNama">Nama</label>

                            <input name="txtNama" id="txtNama" maxlength="20" class="form-control" 
                                   value="<?= $dataUser[1]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtPassword">Password</label>

                            <input type="password" name="txtPassword" id="txtPassword" maxlength="50" class="form-control" 
                                   placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtPassword">Password Baru</label>

                            <input type="password" name="txtPasswordBaru" id="txtPasswordBaru" maxlength="50" class="form-control" 
                                   placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtConfirmPassword">Konfirmasi Password</label>

                            <input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" 
                                   placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
                </div> 
            </div>
        </section>
        
        <!-- Left col -->
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
        <!-- /.Left col -->

    </div>
    <!-- /.row -->
</section>


