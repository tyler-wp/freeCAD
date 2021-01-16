<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';
require_once 'inc/config.php';
require_once 'inc/backend/user/auth/userIsLoggedIn.php';
tylerdator();
$page['name'] = 'Staff Panel';
if (staff_access === 'true' && staff_siteSettings === 'true') {
  if (isset($_POST['enableAlertsBtn'])) {
    $webhook_url    = !empty($_POST['webhook_url']) ? trim($_POST['webhook_url']) : null;
    $webhook_url    = strip_tags($webhook_url);

    if (empty($webhook_url)) {
      echo "<script> location.replace('staff.php?m=settings&error=webhook-empty'); </script>";
      exit();
    }

    if (!filter_var($webhook_url, FILTER_VALIDATE_URL)) {
      echo "<script> location.replace('staff.php?m=settings&error=webhook-invalid'); </script>";
      exit();
    }

    $sql = "UPDATE settings SET discord_alerts=?, discord_webhook=? WHERE setting_id=?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['true', $webhook_url, '1']);

    if ($result) {
      discordAlert('This message is to verify that you have successfully setup Discord Alerts on **freeCAD**. If you would like to disable Discord Alerts, you can do so from the Admin Panel.
      - **freeCAD System**');
    }
    echo "<script> location.replace('staff.php?m=settings&success=webhook-setup'); </script>";
    exit();
  }
  if (isset($_POST['disableAlertsBtn'])) {
    $sql = "UPDATE settings SET discord_alerts=? WHERE setting_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['false', '1']);
    echo "<script> location.replace('staff.php?m=settings&success=webhook-disabled'); </script>";
    exit();
  }

  //Steam login stuff
  if (isset($_POST['disableSteamLoginBtn'])) {
    $sql = "UPDATE settings SET steam_required=? WHERE setting_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['false', '1']);
    echo "<script> location.replace('staff.php?m=settings&success=steam-disabled'); </script>";
    exit();
  }

  if (isset($_POST['enableSteamLoginBtn'])) {
    $steam_api    = !empty($_POST['steam_api']) ? trim($_POST['steam_api']) : null;
    $steam_api    = strip_tags($steam_api);

    if (empty($steam_api)) {
      echo "<script> location.replace('staff.php?m=settings&error=api-empty'); </script>";
      exit();
    }

    $sql = "UPDATE settings SET steam_required=?, steam_domain=?, steam_api=? WHERE setting_id=?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['true', $doul, $steam_api, '1']);

    echo "<script> location.replace('staff.php?m=settings&success=discord-setup'); </script>";
    exit();
  }
}

if (staff_siteSettings === 'true') {
  // Makes sure the user is actually Super Admin before allowing them to wipe anything
  if (isset($_POST['dbu_civLayout'])) {
    $pdo->query("ALTER TABLE `settings` ADD `civ_side_layout` ENUM('regular','tabs') NOT NULL DEFAULT 'regular' AFTER `civ_side_warrants`");
    sleep(5);
  }
  if (isset($_POST['dbu_penalCode'])) {
    $pdo->query("ALTER TABLE `settings` ADD `penalcode_url` TEXT NULL DEFAULT NULL AFTER `group_banGroup`");
    sleep(5);
  }
  if (isset($_POST['dbu_liveMap'])) {
    $pdo->query("ALTER TABLE `settings` ADD `livemap_url` TEXT NULL AFTER `group_banGroup`");
    sleep(5);
  }
  if (isset($_POST['wipeLogsBtn'])) {
    sleep(6);
    $stmt       = $pdo->prepare("DELETE FROM logs");
    $stmt->execute();
    sleep(15);
    $sql2 = "INSERT INTO logs (action, username, timestamp) VALUES (?,?,?)";
    $pdo->prepare($sql2)->execute(['Wiped All Logs', $user['username'], $datetime]);

    header('Location: '.$url['staff'].'?m=settings&success=wiped-logs');
    exit();
  } elseif (isset($_POST['wipeCharactersBtn'])) {
    sleep(6);
    $stmt       = $pdo->prepare("DELETE FROM characters");
    $stmt->execute();
    sleep(6);
    //

    $stmt3       = $pdo->prepare("DELETE FROM arrest_reports");
    $stmt3->execute();
    sleep(6);
    //

    $stmt4       = $pdo->prepare("DELETE FROM tickets");
    $stmt4->execute();
    sleep(6);
    //

    $stmt5       = $pdo->prepare("DELETE FROM vehicles");
    $stmt5->execute();
    sleep(6);
    //

    $stmt6       = $pdo->prepare("DELETE FROM warrants");
    $stmt6->execute();
    sleep(6);
    //

    $stmt7       = $pdo->prepare("DELETE FROM weapons");
    $stmt7->execute();
    sleep(6);
    //

    $sql2 = "INSERT INTO logs (action, username, timestamp) VALUES (?,?,?)";
    $pdo->prepare($sql2)->execute(['Wiped All Characters', $user['username'], $datetime]);

    header('Location: '.$url['staff'].'?m=settings&success=wiped-characters');
    exit();
  } elseif (isset($_POST['wipeIdentitiesBtn'])) {
    sleep(6);
    $stmt       = $pdo->prepare("DELETE FROM identities");
    $stmt->execute();
    sleep(6);
    //

    $stmt3       = $pdo->prepare("DELETE FROM arrest_reports");
    $stmt3->execute();
    sleep(6);
    //

    $stmt4       = $pdo->prepare("DELETE FROM assigned_callunits");
    $stmt4->execute();
    sleep(6);
    //

    $stmt5       = $pdo->prepare("DELETE FROM bolos");
    $stmt5->execute();
    sleep(6);
    //

    $stmt6       = $pdo->prepare("DELETE FROM on_duty");
    $stmt6->execute();
    sleep(6);
    //

    $stmt7       = $pdo->prepare("DELETE FROM tickets");
    $stmt7->execute();
    sleep(6);
    //

    $sql2 = "INSERT INTO logs (action, username, timestamp) VALUES (?,?,?)";
    $pdo->prepare($sql2)->execute(['Wiped All Identities', $user['username'], $datetime]);

    header('Location: '.$url['staff'].'?m=settings&success=wiped-identities');
    exit();
  }

  if (isset($_GET['a']) && strip_tags($_GET['a']) === 'delDivid') {
      if (isset($_GET['divId']) && strip_tags($_GET['divId'])) {
        $divId = strip_tags($_GET['divId']);
        $stmt = $pdo->prepare("DELETE FROM leo_division WHERE id =:id");
        $stmt->bindParam(':id', $divId);
        $stmt->execute();
        sleep(3);
        header('Location: staff.php?success=division-deleted');
        exit();
      } else {
        header('Location: staff.php');
        exit();
      }
  }

  if (isset($_POST['createNewDivLEOBtn'])) {
    $name  = !empty($_POST['divName']) ? trim($_POST['divName']) : null;
    $name  = strip_tags($name);

    $sql = "INSERT INTO leo_division (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$name]);

    header('Location: staff.php?success=division-created');
    exit();
  }
}

if (isset($_POST['postNewsBtn'])) {
  if (staff_newsAccess === 'false') {
    die('Internal Error.');
  }
  $title  = !empty($_POST['title']) ? trim($_POST['title']) : null;
  $title  = strip_tags($title);

  $message  = !empty($_POST['message']) ? trim($_POST['message']) : null;
  $message  = strip_tags($message);

  $sql = "INSERT INTO news (title, message, datetime, author) VALUES (?,?,?,?)";
  $stmt = $pdo->prepare($sql);
  $result = $stmt->execute([$title, $message, $datetime, $user['username']]);

  header('Location: staff.php?success=news-posted');
  exit();
}

if (staff_newsAccess === 'true') {
  if (isset($_GET['a']) && strip_tags($_GET['a']) === 'deleteNews') {
      if (isset($_GET['newsId']) && strip_tags($_GET['newsId'])) {
        $newsId = strip_tags($_GET['newsId']);
        $stmt = $pdo->prepare("DELETE FROM news WHERE id =:id");
        $stmt->bindParam(':id', $newsId);
        $stmt->execute();
        sleep(3);
        header('Location: staff.php?success=news-deleted');
        exit();
      } else {
        header('Location: staff.php');
        exit();
      }
  }
}

include 'inc/page-top.php'; ?>
<script src="assets/js/pages/staff.js"></script>
<body>
    <?php include 'inc/top-nav.php';
    if (isset($_GET['error']) && strip_tags($_GET['error']) === 'webhook-invalid') {
        throwError('Invalid Discord Webhook Entered');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'webhook-empty') {
        throwError('You must enter a Discord Webhook to enable this feature!.');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'webhook-setup') {
      clientNotify('success', 'You have now setup Discord Alerts. We will send a welcome alert to verify it is all working!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'webhook-disabled') {
      clientNotify('success', 'Discord Alerts have been disabled!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'wiped-logs') {
      clientNotify('success', 'All Logs Have Been Wiped!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'wiped-characters') {
      clientNotify('success', 'All Characters Have Been Wiped!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'wiped-identities') {
      clientNotify('success', 'All Characters Have Been Wiped!');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'perm') {
      clientNotify('error', 'You can not edit this user!');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'unf') {
      clientNotify('error', 'User not found!');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'ep') {
      clientNotify('error', 'Error while trying to edit!');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'see') {
      clientNotify('error', 'You can not edit yourself via the admin panel. Please use the settings menu!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'news-posted') {
      clientNotify('success', 'News posted!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'news-deleted') {
      clientNotify('success', 'News has been deleted!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'steam-disabled') {
      clientNotify('success', 'Steam Login has been disabled!');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'api-empty') {
      clientNotify('error', 'Invalid API!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'steam-setup') {
      clientNotify('success', 'Steam Login has been setup!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'division-deleted') {
      clientNotify('success', 'Division Deleted!');
    } elseif (isset($_GET['success']) && strip_tags($_GET['success']) === 'division-created') {
      clientNotify('success', 'Division Created!');
    } elseif (isset($_GET['user']) && strip_tags($_GET['user']) === 'deleted') {
      clientNotify('error', 'User deleted.');
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
            <!-- CONTENT HERE -->
            <div class="row">
                <div class="col-12">
                    <div class="card-box pb-0">
                      <div id="staffPanelWizard" class="pull-in">
                          <ul class="nav nav-pills navtab-bg nav-justified">
                              <li class="nav-item"><a href="#siteSettingsTab" data-toggle="tab" class="nav-link">Settings</a></li>
                              <li class="nav-item"><a href="#siteUsersTab" data-toggle="tab" class="nav-link">Users</a></li>
                          </ul>

                          <div class="tab-content">
                            <div class="tab-pane m-t-10 fade" id="siteSettingsTab">
                              <div id="staffSettingsPanelWizard" class="pull-in">
                                  <ul class="nav nav-pills navtab-bg ">
                                    <?php if (staff_siteSettings === 'true'): ?>
                                      <li class="nav-item"><a href="#globalSettingsTab" data-toggle="tab" class="nav-link">Global Settings</a></li>
                                      <li class="nav-item hide-phone"><a href="#thirdPartySettingsTab" data-toggle="tab" class="nav-link">Third-Party Settings</a></li>
                                      <li class="nav-item hide-phone"><a href="#moduleConfigTab" data-toggle="tab" class="nav-link">Module Config</a></li>
                                    <?php endif; ?>
                                    <?php if (staff_newsAccess === 'true'): ?>
                                      <li class="nav-item hide-phone"><a href="#newsTab" data-toggle="tab" class="nav-link">News System</a></li>
                                    <?php endif; ?>
                                  </ul>

                                  <div class="tab-content">
                                      <?php if (staff_siteSettings === 'true'): ?>
                                        <div class="tab-pane m-t-10 fade" id="globalSettingsTab">
                                          <div class="row">
                                              <div class="col">
                                                  <div class="form-group">
                                                      <label for="IdentityVerification">Identity Verification</label>
                                                      <select class="form-control" id="IdentityVerification" onchange="setIdentityVerification(this.value)">
                                                          <option selected="true" disabled="disabled"><?php if ($settings['identity_validation'] === "no") {
                                                            echo 'No';
                                                          } else {
                                                            echo 'Yes';
                                                          } ?></option>
                                                          <option value="no">No</option>
                                                          <option value="yes">Yes</option>
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="col">
                                                  <div class="form-group">
                                                      <label for="SignUpVerification">Account Verification</label>
                                                      <select class="form-control" id="SignUpVerification" onchange="setAccountVerification(this.value)">
                                                          <option selected="true" disabled="disabled"><?php if ($settings['account_validation'] === "no") {
                                                            echo 'No';
                                                          } else {
                                                            echo 'Yes';
                                                          } ?></option>
                                                          <option value="no">No</option>
                                                          <option value="yes">Yes</option>
                                                      </select>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="row">
                                              <div class="col">
                                                  <form id="updateSiteName" action="inc/backend/staff/settings/setSiteName.php" method="post">
                                                      <div class="form-group">
                                                          <label for="site_name">Site Name</label>
                                                          <input class="form-control" type="text" required="" name="site_name" value="<?php echo $settings['name']; ?>" placeholder="<?php echo $settings['name']; ?>">
                                                      </div>
                                                      <div class="form-group">
                                                          <button class="btn btn-success btn-block" onClick="disableClick()" type="submit">Update</button>
                                                      </div>
                                                  </form>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="tab-pane m-t-10 fade" id="thirdPartySettingsTab">
                                          <div class="row">
                                            <div class="col-6">
                                              <div class="card-box">
                                                  <h4 class="m-t-0 header-title">Discord Alerts</h4>
                                                  <div class="alert alert-warning" role="alert"><strong>How To Setup - </strong>To setup the Discord Alert system, please follow all of the steps.<br>
                                                      1 - Create a Channel In Discord that that alerts will be sent in.<br>
                                                      2 - Right Click the server --> Server Settings --> Web Hooks<br>
                                                      3 - Press "Create Webhook"<br>
                                                      4 - Name (freeCAD Alerts) : Channel (The channel you setup) : Copy the "WEBHOOK URL"<br>
                                                      5 - Paste the Webhook URL in the textbox below
                                                  </div>
                                                  <form method="POST">
                                                      <div class="form-group">
                                                          <div class="col-12">
                                                              <label for="webhook_url">Webhook URL</label>
                                                              <input class="form-control" type="text" required="" name="webhook_url" id="webhook_url" value="<?php if ($discord_webhook === NULL || $discord_webhook === "") {
                                                                echo '';
                                                              } else {
                                                                echo $discord_webhook;
                                                              } ?>" placeholder="Discord Webhook URL">
                                                          </div>
                                                      </div>
                                                      <div class="form-group text-center">
                                                          <div class="col-12">
                                                              <?php if($settings['discord_alerts'] === 'true'): ?>
                                                              <button class="btn btn-danger btn-bordred btn-block waves-effect waves-light" type="submit" name="disableAlertsBtn">Disable Alerts</button>
                                                              <?php else: ?>
                                                              <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="enableAlertsBtn">Enable Alerts</button>
                                                              <?php endif; ?>
                                                          </div>
                                                      </div>
                                                  </form>
                                              </div>
                                            </div>
                                            <div class="col-6">
                                              <div class="card-box">
                                                <form method="POST">
                                                  <h4 class="m-t-0 header-title">Steam Integration</h4>
                                                  <?php if ($settings['steam_required'] === 'true'): ?>
                                                    <div class="alert alert-warning" role="alert">Steam Login is enabled, Press 'Disable' to disable it.</div>
                                                    <button class="btn btn-danger btn-bordred btn-block waves-effect waves-light" type="submit" name="disableSteamLoginBtn">Disable Steam Login</button>
                                                  <?php else: ?>
                                                    <div class="form-group">
                                                      <div class="col-12">
                                                        <input class="form-control" type="text" required="" name="steam_api" id="steam_api" value="<?php if ($settings['steam_api'] === NULL || $settings['steam_api'] === "") {
                                                        echo '';
                                                      } else {
                                                        echo $settings['steam_api'];
                                                      } ?>" placeholder="Steam API">
                                                      </div>
                                                    </div>
                                                    <div class="form-group text-center">
                                                      <div class="col-12">
                                                        <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="enableSteamLoginBtn">Enable Steam Login</button>
                                                      </div>
                                                    </div>
                                                  <?php endif; ?>
                                                </form>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                        <div class="tab-pane m-t-10 fade" id="moduleConfigTab">
                                          <div class="row">
                                            <div class="col-6">
                                              <div class="form-group">
                                                  <label for="darkmode">Civ Side Warrants</label>
                                                  <select class="form-control" id="steam_login" onchange="setCivSideWarrants(this.value)">
                                                      <option selected="true" disabled="disabled"><?php
                                                      if ($settings['civ_side_warrants'] === "true") {
                                                        echo 'Enabled';
                                                      } elseif ($settings['civ_side_warrants'] === "false") {
                                                        echo 'Disabled';
                                                      }
                                                      ?>
                                                      </option>
                                                      <option value="true">Enabled</option>
                                                      <option value="false">Disabled</option>
                                                  </select>
                                              </div>
                                            </div>
                                            <div class="col-6">
                                              <div class="form-group">
                                                  <label for="darkmode">Who Can Add Warrants</label>
                                                  <select class="form-control" id="steam_login" onchange="setAddWarrantPerm(this.value)">
                                                      <option selected="true" disabled="disabled"><?php
                                                      if ($settings['add_warrant'] === "all") {
                                                        echo 'All LEO';
                                                      } elseif ($settings['add_warrant'] === "supervisor") {
                                                        echo 'Supervisors Only';
                                                      }
                                                      ?>
                                                      </option>
                                                      <option value="all">All LEO</option>
                                                      <option value="supervisor">Supervisors Only</option>
                                                  </select>
                                              </div>
                                            </div>
                                          </div>

                                          <div class="row">
                                            <div class="col-6">
                                              <label>Division Manager</label>
                                              <button type="button" class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#divCreator">
                                                New Division
                                              </button>
                                              <!-- Modal -->
                                              <div class="modal fade" id="divCreator" tabindex="-1" role="dialog" aria-labelledby="divCreator" aria-hidden="true">
                                                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                                  <div class="modal-content">
                                                    <div class="modal-header">
                                                      <h5 class="modal-title" id="divCreator">Create New LEO Division</h5>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                      </button>
                                                    </div>
                                                    <div class="modal-body">
                                                      <form method="POST">
                                                        <div class="form-group">
                                                          <div class="col-12">
                                                              <input class="form-control" type="text" required="" name="divName" placeholder="Name">
                                                          </div>
                                                        </div>
                                                        <div class="form-group text-center">
                                                          <div class="col-12">
                                                              <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="createNewDivLEOBtn">Create</button>
                                                          </div>
                                                        </div>
                                                      </form>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <table class="table table-borderless">
                                                  <thead>
                                                      <tr>
                                                          <th>Division</th>
                                                          <th>Actions</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody>
                                                  <?php
                                                  $sql             = "SELECT * FROM leo_division";
                                                  $stmt            = $pdo->prepare($sql);
                                                  $stmt->execute();
                                                  $leodivRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                  foreach ($leodivRow as $division) {
                                                    echo '
                                                    <tr>
                                                      <td width="95%">'. $division['name'] .'</td>
                                                      <td width="5%"><a href="staff.php?a=delDivid&divId='. $division['id'] .'"><input type="button" class="btn btn-sm btn-danger" value="Delete"></a></td>
                                                  </tr>
                                                    ';
                                                  }
                                                  ?>
                                                  </tbody>
                                              </table>
                                            </div>

                                            <?php if ($civ_side_layout_clm_exists === true): ?>
                                              <div class="col-6">
                                                <div class="form-group">
                                                    <label for="civLayout">Civ Module Layout</label>
                                                    <select class="form-control" id="civLayout" onchange="setCivLayout(this.value)">
                                                        <option selected="true" disabled="disabled"><?php
                                                        if ($settings['civ_side_layout'] === "regular") {
                                                          echo 'Regular';
                                                        } elseif ($settings['civ_side_layout'] === "tabs") {
                                                          echo 'Tabs';
                                                        }
                                                        ?>
                                                        </option>
                                                        <option value="regular">Regular</option>
                                                        <option value="tabs">Tabs</option>
                                                    </select>
                                                </div>
                                              </div>
                                            <?php else: ?>
                                              <div class="col-6">
                                                <label>Civ Module Layout</label>
                                                <div class="alert alert-danger" role="alert">Please click below to update your database to work with the Civilian Layout Module. If you do not update it, you can not use it.</div>
                                                <form method="post">
                                                  <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="dbu_civLayout">Start Database Update...</button>
                                                </form>
                                              </div>
                                            <?php endif; ?>

                                            <?php if ($penalcode_db_setup === true): ?>
                                              <div class="col-6">
                                                <div class="form-group">
                                                    <label for="penalCodeUrl">Penal Code</label>
                                                    <form id="setPenalCode" action="inc/backend/staff/settings/setPenalCode.php" method="post">
                                                      <div class="form-group">
                                                          <input type="text" name="pcURL" class="form-control" placeholder="URL" value="<?php echo $settings['penalcode']; ?>" data-lpignore="true" required />
                                                      </div>
                                                      <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="setPenalCodeURL">Set URL</button>
                                                    </form>
                                                </div>
                                              </div>
                                            <?php else: ?>
                                              <div class="col-6">
                                                <label>Penal Code</label>
                                                <div class="alert alert-danger" role="alert">Please click below to update your database to work with the Penal Code Plugin. If you do not update it, you can not use it.</div>
                                                <form method="post">
                                                  <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="dbu_penalCode">Start Database Update...</button>
                                                </form>
                                              </div>
                                            <?php endif; ?>

                                            <?php if ($livemap_db_setup === true): ?>
                                              <div class="col-6">
                                                <div class="form-group">
                                                    <label for="liveMapURL">LiveMap</label>
                                                    <form id="setLiveMap" action="inc/backend/staff/settings/setLiveMap.php" method="post">
                                                      <div class="form-group">
                                                          <input type="text" name="lmURL" class="form-control" placeholder="URL" value="<?php echo $settings['livemap']; ?>" data-lpignore="true" required />
                                                      </div>
                                                      <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="setLiveMapURL">Set URL</button>
                                                    </form>
                                                </div>
                                              </div>
                                            <?php else: ?>
                                              <div class="col-6">
                                                <label>LiveMap</label>
                                                <div class="alert alert-danger" role="alert">Please click below to update your database to work with the LiveMap Plugin. If you do not update it, you can not use it.</div>
                                                <form method="post">
                                                  <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="dbu_liveMap">Start Database Update...</button>
                                                </form>
                                              </div>
                                            <?php endif; ?>

                                            <div class="col-6">
                                              <div class="form-group">
                                                  <label for="civLayout">Civ Character Limit</label>
                                                  <select class="form-control" id="civLayout" onchange="setCivCharLimit(this.value)">
                                                      <option selected="true" disabled="disabled"><?php
                                                      if ($settings['civ_char_limit'] == "999999") {
                                                        echo 'Unlimited';
                                                      } else {
                                                        echo $settings['civ_char_limit'];
                                                      }
                                                      ?>
                                                      </option>
                                                      <option value="999999">Unlimited</option>
                                                      <option value="1">1</option>
                                                      <option value="2">2</option>
                                                      <option value="3">3</option>
                                                      <option value="4">4</option>
                                                      <option value="5">5</option>
                                                  </select>
                                              </div>
                                            </div>

                                          </div>
                                        </div>
                                      <?php endif; ?>
                                      <?php if (staff_newsAccess === 'true'): ?>
                                        <div class="tab-pane m-t-10 fade" id="newsTab">
                                          <div class="row">
                                            <div class="col-12">
                                              <button type="button" class="btn btn-md btn-success" data-toggle="modal" data-target="#newNewsPost">
                                                Create +
                                              </button>
                                              <!-- Modal -->
                                              <div class="modal fade" id="newNewsPost" tabindex="-1" role="dialog" aria-labelledby="newNewsPost" aria-hidden="true">
                                                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                                  <div class="modal-content">
                                                    <div class="modal-header">
                                                      <h5 class="modal-title" id="newNewsPost">Create New News Post</h5>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                      </button>
                                                    </div>
                                                    <div class="modal-body">
                                                      <form method="POST">
                                                        <div class="form-group">
                                                          <div class="col-12">
                                                              <input class="form-control" type="text" required="" name="title" placeholder="Title">
                                                          </div>
                                                        </div>
                                                        <div class="form-group">
                                                          <div class="col-12">
                                                            <textarea class="form-control" rows="4" cols="50" name="message" placeholder="Message"></textarea>
                                                          </div>
                                                        </div>
                                                        <div class="form-group text-center">
                                                          <div class="col-12">
                                                              <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="postNewsBtn">Post</button>
                                                          </div>
                                                        </div>
                                                      </form>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <table id="datatable" class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th>News ID</th>
                                                        <th>Title</th>
                                                        <th>Date / Time</th>
                                                        <th>Author</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>


                                                <tbody>
                                                <?php
                                                $sql             = "SELECT * FROM news";
                                                $stmt            = $pdo->prepare($sql);
                                                $stmt->execute();
                                                $newsRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($newsRow as $news) {
                                                  echo '
                                                  <tr>
                                                    <td>'. $news['id'] .'</td>
                                                    <td>'. $news['title'] .'</td>
                                                    <td>'. $news['datetime'] .'</td>
                                                    <td>'. $news['author'] .'</td>
                                                    <td><a href="staff.php?a=deleteNews&newsId='. $news['id'] .'" class="btn btn-sm btn-danger btn-block">Delete</a></td>
                                                  </tr>
                                                  ';
                                                }
                                                ?>
                                                </tbody>
                                              </table>
                                            </div>
                                          </div>
                                        </div>
                                      <?php endif; ?>
                                  </div>
                              </div>
                            </div>

                            <div class="tab-pane m-t-10 fade" id="siteUsersTab">
                              <div id="staffSettingsPanelWizard" class="pull-in">
                                  <ul class="nav nav-pills navtab-bg nav-justified">
                                    <?php if (staff_viewUsers === 'true'): ?>
                                      <li class="nav-item"><a href="#activeUsersTab" data-toggle="tab" class="nav-link">Active Users</a></li>
                                    <?php endif; ?>
                                    <?php if (staff_approveUsers === 'true'): ?>
                                      <li class="nav-item"><a href="#pendingUsersTab" data-toggle="tab" class="nav-link">Pending Users</a></li>
                                    <?php endif; ?>
                                  </ul>

                                  <div class="tab-content">
                                    <?php if (staff_approveUsers === 'true'): ?>
                                      <div class="tab-pane m-t-10 fade" id="pendingUsersTab">
                                        <div id="getPendingUsers"></div>
                                      </div>
                                    <?php endif; ?>
                                    <?php if (staff_viewUsers === 'true'): ?>
                                      <div class="tab-pane m-t-10 fade" id="activeUsersTab">
                                        <div class="row">
                                          <div class="col-12">
                                            <table id="datatable" class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th class="hide-phone">User ID</th>
                                                        <th>Username</th>
                                                        <th class="hide-phone">Email</th>
                                                        <th class="hide-phone">Usergroup</th>
                                                        <th class="hide-phone">Join Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>


                                                <tbody>
                                                <?php
                                                $sql             = "SELECT * FROM users";
                                                $stmt            = $pdo->prepare($sql);
                                                $stmt->execute();
                                                $usersRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($usersRow as $user) {
                                                  echo '
                                                  <tr>
                                                    <td class="hide-phone">'. $user['user_id'] .'</td>
                                                    <td>'. $user['username'] .'</td>
                                                    <td class="hide-phone">'. $user['email'] .'</td>';
                                                    $sql1_gugp             = "SELECT * FROM usergroups WHERE id = :usergroup";
                                                    $stmt1_gugp            = $pdo->prepare($sql1_gugp);
                                                    $stmt1_gugp->bindValue(':usergroup', $user['usergroup']);
                                                    $stmt1_gugp->execute();
                                                    $groupRow = $stmt1_gugp->fetch(PDO::FETCH_ASSOC);
                                                    echo '<td class="hide-phone">'. $groupRow['name'] .'</td>
                                                    <td class="hide-phone">'. $user['join_date'] .'</td>
                                                    <td><a href="staff-edituser.php?user-id='. $user['user_id'] .'"><input type="button" class="btn btn-sm btn-success btn-block" value="Edit"></a></td>
                                                </tr>
                                                  ';
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                          </div>
                                        </div>
                                      </div>
                                    <?php endif; ?>
                                  </div>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CONTENT END -->
        <?php include 'inc/copyright.php'; ?>
        <?php include 'inc/page-bottom.php'; ?>
        <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
