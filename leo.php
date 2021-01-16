<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';

require_once 'inc/config.php';

require_once 'inc/backend/user/auth/userIsLoggedIn.php';

$page['name'] = 'Law Enforcement Module';
tylerdator();
// Page PHP
$view = strip_tags($_GET['v']);

if (isset($_GET['v']) && strip_tags($_GET['v']) === 'setsession') {
    if (isset($_GET['id']) && strip_tags($_GET['id'])) {
        $id   = $_GET['id'];
        $sql  = "SELECT * FROM identities WHERE identity_id = :identity_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':identity_id', $id);
        $stmt->execute();
        $identityDB = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($identityDB === false) {
          header('Location: ' . $url['leo'] . '?v=nosession&error=identity-not-found');
          exit();
        } else {
          $identity_id             = $identityDB['identity_id'];
          $_SESSION['identity_id'] = $identity_id;

          $identity_name             = $identityDB['name'];
          $_SESSION['identity_name'] = $identity_name;

          $identity_department             = $identityDB['department'];
          $_SESSION['identity_department'] = $identity_department;

          $identity_division             = $identityDB['division'];
          $_SESSION['identity_division'] = $identity_division;

          $identity_supervisor             = $identityDB['supervisor'];
          $_SESSION['identity_supervisor'] = $identity_supervisor;

          $identity_owner             = $identityDB['user'];
          $_SESSION['identity_owner'] = $identity_owner;

          $_SESSION['notepad'] = "";

          $_SESSION['on_duty'] = "LEO";

          if ($identity_owner !== $user_id) {
    				header('Location: '.$url['leo'].'?v=nosession&error=identity-owner');
    				exit();
	        } elseif ($identityDB['status'] === "Suspended") {
            header('Location: '.$url['leo'].'?v=nosession');
          } elseif ($identityDB['status'] === "Pending Approval") {
            header('Location: '.$url['leo'].'?v=nosession');
          }

          $stmt2              = $pdo->prepare("DELETE FROM `on_duty` WHERE `uid`=:uid");
     			$stmt2->bindValue(':uid', $_SESSION['user_id']);
     			$result2 = $stmt2->execute();

    			$stmt3              = $pdo->prepare("INSERT INTO on_duty (name, department, division, status, uid) VALUES (:name, :department, :division, '10-41', :uid)");
    			$stmt3->bindValue(':name', $identity_name);
    			$stmt3->bindValue(':department', $identity_department);
    			$stmt3->bindValue(':division', $identity_division);
          $stmt3->bindValue(':uid', $_SESSION['user_id']);
    			$result3 = $stmt3->execute();
          shiftLog($_SESSION['identity_id']);

          header('Location: '.$url['leo'].'?v=main');
	        exit();
        }
    }
}
?>
<?php include 'inc/page-top.php'; ?>
<script src="assets/js/pages/leo.js?v=<?php echo $assets_ver ?>"></script>
<body>
    <?php include 'inc/top-nav.php';
    if (isset($_GET['error']) && strip_tags($_GET['error']) === 'identity-not-found') {
        clientNotify('error', 'We couldn\'t find that Identity.');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'identity-owner') {
        clientNotify('error', 'No Permission.');
    } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'identity-session') {
        clientNotify('error', 'Session Error. Select Identity again.');
    }
    ?>
    <!-- CONTENT START -->
    <div class="wrapper m-b-15 m-t-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h4 class="page-title">
                        <?php echo $page['name']; ?>
                    </h4>
                </div>
            </div>
            <!-- CONTENT HERE -->
            <?php switch($view):
			         case "nosession": ?>
               <script type="text/javascript">
               noSessionAjax();
               </script>
            <div class="row">
                <div class="col">
                    <div class="card-box">
                        <select class="form-control" id="listIdentitys" onchange="location = this.value;">
                            <option selected="true" disabled="disabled">Loading Identities...</option>
                        </select>
                    </div>
                </div>
                <div class="col hide-phone">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Identity Creator</h4>
                        <form class="form-horizontal m-t-20" id="createIdentity" action="inc/backend/user/leo/createIdentity.php" method="POST">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="name" placeholder="[1A-01] John Doe">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <select class="form-control" id="listLeoDivisions" name="division" required>
                                            <option selected="true" disabled="disabled">Loading Divisions...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input class="btn btn-success btn-block" type="submit" value="Create Character">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php break; ?>
            <?php case "main": ?>
            <!-- js is put here to prevent issues on other parts of leo -->
            <script type="text/javascript">
            getMyCalls();
            noDis911Calls();
            getBolos();
            getAllCharacters();
            checkActiveDispatchers();

            $(document).on('show.bs.modal', '#openFirearmSearch', function (e) {
                getAllFirearms();
            });
            $(document).on('show.bs.modal', '#openVehicleSearch', function (e) {
                getAllVehicles();
            });
            $(document).ready(function() {
                var signal100 = false;

                startTime();
                loadStatus();
                getLeoInfo();
            });
            </script>
            <!-- code here -->
            <div class="row">
                <div class="col">
                    <div class="card-box">
                        <div class="dropdown pull-right">
                            <b>
                                <div id="getTime">Loading...</div>
                            </b>
                        </div>
                        <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['identity_name']; ?> <?php if ($_SESSION['identity_supervisor'] === "Yes"): ?><small>[Supervisor]</small><?php endif; ?> <label id="signal100Status"></label></h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm hide-phone dropdown-toggle" style="height:29px;" data-toggle="dropdown" aria-expanded="false">Databases <i class="mdi mdi-chevron-down"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#openNameSearch" data-toggle="modal" data-target="#openNameSearch">Name Database</a>
                                <a class="dropdown-item" href="#openVehicleSearch" data-toggle="modal" data-target="#openVehicleSearch">Vehicle Database</a>
                                <a class="dropdown-item" href="#openFirearmSearch" data-toggle="modal" data-target="#openFirearmSearch">Weapon Database</a>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm hide-phone dropdown-toggle" style="height:29px;" data-toggle="dropdown" aria-expanded="false">Reports <i class="mdi mdi-chevron-down"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#newTicketModal" data-toggle="modal" data-target="#newTicketModal">Ticket</a>
                                <a class="dropdown-item" href="#newArrestReportModal" data-toggle="modal" data-target="#newArrestReportModal">Arrest</a>
                                <a class="dropdown-item" href="#newBoloModal" data-toggle="modal" data-target="#newBoloModal">Bolo</a>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm hide-phone dropdown-toggle" style="height:29px;" data-toggle="dropdown" aria-expanded="false">Tools <i class="mdi mdi-chevron-down"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#notepadModal" data-toggle="modal" data-target="#notepadModal">Notepad</a>
                                <?php if ($penalcode_db_setup === true): ?>
                                  <a class="dropdown-item" href="#openPenalcode" data-toggle="modal" data-target="#openPenalcode">Penal Code</a>
                                <?php endif; ?>
                                <a class="dropdown-item" href="#impoundManagerModel" data-toggle="modal" data-target="#impoundManagerModel">Impounder</a>
                                <?php if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings === 'true'): ?>
                                  <a class="dropdown-item" href="leo.php?v=supervisor">Supervisor Panel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button class="btn btn-danger float-right btn-sm hide-phone" onclick="officerPanicBtn();">PANIC BUTTON</button>
                    </div>
                </div>
            </div>
            <div id="checkDispatchers"></div>
            <div class="row hide-phone">
                <div class="col-9">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">My Calls
                        </h4>
                        <div id="getMyCalls"></div>
                        <div id="noDis911Calls"></div>
                    </div>

                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Active Bolos</h4>
                        <div id="getBolos"></div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Current Status: <label id="getDutyStatus">Loading...</label></h4>
                        <div class="form-group">
                            <select class="form-control" name="setUnitStatus" onChange='setUnitStatus(this)'>
                                <?php
            										$sql             = "SELECT * FROM 10_codes";
            										$stmt            = $pdo->prepare($sql);
            										$stmt->execute();
            										$dbq10codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            										foreach($dbq10codes as $codes) {
            											echo '<option value="'. $codes['code'] .'">'. $codes['code'] .'</option>';
            										}
            										?>
                            </select>
                        </div>
                    </div>
                    <?php if($settings['add_warrant'] === "supervisor" && $_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings === 'true'): ?>
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Quick Warrant Creator</h4>
                        <form method="post" action="inc/backend/user/leo/addWarrant.php" id="addWarrant">
                            <div class="form-group">
                                <div class="col">
                                    <select required class="form-control select2" name="civilian" id="getAllCharacters4">
                                        <option selected="true" disabled="disabled">Loading Characters...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col">
                                    <input class="form-control" type="text" required="" name="reason" placeholder="Reason">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col">
                                    <button class="btn btn-info btn-bordred btn-block waves-effect waves-light" onClick="disableClick()" type="submit">Add Warrant</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php elseif ($settings['add_warrant'] === "all"): ?>
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Quick Warrant Creator</h4>
                        <form method="post" action="inc/backend/user/leo/addWarrant.php" id="addWarrant">
                            <div class="form-group">
                                <div class="col">
                                    <select class="select2" name="civilian" id="getAllCharacters4">
                                        <option selected="true" disabled="disabled">Loading Characters...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col">
                                    <input class="form-control" type="text" required="" name="reason" placeholder="Reason">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col">
                                    <button class="btn btn-info btn-bordred btn-block waves-effect waves-light" onClick="disableClick()" type="submit">Add Warrant</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <!-- MODALS -->
            <?php if ($penalcode_db_setup === true): ?>
              <!-- Penal Code Modal -->
              <div class="modal fade" id="openPenalcode" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-full" role="document">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Penal Code</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <iframe height="800" src="<?php echo $settings['penalcode']; ?>"></iframe>
                      </div>
                  </div>
              </div>
              <!-- // -->
            <?php endif; ?>
            <!-- Call Info Modal -->
            <div class="modal fade" id="callInfoModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Call Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div id="callModalBody" class="modal-body">

                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- search name modal -->
            <div class="modal fade" id="openNameSearch" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-full" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Name Database</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <select class="select2" name="nameSearch" id="getAllCharacters" onchange="showName(this.value)">
                                    <option selected="true" disabled="disabled">Loading Characters...</option>
                                </select>
                            </form>
                            <br>
                            <div id="showPersonInfo"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- search vehicle modal -->
            <div class="modal fade" id="openVehicleSearch" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Vehicle Database</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <select class="select2" name="vehicleSearch" id="getAllVehicles" onchange="showVehicle(this.value)">
                                    <option selected="true" disabled="disabled">Loading Vehicles...</option>
                                </select>
                            </form>
                            <br>
                            <div id="showVehicleInfo"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- search firearm modal -->
            <div class="modal fade" id="openFirearmSearch" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Firearms Database</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <select class="select2" name="firearmSearch" id="getAllFirearms" onchange="showFirearm(this.value)">
                                    <option selected="true" disabled="disabled">Loading Firearms...</option>
                                </select>
                            </form>
                            <br>
                            <div id="showFirearmInfo"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- notepad modal -->
            <div class="modal fade" id="notepadModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Notepad</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="leo-index.php">
                                <div class="form-group">
                                    <textarea class="form-control" name="textarea" oninput="updateNotepad(this.value)" rows="12" cols="104"><?php echo $_SESSION['notepad']; ?></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- new ticket modal -->
            <div class="modal fade" id="newTicketModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Writing New Ticket</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newTicket" action="inc/backend/user/leo/newTicket.php" method="post">
                                <div class="form-group">
                                    <select class="select2" name="suspect" id="getAllCharacters2" required>
                                        <option selected="true" disabled="disabled">Loading Characters...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="location" class="form-control" placeholder="Ticket Location" data-lpignore="true" required />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="postal" class="form-control" pattern="\d*" placeholder="(Nearest Postal)" data-lpignore="true" required />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="amount" class="form-control" pattern="\d*" placeholder="Fine Amount" data-lpignore="true" required />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="reason" class="form-control" maxlength="255" placeholder="Ticket Reason(s)" data-lpignore="true" required />
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Submit Ticket">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- New Bolo Modal -->
            <div class="modal fade" id="newBoloModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New BOLO</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newBolo" action="inc/backend/user/dispatch/newBolo.php" method="post">
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="Description (Please include as much detail as possible)" id="description" name="description" style="white-space: pre-line;" wrap="hard" rows="6" required></textarea>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Create New BOLO">
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- new arrest modal -->
            <div class="modal fade" id="newArrestReportModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Writing New Arrest Report</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newArrestReport" action="inc/backend/user/leo/newArrestReport.php" method="post">
                                <div class="form-group">
                                    <select class="select2" name="suspect" id="getAllCharacters3" required>
                                        <option selected="true" disabled="disabled">Loading Characters...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="reason" class="form-control" maxlength="500" placeholder="Summary" data-lpignore="true" required />
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Submit Arrest Report">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <!-- impound manager modal -->
            <div class="modal fade" id="impoundManagerModel" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Impounding Vehicle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="impoundVehicle" action="inc/backend/user/leo/impoundVehicle.php" method="post">
                                <div class="form-group">
                                    <input type="text" name="plate" class="form-control" maxlength="8" placeholder="Vehicle Plate" data-lpignore="true" required />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="reason" class="form-control" maxlength="255" placeholder="Reason for Impound" data-lpignore="true" required />
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Send to Impound">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->
            <?php break; ?>

            <?php case "supervisor": ?>
            <script type="text/javascript">
              getPendingIds();
            </script>
            <?php if($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings === 'true'): ?>
            <?php if(isset($_GET['a']) && strip_tags($_GET['a']) === 'edit-id'): ?>
            <?php
								$id   = $_GET['id'];
								$sql  = "SELECT * FROM identities WHERE identity_id = :identity_id AND department='Law Enforcement'";
								$stmt = $pdo->prepare($sql);
								$stmt->bindValue(':identity_id', $id);
								$stmt->execute();
								$idDB = $stmt->fetch(PDO::FETCH_ASSOC);
								if ($idDB === false) {
									 echo '<script> location.replace("' . $url['leo'] . '?v=supervisor&error=id-not-found"); </script>';
									 exit();
								} else {
									$editing_id['id']	= $idDB['identity_id'];
									$_SESSION['editing_identity_id']	= $editing_id['id'];

									$editing_id['name']	= $idDB['name'];
									$editing_id['division']	= $idDB['division'];
									$editing_id['supervisor']	= $idDB['supervisor'];
									$editing_id['user']	= $idDB['user_name'];
									$editing_id['status']	= $idDB['status'];
								}

								if (isset($_POST['suspendIdBtn'])) {
									$sql = "UPDATE identities SET status=? WHERE identity_id=?";
									$stmt = $pdo->prepare($sql);
									$stmt->execute(['Suspended', $_SESSION['editing_identity_id']]);
									echo '<script> location.replace("' . $url['leo'] . '?v=supervisor&id=suspended"); </script>';
									exit();
								}
								if (isset($_POST['unsuspendIdBtn'])) {
									$sql = "UPDATE identities SET status=? WHERE identity_id=?";
									$stmt = $pdo->prepare($sql);
									$stmt->execute(['Active', $_SESSION['editing_identity_id']]);
									echo '<script> location.replace("' . $url['leo'] . '?v=supervisor&id=unsuspended"); </script>';
									exit();
								}
								if (isset($_POST['editIdBtn'])) {
									$updateDivision    = !empty($_POST['division']) ? trim($_POST['division']) : null;
									$updateDivision    = strip_tags($updateDivision);
									$updateSupervisor    = !empty($_POST['supervisor']) ? trim($_POST['supervisor']) : null;
  								$updateSupervisor    = strip_tags($updateSupervisor);

									$sql = "UPDATE identities SET division=?, supervisor=? WHERE identity_id=?";
									$stmt = $pdo->prepare($sql);
									$stmt->execute([$updateDivision, $updateSupervisor, $_SESSION['editing_identity_id']]);
									echo '<script> location.replace("' . $url['leo'] . '?v=supervisor&id=edited"); </script>';
									exit();
								}
								?>
            <div class="row">
                <div class="col-7">
                    <?php if($editing_id['status'] === "Suspended"): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>This identity is Suspended.</strong>
                    </div>
                    <?php endif; ?>
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Identity Editor (<?php echo $editing_id['name']; ?>)</h4>
                        <form method="POST">
                            <div class="form-group">
                                <div class="col-12">
                                    <label for="supervisor">Supervisor</label>
                                    <select class="custom-select my-1 mr-sm-2" id="supervisor" name="supervisor">
                                        <option selected value="<?php echo $editing_id['supervisor']; ?>"><?php echo $editing_id['supervisor']; ?> (Current)</option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-12">
                                    <label for="division">Division</label>
                                    <select class="custom-select my-1 mr-sm-2" id="division" name="division">
                                        <option selected value="<?php echo $editing_id['division']; ?>"><?php echo $editing_id['division']; ?> (Current)</option>
                                        <?php
              														$sql             = "SELECT * FROM leo_division";
              														$stmt            = $pdo->prepare($sql);
              														$stmt->execute();
              														$divRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
              														foreach($divRow as $leoDivision) {
              															echo '
              																<option value="' . $leoDivision['name'] . '">' . $leoDivision['name'] . '</option>
              															';
              														}
            														?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="editIdBtn">Edit</button>
                                    </div>
                                    <div class="col-6">
                                        <?php if($editing_id['status'] === "Suspended"): ?>
                                        <button class="btn btn-danger btn-bordred btn-block waves-effect waves-light" type="submit" name="unsuspendIdBtn">Unsuspend</button>
                                        <?php else: ?>
                                        <button class="btn btn-danger btn-bordred btn-block waves-effect waves-light" type="submit" name="suspendIdBtn">Suspend</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Arrests (<?php echo $editing_id['name']; ?>)</h4>
                        <table id="datatable" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Arrest ID</th>
                                    <th>Date/Time</th>
                                    <th>Suspect</th>
                                    <th>Summary</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php
        											$sql             = "SELECT * FROM arrest_reports WHERE arresting_officer=:editing_idname";
        											$stmt            = $pdo->prepare($sql);
        											$stmt->bindValue(':editing_idname', $editing_id['name']);
        											$stmt->execute();
        											$arrestsRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

        											foreach ($arrestsRow as $arrest) {
        												echo '
        												<tr>
        													<td>'. $arrest['arrest_id'] .'</td>
        													<td>'. $arrest['timestamp'] .'</td>
        													<td>'. $arrest['suspect'] .'</td>
        													<td width="50%">'. $arrest['summary'] .'</td>
        												</tr>
        												';
        											}
      											?>
                        </table>
                    </div>
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Tickets (<?php echo $editing_id['name']; ?>)</h4>
                        <table id="datatable2" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Date/Time</th>
                                    <th>Suspect</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
        											$sql2             = "SELECT * FROM tickets WHERE officer=:editing_idname";
        											$stmt2            = $pdo->prepare($sql2);
        											$stmt2->bindValue(':editing_idname', $editing_id['name']);
        											$stmt2->execute();
        											$ticketRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        											foreach ($ticketRow as $ticket) {
        												echo '
        												<tr>
        													<td>'. $ticket['ticket_id'] .'</td>
        													<td>'. $ticket['ticket_timestamp'] .'</td>
        													<td>'. $ticket['suspect'] .'</td>
        													<td width="50%">'. $ticket['reasons'] .'</td>
        												</tr>
        												';
        											}
      											?>
                        </table>
                    </div>
                </div>
                <div class="col-5">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Shift Logs (<?php echo $editing_id['name']; ?>)</h4>
                        <!-- CONTENT -->
                        <table id="datatable3" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Date and Time</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
        											$sql             = "SELECT * FROM shift_logs WHERE i_id=:c_id";
        											$stmt            = $pdo->prepare($sql);
        											$stmt->bindValue(':c_id', $editing_id['id']);
        											$stmt->execute();
        											$shitRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

        											foreach ($shitRow as $shift) {
        												echo '
        												<tr>
        													<td width="100%">'. $shift['s_start'] .'</td>
        												</tr>
        												';
        											}
      											?>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="row">
                <div class="col">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['identity_name']; ?> <?php if ($_SESSION['identity_supervisor'] === "Yes"): ?><small>
                                <font color="white"><i>Supervisor</i></font>
                            </small><?php endif; ?></h4>
                        <?php if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings === 'true'): ?>
                        <a href="leo.php?v=main"><button class="btn btn-info btn-sm">Back To Patrol Panel</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">All LEO Identities</h4>
                        <table id="datatable" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="hide-phone">Division</th>
                                    <th class="hide-phone">Supervisor</th>
                                    <th class="hide-phone">User</th>
                                    <th class="hide-phone">Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
          										$sql             = "SELECT * FROM identities WHERE department='Law Enforcement'";
          										$stmt            = $pdo->prepare($sql);
          										$stmt->execute();
          										$leoIdsRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

          										foreach ($leoIdsRow as $identity) {
          											echo '
          											<tr>
          												<td >'. $identity['name'] .'</td>
          												<td class="hide-phone">'. $identity['division'] .'</td>
          												<td class="hide-phone">'. $identity['supervisor'] .'</td>
          												<td class="hide-phone">'. $identity['user_name'] .'</td>
          												<td class="hide-phone">'. $identity['status'] .'</td>
          												<td><a href="leo.php?v=supervisor&a=edit-id&id='. $identity['identity_id'] .'"><input type="button" class="btn btn-sm btn-success btn-block" value="Edit"></a></td>
          											</tr>
          											';
          										}
        										?>
                        </table>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Pending Identities</h4>
                        <div id="getPendingIds"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="alert alert-danger" role="alert">
                You are not a supervisor.
            </div>
            <?php endif; ?>
            <?php break; ?>
            <?php endswitch; ?>
        </div>
    </div>
    <!-- CONTENT END -->
    <?php include 'inc/copyright.php'; ?>
    <?php include 'inc/page-bottom.php'; ?>
    <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
