<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';

require_once 'inc/config.php';

require_once 'inc/backend/user/auth/userIsLoggedIn.php';

$page['name'] = 'Fire/EMS Module';
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
            header('Location: fire.php?v=nosession&error=identity-not-found');
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

          $_SESSION['on_duty'] = "Fire/EMS";

          if ($identity_owner !== $user_id) {
    				header('Location: fire.php?v=nosession&error=identity-owner');
    				exit();
	        } elseif ($identityDB['status'] === "Suspended") {
            header('Location: '.$url['leo'].'?v=nosession');
          } elseif ($identityDB['status'] === "Pending Approval") {
            header('Location: '.$url['leo'].'?v=nosession');
          }

    			$stmt2              = $pdo->prepare("DELETE FROM `on_duty` WHERE `uid`=:uid");
    			$stmt2->bindValue(':uid', $_SESSION['user_id']);
    			$result2 = $stmt2->execute();
          $stmt3              = $pdo->prepare("INSERT INTO on_duty (name, department, status, uid) VALUES (:name, :department, 'Off-Duty', :uid)");
    			$stmt3->bindValue(':name', $identity_name);
    			$stmt3->bindValue(':department', $identity_department);
          $stmt3->bindValue(':uid', $_SESSION['user_id']);
    			$result3 = $stmt3->execute();

          header('Location: fire.php?v=main');
          exit();
        }
    }
}
?>
<?php include 'inc/page-top.php'; ?>
<script src="assets/js/pages/fire.js?v=<?php echo $assets_ver ?>"></script>
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
            <div class="row">
                <div class="col">
                    <div class="card-box">
                        <select class="form-control" id="listIdentitys" onchange="location = this.value;">
                            <option selected="true" disabled="disabled">Loading Identities...</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Identity Creator</h4>
                        <form class="form-horizontal m-t-20" id="createIdentity" action="inc/backend/user/fire/createIdentity.php" method="POST">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="name" placeholder="John Doe">
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
            <!-- js is put here to prevent issues on other parts of fire/ems -->
            <script type="text/javascript">
                $(document).ready(function() {
                    var signal100 = false;
                    startTime();
                    getFireInfo();
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
                        <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['identity_name']; ?> <?php if ($_SESSION['identity_supervisor'] === "Yes"): ?><small>[Supervisor]</small><?php endif; ?> <label id="signal100Status">Loading...</label></h4>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#notepadModal">Notepad</button>
                        <?php if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings === 'true'): ?>
                        <a href="fire.php?v=supervisor"><button class="btn btn-darkred btn-sm">Supervisor Panel</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="checkDispatchers"></div>
            <div class="row">
                <div class="col-9">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">My Calls
                        </h4>
                        <div id="getMyCalls"></div>
                        <div id="noDis911Calls"></div>
                    </div>
                </div>
                <div class="col-3">
                    <div id="statusSetter">
                      <div class="card-box">
                          <h4 class="header-title mt-0 m-b-30">Current Status: <label id="getDutyStatus">Loading...</label></h4>
                          <div class="form-group">
                              <select class="form-control" name="setUnitStatus" onChange='setUnitStatus(this)'>
                                  <option value="Available">Available</option>
                                  <option value="Responding To Call">Responding To Call</option>
                                  <option value="Off-Duty">Off Duty</option>
                              </select>
                          </div>
                      </div>
                    </div>

                    <div id="divisionSetter">
                      <div class="card-box">
                          <h4 class="header-title mt-0 m-b-30">Set Division</h4>
                          <div class="form-group">
                              <select class="form-control" name="setFireDivision" onChange='setFireDivision(this)'>
                                 <option value="D.F.D - Engine">D.F.D - Engine</option>
                                 <option value="D.F.D - Ladder">D.F.D - Ladder</option>
                                 <option value="D.F.D - Ambulance">D.F.D - Ambuance</option>

                                 <option value="R.H.F.D - Engine">R.H.F.D - Engine</option>
                                 <option value="R.H.F.D - Ladder">R.H.F.D - Ladder</option>
                                 <option value="R.H.F.D - Ambulance">R.H.F.D - Ambuance</option>

                                 <option value="E.B.H.F.D - Engine">E.B.H.F.D - Engine</option>
                                 <option value="E.B.H.F.D - Ladder">E.B.H.F.D - Ladder</option>
                                 <option value="E.B.H.F.D - Ambulance">E.B.H.F.D - Ambuance</option>

                                 <option value="P.B.F.D - Engine">P.B.F.D - Engine</option>
                                 <option value="P.B.F.D - Ladder">P.B.F.D - Ladder</option>
                                 <option value="P.B.F.D - Ambulance">P.B.F.D - Ambuance</option>

                                 <option value="S.S.F.D - Engine">S.S.F.D - Engine</option>
                                 <option value="S.S.F.D - Ladder">S.S.F.D - Ladder</option>
                                 <option value="S.S.F.D - Ambulance">S.S.F.D - Ambuance</option>
                              </select>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
            <!-- MODALS -->
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
                            <form method="post" action="fire.php">
                                <div class="form-group">
                                    <textarea class="form-control" name="textarea" oninput="updateNotepad(this.value)" rows="12" cols="104"><?php echo $_SESSION['notepad']; ?></textarea>
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
								$sql  = "SELECT * FROM identities WHERE identity_id = :identity_id AND department='Fire / EMS'";
								$stmt = $pdo->prepare($sql);
								$stmt->bindValue(':identity_id', $id);
								$stmt->execute();
								$idDB = $stmt->fetch(PDO::FETCH_ASSOC);
								if ($idDB === false) {
									 echo '<script> location.replace("' . $url['fire'] . '?v=supervisor&error=id-not-found"); </script>';
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
									echo '<script> location.replace("' . $url['fire'] . '?v=supervisor&id=suspended"); </script>';
									exit();
								}
								if (isset($_POST['unsuspendIdBtn'])) {
									$sql = "UPDATE identities SET status=? WHERE identity_id=?";
									$stmt = $pdo->prepare($sql);
									$stmt->execute(['Active', $_SESSION['editing_identity_id']]);
									echo '<script> location.replace("' . $url['fire'] . '?v=supervisor&id=unsuspended"); </script>';
									exit();
								}
								if (isset($_POST['editIdBtn'])) {
									$updateSupervisor    = !empty($_POST['supervisor']) ? trim($_POST['supervisor']) : null;
  								$updateSupervisor    = strip_tags($updateSupervisor);

									$sql = "UPDATE identities SET supervisor=? WHERE identity_id=?";
									$stmt = $pdo->prepare($sql);
									$stmt->execute([$updateSupervisor, $_SESSION['editing_identity_id']]);
									echo '<script> location.replace("' . $url['fire'] . '?v=supervisor&id=edited"); </script>';
									exit();
								}
								?>
            <div class="row">
                <div class="col">
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
                        <a href="fire.php?v=main"><button class="btn btn-info btn-sm">Back To Patrol Panel</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">All Fire / EMS Identities</h4>
                        <table id="datatable" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Supervisor</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
          										$sql             = "SELECT * FROM identities WHERE department='Fire / EMS'";
          										$stmt            = $pdo->prepare($sql);
          										$stmt->execute();
          										$leoIdsRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

          										foreach ($leoIdsRow as $identity) {
          											echo '
          											<tr>
          												<td>'. $identity['name'] .'</td>
          												<td>'. $identity['supervisor'] .'</td>
          												<td>'. $identity['user_name'] .'</td>
          												<td>'. $identity['status'] .'</td>
          												<td><a href="fire.php?v=supervisor&a=edit-id&id='. $identity['identity_id'] .'"><input type="button" class="btn btn-sm btn-success btn-block" value="Edit"></a></td>
          											</tr>
          											';
          										}
        										?>
                        </table>
                    </div>
                </div>
                <div class="col-5">
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
