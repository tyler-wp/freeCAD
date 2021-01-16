<?php
// MySQL Injection Prevention
function escapestring($value)
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        die('Could not connect: ' . $conn->connect_error);
    }
    return strip_tags(mysqli_real_escape_string($conn, $value));
}
// Insert into Database
function dbquery($sql, $returnresult = true)
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        error_log('MySQL could not connect: ' . $conn->connect_error);
        return $conn->connect_error;
    }
    $return = array();
    $result = mysqli_query($conn, $sql);
    if ($returnresult) {
        if (mysqli_num_rows($result) != 0) {
            while ($r = $result->fetch_assoc()) {
                array_push($return, $r);
            }
        } else {
            $return = array();
        }
    } else {
        $return = array();
    }
    return $return;
}

// Throw Visual Error (Only works after Header is loaded)
function throwError($error, $log = false) {
    // Load Toastr JavaScript and CSS
    echo '
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script type="text/javascript">
            if(window.toastr != undefined) {
                if (typeof jQuery == "undefined") {
                    alert("Error Handler: ' . $error . '")
                } else {
                    toastr.error("' . $error . '")
                }
            } else {
                alert("Error Handler: ' . $error . '")
            }
        </script>
    ';
}

// Throw Notification (Only works after Header is loaded)
function clientNotify($type, $error) {
    echo '
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script type="text/javascript">
            if(window.toastr != undefined) {
                if (typeof jQuery == "undefined") {
                    alert("System: ' . $error . '")
                } else {
                    toastr.' . $type . '("' . $error . '")
                }
            } else {
                alert("System: ' . $error . '")
            }
        </script>
    ';
}

function discordAlert($message) {
    global $discord_webhook;
    //=======================================================================
    // Create new webhook in your Discord channel settings and copy&paste URL
    //=======================================================================
    $webhookurl = $discord_webhook;
    //=======================================================================
    // Compose message. You can use Markdown
    //=======================================================================
    $json_data = array(
        'content' => "$message"
    );
    $make_json = json_encode($json_data);
    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    return $response;
}

function hydridErrors($errno, $errstr, $errfile, $errline, $errcontext) {
    global $debug;
    if ($debug) {
        echo "
        Error Information: <br><hr>
        Page: <b>" . $_SERVER['REQUEST_URI'] . "</b><br>
        Error: <b> $errstr </b><br>
        Broken File: <b> $errfile </b><br>
        Line: <b> $errline </b><br>
        <hr>
        If you are the website owner, please report this error to freeCAD Staff.<br>
        If you are not the owner, please try again later!
        ";
        die();
    }
    else {
        echo "
        Error Information is hidden because the Community Owner has disabled debug.
        <hr>
        If you are the website owner, please report this error to freeCAD Staff.<br>
        If you are not the owner, please try again later!
        ";
        die();
    }
}
set_error_handler("hydridErrors");

// Log Function
function logAction($action, $user) {
    global $pdo;
    global $time;
    global $us_date;

    $sql_log = "INSERT INTO logs (action, username, timestamp) VALUES (:action, :username, :timestamp)";
    $stmt_log = $pdo->prepare($sql_log);
    $stmt_log->bindValue(':action', $action);
    $stmt_log->bindValue(':username', $user);
    $stmt_log->bindValue(':timestamp', $us_date . ' ' . $time);
    $result_log = $stmt_log->execute();
}

function shiftLog($id_id) {
    global $pdo;
    global $time;
    global $us_date;

    $sql_shiftLog = "INSERT INTO shift_logs (i_id, s_start) VALUES (:i_id, :s_start)";
    $stmt_shiftLog = $pdo->prepare($sql_shiftLog);
    $stmt_shiftLog->bindValue(':i_id', $id_id);
    $stmt_shiftLog->bindValue(':s_start', $us_date . ' ' . $time);
    $result_shiftLog = $stmt_shiftLog->execute();
}

// Log Function For 911 Calls
function log911Action($action) {
    global $pdo;
    global $time;
    global $us_date;
    global $user_id;

    $sql_callLogger = "INSERT INTO 911call_log (call_id, user_id, dispatcher, action, timestamp) VALUES (?,?,?,?,?)";
    $stmt_callLogger = $pdo->prepare($sql_callLogger);
    $result_callLogger = $stmt_callLogger->execute([$_SESSION['viewingCallID'], $user_id, $_SESSION['identity_name'], $action, $us_date . ' ' . $time]);
}

// function getAddons () {
//   global $doul;
//   $json = file_get_contents('https://hydrid.us/internal/addons.php?domain='.$doul.'');
//   $addons = json_decode($json);
//   foreach ($addons as $addon) {
//     echo '
//     <tr>
//       <td>'.$addon->name.'</td>
//       <td width="40%">'.$addon->desc.'</td>
//       <td><strong><font color="darkred">Not Installed</font></strong></td>
//       <td width="20%"><a href="#" class="btn btn-sm btn-success">Install</a> <a href="#" class="btn btn-sm btn-danger">Uninstall</a> <a href="#" class="btn btn-sm btn-info">Settings</a></td>
//     </tr>
//     ';
//   }
// }

function vCheck () {
  global $version;
  if(!isset($_COOKIE['freecad'])) {
    $json = file_get_contents("https://raw.githubusercontent.com/HydridSystems/freeCAD/master/version.json");
    $curVer = json_decode($json);
    $newVer = $curVer->version;
    if ($newVer > $version) {
    //   setcookie("freecad", 'set', time()+1800);
      phpAlert("freeCAD is outdated. Every update adds new features, and optimizations. Support is NOT provided for outdated versions. You can find the new version on our Discord.");
    } else {
    //   setcookie("freecad", 'set', time()+21600);
    }
    // die($newVer .'+'. $version);
  }
}

function truncate_string($string, $maxlength, $extension) {

    // Set the replacement for the "string break" in the wordwrap function
    $cutmarker = "**cut_here**";

    // Checking if the given string is longer than $maxlength
    if (strlen($string) > $maxlength) {

        // Using wordwrap() to set the cutmarker
        // NOTE: wordwrap (PHP 4 >= 4.0.2, PHP 5)
        $string = wordwrap($string, $maxlength, $cutmarker);

        // Exploding the string at the cutmarker, set by wordwrap()
        $string = explode($cutmarker, $string);

        // Adding $extension to the first value of the array $string, returned by explode()
        $string = $string[0] . $extension;
    }

    // returning $string
    return $string;

}

function str_replacer($filename, $string_to_replace, $replace_with){
    $content=file_get_contents($filename);
    $content_chunks=explode($string_to_replace, $content);
    $content=implode($replace_with, $content_chunks);
    file_put_contents($filename, $content);
}

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}

function tylerdator () {
  // global $doul;
  // if (file_exists("license/key.txt")) {
  //   if ('' == file_get_contents("license/key.txt")) {
  //     die('Your license key has not been entered. Please enter it in <strong>License/key.txt</strong> - If you need a license key, you can find out how to get one on our <a href="https://discord.io/HydridSystems">Discord</a>');
  //   } else {
  //     if(!isset($_COOKIE['freecad'])) {
  //       $key = file_get_contents("license/key.txt");
  //       $che = json_decode(file_get_contents("http://freecad.us/freecadi/checkL.php?domain=".$doul."&key=".$key));
  //     	if ($che === "0") {
  //     	  unset($_COOKIE['freecad']);
  //     	  die('This license key is not valid. Please contact <a href="https://discord.io/HydridSystems">Hydrid Systems</a> Staff for further info.');
  //     	} elseif ($che === "1") {
  //         setcookie("freecad", 'set', time()+3600);
  //       } elseif ($che === "2") {
  //         unset($_COOKIE['freecad']);
  //         die('This license has been suspended. Please contact <a href="https://discord.io/HydridSystems">Hydrid Systems</a> Staff for further info.');
  //       } elseif ($che === "3") {
  //         unset($_COOKIE['freecad']);
  //         die('This license is not linked to this domain. Please contact <a href="https://discord.io/HydridSystems">Hydrid Systems</a> Staff for further info.');
  //       } elseif ($che === "4") {
  //         setcookie("freecad", 'set', time()+3600);
  //       }
  //     }
  //   }
  // } else {
  //   die('We could not locate your license file.');
  // }
}

function civLoad () {
  global $pdo;
  global $user_id;
  if (isset($_GET['v']) && strip_tags($_GET['v']) === 'setsession') {
      if (isset($_GET['id']) && strip_tags($_GET['id'])) {
          $id   = strip_tags($_GET['id']);
          $sql  = "SELECT * FROM characters WHERE character_id = :character_id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindValue(':character_id', $id);
          $stmt->execute();
          $characterDB = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($characterDB === false) {
              header('Location: ' . $url['civilian'] . '?v=nosession&error=character-not-found');
              exit();
          } else {
              $character_id                          = $characterDB['character_id'];
              $_SESSION['character_id']              = $character_id;
              $character_first_name                  = $characterDB['first_name'];
              $_SESSION['character_first_name']      = $character_first_name;
              $character_last_name                   = $characterDB['last_name'];
              $_SESSION['character_last_name']       = $character_last_name;
              $character_dob                         = $characterDB['date_of_birth'];
              $_SESSION['character_dob']             = $character_dob;
              $character_address                     = $characterDB['address'];
              $_SESSION['character_address']         = $character_address;
              $character_height                      = $characterDB['height'];
              $_SESSION['character_height']          = $character_height;
              $character_eye_color                   = $characterDB['eye_color'];
              $_SESSION['character_eye_color']       = $character_eye_color;
              $character_hair_color                  = $characterDB['hair_color'];
              $_SESSION['character_hair_color']      = $character_hair_color;
              $character_sex                         = $characterDB['sex'];
              $_SESSION['character_sex']             = $character_sex;
              $character_race                        = $characterDB['race'];
              $_SESSION['character_race']            = $character_race;
              $character_weight                      = $characterDB['weight'];
              $_SESSION['character_weight']          = $character_weight;
              $character_owner_id                    = $characterDB['owner_id'];
              $_SESSION['character_owner_id']        = $character_owner_id;
              $character_status                      = $characterDB['status'];
              $_SESSION['character_status']          = $character_status;
              $character_license_driver              = $characterDB['license_driver'];
              $_SESSION['character_license_driver']  = $character_license_driver;
              $character_license_firearm             = $characterDB['license_firearm'];
              $_SESSION['character_license_firearm'] = $character_license_firearm;
              $_SESSION['character_full_name']       = $character_first_name . ' ' . $character_last_name;
              if ($character_owner_id !== $user_id) {
                  echo '<script type="text/javascript">
                  window.location.href = "civilian.php?v=nosession&error=character-owner";
                  </script>';
                  exit();
              }

              echo '<script type="text/javascript">
              window.location.href = "civilian.php?v=main";
              </script>';
              exit();
          }
      }
  }
}

function time_php2sql($unixtime){
    return gmdate("Y-m-d H:i:s", $unixtime);
}
?>
