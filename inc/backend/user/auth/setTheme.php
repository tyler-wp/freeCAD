<?php
session_name('hydrid');
session_start();
require '../../../connect.php';
require '../../../config.php';
require '../../../backend/user/auth/userIsLoggedIn.php';

if (isset($_GET['q'])) {
    $q = strip_tags($_GET['q']);
    $error = array();

    $result = $pdo->prepare("UPDATE `users` SET `theme`= ? WHERE user_id = ?")
        ->execute([$q, $_SESSION['user_id']]);
    header('Location: ../../../../index.php');
    exit();
}
