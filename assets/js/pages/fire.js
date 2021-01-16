$(document).ready(function() {
    function getUserIdentitys() {
        fetch('inc/backend/user/fire/getUserIdentitys.php').then(function(response) {
            response.text().then(function(text) {
                $('#listIdentitys').html(text);
            });
        });
    }

    getUserIdentitys();

    $('#createIdentity').ajaxForm(function(error) {
        error = JSON.parse(error);
        if (error['msg'] === "") {
            $.ajax({
                url: 'inc/backend/user/fire/getUserIdentitys.php',
                success: function(data) {
                    $('#listIdentitys').html(data);
                }
            });
            toastr.success('Identity Created! You can now select it.', 'System:', {
                timeOut: 10000
            })
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            })
        }
    });

    $('textarea').keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            this.value = this.value + "\n";
        }
    });

    $('.select2').select2({
        minimumInputLength: 3
    });
});

function checkActiveDispatchers() {
  var source = new EventSource("inc/backend/user/leo/checkActiveDispatchers.php");
  source.onmessage = function(e) {
     if (e.data === "1") {
       var element = document.getElementById("checkDispatchers");
       element.innerHTML = '<div class="alert alert-info" role="alert"><strong>Notice:</strong> No Dispatchers are currently online.</div>';
     } else {
       document.getElementById("checkDispatchers").style.display= "none";
     }
  }
}
checkActiveDispatchers();

function setUnitStatus(selectObject) {
    var i = selectObject.value;
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            //hmmmzz
        }
    };
    xmlhttp.open("GET", "inc/backend/user/fire/setStatus.php?status=" + i, true);
    xmlhttp.send();
    if (i === "Off-Duty") {
      window.location.replace("fire.php?v=nosession");
    }
    toastr.success('Status Updated', 'System');
}

function setFireDivision(selectObject) {
    var i = selectObject.value;
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            //hmmmzz
        }
    };
    xmlhttp.open("GET", "inc/backend/user/fire/setFireDivision.php?div=" + i, true);
    xmlhttp.send();
    toastr.success('Division Updated', 'System');
}

function updateNotepad(str) {
    if (str == "") {
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //hmmm
            }
        };
        xmlhttp.open("GET", "inc/backend/user/leo/updateNotepad.php?txt=" + str, true);
        xmlhttp.send();
    }
}

function getPendingIds() {
    fetch('inc/backend/user/fire/getPendingIds.php').then(function (response) {
        response.text().then(function (text) {
            $('#getPendingIds').html(text);
            setTimeout(getPendingIds, 15000);
        });
    });
}
getPendingIds();

function approveID(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/fire/approveID.php?id=" + i,
        cache: false,
        success: function (result) {
            toastr.success('ID Approved.', 'System:', {
                timeOut: 10000
            })
            getPendingIds();
        }
    });
}

function rejectID(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/fire/rejectID.php?id=" + i,
        cache: false,
        success: function (result) {
            toastr.error('ID Rejected.', 'System:', {
                timeOut: 10000
            })
            getPendingIds();
        }
    });
}

function getMyCalls() {
  fetch('inc/backend/user/fire/getMyCalls.php').then(function (response) {
    response.text().then(function (text) {
        $('#getMyCalls').html(text);
        setTimeout(getMyCalls, 1000);
    });
});
}
getMyCalls();

function getAttchedUnits() {
    fetch('inc/backend/user/dispatch/getAttchedUnits.php').then(function (response) {
        response.text().then(function (text) {
            $('#getAttchedUnits').html(text);
            setTimeout(getAttchedUnits, 1000);
        });
    });
}
getAttchedUnits();

function clear911Call() {
    toastr.warning('Please Wait...')
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            $('#callInfoModal').modal('hide');
            toastr.success('Call Archived.')
        }
    };
    xmlhttp.open("GET", "inc/backend/user/dispatch/archiveCall.php", true);
    xmlhttp.send();
}

function checkTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    // add a zero in front of numbers<10
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('getTime').innerHTML = h + ":" + m + ":" + s;
    t = setTimeout(function() {
        startTime()
    }, 500);
}

function getFireInfo() {
    (function loadStatus() {
        var source = new EventSource("inc/backend/user/fire/getStatus.php");
        source.onmessage = function(e) {
           var element = document.getElementById("getDutyStatus");
           element.innerHTML = e.data;

           if (e.data === "Off-Duty") {
             document.getElementById("divisionSetter").style.display= "block";
             document.getElementById("statusSetter").style.display= "none";
             document.getElementById("getMyCalls").style.display= "none";
           } else {
             document.getElementById("statusSetter").style.display= "block";
             document.getElementById("getMyCalls").style.display= "block";
             document.getElementById("divisionSetter").style.display= "none";
           }
        }
    })();
    (function loadSig100Status() {
        var source = new EventSource("inc/backend/user/leo/checkSignal100.php");
        source.onmessage = function(e) {
           if (e.data === "1") {
             $('#signal100Status').html("<font color='red'><b> - SIGNAL 100 IS IN EFFECT</b></font>");
             if (!signal100) {
                   var audio = new Audio('assets/sounds/signal100.mp3');
                   audio.play();
                   setTimeout(() => {
                       var msg = new SpeechSynthesisUtterance('ALL UNITS HOLD TRAFFIC - SIGNAL 100 ACTIVATED - STAND BY FOR DETAILS.');
                       var voices = window.speechSynthesis.getVoices();
                       window.speechSynthesis.speak(msg);
                   }, 3000);
               }
               signal100 = true;
           } else {
             $('#signal100Status').html("");
             signal100 = false;
           }
        }
    })();
}
