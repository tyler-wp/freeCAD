<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

$editingid_id = strip_tags($_GET['id']);
$sql = "SELECT * FROM identities WHERE identity_id= ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$editingid_id]);
$idRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($idRow['user'] !== $_SESSION['user_id']) {
  die();
}

$_SESSION['editingU_ID_ID'] = $editingid_id;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <script src="assets/js/pages/dispatch.js?v=<?php echo $assets_ver ?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#updateIDName').ajaxForm(function (error) {
                console.log(error);
                var error = JSON.parse(error);
                if (error['msg'] === "") {
                    toastr.success('ID Name Updated. Please Refresh The Page To See Changes.', 'System:', {timeOut: 10000});
                } else {
                    toastr.error(error['msg'], 'System:', {
                        timeOut: 10000
                    });
                }
            });
        });
    </script>
  </head>
  <body>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="idName">ID Name</label>
          <form id="updateIDName" method="post" action="inc/backend/user/other/updateIDName.php">
            <input class="form-control" type="text" name="newIDName" value="<?php echo $idRow['name']; ?>">
            <div class="row">
            <div class="col-12">
              <br /><input class="btn btn-success" type="submit" value="Update Name">
            </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
