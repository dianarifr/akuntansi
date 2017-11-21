<?php
//Author  : dianarifr
//Created : 11/11/2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/userGroup_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan User Group';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_setting.php");
    $userGroup=new c_setting();
    
    //Jika Mode Tambah/Add
    if ($_POST["txtMode"]=="Add")
    {
            $pesan=$userGroup->addUserGroup($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"]=="Edit")
    {
            $pesan=$userGroup->editUserGroup($_POST);
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"]=="Delete")
    {
            $pesan=$userGroup->deleteUserGroup($_GET["kodeUserGroup"]);
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
        PENGATURAN GROUP USER
        <small>List Group User</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Group User</li>
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
                    <h3 class="box-title">Kriteria Pencarian Group User </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariGroupUser" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                            <select name="namaGroup" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0" >Pilih Kode Group...</option>
                                <?php
                                $rsTemp=mysql_query("SELECT kodeGroup, nama FROM groups ORDER BY nama", $dbLink);
                                while($query_data=mysql_fetch_row($rsTemp))
                                {
                                        if( $_GET["namaGroup"]==$query_data[0] )
                                                echo("<option value=".$query_data[0]." selected>".$query_data[1]."</option>");
                                        else
                                                echo("<option value=".$query_data[0].">".$query_data[1]."</option>");
                                }
                                ?>
                            </select>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                        
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="kodeUser" id="kodeUser" placeholder="Kode User..."
                            <?php
                            if ($_GET["kodeUser"]) {
                                echo("value='" . $_GET["kodeUser"] . "'");
                            }
                            ?>
                                   onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php
                    if ($hakUser == 90) {
                        ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?page=html/userGroup_detail&mode=add"; ?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
                        <?php
                    }
                    ?>
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
                $namaGroup=secureParam($_GET["namaGroup"], $dbLink);
                $kodeUser=secureParam($_GET["kodeUser"], $dbLink);

                //Set Filter berdasarkan query string
                $filter = "";
                if($namaGroup)
                    $filter= $filter." AND g.kodeGroup = '".$namaGroup."'";
                if($kodeUser)
                    $filter= $filter." AND g.kodeUser LIKE '%".$kodeUser."%'";

                //Query
                $q = "SELECT g.iduserGroup, g.kodeGroup, g.kodeUser, gp.nama, u.nama ";
                $q.= "FROM userGroup g, groups gp, user u ";
                $q.= "WHERE g.kodeGroup = gp.kodeGroup AND g.kodeUser = u.kodeUser ".$filter;
                $q.= " ORDER BY gp.nama, g.kodeUser";

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
                                <th style="width: 3%">#</th>
                                <th width="20%" class="sort-alpha">Nama Group</th>
                                <th width="20%" class="sort-alpha">User ID</th>
                                <th width="52%">Nama</th>
                                <th colspan="3" width="5%">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . $query_data[3] . "</td>";
                                echo "<td>" . $query_data[2] . "</td>";
                                echo "<td>" . $query_data[4] . "</td>";
                                

                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/userGroup_detail&mode=edit&kode=" . md5($query_data[0]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                    echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kodeUserGroup=" . md5($query_data[0]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");
                                    
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
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

