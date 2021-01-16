<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
if ($_SESSION['on_duty'] === "Dispatch" || $_SESSION['on_duty'] === "LEO") {
    // Page PHP

    if(!empty($_POST['call_description']) || strstr($_POST['call_description'], '//')) {
        $qcall = explode('//', strip_tags($_POST['call_description']), 4);
        if(isset($qcall[3])) {
            $call_unitspre = explode(',', $qcall[0], 4);
            $call_description = $qcall[1];
            $call_location = $qcall[2];
            $call_postal = $qcall[3];
        } else {
            $error['msg'] = "Invalid Format";
            echo json_encode($error);
            exit();
        }
    } else {
        $error['msg'] = "Invalid Format";
        echo json_encode($error);
        exit();
    }

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
        
        $callid = dbquery('SELECT * FROM 911calls WHERE call_timestamp = "' . escapestring($us_date . ' ' . $time) . '"')[0]['call_id'];
        $active_units = dbquery('SELECT * FROM on_duty WHERE department="Law Enforcement"');
        foreach($call_unitspre as $unit) {
            foreach($active_units as $unitdb) {
                if(strstr($unitdb['name'], $unit)) {
                    dbquery('INSERT INTO assigned_callunits (call_id, unit_id) VALUES ("' . $callid . '", "' . escapestring($unitdb['id']) . '")', false);
                }
            }
        }

        $error['msg'] = "";
        echo json_encode($error);
        exit();
    }

}
