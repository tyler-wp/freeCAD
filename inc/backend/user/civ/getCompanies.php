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

// Page PHP
$sql = "SELECT * FROM companies WHERE c_owner=:c_owner";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':c_owner', $_SESSION['character_full_name']);
$stmt->execute();
$companiesDBCall = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($companiesDBCall)) {
    echo '
      You have no Companies in your name.
      ';
}
else {
    echo '
      <table class="table table-borderless">
          <thead>
            <tr>
                <th width="95%"><strong>Name</strong></th>
                <th width="5%"><strong>Actions</strong></th>
            </tr>
          </thead>
            <tbody>
              ';
    foreach ($companiesDBCall as $company) {
        echo '
        <tr>
            <td>' . $company['c_name'] . '</td>
            <td><input type="button" class="btn btn-danger btn-sm" name="deleteCompany" value="Delete" id=' . $company['c_id'] . ' onclick="deleteCompany(this)"></td>
        </tr>';
    }

    echo '
            </tbody>
      </table>';
}
