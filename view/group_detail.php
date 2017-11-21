<?php
/* ==================================================
//Author  : dianarifr
//Created : 11/11/2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/group_detail";

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
    <!-- Script untuk check all checkbox -->
    function jqCheckAll3( id, pID )
    {
        $( "#" + pID + " :checkbox").attr('checked', $('#' + id).is(':checked'));
    }
    <!-- Script untuk validasi input user -->
    function validasiForm(form)
    {
        if(form.txtKodeGroup.value=="")
        {
            alert("Kode Group harus diisi !");
            form.txtKodeGroup.focus();
            return false;
        }
        if(isNumeric(form.txtKodeGroup.value))
        {
            alert("Kode Group tidak bolah berupa angka!");
            form.txtKodeGroup.focus();
            return false;
        }
        if(form.txtNamaGroup.value=="")
        {
            alert("Nama harus diisi !");
            form.txtNamaGroup.focus();
            return false;
        }
        return true; 
    }
</SCRIPT>

<section class="content-header">
    <h1>
        PENGATURAN GROUP
        <small>Detail Pengaturan Group</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Group</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <form action="index2.php?page=view/group_list" method="post" name="frmGroupDetail" onSubmit="return validasiForm(this);">
            <section class="col-lg-6">
                <div class="box box-primary">

                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA GROUP </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT g.kodeGroup, g.nama ";
                            $q.= "FROM groups g WHERE md5(g.kodeGroup)='" . $kode . "'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataGroup = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeGroup' value='" . $dataUser["kodeGroup"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA GROUP </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="txtKodeGroup">Kode Group</label>

                            <script language="javascript">
                                $(document).ready(function(){
                                    $("#kodeGroup").blur(function()
                                    {
                                        $("#msgbox").text('Checking...');

                                        $.post("function/ajax_function.php",{ fungsi: "checkKodeGroup", kodeGroup:$("#kodeGroup").val() } ,function(data)
                                        {
                                            if(data=='yes') 
                                            {
                                                $("#msgbox").removeClass().addClass('messageboxerror').text('Kode Group telah ada. Gunakan kode lain.').fadeIn("slow");
                                            }
                                            else if (data=='no') 
                                            {
                                                $("#msgbox").removeClass().addClass('messageboxok').text('Kode Group belum tercatat - data baru.').fadeIn("slow");
                                            } else {
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

                            <input name="txtKodeGroup" id="txtKodeGroup" maxlength="15" class="form-control" 
                                   value="<?= $dataGroup["kodeGroup"]; ?>" placeholder="Wajib diisi" 
                                   onKeyPress="return handleEnter(this, event)"><span id="msgbox"></span>

                        </div>

                        <div class="form-group">
                            <label class="control-label" for="txtNamaGroup">Nama</label>

                            <input name="txtNamaGroup" id="txtNamaGroup" maxlength="20" class="form-control" 
                                   value="<?= $dataGroup["nama"]; ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>


                    </div>
<!--                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=html/user_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>-->

                </div>    
            </section>

            <section class="col-lg-12">
                <div class="box box-primary">

                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">DAFTAR MENU YANG DAPAT DIAKSES </h3>
                        <span id="msgbox"> </span>
                    </div>
                    <!--                <div class="box-body">-->
                    <?php
                    $rowCounter = 0;
                    $access = array();
                    if ($_GET['mode'] == "add") {
                        $q = "SELECT kodeMenu, judul FROM menu ORDER BY kodeMenu";
                        $rsTemp = mysql_query($q, $dbLink);
                        $akhirNoAccess = mysql_num_rows($rsTemp);
                        while ($row = mysql_fetch_array($rsTemp)) {
                            $noAccess[] = $row;
                        }
                        $akhirAccess = 0;
                    } else if ($_GET['mode'] == "edit") {
                        $allowedGroups = "'0'";
                        // Menu - menu yang dapat diakses terdapat di Tabel Group Privilege
                        $q = "SELECT gp.kodeMenu, m.judul, gp.level, m.link FROM groupPrivilege gp, menu m ";
                        $q.= "WHERE gp.kodeMenu=m.kodeMenu AND gp.kodeGroup = '" . $dataGroup[0] . "' 
                                  ORDER BY gp.kodeMenu";
                        $rsTemp = mysql_query($q, $dbLink);
                        $akhirAccess = mysql_num_rows($rsTemp);
                        while ($row = mysql_fetch_array($rsTemp)) {
                            $access[] = $row;
                            $allowedGroups.=",'" . $row[0] . "'";
                        }
                        // Menu - menu yang tidak dapat diakses
                        $q = "SELECT kodeMenu, judul, link FROM menu WHERE kodeMenu NOT IN (" . $allowedGroups . ") 
                                  ORDER BY kodeMenu";
                        $rsTemp = mysql_query($q, $dbLink);
                        $akhirNoAccess = mysql_num_rows($rsTemp);
                        while ($row = mysql_fetch_array($rsTemp)) {
                            $noAccess[] = $row;
                        }
                    }
                    ?>

                    <div class="box-body">

                        <table class="table table-bordered table-striped table-hover" >
                            <thead>
                                <tr>
                                    <th style="width: 2%"><input type="checkbox" id="checkL" onclick="jqCheckAll3(this.id, 'access');"/></th>
                                    <th style="width: 15%">Kode Menu</th>
                                    <th style="width: 25%">Nama Menu</th>
                                    <th style="width: 25%">Link</th>
                                    <th style="width: 25%">Level Akses</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                for ($i = 0; $i < $akhirAccess; $i++) {
                                    echo("<tr>");
                                    echo "<td align='center' id='access'><input type= 'checkbox' name = 'access[]' value = '" . $access[$i][0] . "' ></td>";
                                    echo "<td align='left'>" . $access[$i][0] . "</td>";
                                    echo "<td align='left'>" . $access[$i][1] . "</td>";
                                    echo "<td align='left'>" . $access[$i][3] . "</td>";
                                    ?>
                                <td align='center'><select name='cboLevelAccess[]'  class="form-control">
                                        <option value='0'>No access - 0</option>
                                        <option value='10' <?php
                                if ($access[$i][2] == 10) {
                                    echo " selected";
                                }
                                ?>>Read Only - 10</option>
                                        <option value='20' <?php
                                if ($access[$i][2] == 20) {
                                    echo " selected";
                                }
                                    ?>>20</option>
                                        <option value='30' <?php
                                            if ($access[$i][2] == 30) {
                                                echo " selected";
                                            }
                                    ?>>30</option>
                                        <option value='40' <?php
                                    if ($access[$i][2] == 40) {
                                        echo " selected";
                                    }
                                    ?>>40</option>
                                        <option value='50' <?php
                                    if ($access[$i][2] == 50) {
                                        echo " selected";
                                    }
                                    ?>>50</option>
                                        <option value='60' <?php
                                            if ($access[$i][2] == 60) {
                                                echo " selected";
                                            }
                                    ?>>60</option>
                                        <option value='70' <?php
                            if ($access[$i][2] == 70) {
                                echo " selected";
                            }
                            ?>>70</option>
                                        <option value='80' <?php
                            if ($access[$i][2] == 80) {
                                echo " selected";
                            }
                            ?>>80</option>
                                        <option value='90' <?php
                            if ($access[$i][2] == 90) {
                                echo " selected";
                            }
                            ?>>Full Access - 90</option>
                                    </select></td>
    <?php
    echo "</tr>";
    $rowCounter++;
}
?>
                            </tbody>
                        </table>
                        <div class="box-header">
                            <i class="ion ion-clipboard"></i>
                            <h3 class="box-title">DAFTAR MENU YANG TIDAK DAPAT DIAKSES </h3>
                            <span id="msgbox"> </span>
                        </div>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr >
                                    <th width="5%" align="center"><input type="checkbox" id="checkN" onclick="jqCheckAll3(this.id, 'down');"/></th>
                                    <th width="25%" align="center">Kode Menu</th>
                                    <th width="30%" align="center" valign="middle">Nama Menu</th>
                                    <th width="30%" align="center" valign="middle">Link</th>	
                                    <th width="10%" align="center" valign="middle">Level Akses</th>	
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < $akhirNoAccess; $i++) {
                                    if ($rowCounter % 2 == 0)
                                        echo("<tr class='even'>");
                                    else
                                        echo("<tr class='odd'>");
                                    echo "<td align='left' id='down'><input type= 'checkbox' name = 'noAccess[]' id = 'noAccess[]' value = '" . $noAccess[$i][0] . "'></td>";
                                    echo "<td align='left'>" . $noAccess[$i][0] . "</td>";
                                    echo "<td align='left'>" . $noAccess[$i][1] . "</td>";
                                    echo "<td align='left'>" . $noAccess[$i][2] . "</td>";
                                    echo "<td align='center'><select name='cboLevelNoAccess[]'  class='form-control'>" .
                                    "<option value='0' selected>No access - 0</option>" .
                                    "<option value='10'>Read Only - 10</option>" .
                                    "<option value='20'>20</option>" .
                                    "<option value='30'>30</option>" .
                                    "<option value='40'>40</option>" .
                                    "<option value='50'>50</option>" .
                                    "<option value='60'>60</option>" .
                                    "<option value='70'>70</option>" .
                                    "<option value='80'>80</option>" .
                                    "<option value='90'>Full Access - 90</option>" .
                                    "</select></td>";
                                    echo "</tr>";
                                    echo "</tr>";
                                    $rowCounter++;
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>

                    <!--                </div>-->
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=html/group_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </div>

            </section>
        </form>
    </div>
</section>
