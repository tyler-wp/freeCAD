<?php
require_once 'inc/connect.php';

require_once 'inc/config.php';

$page['name'] = 'Login';
tylerdator();
?>
<?php include 'inc/page-top.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#userLogin').ajaxForm(function(error) {
            console.log(error);
            error = JSON.parse(error);
            if (error['msg'] === "") {
                toastr.success('Logged in... Redirecting', 'System:', {
                    timeOut: 10000
                })
                window.location.href = "index.php";
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                })
            }
        });
    });
</script>

<body>
    <?php
        if (isset($_GET['error']) && strip_tags($_GET['error']) === 'banned') {
            throwError('Your account has been banned from accessing this Panel. If you have any further questions, Please make a ban appeal.');
        } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'access') {
            throwError('You must be logged in to access that page.');
        } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'vc') {
            throwError('This community requires account verification to access.');
        }
        ?>
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
                                  <p class="text-muted mb-4 mt-3">Login to access the CAD</p>
                              </div>

                              <form id="userLogin" action="inc/backend/user/auth/userLogin.php" method="POST">

                                  <div class="form-group mb-3">
                                      <label for="username">Username</label>
                                      <input class="form-control" type="text" name="username" required="" placeholder="Username...">
                                  </div>

                                  <div class="form-group mb-3">
                                      <label for="password">Password</label>
                                      <input class="form-control" type="password" required="" name="password" placeholder="Password...">
                                  </div>
                                  <div class="form-group mb-0 text-center">
                                      <button class="btn btn-primary btn-block" type="submit"> Login </button>
                                  </div>
                              </form>
                          </div> <!-- end card-body -->
                      </div>
                      <!-- end card -->

                      <div class="row mt-3">
                          <div class="col-12 text-center">
                              <p class="text-muted">Don't have an account? <a href="register.php" class="text-primary font-weight-medium ml-1">Sign Up</a></p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    <?php include 'inc/page-bottom.php'; ?>
    <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
