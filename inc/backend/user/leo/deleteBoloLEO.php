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

// Supervisor Check
if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings) {
  $error = array();

  if (isset($_GET['id']) && strip_tags($_GET['id'])) {
  	$id   = $_GET['id'];
    $stmt              = $pdo->prepare("DELETE FROM bolos WHERE `id`= ?");
    $result = $stmt->execute([$id]);
    $error['msg'] = "";
  	echo json_encode($error);
  	exit();

    if ($settings['discord_alerts'] === 'true') {
    discordAlert('**Bolo Deleted**
    	  Bolo ID #'.$id.' has been deleted by '.$_SESSION['identity_name'].';
    	  - **freeCAD System**');
    }
  } else {
  	$error['msg'] = "FATAL SYSTEM ERROR";
  	echo json_encode($error);
  	exit();
  }
} else {
  header('Location: ../../../../' . $url['leo'] . '?v=nosession');
	exit();
}
?>
