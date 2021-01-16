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

// Page PHP
$id = strip_tags($_GET['character']);
$stmt              = $pdo->prepare("UPDATE `characters` SET `status`='Deceased' WHERE `character_id`=:id");
$stmt->bindValue(':id', $id);
$result = $stmt->execute();
