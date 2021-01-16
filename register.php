<?php
require_once 'inc/connect.php';
require_once 'inc/config.php';
$page['name'] = 'Register';
tylerdator();
?>
<?php include 'inc/page-top.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#userRegister').ajaxForm(function(error) {
            console.log(error);
            if (error['msg'] == "") {
                toastr.success('Account Created! You will be redirected to the Login page shortly.', 'System', {
                    timeOut: 10000
                })
                window.location.href = "<?php echo $url['login']; ?>";
            } else {
                toastr.error(error['msg'], 'System', {
                    timeOut: 10000
                })
            }
        });
    });
</script>

<body>
  <div class="account-pages mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center w-75 m-auto">
                            <a href="index.php">
                                <span><strong><font size="6"><?php echo $settings['name']; ?></font></strong></span>
                            </a>
                            <p class="text-muted mb-4 mt-3">Register your account</p>
                        </div>

                        <form id="userRegister" action="inc/backend/user/auth/userRegister.php" method="POST">

                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input class="form-control" type="text" name="username" required="" placeholder="Username...">
                            </div>

                            <div class="form-group mb-3">
                                <label for="emailaddress">Email</label>
                                <input class="form-control" type="text" name="email" required="" placeholder="Email...">
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input class="form-control" type="password" required="" name="password" placeholder="Password...">
                            </div>
                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block" type="submit"> Register </button>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">Already have an account? <a href="login.php" class="text-primary font-weight-medium ml-1">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
    <?php include 'inc/page-bottom.php'; ?>
    <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
