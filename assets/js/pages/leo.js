$(document).ready(function () {
    $('.select2').select2({
        minimumInputLength: 3
    });
    $('#createIdentity').ajaxForm(function (error) {
        error = JSON.parse(error);
        if (error['msg'] === "") {
            $.ajax({
                url: 'inc/backend/user/leo/getUserIdentitys.php',
                success: function (data) {
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
    $('#newTicket').ajaxForm(function (error) {
        error = JSON.parse(error);
        if (error['msg'] === "") {
            $('#newTicketModal').modal('hide');
            toastr.success('Ticket Created!', 'System:', {
                timeOut: 10000
            })
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            })
        }
    });
    $('#newArrestReport').ajaxForm(function (error) {
        error = JSON.parse(error);
        if (error['msg'] === "") {
            $('#newArrestReportModal').modal('hide');
            toastr.success('Arrest Report Created!', 'System:', {
                timeOut: 10000
            })
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            })
        }
    });
    $('textarea').keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            this.value = this.value + "\n";
        }
    });

    $('#addWarrant').ajaxForm(function (error) {
        console.log(error);
        error = JSON.parse(error);
        if (error['msg'] === "") {
            $("#addWarrant")[0].reset();
            toastr.success('Warrant Added.', 'System:', {
                timeOut: 10000
            })
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            })
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

    $('#impoundVehicle').ajaxForm(function (error) {
        error = JSON.parse(error);
        if (error['msg'] === "") {
            $('#impoundManagerModel').modal('hide');
            toastr.success('Vehicle Sent To Impound Yard!', 'System:', {
                timeOut: 10000
            })
        } else {
            toastr.error(error['msg'], 'System:', {
                timeOut: 10000
            })
        }
    });
});

function noSessionAjax() {
    fetch('inc/backend/user/leo/getUserIdentitys.php').then(function(response) {
        response.text().then(function(text) {
            $('#listIdentitys').html(text);
        });
    });
    fetch('inc/backend/user/leo/getLeoDivisions.php').then(function(response) {
        response.text().then(function(text) {
            $('#listLeoDivisions').html(text);
        });
    });
}

function checkActiveDispatchers() {
    var source = new EventSource("inc/backend/user/leo/checkActiveDispatchers.php");
    source.onmessage = function (e) {
        if (e.data === "1") {
            var element = document.getElementById("checkDispatchers");
            element.innerHTML = '<div class="alert alert-dark" role="alert"><strong>Notice:</strong> No Dispatchers are currently online. You will be able to see all 911 Calls until a Dispatcher is active</div>';
            document.getElementById("getMyCalls").style.display = "none";
            document.getElementById("noDis911Calls").style.display = "block";
        } else {
            document.getElementById("getMyCalls").style.display = "block";
            document.getElementById("noDis911Calls").style.display = "none";
        }
    }
}

function getAllCharacters() {
    fetch('inc/backend/user/leo/getAllCharacters.php').then(function (response) {
        response.text().then(function (text) {
            $('#getAllCharacters').html(text);
            $('#getAllCharacters2').html(text);
            $('#getAllCharacters3').html(text);
            $('#getAllCharacters4').html(text);
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
    var alert_needed = getCookie("personWantedAlert");
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

function getPendingIds() {
    fetch('inc/backend/user/leo/getPendingIds.php').then(function (response) {
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
        url: "inc/backend/user/leo/approveID.php?id=" + i,
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
        url: "inc/backend/user/leo/rejectID.php?id=" + i,
        cache: false,
        success: function (result) {
            toastr.error('ID Rejected.', 'System:', {
                timeOut: 10000
            })
            getPendingIds();
        }
    });
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

function deleteWarrantLEO(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/leo/deleteWarrantLEO.php?id=" + i,
        cache: false,
        success: function (error) {
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                showName();
                toastr.success('Warrant Deleted', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        }
    });
}

function deleteBoloLEO(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/leo/deleteBoloLEO.php?id=" + i,
        cache: false,
        success: function (error) {
            var error = JSON.parse(error);
            if (error['msg'] === "") {
                showName();
                toastr.success('BOLO Deleted', 'System:', {
                    timeOut: 10000
                });
            } else {
                toastr.error(error['msg'], 'System:', {
                    timeOut: 10000
                });
            }
        }
    });
}

function getBolos() {
    fetch('inc/backend/user/leo/getBolos.php').then(function (response) {
        response.text().then(function (text) {
            $('#getBolos').html(text);
            setTimeout(getBolos, 5000);
        });
    });
}

function getMyCalls() {
    fetch('inc/backend/user/leo/getMyCalls.php').then(function (response) {
        response.text().then(function (text) {
            $('#getMyCalls').html(text);
            setTimeout(getMyCalls, 1000);
        });
    });
}

function noDis911Calls() {
    $.ajax({
        url: 'inc/backend/user/dispatch/get911Calls.php',
        success: function (data) {
            $('#noDis911Calls').html(data);
        },
        complete: function () {
            setTimeout(noDis911Calls, 1000);
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

function officerPanicBtn() {
    $.ajax({
        url: 'inc/backend/user/leo/officerPanicButton.php',
        success: function (data) {
            changeSignal();
            toastr.error('PANIC BUTTON PUSHED.')
        },
    });
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

function loadStatus() {
    var source = new EventSource("inc/backend/user/leo/getStatus.php");
    source.onmessage = function (e) {
        var element = document.getElementById("getDutyStatus");
        element.innerHTML = e.data;
        document.cookie = "curStat=" + e.data;
    }
}

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

function markCivDeceased(str) {
    var i = str.id;

    $.ajax({
        url: "inc/backend/user/leo/markCivDeceased.php?character=" + i,
        cache: false,
        success: function (result) {
            toastr.info('Character marked as deceased - Changes will take effect in a moment.', 'System:', {
                timeOut: 10000
            })
        }
    });
}
