<?php
/* ==================================================
//Author  : dianarifr
//Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/user_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan User';
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
	if(form.txtKodeUser.value=="")
	{
            alert("Kode User harus diisi !");
            form.txtKodeUser.focus();
            return false;
	}
	if(form.txtNama.value=="")
	{
            alert("Nama harus diisi !");
            form.txtNama.focus();
            return false;
	}
	<?php
	if($_GET["mode"]!='edit')
	{
	?>
            if(form.txtPassword.value=="")
            {
                alert("Password harus diisi !");
                form.txtPassword.focus();
                return false;
            }
            if(form.txtConfirmPassword.value=="")
            {
                alert("Konfirmasi Password harus diisi !");
                form.txtConfirmPassword.focus();
                return false;
            }
            if(form.txtConfirmPassword.value!=form.txtPassword.value)
            {
                alert("Password tidak sesuai dengan konfirmasi. Silakan ulangi !");
                form.txtPassword.value = "";
                form.txtConfirmPassword.value = "";
                form.txtPassword.focus();
                return false;
            }
	<?php
	}
	?>
	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        PENGATURAN USER
        <small>Detail Pengaturan User</small>
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
                <form action="index2.php?page=view/user_list" method="post" name="frmUserDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA USER </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

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
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA USER </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
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
                        <?php 
                        if($_GET["mode"]!="edit")
                        {
                        ?>
                        <div class="form-group">
                            <label class="control-label" for="txtPassword">Password</label>

                            <input type="password" name="txtPassword" id="txtPassword" maxlength="50" class="form-control" 
                                   value="<?= $dataUser["password"]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtConfirmPassword">Konfirmasi Password</label>

                            <input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" 
                                   value="" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <?php
                        }
                        ?>
                        <div class="form-group">
                            <label class="control-label" for="rdoStatus">Status</label>

                            <input name="rdoStatus" id="rdoStatus" type="radio" value="Y"  <?php if($_GET['mode']=="edit") { if($dataUser[2]=="Y") {echo "checked"; }} else {echo "checked";} ?> onKeyPress="return handleEnter(this, event)">&nbsp;Aktif&nbsp;&nbsp;
	    <input name="rdoStatus" id="rdoStatus" type="radio" value="T" <?php if($_GET['mode']=="edit") { if($dataUser[2]=="T") {echo "checked"; }} ?> onKeyPress="return handleEnter(this, event)">&nbsp;Tidak Aktif&nbsp;&nbsp;
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
