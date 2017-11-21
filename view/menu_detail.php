<?php
/* ==================================================
  //Author  : Kristoforus H. Abadi
  //Created : 10 Nopember 2016
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/menu_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan Menu';
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
<!-- Script untuk validasi input user -->
function validasiForm(form)
{
	if(form.cboRootMenu.value=="0")
	{
		alert("Root Menu harus diisi !");
		form.cboRootMenu.focus();
		return false;
	}
	if(form.txtSubKode.value=="")
	{
		alert("Kode Sub Menu harus diisi !");
		form.txtSubKode.focus();
		return false;
	}
	if(form.txtJudul.value=="")
	{
		alert("Judul Menu harus diisi !");
		form.txtJudul.focus();
		return false;
	}
	return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        PENGATURAN MENU
        <small>Detail Pengaturan Menu</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Menu</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/menu_list" method="post" name="frmUserDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA MENU </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT kodeMenu, judul, link, aktif ";
                            $q.= "FROM menu WHERE md5(kodeMenu)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataMenu = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeMenu' value='" . $dataMenu[0] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA MENU </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        
                        function getParentMenu($kode)
                        {
                            $array = array();
                            $array = explode('.', $kode, -1);
                            $kode="";
                            for($i=0; $i<count($array); $i++)
                            {
                                    if($kode=="")
                                            $kode = $array[$i];
                                    else
                                            $kode = $kode . "." .$array[$i];
                            }
                            return $kode;
                        }
                        
                        function getSubKode($kode)
                        {
                                $ct = substr_count($kode,".");
                                if($ct==0)
                                        return $kode;
                                else
                                        return str_replace(".","",substr($kode,-2));
                        }
                        ?>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="txtKodeUser">Root Menu</label>
                            
                            <script language="javascript">
                            function getLink()
                            {
                                $("#txtLinkedit").val($("#cboLink").val());
                            }

                            function cekKode()
                            {
                                $("#msgbox").text('Checking...');

                                $.post("function/ajax_function.php",{ fungsi: "checkKodeMenu", kodeMenu:$("#cboRootMenu").val() + "." +$("#txtSubKode").val()   } ,function(data)
                                {
                                    if(data=='yes') 
                                    {
                                        $("#msgbox").removeClass().addClass('messageboxerror').text('Kode Menu telah ada. Gunakan kode lain.').fadeIn("slow");
                                    }
                                    else if (data=='no') 
                                    {
                                        $("#msgbox").removeClass().addClass('messageboxok').text('Kode Menu belum tercatat - data baru.').fadeIn("slow");
                                    } else {
                                        $("#msgbox").removeClass().addClass('messageboxerror').text('Maaf, terjadi error pada System').fadeIn("slow");

                                    }

                            });
                            }


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
                            
                            <select name="cboRootMenu" id="cboRootMenu" class="form-control" <?php if ($_GET["mode"]=="edit")  echo "readonly"; ?>  onKeyPress="return handleEnter(this, event)">
                            <option value="0" >Pilih Root Menu...</option>
                            <option value="M" <?php if($_GET['mode']=="edit") { if(getSubKode($dataMenu[0])==$dataMenu[0]) {echo " selected";} }?>>Main Menu</option>
                                        <?php
					$q = "SELECT kodeMenu, judul FROM menu ORDER BY kodeMenu";
                                        $rsTemp=mysql_query($q, $dbLink);
                                        while($query_data=mysql_fetch_row($rsTemp))
                                        {
                                                if(getParentMenu($dataMenu[0])==$query_data[0])
                                                        echo("<option value=".$query_data[0]." selected>".$query_data[1]." - ".$query_data[0]."</option>");
                                                else
                                                        echo("<option value=".$query_data[0].">".$query_data[1]." - ".$query_data[0]."</option>");
                                        }
                                        ?>
                                        </select> + <input name="txtSubKode" id="txtSubKode" size="4" maxlength="4" class="form-control" onblur="cekKode();" value="<?php echo getSubKode($dataMenu[0]) ?>" <?php if($_GET['mode']=="edit") {echo " disabled";} ?> placeholder="Wajib isi..." onKeyPress="return handleEnter(this, event)"> <span id="msgbox"></span>
                                        <br />
                                        (Kode Menu = 4 Root Menu + 3 Digit Kode Sub Menu yang akan ditambahkan)

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtJudul">Judul Menu</label>
                            
                            <input name="txtJudul" id="txtJudul" size="42" maxlength="40" class="form-control" 
                                   value="<?php echo $dataMenu[1]; ?>" placeholder="Wajib diisi..."
                                   onKeyPress="return handleEnter(this, event)">
                            
                            <select name="cboLink" id="cboLink" class="form-control" size="10" onblur="getLink();" onKeyPress="return handleEnter(this, event)" >
                            <option value="" <?php if($dataMenu[2] == "") {echo " selected"; } ?>>Tanpa Link</option>
                            <?php
                                    $dir = "view/";
                                    $file = scandir($dir);
                                    $lstFile = array();
                                    for ($i=0; $i<count($file); $i++)
                                    {
                                            if ($file[$i] != "." && $file[$i] != "..")
                                            {
                                                    if (!is_dir($dir . $file[$i]))
                                                    {	$lstFile[] = $file[$i]; }
                                            }
                                    }
                                    foreach ($lstFile as $menu)
                                    {
                                            //if(substr_count($menu,"_list") != 0)
                                            if(substr_count($menu,".php") != 0)
                                            {
                                                    $menu = str_replace(".php","",$menu);
                                                    $link = "view/".$menu;
                                                    if ($dataMenu[2] == $link)
                                                    {
                                                            echo "<option value=" . $link;
                                                            echo " selected";
                                                            echo ">" . $link . "</option>\n";
                                                    }
                                                    else
                                                    {
                                                            echo "<option value=" . $link;
                                                            echo ">" . $link . "</option>\n";
                                                    }
                                            }
                                    }
                            ?>
                            </select>

                        </div>
                        
                        <div class="form-group">
                            <label class="control-label" for="txtLink">Link</label>

                            <input name="txtLinkedit" id="txtLinkedit" size="50" maxlength="200" class="form-control" value="<?php echo $dataMenu[2]; ?>" placeholder="Wajib isi..." onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="rdoStatus">Status</label>

                            <input name="rdoStatus" id="rdoStatus" type="radio" value="Y" <?php if($_GET['mode']=="edit") { if($dataMenu[3]=="Y") {echo "checked"; } } else {echo "checked";}?> onKeyPress="return handleEnter(this, event)">&nbsp;Aktif&nbsp;&nbsp;
                            <input name="rdoStatus" id="rdoStatus" type="radio" value="T" <?php if($_GET['mode']=="edit") { if($dataMenu[3]=="T") {echo "checked"; } }?> onKeyPress="return handleEnter(this, event)">&nbsp;Tidak Aktif&nbsp;&nbsp;
                        </div>
                        
                        
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=view/menu_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
