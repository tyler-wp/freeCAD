<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
$error = array();
$idNewName = $_POST['newIDName'];
$id = $_SESSION['editingU_ID_ID'];

$sql = "UPDATE identities SET `name`=? WHERE `identity_id`=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idNewName, $id]);

$error['msg'] = "";
echo json_encode($error);
exit();