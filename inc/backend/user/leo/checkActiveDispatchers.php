<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Access-Control-Allow-Origin: *");

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

$dispatchCheck = array();


// Page PHP

$stmt = $pdo->prepare("SELECT * FROM on_duty WHERE department = ? AND status = ?");
$stmt->execute(['Dispatch', 'On-Duty']);
$getDispatchers = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($getDispatchers)) {
	echo "data: 1\n\n";
	echo "retry: 1000\n\n";
	flush();
}

?>
