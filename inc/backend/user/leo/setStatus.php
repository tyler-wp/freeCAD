<?php
session_name('hydrid');
session_start();
require '../../../connect.php';
require '../../../config.php';
require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
if (!isset($_SESSION['identity_name'])) {
  header('Location: ../../../../' . $url['leo'] . '?v=nosession');
  exit();
}

// Page PHP
$status = strip_tags($_GET['status']);

if ($status === "10-42") {
  $stmt = $pdo->prepare("DELETE FROM `on_duty` WHERE `uid` = :defr");
  $stmt->bindValue(':defr', $_SESSION['user_id']);
  $result = $stmt->execute();
} else {
  $updateTime = time_php2sql(time());

  $sql2 = "UPDATE `on_duty` SET `status` = ?, `timestamp` = ? WHERE `uid`= ?";
  $stmt2 = $pdo->prepare($sql2);
  $stmt2->execute([$status, $updateTime, $_SESSION['user_id']]);
}
