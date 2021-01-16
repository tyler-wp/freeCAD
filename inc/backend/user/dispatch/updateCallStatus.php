<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
$error = array();
if ($_SESSION['on_duty'] === "Dispatch" || $_SESSION['on_duty'] === "LEO") {
    $updated_status = strip_tags($_GET['newStatus']);

    if ($updated_status === "PRIORITY") {
      log911Action('Set call as PRIORITY');
      $stmt_pbu = $pdo->prepare("UPDATE `servers` SET `priority`='1' WHERE `id`=:server_id");
    	$stmt_pbu->bindValue(':server_id', $_SESSION['server']);
    	$result_pbu = $stmt_pbu->execute();
    } else {
      $stmt_pbu = $pdo->prepare("UPDATE `servers` SET `priority`='0' WHERE `id`=:server_id");
    	$stmt_pbu->bindValue(':server_id', $_SESSION['server']);
    	$result_pbu = $stmt_pbu->execute();
    }

    log911Action('Updated Call Status');

    $sql = "UPDATE 911calls SET call_status=? WHERE call_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$updated_status, $_SESSION['viewingCallID']]);

    $error['msg'] = "";
    echo json_encode($error);
    exit();
}
