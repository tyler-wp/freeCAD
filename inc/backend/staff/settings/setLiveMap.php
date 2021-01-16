<?php
session_name('hydrid');
session_start();
require '../../../connect.php';
require '../../../config.php';
require '../../../backend/user/auth/userIsLoggedIn.php';

$error = array();

if (staff_access === 'true' && staff_siteSettings === 'true') {
    $pc = strip_tags($_POST['lmURL']);

    $result = $pdo->prepare("UPDATE `settings` SET `livemap_url`= ?")
        ->execute([$pc]);

    logAction('Changed LiveMap URL', $user['username']);

    if ($settings['discord_alerts'] === 'true') {
        discordAlert('**LiveMap Changed**
	  LiveMap URL has been updated by ' . $user['username'] . '
      - **freeCAD System**');
    }
    $error['msg'] = "";
    echo json_encode($error);
    exit();
} else {
    logAction('Attempted To Changed Penal Code URL', $user['username']);
    $error['msg'] = "You don't have permission.";
    echo json_encode($error);
    exit();
}
