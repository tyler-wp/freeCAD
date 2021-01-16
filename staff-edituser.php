<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';
require_once 'inc/config.php';
require_once 'inc/backend/user/auth/userIsLoggedIn.php';

$page['name'] = 'Editing User';

if (staff_editUsers === 'false') {
  die('Internal Error.');
}
tylerdator();
$view = strip_tags($_GET['user-id']);

if (isset($_GET['user-id']) && strip_tags($_GET['user-id'])) {
  $id   = strip_tags($_GET['user-id']);
  $_SESSION['editingUser_id'] = $id;
  $sql  = "SELECT * FROM users WHERE user_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id]);
  $editingUser = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($editingUser === false) {
      header('Location: staff.php?error=unf');
      exit();
  } else {
    // First we will check if the user is staff
    if ($editingUser['usergroup'] >= 4) {
      $thisUser['staff'] = 'true';
    } else {
      $thisUser['staff'] = 'false';
    }

    // Now check if the user is trying to edit themselves
    if ($user_id === $editingUser['user_id']) {
      header('Location: staff.php?error=see');
      exit();
    }

    // Now check if the person editing the user, is in a lower usergroup than that person
    if ($user['usergroup'] <= $editingUser['usergroup']) {
      header('Location: staff.php?error=ep');
      exit();
    }

    // Gets the persons usergroup, and sets it as a session variable for later stuff
    $_SESSION['editingUser_group'] = $editingUser['usergroup'];
  }
}

if (isset($_POST['unbanUser'])) {
  if (staff_banUsers === 'false') {
    die('Internal Error.');
  }
  $sql = "UPDATE users SET usergroup=? WHERE user_id=?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$settings['verifiedGroup'], $_SESSION['editingUser_id']]);

  header('Location: staff-edituser.php?user-id='.$_SESSION['editingUser_id'].'&success=unbanned');
  exit();
}

if (isset($_POST['editUserBtn'])) {
  if (staff_editUsers === 'false') {
    die('Internal Error.');
  }
  $updateUsergroup  = !empty($_POST['usergroup']) ? trim($_POST['usergroup']) : null;
  $updateUsergroup  = strip_tags($updateUsergroup);

  if ($updateUsergroup >= $user['usergroup']) {
    header('Location: staff-edituser.php?user-id='.$_SESSION['editingUser_id'].'&error=gp');
    exit();
  }

  $sql = "UPDATE users SET usergroup = ? WHERE user_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$updateUsergroup, $_SESSION['editingUser_id']]);

  header('Location: staff-edituser.php?user-id='.$_SESSION['editingUser_id'].'&success=edited');
  exit();
}

if (isset($_POST['banUserBtn'])) {
  if (staff_banUsers === 'false') {
    die('Internal Error.');
  }
  $banReason  = !empty($_POST['reason']) ? trim($_POST['reason']) : null;
  $banReason  = strip_tags($banReason);

  if ($editingUser['usergroup'] >= $user['usergroup']) {
    header('Location: staff-edituser.php?user-id='.$_SESSION['editingUser_id'].'&error=gp');
    exit();
  }

  $sql = "UPDATE users SET usergroup = ?, ban_reason = ? WHERE user_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$settings['banGroup'], $banReason, $_SESSION['editingUser_id']]);

  header('Location: staff-edituser.php?user-id='.$_SESSION['editingUser_id'].'&success=banned');
  exit();
}

if (isset($_POST['delUserBtn'])) {
  if (staff_SuperAdmin === 'false') {
    die('Internal Error.');
  }

  $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
  $result = $stmt->execute([$_SESSION['editingUser_id']]);

  header('Location: staff.php?user=deleted');
  exit();
}
?>
<?php include 'inc/page-top.php'; ?>
<script src="assets/js/pages/staff.js"></script>
<body>
    <?php include 'inc/top-nav.php';
    if (isset($_GET['success']) && strip_tags($_GET['success']) === 'unbanned') {
      clientNotify('success', 'User has been unbanned.');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'edited') {
      clientNotify('success', 'User has been edited!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'banned') {
      clientNotify('success', 'User has been banned!');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'gp') {
      clientNotify('error', 'Group Permission error.');
    }
    ?>
    <!-- CONTENT START -->
    <div class="wrapper m-b-15 m-t-10">
      <div class="container-fluid">
        <div class="row">
          <div class="col">
            <h4 class="page-title"><?php echo $page['name']; ?></h4>
          </div>
        </div>
        <?php if ($thisUser['staff'] === 'true'): ?>
          <div class="alert alert-warning" role="alert">
            This user is Staff!
          </div>
        <?php endif; ?>
        <?php if ($editingUser['usergroup'] === $settings['banGroup']): ?>
          <div class="row">
            <div class="col-12">
              <div class="alert alert-danger" role="alert">
                <?php echo $editingUser['username']; ?> is banned. This user can not be edited until they are unbanned.
              </div>
              <div class="card-box">
                  <h4 class="m-t-0 header-title">Ban Manager</h4>
                  <form method="POST">
                      <div class="form-group text-center">
                          <div class="col-12">
                            <button class="btn btn-danger btn-bordred btn-block waves-effect waves-light" type="submit" onclick="return confirm('Are you sure you want to unban <?php echo $editingUser['username']; ?>?')" name="unbanUser">Unban <?php echo $editingUser['username']; ?></button>
                          </div>
                      </div>
                  </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="row">
            <div class="col-12">
              <div class="bg-picture card-box">
                <div class="profile-info-name">
                  <img src="<?php echo $editingUser['avatar']; ?>" class="img-thumbnail" alt="profile-image">
                  <div class="profile-info-detail">
                    <div id="editingUserPanelWizard" class="pull-in">
                      <ul class="nav nav-tabs nav-justified">
                        <li class="nav-item"><a href="#userInfoTab" data-toggle="tab" class="nav-link">User Info</a></li>
                        <li class="nav-item"><a href="#logsTab" data-toggle="tab" class="nav-link">Logs</a></li>
                        <li class="nav-item"><a href="#actionsTab" data-toggle="tab" class="nav-link">Actions</a></li>
                      </ul>

                      <div class="tab-content b-0 mb-0">
                        <div class="tab-pane m-t-10 fade" id="userInfoTab">
                          <form method="POST">
                            <div class="row">
                              <div class="col">
                                <label>Username (<i>Locked</i>)</label>
                                <input type="text" class="form-control" value="<?php echo $editingUser['username']; ?>" placeholder="Username" readonly="" disabled>
                              </div>
                              <div class="col">
                                <label>Email (<i>Locked</i>)</label>
                                <input type="email" class="form-control"  value="<?php echo $editingUser['email']; ?>" placeholder="Email" readonly="" disabled>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col">
                                <div class="form-group">
                                  <label for="usergroupSelect">Usergroup</label>
                                  <select class="form-control" name="usergroup" id="usergroupSelect">
                                    <option value="<?php echo $editingUser['usergroup']; ?>" disabled="disabled" selected="true">
                                      <?php
                                        $sql = "SELECT * FROM usergroups where id = ?";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([$editingUser['usergroup']]);
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $result['name'];
                                      ?>
                                    </option>
                                    <?php
                                      $sql = "SELECT * FROM usergroups";
                                      $stmt = $pdo->prepare($sql);
                                      $stmt->execute();
                                      while ($usergroupDB = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                          echo '<option value="'.$usergroupDB['id'].'">' . $usergroupDB['name'] . '</option>';
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                              <div class="col">
                                <label>Join Date</label>
                                <input type="text" class="form-control" value="<?php echo $editingUser['join_date']; ?>" placeholder="Join Date" readonly="" disabled>
                              </div>
                            </div>
                            <div class="form-group text-center">
                              <div class="col-12">
                                  <button class="btn btn-success btn-bordred waves-effect waves-light float-right" type="submit" name="editUserBtn">Edit <?php echo $editingUser['username']; ?></button>
                              </div>
                            </div>
                          </form>
                        </div>

                        <div class="tab-pane m-t-10 fade" id="logsTab">
                          <table id="datatable" class="table table-borderless">
                            <thead>
                              <tr>
                                <th>Log ID</th>
                                <th>Action</th>
                                <th>Date/Time</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              $sql             = "SELECT * FROM logs WHERE username=?";
                              $stmt            = $pdo->prepare($sql);
                              $stmt->execute([$editingUser['username']]);
                              $logRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
                              foreach ($logRow as $log) {
                              echo '
                              <tr>
                                  <td>'. $log['log_id'] .'</td>
                                  <td>'. $log['action'] .'</td>
                                  <td>'. $log['timestamp'] .'</td>
                              </tr>
                              ';
                              }
                            ?>
                            </tbody>
                          </table>
                        </div>

                        <div class="tab-pane m-t-10 fade" id="actionsTab">
                          <div class="row">
                            <div class="col-12">
                              <h4 class="m-t-0 header-title">Ban Manager</h4>
                              <form method="POST">
                                <div class="form-group">
                                  <div class="col-12">
                                    <label for="reason">Reason</label>
                                    <input class="form-control" type="text" id="reason" name="reason" placeholder="Reason" required>
                                  </div>
                                </div>
                                <div class="form-group text-center">
                                  <div class="col-12">
                                    <button class="btn btn-danger btn-bordred waves-effect waves-light float-right" style="margin-left:10px;" type="submit" name="banUserBtn" onclick="return confirm('Are you sure you want to ban <?php echo $editingUser['username']; ?>?')">Ban <?php echo $editingUser['username']; ?></button>
                                    <button class="btn btn-danger btn-bordred waves-effect waves-light float-right" type="submit" name="delUserBtn" onclick="return confirm('Are you sure you want to delete <?php echo $editingUser['username']; ?>?')">DELETE <?php echo $editingUser['username']; ?></button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php include 'inc/copyright.php'; ?>
    <?php include 'inc/page-bottom.php'; ?>
    <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
