<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

// Page PHP
$call_description = !empty($_GET['desc']) ? trim($_GET['desc']) : null;
$call_location = !empty($_GET['loc']) ? trim($_GET['loc']) : null;

$call_description = strip_tags($_GET['desc']);
$call_location = strip_tags($_GET['loc']);
$call_postal = strip_tags($_GET['pos']);

$dispatchCheck = array();


// Page PHP

$stmt_dc = $pdo->prepare("SELECT * FROM on_duty WHERE department = ? AND status = ?");
$stmt_dc->execute(['Dispatch', 'On-Duty']);
$getDispatchers = $stmt_dc->fetch(PDO::FETCH_ASSOC);

if (empty($getDispatchers)) {
		// Do nothing
} else {
	$error = array();
	$sql = "INSERT INTO 911calls (caller_id, call_description, call_location, call_postal, call_timestamp) VALUES (
		:caller_id,
		:call_description,
		:call_location,
		:call_postal,
		:call_timestamp
		)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':caller_id', '0');
	$stmt->bindValue(':call_description', $call_description);
	$stmt->bindValue(':call_location', $call_location);
	$stmt->bindValue(':call_postal', $call_postal);
	$stmt->bindValue(':call_timestamp', $us_date . ' ' . $time);
	$result = $stmt->execute();
	if ($result) {
	    if ($settings['discord_alerts'] === 'true') {
	        discordAlert('**NEW 911 CALL**
		**Description:** ' . $call_description . '
		**Location:** ' . $call_location . ' / ' . $call_postal . '
		**Called On:** ' . $datetime . '
		  - **freeCAD System**');
	    }
	    $error['msg'] = "";
	    echo json_encode($error);
	    exit();
	}
}
