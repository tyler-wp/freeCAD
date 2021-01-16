<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set

if (!isset($_SESSION['on_duty'])) {
	header('Location: ../../../../' . $url['leo'] . '?v=nosession');
	exit();
}

$error = array();
$pendVeh['plate'] = strip_tags($_POST['plate']);
$pendVeh['reason'] = strip_tags($_POST['reason']);

// Check if vehicle actually exists
$sql = "SELECT * FROM vehicles WHERE vehicle_plate = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$pendVeh['plate']]);
$vehsDB = $stmt->fetch(PDO::FETCH_ASSOC);

if ($vehsDB === false) {
	$error['msg'] = "Vehicle Not Registered";
	echo json_encode($error);
	exit();
}

// Check if vehicle is (already) impounded
if ($vehsDB['vehicle_isImpounded'] === 'true') {
	$error['msg'] = "You can't impound a vehicle that is already set as impounded :(";
	echo json_encode($error);
	exit();
}

// We will set the vehicle as impounded now
// if ($vehsDB['vehicle_impoundedCount'] == '0') {
// 	// 24 Hours , First time being impounded
// 	$irel = time() + (86400);
// } elseif ($vehsDB['vehicle_impoundedCount'] < '3') {
// 	// 3 Days , Third time being impounded
// 	$irel = time() + (259200);
// } else {
// 	// 11 Days , More then third time
// 	$irel = time() + (950400);
// }
$irel = time() + (86400);
$impoundRel = time_php2sql($irel);

$sql2 = "UPDATE `vehicles` SET `vehicle_isImpounded`='true', `vehicle_impoundedTill`=?, `vehicle_impoundedCount`=`vehicle_impoundedCount` + 1 WHERE `vehicle_plate` = ?";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([$impoundRel, $pendVeh['plate']]);
$error['msg'] = "";
echo json_encode($error);
exit();
