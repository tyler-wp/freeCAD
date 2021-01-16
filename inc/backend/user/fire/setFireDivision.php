<?php
    session_name('hydrid');
    session_start();
    require '../../../connect.php';
    require '../../../config.php';
    require '../../../backend/user/auth/userIsLoggedIn.php';

    // Makes sure the person actually has a character set
    if (!isset($_SESSION['identity_name'])) {
      header('Location: ../../../../fire.php?v=nosession');
      exit();
    }

    // Page PHP
    $div = strip_tags($_GET['div']);
    $stmt2              = $pdo->prepare("UPDATE `on_duty` SET `division`=:div WHERE `name`=:name");
    $stmt2->bindValue(':div', $div);
    $stmt2->bindValue(':name', $_SESSION['identity_name']);
    $result = $stmt2->execute();

    $stmt3              = $pdo->prepare("UPDATE `on_duty` SET `status`=:status WHERE `name`=:name");
    $stmt3->bindValue(':status', 'Available');
    $stmt3->bindValue(':name', $_SESSION['identity_name']);
    $result3 = $stmt3->execute();
