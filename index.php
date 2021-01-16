<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';

require_once 'inc/config.php';

require_once 'inc/backend/user/auth/userIsLoggedIn.php';
$page['name'] = 'Home';
$stats['users'] = null;
$stats['civ'] = null;
$stats['ems'] = null;
$stats['news_count'] = null;

$stats['users'] = $pdo->query('select count(*) from users')->fetchColumn();
$stats['civ'] = $pdo->query('select count(*) from characters')->fetchColumn();
$stats['ems'] = $pdo->query('select count(*) from identities')->fetchColumn();
$stats['news_count'] = $pdo->query('select count(*) from news')->fetchColumn();

vCheck();
?>
<?php include 'inc/page-top.php'; ?>

<body>
    <?php include 'inc/top-nav.php'; ?>
    <?php
        if (isset($_GET['notify']) && strip_tags($_GET['notify']) === 'steam-linked') {
            clientNotify('success', 'Your Steam Account Has Been Linked.');
        }
      ?>
    <!-- CONTENT START -->
    <div class="wrapper m-b-15 m-t-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h4 class="page-title"><?php echo $page['name']; ?></h4>
                </div>
            </div>
            <div class="alert alert-success" role="alert">
              <strong>Please report any issues on freeCAD's <a href="https://discord.io/HydridSystems">Discord</a></strong>
            </div>
            <div class="row">
                <div class="col col-xs-6 hide-phone">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Total Users</h4>
                        <h2 class="p-t-10 mb-0"><?php echo $stats['users']; ?></h2>
                    </div>
                </div>
                <div class="col col-xs-6 hide-phone">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Total Civilians</h4>
                        <h2 class="p-t-10 mb-0"><?php echo $stats['civ']; ?></h2>
                    </div>
                </div>
                <div class="col col-xs-6 hide-phone">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Total Emergency Services</h4>
                        <h2 class="p-t-10 mb-0"><?php echo $stats['ems']; ?></h2>
                    </div>
                </div>
            </div>
            <?php if ($stats['news_count'] > '0'): ?>
              <div class="row">
                  <div class="col">
                    <div class="card-box">
                      <h4 class="header-title mt-0 m-b-30">Community News</h4>
                      <?php
                        $sql = "SELECT * FROM news ORDER BY id desc";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        while ($newsDB = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="card-box text-white bg-success outline-white-c">
                                <h4 class="header-title mt-0 m-b-30">'. $newsDB['title'] .'</h4>
                                <p>'.$newsDB['message'].'<br /><hr />
                                <strong>'.$newsDB['datetime'].'</strong> - Posted By: <strong>'.$newsDB['author'].'</strong></p>
                            </div>';
                        }
                      ?>
                    </div>
                  </div>
              </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- CONTENT END -->
    <?php include 'inc/copyright.php'; ?>
    <?php include 'inc/page-bottom.php'; ?>
    <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
