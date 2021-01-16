<?php
session_name('hydrid');
session_start();
require_once 'inc/connect.php';

require_once 'inc/config.php';

require_once 'inc/backend/user/auth/userIsLoggedIn.php';

$page['name'] = 'Civilian Module';
tylerdator();
// Page PHP

$view = strip_tags($_GET['v']);
civLoad();

$stmt_charlimitcheck = $pdo->prepare("SELECT count(*) FROM characters WHERE owner_id = ?");
$stmt_charlimitcheck->execute([$_SESSION['user_id']]);
$myCharCount = $stmt_charlimitcheck->fetchColumn();

?>
<?php include 'inc/page-top.php'; ?>
<script src="assets/js/pages/civilian.js?v=<?php echo $assets_ver ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#createCharacter').ajaxForm(function(error) {
            console.log(error);
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                $.ajax({
                    url: 'inc/backend/user/civ/getCharacters.php',
                    success: function(data) {
                        $('#listCharacters').html(data);
                    }
                });
                $("#createCharacter")[0].reset();
                toastr.success('Character Created', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        });
        $('#new911call').ajaxForm(function(error) {
            console.log(error);
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                $('#new911callModal').modal('hide');
                toastr.success('911 Call Created', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        });
        $('#createVehicle').ajaxForm(function(error) {
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                $('#newVehicleModal').modal('hide');
                toastr.success('Vehicle Added To System', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        });
        $('#createFirearm').ajaxForm(function(error) {
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                $('#newFirearmModel').modal('hide');
                toastr.success('Firearm Added To System', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        });
        $('#createWarrant').ajaxForm(function(error) {
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                $('#newSelfWarrantModal').modal('hide');
                refreshWarrants();
                toastr.success('Warrant Created.', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        });
        $('#createCompany').ajaxForm(function(error) {
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                $('#newCompanyModal').modal('hide');
                refreshCompanies();
                toastr.success('Company Created.', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        });
    });
</script>

<body>
    <?php
        if (isset($_GET['error']) && strip_tags($_GET['error']) === 'character-not-found') {
            clientNotify('error', 'We couldn\'t find that Character.');
        } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'character-owner') {
            clientNotify('error', 'No Permission.');
        } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'character-session') {
            clientNotify('error', 'Session Error. Select Character again.');
        } elseif (isset($_GET['error']) && strip_tags($_GET['error']) === 'character-deleted') {
            clientNotify('error', 'Character Deleted!');
        }
        ?>
    <?php include 'inc/top-nav.php'; ?>
    <!-- CONTENT START -->
    <div class="wrapper m-b-15 m-t-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h4 class="page-title">
                        <?php echo $page['name']; ?></h4>
                </div>
            </div>
            <!-- CONTENT HERE -->
            <?php switch($view):
                  case "nosession": ?>
            <script type="text/javascript">
            getUserCharacters();
            </script>
            <?php if ($myCharCount === $settings['civ_char_limit']): ?>
              <div class="alert alert-light" role="alert">
                Warning! This community only allows <?php echo $settings['civ_char_limit']; ?> Civilian Character(s) per person! You have reached your limit, and can not create anymore.
              </div>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <div class="card-box">
                        <select class="form-control" id="listCharacters" onchange="location = this.value;">
                            <option selected="true" disabled="disabled">Loading Characters...</option>
                        </select>
                    </div>
                </div>
                <div class="col <?php if ($myCharCount === $settings['civ_char_limit']): ?>
                  blurrred
                <?php endif; ?>">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Character Creator</h4>
                        <form class="form-horizontal m-t-20" id="createCharacter" action="inc/backend/user/civ/createCharacter.php" method="POST">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="firstname" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="lastname" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <select class="form-control" name="gender" required="">
                                            <option selected="true" disabled="disabled">Select Gender</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                            <option value="O">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <select class="form-control" name="race" required="">
                                            <option selected="true" disabled="disabled">Select Race</option>
                                            <option value="Alaska Native">Alaska Native</option>
                                            <option value="American Indian">American Indian</option>
                                            <option value="Asian">Asian</option>
                                            <option value="Black">Black</option>
                                            <option value="African American">African American</option>
                                            <option value="Native Hawaiian">Native Hawaiian</option>
                                            <option value="White">White</option>
                                            <option value="Hispanic">Hispanic</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="address" placeholder="Address">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <input class="form-control" type="date" required="" name="date_of_birth">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <select class="form-control" name="height" required="">
                                            <option selected="true" disabled="disabled">Select Height</option>
                                            <option value="4'6">4'6</option>
                                            <option value="4'7">4'7</option>
                                            <option value="4'8">4'8</option>
                                            <option value="4'9">4'9</option>
                                            <option value="4'10">4'10</option>
                                            <option value="4'11">4'11</option>
                                            <option value="5'0">5'0</option>
                                            <option value="5'1">5'1</option>
                                            <option value="5'2">5'2</option>
                                            <option value="5'3">5'3</option>
                                            <option value="5'4">5'4</option>
                                            <option value="5'5">5'5</option>
                                            <option value="5'6">5'6</option>
                                            <option value="5'7">5'7</option>
                                            <option value="5'8">5'8</option>
                                            <option value="5'9">5'9</option>
                                            <option value="5'10">5'10</option>
                                            <option value="5'11">5'11</option>
                                            <option value="6'0">6'0</option>
                                            <option value="6'1">6'1</option>
                                            <option value="6'2">6'2</option>
                                            <option value="6'3">6'3</option>
                                            <option value="6'4">6'4</option>
                                            <option value="6'5">6'5</option>
                                            <option value="6'6">6'6</option>
                                            <option value="6'7">6'7</option>
                                            <option value="6'8">6'8</option>
                                            <option value="6'9">6'9</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="weight" placeholder="Weight">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="eye_color" placeholder="Eye Color">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="" name="hair_color" placeholder="Hair Color">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input class="btn btn-success btn-block" onClick="disableClick()" type="submit" value="Create Character">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php break; ?>
            <?php case "main": ?>
            <?php
                $sql             = "SELECT * FROM characters WHERE character_id = :character_id";
                $stmt            = $pdo->prepare($sql);
                $stmt->bindValue(':character_id', $_SESSION['character_id']);
                $stmt->execute();
                $characterDB = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$characterDB) {
                   die("<font color='red'><b>FATAL SYSTEM ERROR - TRY AGAIN</b></font>");
                } ?>
            <script type="text/javascript">
                function getCivInfo() {
                    (function loadVehicles() {
                        $.ajax({
                            url: 'inc/backend/user/civ/getVehicles.php',
                            success: function(data) {
                                $('#getVehicles').html(data);
                            },
                            complete: function() {
                                // Schedule the next request when the current one's complete
                                setTimeout(loadVehicles, 5000);
                            }
                        });
                    })();
                    (function loadFirearms() {
                        $.ajax({
                            url: 'inc/backend/user/civ/getFirearms.php',
                            success: function(data) {
                                $('#getFirearms').html(data);
                            },
                            complete: function() {
                                // Schedule the next request when the current one's complete
                                setTimeout(loadFirearms, 5005);
                            }
                        });
                    })();
                }
                getCivInfo();

                function loadArrests() {
                    $.ajax({
                        url: 'inc/backend/user/civ/getArrests.php',
                        success: function(data) {
                            $('#getArrests').html(data);
                        }
                    });
                }
                loadArrests();

                function refreshArrests() {
                    // Disable refresh button for 15 seconds to prevent db spam
                    document.getElementById("refreshArrestsBtn").disabled = true;
                    setTimeout(function(){document.getElementById("refreshArrestsBtn").disabled = false;},15000);

                    // Notifys
                    $('#getArrests').html("Refreshing...");
                    toastr.success('Refreshing Arrests', 'System:', {
                        timeOut: 10000
                    });
                    $.ajax({
                        url: 'inc/backend/user/civ/getArrests.php',
                        success: function(data) {
                            $('#getArrests').html(data);
                        }
                    });
                }

                function loadCompanies() {
                    $.ajax({
                        url: 'inc/backend/user/civ/getCompanies.php',
                        success: function(data) {
                            $('#getCompanies').html(data);
                        }
                    });
                }
                loadCompanies();

                function refreshCompanies() {
                    // Disable refresh button for 15 seconds to prevent db spam
                    document.getElementById("refreshCompaniesBtn").disabled = true;
                    setTimeout(function(){document.getElementById("refreshCompaniesBtn").disabled = false;},15000);

                    // Notifys
                    $('#getArrests').html("Refreshing...");
                    toastr.success('Refreshing Companies', 'System:', {
                        timeOut: 10000
                    });
                    $.ajax({
                        url: 'inc/backend/user/civ/getCompanies.php',
                        success: function(data) {
                            $('#getCompanies').html(data);
                        }
                    });
                }

                function loadTickets() {
                    $.ajax({
                        url: 'inc/backend/user/civ/getTickets.php',
                        success: function(data) {
                            $('#getTickets').html(data);
                        }
                    });
                }
                loadTickets();

                function refreshTickets() {
                    // Disable refresh button for 15 seconds to prevent db spam
                    document.getElementById("refreshTicketsBtn").disabled = true;
                    setTimeout(function(){document.getElementById("refreshTicketsBtn").disabled = false;},15000);

                    // Notifys
                    $('#getTickets').html("Refreshing...");
                    toastr.success('Refreshing Tickets', 'System:', {
                        timeOut: 10000
                    });
                    $.ajax({
                        url: 'inc/backend/user/civ/getTickets.php',
                        success: function(data) {
                            $('#getTickets').html(data);
                        }
                    });
                }

                function loadWarrants() {
                    $.ajax({
                        url: 'inc/backend/user/civ/getWarrants.php',
                        success: function(data) {
                            $('#getWarrants').html(data);
                        }
                    });
                }
                loadWarrants();

                function refreshWarrants() {
                    // Disable refresh button for 15 seconds to prevent db spam
                    document.getElementById("refreshWarrantsBtn").disabled = true;
                    setTimeout(function(){document.getElementById("refreshWarrantsBtn").disabled = false;},15000);

                    // Notifys
                    $('#getWarrants').html("Refreshing...");
                    toastr.success('Refreshing Warrants', 'System:', {
                        timeOut: 10000
                    });
                    $.ajax({
                        url: 'inc/backend/user/civ/getWarrants.php',
                        success: function(data) {
                            $('#getWarrants').html(data);
                        }
                    });
                }

                $(document).ready(function() {
                    var signal100 = false;

                    function loadSig100Status() {
                        $.ajax({
                            url: 'inc/backend/user/leo/checkSignal100.php',
                            success: function(data) {
                                if (data === "1") {
                                    toastr.options = {
                                        "preventDuplicates": true,
                                        "preventOpenDuplicates": true
                                    };
                                    toastr.error('SIGNAL 100 IS IN EFFECT. DO NOT START A NEW HIGH PRIORITY', 'System:', {
                                        timeOut: 10000
                                    })
                                    $('#civSignal100Notice').html("<font color='red'><b>SIGNAL 100 IS IN EFFECT. DO NOT START A NEW HIGH PRIORITY</b></font>");
                                    signal100 = true;
                                } else {
                                    $('#civSignal100Notice').html("");
                                    signal100 = false;
                                }
                            },
                            complete: function() {
                                // Schedule the next request when the current one's complete
                                setTimeout(loadSig100Status, 500);
                            }
                        });
                    }
                    loadSig100Status();
                });
            </script>
            <!-- Character Actions -->
            <div class="row">
                <div class="col">
                    <div class="card-box">
                        <h4 class="header-title mt-0 m-b-30">Actions for <?php echo $_SESSION['character_first_name'] .' '. $_SESSION['character_last_name'] ?></h4>
                        <button class="btn btn-info btn-sm w-40" data-toggle="modal" data-target="#licenseModal">License Management</button>
                        <button class="btn btn-secondary btn-sm ml-2 w-40" data-toggle="modal" data-target="#new911callModal">New 911 Call</button>
                        <button class="btn btn-danger btn-sm ml-2 w-40" data-toggle="modal" data-target="#deleteCharacterModel">Delete Character</button>
                    </div>
                </div>
            </div>

            <div id="civSignal100Notice"></div>

            <?php
            if ($civ_side_layout_clm_exists === false) {
              die('<div class="alert alert-danger" role="alert">Sorry, it looks like something is missing from the database! Please tell the community owner to update in the Admin Panel.
              <br /> If you are the Community Owner, go to Admin Panel --> Module Settings --> Civ Layout --> Update Button</div>');
            }
            ?>

            <?php if ($settings['civ_side_layout'] === "tabs"): ?>
              <div id="civTabbedStyleWizard" class="pull-in">
                  <ul class="nav nav-tabs nav-justified">
                      <li class="nav-item"><a href="#charInfo" data-toggle="tab" class="nav-link">Info</a></li>
                      <li class="nav-item"><a href="#charVeh" data-toggle="tab" class="nav-link">Vehicles</a></li>
                      <li class="nav-item"><a href="#charWpns" data-toggle="tab" class="nav-link">Weapons</a></li>
                      <li class="nav-item"><a href="#charTickets" data-toggle="tab" class="nav-link">Tickets</a></li>
                      <li class="nav-item"><a href="#charArrests" data-toggle="tab" class="nav-link">Arrests</a></li>
                      <li class="nav-item"><a href="#charWarrants" data-toggle="tab" class="nav-link">Warrants</a></li>
                      <li class="nav-item"><a href="#charCompany" data-toggle="tab" class="nav-link">Companies</a></li>
                  </ul>

                  <div class="tab-content b-0 mb-0">
                    <div class="tab-pane m-t-10 fade" id="charInfo">
                      <h5>Name: <?php echo $_SESSION['character_first_name'] .' '. $_SESSION['character_last_name']; ?></h5>
                      <h5>Sex: <?php echo $_SESSION['character_sex']; ?></h5>
                      <h5>Race: <?php echo $_SESSION['character_race']; ?></h5>
                      <h5>Date of Birth: <?php echo $_SESSION['character_dob']; ?></h5>
                      <h5>Address: <?php echo $_SESSION['character_address']; ?></h5>

                      <h5>Height / Weight: <?php echo $_SESSION['character_height'] .' '. $_SESSION['character_weight']; ?></h5>
                      <h5>Hair Color: <?php echo $_SESSION['character_hair_color']; ?></h5>
                      <h5>Eye Color: <?php echo $_SESSION['character_eye_color']; ?></h5>
                    </div>

                    <div class="tab-pane m-t-10 fade" id="charVeh">
                      <div class="col">
                          <div class="card-box">
                              <div class="alert alert-info" role="alert">
                                <strong>If one of your vehicles is blurred out, that means it is currently impounded. Check back in a few days.</strong>
                              </div>
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40" data-toggle="modal" data-target="#newVehicleModal">Add New Vehicle</button>
                              </div>
                              <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Vehicles</h4>
                              <!-- CONTENT -->
                              <div id="getVehicles">Loading Info...</div>
                          </div>
                      </div>
                    </div>

                    <div class="tab-pane m-t-10 fade" id="charWpns">
                      <div class="col">
                          <div class="card-box">
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40" data-toggle="modal" data-target="#newFirearmModel">Add New Firearm</button>
                              </div>
                              <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Firearms</h4>
                              <!-- CONTENT -->
                              <div id="getFirearms">Loading Info...</div>
                          </div>
                      </div>
                    </div>

                    <div class="tab-pane m-t-10 fade" id="charTickets">
                      <div class="col">
                          <div class="card-box">
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40" id="refreshTicketsBtn" onclick="refreshTickets();"><i class="ti-loop m-r-5"></i> Refresh</button>
                              </div>
                              <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Tickets</h4>
                              <!-- CONTENT -->
                              <div id="getTickets">Loading Info...</div>
                          </div>
                      </div>
                    </div>

                    <div class="tab-pane m-t-10 fade" id="charArrests">
                      <div class="col">
                          <div class="card-box">
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40" id="refreshArrestsBtn" onclick="refreshArrests();"><i class="ti-loop m-r-5"></i> Refresh</button>
                              </div>
                              <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Arrests</h4>
                              <!-- CONTENT -->
                              <div id="getArrests">Loading Info...</div>
                          </div>
                      </div>
                    </div>

                    <div class="tab-pane m-t-10 fade" id="charWarrants">
                      <div class="col">
                          <div class="card-box">
                              <?php if ($settings['civ_side_warrants'] === "true"): ?>
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40 m-l-5" data-toggle="modal" data-target="#newSelfWarrantModal">Add New Warrant (Self)</button>
                              </div>
                              <?php endif; ?>
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40" id="refreshWarrantsBtn" onclick="refreshWarrants();"><i class="ti-loop m-r-5"></i> Refresh</button>
                              </div>
                              <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Warrants</h4>
                              <!-- CONTENT -->
                              <div id="getWarrants">Loading Info...</div>
                          </div>
                      </div>
                    </div>

                    <div class="tab-pane m-t-10 fade" id="charCompany">
                      <div class="col">
                          <div class="card-box">
                              <div class="alert alert-warning" role="alert">
                                <strong>The Company features are still an early WIP. This module is only to allow users to create companies. Features will soon be released that allow adding users to your company, editing, etc.</strong>
                              </div>
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40 m-l-5" data-toggle="modal" data-target="#newCompanyModal">Create Company</button>
                              </div>
                              <div class="dropdown pull-right">
                                  <button class="btn btn-success btn-sm w-40" id="refreshCompaniesBtn" onclick="refreshCompanies();"><i class="ti-loop m-r-5"></i> Refresh</button>
                              </div>
                              <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Companies</h4>
                              <!-- CONTENT -->
                              <div id="getCompanies">Loading Info...</div>
                          </div>
                      </div>
                    </div>
                  </div>
              </div>
            <?php else: ?>
                <div class="row">
                    <div class="col">
                        <div class="card-box">
                            <div class="dropdown pull-right">
                                <button class="btn btn-success btn-sm w-40" data-toggle="modal" data-target="#newVehicleModal">Add New Vehicle</button>
                            </div>
                            <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Vehicles</h4>
                            <!-- CONTENT -->
                            <div id="getVehicles">Loading Info...</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-box">
                            <div class="dropdown pull-right">
                                <button class="btn btn-success btn-sm w-40" data-toggle="modal" data-target="#newFirearmModel">Add New Firearm</button>
                            </div>
                            <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Firearms</h4>
                            <!-- CONTENT -->
                            <div id="getFirearms">Loading Info...</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card-box">
                            <div class="dropdown pull-right">
                                <button class="btn btn-success btn-sm w-40" id="refreshTicketsBtn" onclick="refreshTickets();"><i class="ti-loop m-r-5"></i> Refresh</button>
                            </div>
                            <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Tickets</h4>
                            <!-- CONTENT -->
                            <div id="getTickets">Loading Info...</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-box">
                            <?php if ($settings['civ_side_warrants'] === "true"): ?>
                            <div class="dropdown pull-right">
                                <button class="btn btn-success btn-sm w-40 m-l-5" data-toggle="modal" data-target="#newSelfWarrantModal">Add New Warrant (Self)</button>
                            </div>
                            <?php endif; ?>
                            <div class="dropdown pull-right">
                                <button class="btn btn-success btn-sm w-40" id="refreshWarrantsBtn" onclick="refreshWarrants();"><i class="ti-loop m-r-5"></i> Refresh</button>
                            </div>
                            <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Warrants</h4>
                            <!-- CONTENT -->
                            <div id="getWarrants">Loading Info...</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card-box">
                            <div class="dropdown pull-right">
                                <button class="btn btn-success btn-sm w-40" id="refreshArrestsBtn" onclick="refreshArrests();"><i class="ti-loop m-r-5"></i> Refresh</button>
                            </div>
                            <h4 class="header-title mt-0 m-b-30"><?php echo $_SESSION['character_first_name'] ?>'s Arrests</h4>
                            <!-- CONTENT -->
                            <div id="getArrests">Loading Info...</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- MODALS -->
            <!-- New Company Modal Model -->
            <div class="modal fade" id="newCompanyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Creating Company</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="createCompany" action="inc/backend/user/civ/createCompany.php" method="post">
                                <div class="form-group">
                                    <input type="text" name="companyName" class="form-control" placeholder="Your Company Name" data-lpignore="true" required />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="companyDesc" class="form-control" placeholder="Give a brief description of your company" data-lpignore="true" required />
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <input class="btn btn-primary" type="submit" value="Create Company">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Warrant Model -->
            <div class="modal fade" id="newSelfWarrantModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Adding Warrant</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="createWarrant" action="inc/backend/user/civ/createWarrant.php" method="post">
                                <div class="form-group">
                                    <select class="form-control" name="warrant_reason" required>
                                        <option value="" disabled selected>Select Warrant...</option>
                                        <option value="Murder">Murder</option>
                                        <option value="Murder of a LEO">Murder of a LEO</option>
                                        <option value="Murder of LEO(s)">Murder of LEO(s)</option>
                                        <option value="Murder of a First Responder">Murder of a First Responder</option>
                                        <option value="Murder of First Responder(s)">Murder of First Responder(s)</option>
                                        <option value="Murder of a Government Official">Murder of a Government Official</option>
                                        <option value="Murder of Government Official(s)">Murder of Government Official(s)</option>
                                        <option value="Kidnapping">Kidnapping</option>
                                        <option value="Kidnapping of a LEO">Kidnapping of a LEO</option>
                                        <option value="Kidnapping of LEO(s)">Kidnapping of LEO(s)</option>
                                        <option value="Kidnapping of a First Responder">Kidnapping of a First Responder</option>
                                        <option value="Kidnapping of First Responder(s)">Kidnapping of First Responder(s)</option>
                                        <option value="Kidnapping of a Government Official">Kidnapping of a Government Official</option>
                                        <option value="Kidnapping of Government Official(s)">Kidnapping of Government Official(s)</option>
                                        <option value="Robbery">Robbery</option>
                                        <option value="Robbery /w Deadly Weapon">Robbery /w Deadly Weapon</option>
                                        <option value="Bank Robbery">Bank Robbery</option>
                                        <option value="Bank Robbery /w Deadly Weapon">Bank Robbery /w Deadly Weapon</option>
                                        <option value="Prison Break">Prison Break</option>
                                        <option value="Prison Break /w Deadly Weapon">Prison Break /w Deadly Weapon</option>
                                        <option value="Prison Escape">Prison Escape</option>
                                        <option value="Failure To Appear In Court">Failure To Appear In Court</option>
                                        <option value="Grand Theft">Grand Theft</option>
                                        <option value="Grand Theft Auto">Grand Theft Auto</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <input class="btn btn-primary" type="submit" value="Add Warrant">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- License Modal -->
            <div class="modal fade" id="licenseModal" tabindex="-1" role="dialog" aria-labelledby="licenseModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="licenseModal">License Management</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="lic_mgt_driver">Drivers License</label>
                                <select class="form-control" name="lic_mgt_driver" onChange='updateDriversLicense(this);'>
                                    <?php
                                 if ($_SESSION['character_license_driver'] === "None") {
                                   echo '
                                   <option value="None" selected>None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake">Fake</option>
                                   <option value="Permit">Permit</option>
                                   ';
                                 } elseif ($_SESSION['character_license_driver'] === "Valid") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid" selected>Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake">Fake</option>
                                   <option value="Permit">Permit</option>
                                   ';
                                 } elseif ($_SESSION['character_license_driver'] === "Suspended") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended" selected>Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake">Fake</option>
                                   <option value="Permit">Permit</option>
                                   ';
                                 } elseif ($_SESSION['character_license_driver'] === "Revoked") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked" selected>Revoked</option>
                                   <option value="Fake">Fake</option>
                                   <option value="Permit">Permit</option>
                                   ';
                                 } elseif ($_SESSION['character_license_driver'] === "Fake") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake" selected>Fake</option>
                                   <option value="Permit">Permit</option>
                                   ';
                                 } elseif ($_SESSION['character_license_driver'] === "Permit") {
                                    echo '
                                    <option value="None">None</option>
                                    <option value="Valid">Valid</option>
                                    <option value="Suspended">Suspended</option>
                                    <option value="Revoked">Revoked</option>
                                    <option value="Fake">Fake</option>
                                    <option value="Permit" selected>Permit</option>
                                    ';
                                  }
                                  ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="lic_mgt_ccw">CCW License</label>
                                <select class="form-control" name="lic_mgt_ccw" onChange='updateFirearmLicense(this);'>
                                    <?php
                                 if ($_SESSION['character_license_firearm'] === "None") {
                                   echo '
                                   <option value="None" selected>None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake">Fake</option>
                                   ';
                                 } elseif ($_SESSION['character_license_firearm'] === "Valid") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid" selected>Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake">Fake</option>
                                   ';
                                 } elseif ($_SESSION['character_license_firearm'] === "Suspended") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended" selected>Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake">Fake</option>
                                   ';
                                 } elseif ($_SESSION['character_license_firearm'] === "Revoked") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked" selected>Revoked</option>
                                   <option value="Fake">Fake</option>
                                   ';
                                 } elseif ($_SESSION['character_license_firearm'] === "Fake") {
                                   echo '
                                   <option value="None">None</option>
                                   <option value="Valid">Valid</option>
                                   <option value="Suspended">Suspended</option>
                                   <option value="Revoked">Revoked</option>
                                   <option value="Fake" selected>Fake</option>
                                   ';
                                 }
                                  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Call Modal -->
            <div class="modal fade" id="new911callModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New 911 Call</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="new911call" action="inc/backend/user/civ/new911call.php" method="post">
                                <div class="form-group">
                                    <input type="text" name="call_description" class="form-control" placeholder="Call Desc" data-lpignore="true" required />
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" id="street_ac2" name="call_location" class="form-control" placeholder="Street" data-lpignore="true" required />
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" name="call_postal" class="form-control" pattern="\d*" placeholder="Postal" data-lpignore="true" />
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Create New Call">
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Delete Character Modal -->
            <div class="modal fade" id="deleteCharacterModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">DELETING CHARACTER (<?php echo $_SESSION['character_full_name']; ?>)</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="deleteCharacter" action="inc/backend/user/civ/deleteCharacter.php" method="post">
                                <div class="alert alert-danger" role="alert"><strong>Are you sure you want to delete this Character? This can NOT be undone.</strong></div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                <input class="btn btn-danger" onClick="disableClick()" type="submit" value="Yes">
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- New Vehicle Model -->
            <div class="modal fade" id="newVehicleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Register Vehicle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="createVehicle" action="inc/backend/user/civ/createVehicle.php" method="post">
                                <div class="form-group">
                                    <input type="text" name="plate" class="form-control" maxlength="8" style="text-transform:uppercase" placeholder="License Plate" data-lpignore="true" required />
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <select class="form-control" name="color" required>
                                                <option value="" disabled selected>Vehicle Color</option>
                                                <option value="Black">Black</option>
                                                <option value="White">White</option>
                                                <option value="Red">Red</option>
                                                <option value="Blue">Blue</option>
                                                <option value="Green">Green</option>
                                                <option value="Yellow">Yellow</option>
                                                <option value="Orange">Orange</option>
                                                <option value="Brown">Brown</option>
                                                <option value="Gray">Gray</option>
                                                <option value="Silver">Silver</option>
                                                <option value="Gold">Gold</option>
                                                <option value="Cyan">Cyan</option>
                                                <option value="Purple">Purple</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" name="model" class="form-control" maxlength="64" placeholder="Vehicle Model" data-lpignore="true" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <select class="form-control" name="insurance_status" required>
                                                <option value="" disabled selected>Insurance Status</option>
                                                <option value="None">None</option>
                                                <option value="Valid">Valid</option>
                                                <option value="Invalid">Invalid</option>
                                                <option value="Expired">Expired</option>
                                                <option value="Fake">Fake</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <select class="form-control" name="registration_status" required>
                                                <option value="" disabled selected>Registration Status</option>
                                                <option value="None">None</option>
                                                <option value="Valid">Valid</option>
                                                <option value="Invalid">Invalid</option>
                                                <option value="Expired">Expired</option>
                                                <option value="Fake">Fake</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Complete">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Firearm Model -->
            <div class="modal fade" id="newFirearmModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New Firearm</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="createFirearm" action="inc/backend/user/civ/createFirearm.php" method="post">
                                <div class="form-group">
                                    <select class="form-control" id="weaponSelector" name="weapon" required>
                                        <option value="" disabled selected>Weapon...</option>
                                        <option value="AP Pistol">AP Pistol</option>
                                        <option value="Combat Pistol">Combat Pistol</option>
                                        <option value="Heavy Pistol">Heavy Pistol</option>
                                        <option value="Heavy Revolver">Heavy Revolver</option>
                                        <option value="Heavy Revolver Mk II">Heavy Revolver Mk II</option>
                                        <option value="Marksman Pistol">Marksman Pistol</option>
                                        <option value="Pistol">Pistol</option>
                                        <option value="Pistol Mk II">Pistol Mk II</option>
                                        <option value="Pistol .50">Pistol .50</option>
                                        <option value="SNS Pistol">SNS Pistol</option>
                                        <option value="SNS Pistol Mk II">SNS Pistol Mk II</option>
                                        <option value="Vintage Pistol">Vintage Pistol</option>
                                        <option value="Double-Action Revolver">Double-Action Revolver</option>
                                        <option value="Assault Shotgun">Assault Shotgun</option>
                                        <option value="Bullpup Shotgun">Bullpup Shotgun</option>
                                        <option value="Double Barrel Shotgun">Double Barrel Shotgun</option>
                                        <option value="Heavy Shotgun">Heavy Shotgun</option>
                                        <option value="Musket">Musket</option>
                                        <option value="Pump Shotgun">Pump Shotgun</option>
                                        <option value="Pump Shotgun Mk II">Pump Shotgun Mk II</option>
                                        <option value="Sawed-Off Shotgun">Sawed-Off Shotgun</option>
                                        <option value="Sweeper Shotgun">Sweeper Shotgun</option>
                                        <option value="Assault SMG">Assault SMG</option>
                                        <option value="Combat MG">Combat MG</option>
                                        <option value="Combat MG Mk II">Combat MG Mk II</option>
                                        <option value="Combat PDW">Combat PDW</option>
                                        <option value="Gusenberg Sweeper">Gusenberg Sweeper</option>
                                        <option value="Machine Pistol">Machine Pistol</option>
                                        <option value="MG">MG</option>
                                        <option value="Micro SMG">Micro SMG</option>
                                        <option value="Mini SMG">Mini SMG</option>
                                        <option value="SMG">SMG</option>
                                        <option value="SMG Mk II">SMG Mk II</option>
                                        <option value="Advanced Rifle">Advanced Rifle</option>
                                        <option value="Assault Rifle">Assault Rifle</option>
                                        <option value="Assault Rifle Mk II">Assault Rifle Mk II</option>
                                        <option value="Bullpup Rifle">Bullpup Rifle</option>
                                        <option value="Bullpup Rifle Mk II">Bullpup Rifle Mk II</option>
                                        <option value="Carbine Rifle">Carbine Rifle</option>
                                        <option value="Carbine Rifle Mk II">Carbine Rifle Mk II</option>
                                        <option value="Compact Rifle">Compact Rifle</option>
                                        <option value="Special Carbine">Special Carbine</option>
                                        <option value="Special Carbine Mk II">Special Carbine Mk II</option>
                                        <option value="Heavy Sniper">Heavy Sniper</option>
                                        <option value="Heavy Sniper Mk II">Heavy Sniper Mk II</option>
                                        <option value="Marksman Rifle">Marksman Rifle</option>
                                        <option value="Marksman Rifle Mk II">Marksman Rifle Mk II</option>
                                        <option value="Sniper Rifle">Sniper Rifle</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="rpstatus" required>
                                        <option value="" disabled selected>Status...</option>
                                        <option value="Valid">Valid</option>
                                        <option value="Stolen">Stolen</option>
                                        <option value="Blackmarket">Blackmarket</option>
                                    </select>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <input class="btn btn-primary" onClick="disableClick()" type="submit" value="Complete">
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php break; ?>
        <?php endswitch; ?>
    </div>
    </div>
    <!-- CONTENT END -->
    <?php include 'inc/copyright.php'; ?>
    <?php include 'inc/page-bottom.php'; ?>
    <!-- this community uses freeCAD. freeCAD is a free and open-source CAD/MDT system. Find our discord here: https://discord.gg/NeRrWZC -->
