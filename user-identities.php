<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';
require_once 'inc/config.php';
require_once 'inc/backend/user/auth/userIsLoggedIn.php';

$page['name'] = 'My Identities';
tylerdator();
if (isset($_GET['a']) && strip_tags($_GET['a']) === 'deleteID') {
    if (isset($_GET['id']) && strip_tags($_GET['id'])) {
      $i_id = strip_tags($_GET['id']);
      $sql  = "SELECT * FROM identities WHERE identity_id = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$i_id]);
      $idDB = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($idDB === false) {
        header('Location: user-identities.php');
        exit();
      } else {
        if ($idDB['user'] === $user_id) {
          $stmt2 = $pdo->prepare("DELETE FROM identities WHERE identity_id = ?");
          $stmt2->execute([$i_id]);
          sleep(3);
          header('Location: user-identities.php?success=id-deleted');
          exit();
        } else {
          header('Location: user-identities.php');
          exit();
        }
      }
    }
} elseif (isset($_POST['purgeUserIDS'])) {
  $stmt = $pdo->prepare("DELETE FROM identities WHERE user = ?");
  $stmt->execute([$user_id]);
  sleep(10);
  header('Location: user-identities.php?success=ids-purged');
}
?>
<?php include 'inc/page-top.php'; ?>

<body>
    <?php include 'inc/top-nav.php';
    if (isset($_GET['success']) && strip_tags($_GET['success']) === 'id-deleted') {
        clientNotify('success', 'You have deleted that Identity!');
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
            <!-- CONTENT HERE -->
            <div class="row">
              <div class="col">
                  <div class="card-box">
                    <form method="post">
                      <button type="submit" name="purgeUserIDS" onclick="return confirm('Are you sure you want to PURGE all identities?');" class="btn btn-md btn-danger float-right">
                        Purge
                      </button>
                    </form>
                    <table id="datatable" class="table table-borderless">
                      <thead>
                          <tr>
                              <th>Name</th>
                              <th>Department</th>
                              <th>Supervisor</th>
                              <th>Created On</th>
                              <th>Status</th>
                              <th>Actions</th>
                          </tr>
                      </thead>


                      <tbody>
                      <?php
                      $sql             = "SELECT * FROM identities WHERE user=?";
                      $stmt            = $pdo->prepare($sql);
                      $stmt->execute([$user_id]);
                      $idsRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      foreach ($idsRow as $id) { ?>
                        <tr>
                          <td><?php echo $id['name']; ?></td>
                          <td><?php echo $id['department']; ?></td>
                          <td><?php echo $id['supervisor']; ?></td>
                          <td><?php echo $id['created_on']; ?></td>
                          <td><?php echo $id['status']; ?></td>
                          <td>
                          <a href="javascript:void(0);" data-href="inc/backend/user/other/getIDInfo.php?id=<?php echo $id['identity_id']; ?>" class="btn btn-sm btn-info openIDEditorModal">Edit</a>
                          <a href="user-identities.php?a=deleteID&id=<?php echo $id['identity_id']; ?>" onclick="return confirm('Are you sure you want to delete this Identity?');" class="btn btn-sm btn-danger">Delete</a></td>
                        </tr>
                      <?php } ?>
                      </tbody>
                    </table>
                  </div>
              </div>
            </div>

            <!-- ID Editor Modal -->
            <div class="modal fade" id="idEditorModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-full" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit ID</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div id="idEditorModalBody" class="modal-body">

                        </div>
                    </div>
                </div>
            </div>
            <!-- // -->

        </div>
        <!-- CONTENT END -->

        <script type="text/javascript">
        $(document).ready(function() {
          $('.openIDEditorModal').on('click',function(){
              var dataURL = $(this).attr('data-href');
              $('#idEditorModalBody.modal-body').load(dataURL,function(){
                  $('#idEditorModal').modal({show:true});
              });
          });
        });
        </script>
        <?php include 'inc/copyright.php'; ?>
        <?php include 'inc/page-bottom.php'; ?>
        <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
