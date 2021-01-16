<?php
session_name('hydrid');
session_start();
require '../../../connect.php';
require '../../../config.php';
require '../../../backend/user/auth/userIsLoggedIn.php';

if (staff_access === 'true' && staff_siteSettings === 'true') {
    if (isset($_GET['q'])) {
        $q = strip_tags($_GET['q']);
        $error = array();

        $result = $pdo->prepare("UPDATE `settings` SET `civ_char_limit`= ?")
            ->execute([$q]);

        logAction('Changed Module Setting: Civilian Character Limit', $user['username']);

        if ($settings['discord_alerts'] === 'true') {
            discordAlert('**Panel Settings Changed**
	    Civilian Character Limit has been updated by ' . $user['username'] . '
      - **freeCAD System**');
        }
    }
    else {
        $error['msg'] = "System Error";
        echo json_encode($error);
        exit();
    }
} else {
    logAction('Attempted To Module Setting: Civilian Character Limit', $user['username']);
    $error['msg'] = "No Permissions";
    echo json_encode($error);
    exit();
}
