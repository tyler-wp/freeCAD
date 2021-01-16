<?php
    session_name('hydrid');
    session_start();

    require_once 'inc/connect.php';
    $stmt              = $pdo->prepare("DELETE FROM `on_duty` WHERE `uid`=:uid");
    $stmt->bindValue(':uid', $_SESSION['user_id']);
    $result = $stmt->execute();

    session_unset();
    session_destroy();
    header('Location: login.php?error=access');
    exit();
?>
