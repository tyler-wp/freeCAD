<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Access-Control-Allow-Origin: *");

session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Page PHP
$stmt = $pdo->prepare("SELECT * FROM servers WHERE id=:server_id");
$stmt->bindValue(':server_id', $_SESSION['server']);
$stmt->execute();
$pb_row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($pb_row['priority'] === 1) {
    echo "data: 1\n\n";
    echo "retry: 1000\n\n";
    flush();
}
else {
    echo "data: 0\n\n";
    echo "retry: 1000\n\n";
    flush();
}
