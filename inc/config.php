<?php
// General Configuration
$GLOBAL['language'] = "en-us"; // Set Language
$debug = true; // Toggle Debug
// Version Number -- Do Not Change
$version = "2.1.2";
$assets_ver = "3010";

// Set Language
// require('languages/' . $GLOBAL['language'] . '.php');
// Get Global Functions
require_once "functions.php";
// Get Site Config
$sql_fs = "SELECT * FROM settings";
$stmt_fs = $pdo->prepare($sql_fs);
$stmt_fs->execute();
$settingsRow = $stmt_fs->fetch(PDO::FETCH_ASSOC);

if (empty($settingsRow)) {
    throwError('Settings Table Missing/Broken', true);
    die("Settings Table Missing/Broken");
}

// Define variables
$settings['name'] = $settingsRow['site_name'];
$settings['account_validation'] = $settingsRow['account_validation'];
$settings['identity_validation'] = $settingsRow['identity_validation'];
$settings['steam_required'] = $settingsRow['steam_required'];
$settings['steam_domain'] = $settingsRow['steam_domain'];
$settings['steam_api'] = $settingsRow['steam_api'];
$settings['timezone'] = $settingsRow['timezone'];
$settings['civ_side_warrants'] = $settingsRow['civ_side_warrants'];

$stmt_clc = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'civ_side_layout'");
$clc = $stmt_clc->fetchAll();

if ($clc) {
  $settings['civ_side_layout'] = $settingsRow['civ_side_layout'];
  $civ_side_layout_clm_exists = true;
} else {
  $civ_side_layout_clm_exists = false;
}

$stmt_pcu = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'penalcode_url'");
$pcu = $stmt_pcu->fetchAll();

if ($pcu) {
  $settings['penalcode'] = $settingsRow['penalcode_url'];
  $penalcode_db_setup = true;
} else {
  $penalcode_db_setup = false;
}

$stmt_lmu = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'livemap_url'");
$lmu = $stmt_lmu->fetchAll();

if ($lmu) {
  $settings['livemap'] = $settingsRow['livemap_url'];
  $livemap_db_setup = true;
} else {
  $livemap_db_setup = false;
}

$settings['add_warrant'] = $settingsRow['add_warrant'];
$settings['discord_alerts'] = $settingsRow['discord_alerts'];
$discord_webhook = $settingsRow['discord_webhook'];

//group settings
$settings['unverifiedGroup'] = $settingsRow['group_unverifiedGroup'];
$settings['verifiedGroup'] = $settingsRow['group_verifiedGroup'];
$settings['banGroup'] = $settingsRow['group_banGroup'];
$settings['civ_char_limit'] = $settingsRow['civ_char_limit'];


$sql2 = "SELECT * FROM servers";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute();
$serversRow = $stmt2->fetch(PDO::FETCH_ASSOC);

if (empty($serversRow)) {
    throwError('Servers Table Missing/Broken', true);
    die("Servers Table Missing/Broken");
}

$_SESSION['server'] = '1';

// Define URLS
require_once "urls.php";

$ip = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set($settings['timezone']);
$date = date('Y-m-d');
$us_date = date_format(date_create_from_format('Y-m-d', $date) , 'm/d/Y');
$time = date('h:i:s A', time());
$datetime = $us_date . ' ' . $time;

$doul = $_SERVER['HTTP_HOST'];

$stmt_activeCheck   = $pdo->prepare("DELETE FROM on_duty WHERE `timestamp` < (NOW() - INTERVAL 60 MINUTE)");
$result_activeCheck = $stmt_activeCheck->execute();
?>
