<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');

require_once('./config.php');
require_once('./class/c_user.php');

function menu() {
    global $dbLink;
    $privilege = secureParam($_SESSION["my"]->privilege, $dbLink);
    if ($privilege != "ADMIN")
        $filter = " INNER JOIN groupPrivilege gp ON m.kodeMenu=gp.kodeMenu AND gp.kodeGroup='" . $privilege . "'";

    $q = "SELECT DISTINCT m.kodeMenu, m.judul, m.link FROM menu m" . $filter . " WHERE m.aktif='Y' AND 
              m.kodeMenu IN (" . $_SESSION["my"]->menus . ") ORDER BY m.kodeMenu;";
    $cari_menu = mysql_query($q, $dbLink);
    ?>

        <a href="index.php">
            <i class="fa fa-dashboard"></i><span>Home</span>
            <span class="pull-right-container"></span>
        </a>
        
        <?php
        $currentLevel = 0;
        while ($menu = mysql_fetch_array($cari_menu)) {
            if ($menu['kodeMenu'] == '99') {
                $fa99 = 1;$fa=99;
            }
            if ($menu['kodeMenu'] == '10') {
                $fa10 = 1;$fa=10;
            }
            if ($menu['kodeMenu'] == '20') {
                $fa20 = 1;$fa=20;
            }
            if ($menu['kodeMenu'] == '30') {
                $fa30 = 1;$fa=30;
            }
            

//        echo $menu['Link'];
            $tempArr = explode(".", $menu['kodeMenu']);
            if (strlen($menu['link']) == 0) {
                $tempLink = '';
            } else {
                $tempLink = "index.php?page=" . $menu['link'];
            }

            if (count($tempArr) > $currentLevel) {
                if (count($tempArr) == 1) {
                    if ($fa10){
                            echo '<li class="treeview"><a href="' . $tempLink . '"><i class="fa fa-edit"></i><span>' . $menu['judul'] . '
                                </span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></a>';
                    }else{
                        echo '<li><a href="' . $tempLink . '"><i class="fa fa-circle-o"></i> ' . $menu['judul'] . '</a></li>';
                    }
                } elseif (count($tempArr) == 2) {
                    echo '<ul class="treeview-menu">';
                    echo '<li><a href="' . $tempLink . '"><i class="fa fa-circle-o"></i> ' . $menu['judul'] . '</a></li>';
                }
            } elseif (count($tempArr) == $currentLevel) {
                if (count($tempArr) == 1) {
                    echo '</li>';
                    echo '<li><a href="' . $tempLink . '"><i class="fa fa-circle-o"></i> ' . $menu['judul'] . '</a></li>';
                } else {
                    echo '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
                    echo '<li><a href="' . $tempLink . '"><i class="fa fa-circle-o"></i> ' . $menu['judul'] . '</a></li>';
                }
            } elseif (count($tempArr) < $currentLevel) {
                echo '</ul></li>';
                echo '<li class="treeview"><a href="' . $tempLink . '">';
                if ($fa=='99'){
                    echo '<i class="fa fa-gears"></i>';
                }elseif ($fa=='20'){
                    echo '<i class="fa fa-laptop"></i>';
                }elseif ($fa=='30'){
                    echo '<i class="fa fa-files-o"></i>';
                }
                echo '<span>' . $menu['judul'] . '
                </span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
            }
            $currentLevel = count($tempArr);
        }
        if ($currentLevel == 1) {
            echo '</li>';
        } elseif ($currentLevel == 2) {
            echo '</ul></li>';
        }
        ?>
        <li><a href="index.php?page=login_detail&eventCode=20"><i class="fa fa-sign-out"></i><span>Log Out</span></a></li>
        

        <?php
}

//function menu
?>

