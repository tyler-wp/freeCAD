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

    // Page PHP
        $q      = strip_tags($_GET['id']);
        $getVeh = "SELECT * FROM vehicles WHERE vehicle_id='$q'";
        $result = $pdo->prepare($getVeh);
        $result->execute();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['vehicle_isImpounded'] === 'true') {
              echo '
              <div class="alert alert-danger" role="alert">
                <strong>WARNING</strong>: THIS VEHICLE IS MARKED AS IMPOUNDED, AND MAY BE STOLEN FROM THE IMPOUND YARD.
              </div>';
            }
            echo "<h5>Plate: " . $row['vehicle_plate'] . "</h5><br-leo-name-search>";
            echo "<h5>Color: " . $row['vehicle_color'] . "</h5><br-leo-name-search>";
            echo "<h5>Model: " . $row['vehicle_model'] . "</h5><br-leo-name-search>";
            echo "<h5>Insurance Status: " . $row['vehicle_is'] . "</h5><br-leo-name-search>";
            echo "<h5>Registration Status: " . $row['vehicle_rs'] . "</h5><br-leo-name-search>";
            echo "<h5>VIN: " . $row['vehicle_vin'] . "</h5><br-leo-name-search>";
            echo "<h5>Owner: " . $row['vehicle_ownername'] . "</h5><br-leo-name-search>";
        }
