<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');

if (isset($_POST[txtUserID])) {
    global $siteTitle;
    require_once('./class/c_login.php');
    $tmpLogin = new c_login();
//    if (strtoupper($_POST["captcha_code"]) != strtoupper($_SESSION['captcha_id']) && !reserved_ip($_SERVER['REMOTE_ADDR'])) {
//        header("Location:index.php?page=login_detail&eventCode=30"); 	
//        exit;
//    }
    $tempResult = $tmpLogin->validateUser($_POST[txtUserID]);
    if ($tempResult == 'Sukses') {
        header("Location:index.php");
        exit;
    } else {
        header("Location:index.php?page=login_detail&eventCode=" . $tempResult);
        exit;
    }
} else {
    ?>


<!-- CONTENT -->
    <div class="login-box">
      <div class="login-logo">
        <a href="index.php">PT. <b>LANA GLOBAL INDOTAMA</b></a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">
                <font style="color:#FF0000; font-weight: bold; ">
                <?php
                switch ($_GET['eventCode']) {
                    case 10:
                        echo('User ID atau Password tidak valid!');
                        break;
                    case 20:
                        echo('Log out berhasil!');
                        unset($_SESSION['my']);

                        break;
//                    case 30:
//                        echo('Kode Security tidak valid!');
//                        break;
                    case 90:
                        echo('Harap Log In ...');
                        unset($_SESSION['my']);
                        break;
                    default:
                        //echo('Harap Log In!');
                        break;
                }
                ?>
                </font>
            </p>
        <form id="loginform" name="loginform" action="index2.php?page=login_detail" class="" method="post">
          <div class="form-group has-feedback">
             <input type="text" name="txtUserID" id="txtUserID" class="form-control has-feedback" placeholder="Username" >
            <span class="fa fa-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="txtPassword" id="txtPassword" class="form-control has-feedback" placeholder="Password" >
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <!--<div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> Remember Me
                </label>
              </div>
            </div><!-- /.col -->
            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
            </div><!-- /.col -->
          </div>
        </form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->


<!-- FOOTER -->

<footer class="main-footer" style="margin-left: 0px;margin-top: 232px;">
    <div class="pull-right hidden-xs">
        <b>V</b> 1.0.0
    </div>
    <center><strong>Copyright &copy; <?php echo date('Y')." ".$siteTitle; ?></strong> All rights reserved.</center>
 </footer>
    <!-- jQuery 2.2.3 -->
    <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="plugins/iCheck/icheck.min.js"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>

    <?php
}


?>
