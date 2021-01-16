<?php
session_name('hydrid');
session_start();
require_once '../../../connect.php';

require_once '../../../config.php';

$username = !empty($_POST['username']) ? trim($_POST['username']) : null;
$passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;

$username = strip_tags($username);
$passwordAttempt = strip_tags($passwordAttempt);

$error = array();

$sql = "SELECT * FROM users WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user === false) {
    $error['msg'] = "That account couldn't be found in our Database.";
    echo json_encode($error);
    exit();
} else {
    $validPassword = password_verify($passwordAttempt, $user['password']);
    if ($validPassword) {
        if ($user['usergroup'] === NULL) {
            $sql2 = "UPDATE `users` SET `usergroup`= ? WHERE `user_id`= ?";
            $stmt2 = $pdo->prepare($sql2);
            $updateUserGroup = $stmt2->execute([$settings['verifiedGroup'], $user['user_id']]);
        }
        if ($settings['account_validation'] === "yes" && $user['usergroup'] === $settings['unverifiedGroup']) {
            $error['msg'] = "This community has verification required for new accounts. Please wait for Staff to approve your account. During this time, please don't message Staff about account validation.";
            echo json_encode($error);
            exit();
        }
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['logged_in'] = time();

        logAction('Logged In', $user['username']);

        //Successful login
        $error['msg'] = "";
        echo json_encode($error);
        exit();
    } else {
        logAction('Wrong Password', $username);
        $error['msg'] = "Sorry, your password is wrong. Please try again or contact Staff if you forgot it!";
        echo json_encode($error);
        exit();
    }
}
