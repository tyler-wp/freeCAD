$(document).ready(function () {
    $('.select2').select2({
        minimumInputLength: 3
    });
    $('.select2multi').select2();
    $('.select2_assignUnit').select2();


    $('textarea').keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            this.value = this.value + "\n";
        }
    });
    $('#createIdentity').ajaxForm(function (error) {
        error = JSON.parse(error);
        if (error['msg'] === "") {
            fetch('inc/backend/user/dispatch/getUserIdentitys.php').then(function (response) {
                response.text().then(function (text) {
                    $('#listIdentitys').html(text);
                });
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

    $('#new911call').ajaxForm(function (error) {
        console.log(error);
        var error = JSON.parse(error);
        if (error['msg'] === "") {
            $("#new911call")[0].reset();
            $('#new911callModal').modal('hide');
            toastr.success('Call Added', 'System:', {
                timeOut: 10000
            });
        } else if (error['msg'] === "allCall") {
            $("#new911call")[0].reset();
            $('#new911callModal').modal('hide');
            changeSignal();
            toastr.success('Call Added', 'System:', {
                timeOut: 10000
            });
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            });
        }
    });

    $('#newq911call').ajaxForm(function (error) {
        console.log(error);
        var error = JSON.parse(error);
        if (error['msg'] === "") {
            $("#newq911call")[0].reset();
            $('#newq911callModal').modal('hide');
            toastr.success('Call Added', 'System:', {
                timeOut: 10000
            });
        } else if (error['msg'] === "allCall") {
            $("#newq911call")[0].reset();
            $('#newq911callModal').modal('hide');
            changeSignal();
            toastr.success('Call Added', 'System:', {
                timeOut: 10000
            });
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            });
        }
    });

    $(document).bind('keydown', function (event) {
        if (event.which === 45) {
            $('#newq911callModal').modal('show');

            setTimeout(function () {
                $('#quickcalltext').focus();
            }, 400);
        }
    });

    $('#newBolo').ajaxForm(function (error) {
        console.log(error);
        var error = JSON.parse(error);
        if (error['msg'] === "") {
            $("#newBolo")[0].reset();
            $('#newBoloModal').modal('hide');
            toastr.success('BOLO Added', 'System:', {
                timeOut: 10000
            });
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            });
        }
    });


});

function getLeoInfo() {
    (function loadSig100Status() {
        var source = new EventSource("inc/backend/user/leo/checkSignal100.php");
        source.onmessage = function (e) {
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
function getDispatcherStatus() {
    var source = new EventSource("inc/backend/user/dispatch/getStatus.php");
    source.onmessage = function (e) {
        if (e.data === "Off-Duty") {
            document.getElementById("dispatchOnDuty").style.display = "none";
            document.getElementById("dispatchOnDuty2").style.display = "none";
            document.getElementById("dispatchOffDuty").style.display = "inline";
        } else if (e.data === "On-Duty") {
            document.getElementById("dispatchOnDuty").style.display = "inline";
            document.getElementById("dispatchOnDuty2").style.display = "inline";
            document.getElementById("dispatchOffDuty").style.display = "none";
        }
    }
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
    t = setTimeout(function () {
        startTime()
    }, 500);
}

function getUserIdentitys() {
    fetch('inc/backend/user/dispatch/getUserIdentitys.php').then(function (response) {
        response.text().then(function (text) {
            $('#listIdentitys').html(text);
        });
    });
}

function getAllCharacters() {
    fetch('inc/backend/user/leo/getAllCharacters.php').then(function (response) {
        response.text().then(function (text) {
            $('#getAllCharacters').html(text);
            setTimeout(getAllCharacters, 60000);
        });
    });
}

function getAllVehicles() {
    fetch('inc/backend/user/leo/getAllVehicles.php').then(function (response) {
        response.text().then(function (text) {
            $('#getAllVehicles').html(text);
            setTimeout(getAllVehicles, 60000);
        });
    });
}

function getAllFirearms() {
    fetch('inc/backend/user/leo/getAllFirearms.php').then(function (response) {
        response.text().then(function (text) {
            $('#getAllFirearms').html(text);
            setTimeout(getAllFirearms, 60000);
        });
    });
}

function setUnitStatus(selectObject) {
    var i = selectObject.value;
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            //hmmmzz
        }
    };
    xmlhttp.open("GET", "inc/backend/user/leo/setStatus.php?status=" + i, true);
    xmlhttp.send();
    toastr.success('Status Updated', 'System');
}

function showName(str) {
    if (str == "") {
        document.getElementById("showPersonInfo").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("showPersonInfo").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "inc/backend/user/leo/searchNameDB.php?id=" + str, true);
        xmlhttp.send();

    }
}

function showVehicle(str) {
    if (str == "") {
        document.getElementById("showVehicleInfo").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("showVehicleInfo").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "inc/backend/user/leo/searchVehicleDB.php?id=" + str, true);
        xmlhttp.send();
    }
}

function showFirearm(str) {
    if (str == "") {
        document.getElementById("showFirearmInfo").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("showFirearmInfo").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "inc/backend/user/leo/searchWeaponDB.php?id=" + str, true);
        xmlhttp.send();
    }
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
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                //hmmm
            }
        };
        xmlhttp.open("GET", "inc/backend/user/leo/updateNotepad.php?txt=" + str, true);
        xmlhttp.send();
    }
}

function suspendDriversLicense(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/leo/suspendDriversLicense.php?character=" + i,
        cache: false,
        success: function (result) {
            toastr.info('Drivers License Suspended - Changes will take effect in a moment.', 'System:', {
                timeOut: 10000
            })
        }
    });
}

function suspendFirearmsLicense(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/leo/suspendFirearmsLicense.php?character=" + i,
        cache: false,
        success: function (result) {
            toastr.info('Firearms License Suspended - Changes will take effect in a moment.', 'System:', {
                timeOut: 10000
            })
        }
    });
}

var isFocusedDispatch = false;
function getActiveUnits() {
    $.ajax({
        url: 'inc/backend/user/dispatch/getActiveUnits.php',
        success: function (data) {
            $(document).ajaxComplete(function () {
                $('.select-units').focus(function () {
                    isFocusedDispatch = true;
                });
                $('.select-units').blur(function () {
                    isFocusedDispatch = false;
                });
            });
            if (!isFocusedDispatch) {
                $('#getActiveUnits').html(data);
            }
        },
        complete: function () {
            setTimeout(getActiveUnits, 1000);
        }
    });
}
getActiveUnits();


function get911Calls() {
    fetch('inc/backend/user/dispatch/get911Calls.php').then(function (response) {
        response.text().then(function (text) {
            $('#get911Calls').html(text);
            setTimeout(get911Calls, 1000);
        });
    });
}

function updateUnitStatus(selectObject) {
    var i = selectObject.id;
    var str = selectObject.value;
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            //hmmmzz
        }
    };
    xmlhttp.open("GET", "inc/backend/user/dispatch/updateUnitStatus.php?unit=" + i + "&status=" + str, true);
    xmlhttp.send();
    // alert(str + " " + uid);
    $(".select-units").blur();
    isFocused = false;
}

function getAllActiveUnitsForCall() {
    fetch('inc/backend/user/dispatch/getAllActiveUnitsForCall.php?opt=1').then(function (response) {
        response.text().then(function (text) {
            $('#getAllActiveUnitsForCall').html(text);
            setTimeout(getAllActiveUnitsForCall, 2000);
        });
    });
}

function getAllActiveUnitsForNewCall() {
    $.ajax({
        url: 'inc/backend/user/dispatch/getAllActiveUnitsForCall.php?opt=2',
        success: function (data) {
            $('#attachUnits').html(data);
        },
        complete: function () {
            if ('#attachUnits' === "") {
                setTimeout(getAllActiveUnitsForNewCall, 2000);
            } else {
                setTimeout(getAllActiveUnitsForNewCall, 60000);
            }
        }
    });
}

function getAttchedUnits() {
    fetch('inc/backend/user/dispatch/getAttchedUnits.php').then(function (response) {
        response.text().then(function (text) {
            $('#getAttchedUnits').html(text);
            setTimeout(getAttchedUnits, 1000);
        });
    });
}

function assignUnit(str) {
    toastr.warning('Please Wait...')
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
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                toastr.success('Unit Assigned To Call.')
            }
        };
        xmlhttp.open("GET", "inc/backend/user/dispatch/assignUnit.php?unit=" + str, true);
        xmlhttp.send();
        getAttchedUnits();
    }
}

function unassignUnit(str) {
    toastr.warning('Please Wait...')
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
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                toastr.success('Unit Detached From Call.')
            }
        };
        xmlhttp.open("GET", "inc/backend/user/dispatch/unassignUnit.php?unit=" + str, true);
        xmlhttp.send();
        getAttchedUnits();
    }
}

function clear911Call() {
    toastr.warning('Please Wait...')
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $('#callInfoModal').modal('hide');
            toastr.success('Call Archived.')
        }
    };
    xmlhttp.open("GET", "inc/backend/user/dispatch/archiveCall.php", true);
    xmlhttp.send();
}

function getBolos() {
    fetch('inc/backend/user/leo/getBolos.php').then(function (response) {
        response.text().then(function (text) {
            $('#getBolos').html(text);
            setTimeout(getBolos, 5000);
        });
    });
}

function clearBOLO() {
    toastr.warning('Please Wait...')
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $('#boloInfoModal').modal('hide');
            toastr.success('BOLO Cleared.')
        }
    };
    xmlhttp.open("GET", "inc/backend/user/dispatch/clearBolo.php", true);
    xmlhttp.send();
}

function getPendingIds() {
    fetch('inc/backend/user/dispatch/getPendingIds.php').then(function (response) {
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
        url: "inc/backend/user/dispatch/approveID.php?id=" + i,
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
        url: "inc/backend/user/dispatch/rejectID.php?id=" + i,
        cache: false,
        success: function (result) {
            toastr.error('ID Rejected.', 'System:', {
                timeOut: 10000
            })
            getPendingIds();
        }
    });
}

function updateCallStatus(str) {
    var i = str.value;
    toastr.warning('Please Wait...')
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
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                toastr.success('Unit Assigned To Call.')
            }
        };
        xmlhttp.open("GET", "inc/backend/user/dispatch/updateCallStatus.php?newStatus=" + i, true);
        xmlhttp.send();
    }
}

function dispatchGoOnDuty() {
    $.ajax({
        url: "inc/backend/user/dispatch/setStatus.php?status=onduty",
        cache: false,
        success: function (result) {
            toastr.success('Going on duty...', 'System:', {
                timeOut: 10000
            })
        }
    });
}

function dispatchGoOffDuty() {
    $.ajax({
        url: "inc/backend/user/dispatch/setStatus.php?status=offduty",
        cache: false,
        success: function (result) {
            toastr.error('Going Off duty...', 'System:', {
                timeOut: 10000
            })
        }
    });
}
