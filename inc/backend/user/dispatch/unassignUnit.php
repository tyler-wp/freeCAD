<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
if ($_SESSION['on_duty'] === "Dispatch" || $_SESSION['on_duty'] === "LEO") {
    $unit = strip_tags($_GET['unit']);

    log911Action('Un-Assigned Unit');

    $stmt = $pdo->prepare("DELETE FROM assigned_callunits WHERE `unit_id`= ?");
    $result = $stmt->execute([$unit]);
}
