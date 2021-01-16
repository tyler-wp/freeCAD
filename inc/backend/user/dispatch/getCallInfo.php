<?php
session_name('hydrid');
session_start();
require '../../../connect.php';

require '../../../config.php';

require '../../../backend/user/auth/userIsLoggedIn.php';

// Makes sure the person actually has a character set
if ($_SESSION['on_duty'] === "Dispatch" || $_SESSION['on_duty'] === "LEO") {
    $call_id = strip_tags($_GET['id']);
    $sql = "SELECT * FROM 911calls WHERE call_id= ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$call_id]);
    $callInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['viewingCallID'] = $call_id;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <script src="assets/js/pages/dispatch.js?v=<?php echo $assets_ver ?>"></script>
    <script type="text/javascript">
        getAllActiveUnitsForCall();
        getAllActiveUnitsForNewCall();
        getAttchedUnits();
        $(document).ready(function () {
            $('#updateCallDesc').ajaxForm(function (error) {
                console.log(error);
                var error = JSON.parse(error);
                if (error['msg'] === "") {
                    toastr.success('Call Description Updated.', 'System:', {timeOut: 10000});
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
      <div class="col-6">
        <div class="form-group">
          <label for="callLocation">Call Location</label>
          <input class="form-control" type="text" readonly="" value="<?php echo $callInfo['call_location'] .' / '. $callInfo['call_postal']; ?>">
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="callStatus">Call Status</label>
          <div class="form-group">
              <select class="form-control" name="updateCallStatus" onChange='updateCallStatus(this)'>
                  <option selected="true" disabled="true"><?php echo $callInfo['call_status']; ?></option>
                  <?php if ($callInfo['call_status'] === "ASSIGNED" || $callInfo['call_status'] === "NOT ASSIGNED"): ?>
                    <option value="PRIORITY">PRIORITY</option>
                  <?php else: ?>
                    <option value="ASSIGNED">UN-PRIORITIZE</option>
                  <?php endif; ?>
              </select>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <div class="form-group">
          <label for="callDetails">Call Details</label>
          <form id="updateCallDesc" method="post" action="inc/backend/user/dispatch/updateCallDesc.php">
            <textarea class="form-control" id="callDetails" name="callDesc" style="white-space: pre-line;" wrap="hard" rows="6"><?php echo $callInfo['call_description']; ?></textarea>
            <div class="row">
              <div class="col-12">
                <input class="btn btn-warning btn-block" type="submit" value="Update Call Description">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php if ($_SESSION['on_duty'] === "Dispatch"): ?>
      <?php if ($callInfo['call_status'] === "PRIORITY"): ?>
        <div class="alert alert-danger" role="alert">
          This is a PRIORITY call. All units are automatically attached.
        </div>
      <?php else: ?>
        <div class="row">
          <div class="col-5">
            <div class="form-group">
              <label for="getAllActiveUnitsForCall">Assign Unit</label>
              <select class="form-control" name="getAllActiveUnitsForCall" id="getAllActiveUnitsForCall" onchange="assignUnit(this.value)">
                 <option selected="true" disabled="disabled">Loading Units...</option>
              </select>
            </div>
          </div>
          <div class="col-7">
            <div id="getAttchedUnits"></div>
          </div>
        </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-5">
          <input type="button" class="btn btn-danger btn-block" name="clearCall" value="Clear Call" onclick="clear911Call()">
        </div>
      </div>
    <?php else: ?>
      <div class="row">
        <div class="col-6">
          <h4 class="header-title"><center>Other Attached Units</center></h4>
          <div id="getAttchedUnits"></div>
        </div>
        <div class="col-6">
          <input type="button" class="btn btn-danger btn-block" name="clearCall" value="Clear Call" onclick="clear911Call()">
        </div>
      </div>
    <?php endif; ?>
  </body>
</html>
