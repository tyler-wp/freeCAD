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
    if ($status === "offduty") {
      $stmt              = $pdo->prepare("DELETE FROM on_duty WHERE `name`=:name");
      $stmt->bindValue(':name', $_SESSION['identity_name']);
      $result = $stmt->execute();
    } elseif ($status === "onduty") {
      $stmt3              = $pdo->prepare("UPDATE `on_duty` SET `department`=:department, `division`=:division, `status`=:status WHERE `name`=:name");
      $stmt3->bindValue(':department', $_SESSION['identity_department']);
      $stmt3->bindValue(':division', $_SESSION['identity_division']);
      $stmt3->bindValue(':name', $_SESSION['identity_name']);
      $stmt3->bindValue(':status', 'On-Duty');
      $result = $stmt3->execute();
      logAction('Started Shift (Dispatch) - '.$datetime.'', $_SESSION['identity_name']);
    }
