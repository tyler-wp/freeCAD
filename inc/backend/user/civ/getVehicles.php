<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
if (!isset($_SESSION['character_full_name'])) {
    header('Location: ../../../../' . $url['civilian'] . '?v=nosession');
    exit();
}

// Page PHP
$sql = "SELECT * FROM vehicles WHERE vehicle_owner=:character_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':character_id', $_SESSION['character_id']);
$stmt->execute();
$vehicleDBcall = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($vehicleDBcall)) {
    echo '
      You have no Vehicles.
      ';
}
else {
    echo '
      <table class="table table-borderless">
          <thead>
            <tr>
                <th>Plate</th>
                <th>Color</th>
                <th>Model</th>
                <th>Insurance</th>
                <th>Registration</th>
                <th>Actions</th>
            </tr>
          </thead>
            <tbody>';
            foreach ($vehicleDBcall as $vehicle) {
                $stmt_impoundCheck   = $pdo->prepare("UPDATE `vehicles` SET `vehicle_isImpounded`='false' WHERE `vehicle_impoundedTill` < (NOW() - INTERVAL 1440 MINUTE)");
                $result_impoundCheck = $stmt_impoundCheck->execute();
                if ($vehicle['vehicle_isStolen'] === 'true') {
                  echo '<tr class="pulse-stolenVehicle">';
                } elseif ($vehicle['vehicle_isImpounded'] === 'true') {
                  echo '<tr class="blurrred-impoundedVehicle">';
                } else {
                  echo '<tr>';
                }
                echo '
                    <td>' . $vehicle['vehicle_plate'] . '</td>
                    <td>' . $vehicle['vehicle_color'] . '</td>
                    <td>' . $vehicle['vehicle_model'] . '</td>
                    <td>' . $vehicle['vehicle_is'] . '</td>
                    <td>' . $vehicle['vehicle_rs'] . '</td>
                    <td>';
                    if ($vehicle['vehicle_isStolen'] === 'true') {
                      echo '<input type="button" class="btn btn-success btn-sm" name="stolenVehicle" value="Not Stolen" id=' . $vehicle['vehicle_id'] . ' onclick="stolenVehicle(this)">';
                    } else {
                      echo '<input type="button" class="btn btn-danger btn-sm" name="stolenVehicle" value="Stolen" id=' . $vehicle['vehicle_id'] . ' onclick="stolenVehicle(this)">';
                    }
                    echo '
                      <input type="button" class="btn btn-danger btn-sm" name="deleteVehicle" value="Delete" id=' . $vehicle['vehicle_id'] . ' onclick="deleteVehicle(this)">
                    </td>
                </tr>
                ';
            }
            echo '
            </tbody>
      </table>';
}
