<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
if (!isset($_SESSION['character_full_name'])) {
    header('Location: ../../../../' . $url['civilian'] . '?v=nosession');
    exit();
}

$newCompany['name'] = !empty($_POST['companyName']) ? trim($_POST['companyName']) : null;
$newCompany['name'] = strip_tags($_POST['companyName']);

$newCompany['desc'] = !empty($_POST['companyDesc']) ? trim($_POST['companyDesc']) : null;
$newCompany['desc'] = strip_tags($_POST['companyDesc']);

$error = array();

if (strlen($newCompany['name']) < 6) {
    $error['msg'] = "Your company name must be longer then 6 characters.";
    echo json_encode($error);
    exit();
}

// check if the company name already exists
$sql = "SELECT COUNT(c_name) AS num FROM companies WHERE c_name = :c_name";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':c_name', $newCompany['name']);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['num'] > 0) {
  $error['msg'] = "That company already exists. Please use a different name.";
  echo json_encode($error);
  exit();
}

// check if the company owner already owns a company, as we only want them to own one
$sql1 = "SELECT COUNT(c_owner) AS num FROM companies WHERE c_owner = :c_owner";
$stmt1 = $pdo->prepare($sql1);
$stmt1->bindValue(':c_owner', $_SESSION['character_full_name']);
$stmt1->execute();
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
if ($row1['num'] > 0) {
  $error['msg'] = "Sorry, you can only own one company.";
  echo json_encode($error);
  exit();
}

// finally, we will register the company
$sql2 = "INSERT INTO companies (c_name, c_owner, created_on) VALUES (
  :c_name,
  :c_owner,
  :created_on
  )";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindValue(':c_name', $newCompany['name']);
$stmt2->bindValue(':c_owner', $_SESSION['character_full_name']);
$stmt2->bindValue(':created_on', $us_date . ' ' . $time);
$result2 = $stmt2->execute();
if ($result2) {
    $error['msg'] = "";
    echo json_encode($error);
    exit();
}
