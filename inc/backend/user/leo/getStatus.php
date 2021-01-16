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

// Page PHP

$stmt = $pdo->prepare("SELECT * FROM on_duty WHERE name=:name");
$stmt->bindValue(':name', $_SESSION['identity_name']);
$stmt->execute();
$status_row = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['duty_id'] = $status_row['id'];
echo "data: ".$status_row['status']."\n\n";
echo "retry: 1000\n\n";
flush();

if (empty($status_row)) {
	echo "data: Off-Duty\n\n";
	echo "retry: 1000\n\n";
	flush();
}

?>
