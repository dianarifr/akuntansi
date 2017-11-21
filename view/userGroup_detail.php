<?php
/* ==================================================
//Author  : dianarifr
//Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/userGroup_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Group User';
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
	if(form.cboKodeGrup.value=="0")
	{
		alert("Kode Group harus diisi !");
		form.cboKodeGrup.focus();
		return false;
	}
 	if(form.cboKodeUser.value=="0")
	{
		alert("Kode User harus diisi !");
		form.cboKodeUser.focus();
		return false;
	}
	return true; 
}
</SCRIPT>

<section class="content-header">
    <h1>
        PENGATURAN GROUP USER
        <small>Detail Pengaturan Group User</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Group User</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/userGroup_list" method="post" name="frmUserGroupDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA GROUP USER </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT g.iduserGroup, g.kodeGroup, g.kodeUser ";
                            $q.= "FROM userGroup g WHERE md5(g.iduserGroup)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataGroup = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='ID' value='" . $dataGroup[0] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA GROUP USER </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                            $q = "SELECT MAX(iduserGroup) FROM userGroup";
                            $result=mysql_query($q, $dbLink);
                            while($query_data=mysql_fetch_row($result))
                            { $lastID =  $query_data[0]+1; }
                        }
                        ?>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="txtKodeUser">Kode Group</label>
                            <select name="cboKodeGrup" id="cboKodeGrup" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0" <?php if($_GET['mode']=="add") {echo " selected";} ?> >Pilih Kode Group...</option>
                                <?php
                                $rsTemp=mysql_query("SELECT kodeGroup, nama FROM groups ORDER BY kodeGroup", $dbLink);
                                while($query_data=mysql_fetch_row($rsTemp))
                                {
                                    if( $dataGroup[1]==$query_data[0] )
                                            echo("<option value=".$query_data[0]." selected>".$query_data[0]." - ".$query_data[1]."</option>");
                                    else
                                            echo("<option value=".$query_data[0].">".$query_data[0]." - ".$query_data[1]."</option>");
                                }           
                                ?>
                            </select>

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtNama">Kode User</label>

                            <select name="cboKodeUser" id="cboKodeUser" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0" <?php if($_GET['mode']=="add") {echo " selected";} ?>>Pilih Kode User...</option>
                                <?php
                                $rsTemp=mysql_query("SELECT kodeUser, nama FROM user ORDER BY kodeUser", $dbLink);
                                while($query_data=mysql_fetch_row($rsTemp))
                                {
                                        if( $dataGroup[2]==$query_data[0] )
                                                echo("<option value=".$query_data[0]." selected>".$query_data[0]." - ".$query_data[1]."</option>");
                                        else
                                                echo("<option value=".$query_data[0].">".$query_data[0]." - ".$query_data[1]."</option>");
                                }
                                ?>
                            </select>

                        </div>
                        
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=html/userGroup_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
