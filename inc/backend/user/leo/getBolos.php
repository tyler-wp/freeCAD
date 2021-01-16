<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set

if ($_SESSION['on_duty'] === "Dispatch" || $_SESSION['on_duty'] === "LEO") {
    // First we will check if any units are actually online
    $countActiveBolos = $pdo->query('select count(*) from bolos')->fetchColumn();
    if ($countActiveBolos === 0) {
      echo 'No Active BOLOs';
    } else {
      echo '
      <table class="table table-borderless">
      <tr>
        <th>Description</th>
        <th>Created On</th>';
        if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings) {
        echo '<th>Actions</th>';
        }
      echo '</tr>
      ';
      $getActiveBolos = 'SELECT * FROM bolos';
      $result         = $pdo->prepare($getActiveBolos);
      $result->execute();
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
          echo "<td width='55%'>" . $row['description'] . "</td>";
          echo "<td>" . $row['created_on'] . "</td>";
          if ($_SESSION['identity_supervisor'] === "Yes" || staff_siteSettings) {
            echo '<td><input type="button" class="btn btn-danger btn-sm" name="deleteBolo" value="Delete Bolo" id='.$row['id'].' onclick="deleteBoloLEO(this)"></td>';
          }
          echo "</tr>";
    }
    echo '</table>';

  }
}

?>
