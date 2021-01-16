<?php
    session_name('hydrid');
    session_start();
    require '../../../connect.php';
    require '../../../config.php';
    require '../../../backend/user/auth/userIsLoggedIn.php';

    // Makes sure the person actually has a character set
    if (!isset($_SESSION['identity_name'])) {
      header('Location: ../../../../' . $url['leo'] . '?v=nosession');
      exit();
    }

    // Gets the characters ID that should be searched
    $charID = strip_tags($_GET['id']);
    // Selects the Character from Character Table
    $charTable = $pdo->prepare("SELECT * FROM characters WHERE character_id=?");
    $charTable->execute([$charID]);
    $character = $charTable->fetch();

    $charVehicles = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_owner=?");
    $charVehicles->execute([$charID]);
    $characterVehicles = $charVehicles->fetchAll();

    $charWeapons = $pdo->prepare("SELECT * FROM weapons WHERE wpn_owner=?");
    $charWeapons->execute([$charID]);
    $characterWeapons = $charWeapons->fetchAll();

    $charTickets = $pdo->prepare("SELECT * FROM tickets WHERE suspect_id=?");
    $charTickets->execute([$charID]);
    $characterTickets = $charTickets->fetchAll();

    $charArrests = $pdo->prepare("SELECT * FROM arrest_reports WHERE suspect_id=?");
    $charArrests->execute([$charID]);
    $characterArrests = $charArrests->fetchAll();

    $charWanted = $pdo->prepare("SELECT * FROM warrants WHERE wanted_person_id=?");
    $charWanted->execute([$charID]);
    $characterWarrants = $charWanted->fetchAll();

    // Always sets the alert cookie as false at first
    setcookie('personWantedAlert', 'false', time() + (120 * 30), "/");

    if (!empty($characterWarrants)) {
      setcookie('personWantedAlert', 'true', time() + (120 * 30), "/");
      echo '<div class="alert alert-danger" role="alert">This Person Is WANTED. Proceed with caution</div>';
    }

    if ($character['status'] === "Deceased") {
      echo '<div class="alert alert-warning" role="alert">This person is marked as Deceased</div>';
    }

    if ($character['license_driver'] === "Permit") {
      echo '<div class="alert alert-info" role="alert">This person only has a <strong>permit</strong>, and should not be driving without an adult in the vehicle.</div>';
    }


    echo '<div id="staffPanelWizard" class="pull-in">
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item"><a href="#personInfo" data-toggle="tab" class="nav-link">Information</a></li>
            <li class="nav-item"><a href="#personLicenses" data-toggle="tab" class="nav-link">Licenses</a></li>
            <li class="nav-item"><a href="#personVehicles" data-toggle="tab" class="nav-link">Vehicles</a></li>
            <li class="nav-item"><a href="#personWeapons" data-toggle="tab" class="nav-link">Weapons</a></li>
            <li class="nav-item"><a href="#personTickets" data-toggle="tab" class="nav-link">Tickets</a></li>
            <li class="nav-item"><a href="#personArrests" data-toggle="tab" class="nav-link">Arrests</a></li>
            <li class="nav-item"><a href="#personWarrants" data-toggle="tab" class="nav-link">Warrants</a></li>
        </ul>

        <div class="tab-content b-0 mb-0">
          <div class="tab-pane m-t-10 fade" id="personInfo">
            <h5>Name: '.$character['first_name'].' '.$character['last_name'].'</h5>
            <h5>Sex: '.$character['sex'].'</h5>
            <h5>Race: '.$character['race'].'</h5>
            <h5>Date of Birth: '.$character['date_of_birth'].'</h5>
            <h5>Address: '.$character['address'].'</h5>

            <h5>Height / Weight: '.$character['height'].' '.$character['weight'].'</h5>
            <h5>Hair Color: '.$character['hair_color'].'</h5>
            <h5>Eye Color: '.$character['eye_color'].'</h5>
            <input type="button" class="btn btn-danger btn-sm" name="markAsDeceased" value="Mark Deceased" id='.$charID.' onclick="markCivDeceased(this)">
          </div>

          <div class="tab-pane m-t-10 fade" id="personLicenses">
            <table class="table table-borderless">
              <thead>
                <tr>
                  <th>License</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Driver\'s License</td>
                  <td>' . $character['license_driver'] . '</td>
                  <td><input type="button" class="btn btn-danger btn-xs" name="suspendDriversLicense" value="Suspend" id='.$charID.' onclick="suspendDriversLicense(this)"></td>
                </tr>
                <tr>
                  <td>Firearm\'s License</td>
                  <td>' . $character['license_firearm'] . '</td>
                  <td><input type="button" class="btn btn-danger btn-xs" name="suspendFirearmsLicense" value="Suspend" id='.$charID.' onclick="suspendFirearmsLicense(this)"></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="tab-pane m-t-10 fade" id="personVehicles">';
            if (empty($characterVehicles)) {
              echo 'No Vehicles On File.';
            } else {
              echo '<table class="table table-borderless">
                      <thead>
                        <tr>
                            <th>Plate</th>
                            <th>Color</th>
                            <th>Model</th>
                            <th>Insurance</th>
                            <th>Registration</th>
                            <th>VIN</th>
                        </tr>
                      </thead>
                      <tbody>';
              foreach($characterVehicles as $vehicle) {
                echo '<tr>
                        <td>' . $vehicle['vehicle_plate'] . '</td>
                        <td>' . $vehicle['vehicle_color'] . '</td>
                        <td>' . $vehicle['vehicle_model'] . '</td>
                        <td>' . $vehicle['vehicle_is'] . '</td>
                        <td>' . $vehicle['vehicle_rs'] . '</td>
                        <td>' . $vehicle['vehicle_vin'] . '</td>
                    </tr>';
              }
              echo '</tbody>
                  </table>';
            }
          echo '
          </div>

          <div class="tab-pane m-t-10 fade" id="personWeapons">';
            if (empty($characterWeapons)) {
              echo 'No Weapons On File.';
            } else {
              echo '<table class="table table-borderless">
                      <thead>
                        <tr>
                            <th>Type</th>
                            <th>Serial</th>
                            <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>';
              foreach($characterWeapons as $weapon) {
                echo '<tr>
                        <td>' . $weapon['wpn_type'] . '</td>
                        <td>' . $weapon['wpn_serial'] . '</td>
                        <td>' . $weapon['wpn_rpstatus'] . '</td>
                    </tr>';
              }
              echo '</tbody>
                  </table>';
            }
          echo '
          </div>

          <div class="tab-pane m-t-10 fade" id="personTickets">';
            if (empty($characterTickets)) {
              echo 'No Tickets On File.';
            } else {
              echo '<table class="table table-borderless">
                      <thead>
                        <tr>
                            <th>Reason</th>
                            <th>Fine Amount</th>
                            <th>Timestamp</th>
                            <th>Officer</th>
                        </tr>
                      </thead>
                      <tbody>';
              foreach($characterTickets as $ticket) {
                echo '<tr>
                        <td>' . $ticket['reasons'] . '</td>
                        <td>' . $ticket['amount'] . '</td>
                        <td>' . $ticket['ticket_timestamp'] . '</td>
                        <td>' . $ticket['officer'] . '</td>
                    </tr>';
              }
              echo '</tbody>
                  </table>';
            }
          echo '
          </div>

          <div class="tab-pane m-t-10 fade" id="personArrests">';
            if (empty($characterArrests)) {
              echo 'No Arrests On File.';
            } else {
              echo '<table class="table table-borderless">
                      <thead>
                        <tr>
                            <th>Officer</th>
                            <th>Timestamp</th>
                            <th>Summary</th>
                        </tr>
                      </thead>
                      <tbody>';
              foreach($characterArrests as $arrest) {
                echo '<tr>
                        <td>' . $arrest['arresting_officer'] . '</td>
                        <td>' . $arrest['timestamp'] . '</td>
                        <td>' . $arrest['summary'] . '</td>
                    </tr>';
              }
              echo '</tbody>
                  </table>';
            }
          echo '
          </div>

          <div class="tab-pane m-t-10 fade" id="personWarrants">';
            if (empty($characterWarrants)) {
              echo '<div class="alert alert-success" role="alert">No Active Warrants</div>';
            } else {
              echo '<table class="table table-borderless">
                      <thead>
                        <tr>
                            <th>Issued On</th>
                            <th>Signed By</th>
                            <th>Reason</th>';
                            if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings) {
                              echo '<th>Actions</th>';
                            }
                            echo '
                        </tr>
                      </thead>
                      <tbody>';
              foreach($characterWarrants as $warrant) {
                echo '<tr>
                        <td>' . $warrant['issued_on'] . '</td>
                        <td>' . $warrant['signed_by'] . '</td>
                        <td>' . $warrant['reason'] . '</td>';
                        if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings) {
                          echo '<td><input type="button" class="btn btn-danger btn-sm" name="deleteWarrant" value="Delete Warrant" id='.$warrant['warrant_id'].' onclick="deleteWarrantLEO(this)"></td>';
                        }
                        echo '
                    </tr>';
              }
              echo '</tbody>
                  </table>';
            }
          echo '
          </div>
        </div>
    </div>';
