var selectedVehicleIndex = "";

// the cancel button of the Add Vehicle form is initially unclicked
var cancelButtonClicked = 0;
// and set its value to clicked
function doCancel()
{
    cancelButtonClicked = 1;
}


function setSelectedVehicleId()
{
    console.log ( "entering setSelectedVehicleId." );
    vehicleSelectOption = document.getElementById ( "vehicleSelector" );
    for ( var i = 0; i < vehicleSelectOption.options.length; ++i ) {
        if ( vehicleSelectOption[i].selected == true ) {
            tempArray = vehicleSelectOption[i].innerHTML.split(" ");
            selectedVehicleIndex = tempArray[0];
        }
    }
    console.log ( "setSelectedVehicleId-- set selected vehicle to " +
            selectedVehicleIndex );
}

function updateVehicleIdRequestParm()
{
    setSelectedVehicleId();

    node = document.getElementById ( "addFillupRecord" );
    node.outerHTML = "<a href=\"?action=addFillupRecord&amp;vehicleId=" + selectedVehicleIndex + "\">Add Fillup Record</a>";

    node = document.getElementById ( "addServiceRecord" );
    node.outerHTML = "<a href=\"?action=addServiceRecord&amp;vehicleId=" + selectedVehicleIndex + "\">Add Service Record</a>";

    node = document.getElementById ( "addExpenseRecord" );
    node.outerHTML = "<a href=\"?action=addExpenseRecord&amp;vehicleId=" + selectedVehicleIndex + "\">Add Expense Record</a>";

    node = document.getElementById ( "addTripRecord" );
    node.outerHTML = "<a href=\"?action=addTripRecord&amp;vehicleId=" + selectedVehicleIndex + "\">Add Trip Record</a>";

    node = document.getElementById ( "browseRecords" );
    node.outerHTML = "<a href=\"?action=browseRecords&amp;vehicleId=" + selectedVehicleIndex + "\">Browse Records</a>";

    node = document.getElementById ( "viewStats" );
    node.outerHTML = "<a href=\"?action=viewStats&amp;vehicleId=" + selectedVehicleIndex + "\">Stats</a>";

    node = document.getElementById ( "viewCharts" );
    node.outerHTML = "<a href=\"?action=viewCharts&amp;vehicleId=" + selectedVehicleIndex + "\">Charts</a>";
}

function getSelectedVehicleId()
{
    return selectedVehicleIndex;
}

// Given a string like "2012-02-27", returns a Date object representing the
// date. False is returned on error.
function createDate ( dateStr )
{
    var dateRe = /^\d{4}-\d{2}-\d{2}$/;
    if ( dateStr.match ( dateRe ) == null ) {
        return false;
    }
    var firstDash = dateStr.indexOf ( '-' );
    var lastDash = dateStr.lastIndexOf ( '-' );

    var year = parseInt ( dateStr.slice(0, firstDash), 10 );
    var month = parseInt ( dateStr.slice(firstDash+1, lastDash), 10 );
    if ( month <= 0 || month > 12 ) {
        return false;
    }
    var day = parseInt ( dateStr.slice(lastDash+1), 10 );
    if ( day <= 0 ) {
        return false;
    }

    switch(month) {
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            if ( day > 31 ) {
                return false;
            }
            break;

        case 4:
        case 6:
        case 9:
        case 11:
            if ( day > 30 ) {
                return false;
            }
            break;

        default: // month = 2 (February)
            if ( year % 4 == 0 &&
                    ( year % 100 != 0 || year % 400 == 0 ) ) {
                if ( day > 29 ) {
                    return false;
                }
            }
            else {
                if ( day > 28 ) {
                    return false;
                }
            }
    }

    return Date ( year, month, day );
}

function validateDate ( dateStr )
{
    var dateRe = /^\d{4}-\d{2}-\d{2}$/;
    if ( dateStr.match ( dateRe ) == null ) {
        return false;
    }
    var firstDash = dateStr.indexOf ( '-' );
    var lastDash = dateStr.lastIndexOf ( '-' );
    var yearStr = dateStr.slice ( 0, firstDash );
    var monthStr = dateStr.slice ( firstDash + 1, lastDash );
    var dayStr = dateStr.slice ( lastDash + 1 );
    console.log("string y = " + yearStr + ", m = " + monthStr + ", d = " +
            dayStr );

    var year = parseInt ( yearStr, 10 );
    console.log("year = " + year );
    var month = parseInt ( monthStr, 10 );
    console.log("month = " + month );
    if ( month <= 0 || month > 12 ) {
        console.log("month match failure.");
        return false;
    }
    var day = parseInt ( dayStr, 10 );
    console.log("day = " + day );
    if ( day <= 0 ) {
        console.log("day match failure.");
        return false;
    }

    if ( month == 1 || month == 3 || month == 5 || month == 7 || month == 8 ||
            month == 10 || month == 12 ) {
        if ( day > 31 ) {
            console.log("greater-than-31-failure.");
            return false;
        }
    }
    else if ( month == 4 || month == 6 || month == 9 || month == 11 ) {
        if ( day > 30 ) {
            console.log("greater-than-30-failure.");
            return false;
         }
    }
    else {
        console.log("rem400 = " + (year%400) + ", rem4 = " + (year%4) );
        if ( year%4 == 0 && ( year%100 != 0 || year%400 == 0 ) ) {
            if ( day > 29 ) {
                console.log("greater-than-29-failure.");
                return false;
            }
        }
        else {
            if ( day > 28 ) {
                console.log("greater-than-28-failure.");
                return false;
            }
        }
    }

    return true;
}

function isLatestDate ( curDate, lastDate )
{
    console.log("isLatestDate: cur = " + curDate + ", last = " + lastDate );
    var firstDash = curDate.indexOf ( '-' );
    var lastDash = curDate.lastIndexOf ( '-' );
    var curYear = parseInt ( curDate.slice ( 0, firstDash ), 10 );
    var curMonth = parseInt ( curDate.slice ( firstDash + 1, lastDash ), 10 );
    var curDay = parseInt ( curDate.slice ( lastDash + 1 ), 10 );

    firstDash = lastDate.indexOf ( '-' );
    lastDash = lastDate.lastIndexOf ( '-' );
    var lastYear = parseInt ( lastDate.slice ( 0, firstDash ), 10 );
    var lastMonth = parseInt ( lastDate.slice ( firstDash + 1, lastDash ), 10 );
    var lastDay = parseInt ( lastDate.slice ( lastDash + 1 ), 10 );

    date1 = new Date ( curYear, curMonth, curDay );
    date2 = new Date ( lastYear, lastMonth, lastDay );
    if ( (date1.getTime() - date2.getTime()) <= 0 ) {
        return false;
    }

    return true;
}

function ValidateAddTripRecordForm ( theForm, lastOdoDate, lastOdoReading )
{
    if ( cancelButtonClicked == 1 ) {
        return true;
    }

    if ( theForm.entryDate.value.length == 0 ) {
        inlineMsg ( "entryDate", "Please enter the entry date.", 2 );
        return false;
    }
    
    var entryDateStr = theForm.entryDate.value;
    var curdate = createDate ( theForm.entryDate.value );
    if ( validateDate ( entryDateStr ) == false ) {
        inlineMsg ( "entryDate", "Please fill a valid entry date.", 2 );
        return false;
    }
    if ( isLatestDate ( entryDateStr, lastOdoDate ) == false ) {
        inlineMsg ( "entryDate", "Entry date can't be <= the last entry date.",
                2 );
        return false;
    }

    if ( theForm.entryOdo.value.length == 0 ) {
        inlineMsg ( "entryOdo", "Please enter the odo reading.", 2 );
        return false;
    }

    return true;
}

function validateNewVehicleForm ( theForm )
{
    if ( cancelButtonClicked == 1 ) {
        // if we were invoked via the cancel button, just return true and submit
        return true;
    }

    if ( theForm.vehicle_make.value.length == 0 ) {
        inlineMsg ( "vehicle_make", "Please enter your vehicle make.", 2 );
        return false;
    }

    if ( theForm.vehicle_model.value.length == 0 ) {
        inlineMsg ( "vehicle_model", "Please enter your vehicle model.", 2 );
        return false;
    }

    if ( theForm.vehicle_license_number.value.length == 0 ) {
        inlineMsg ( "vehicle_make", "Please enter your vehicle registration number.", 2 );
        return false;
    }

    return true;
}

var MSGTIMER = 20;
var MSGSPEED = 5;
var MSGOFFSET = 3;
var MSGHIDE = 3;

function inlineMsg ( target, message, autohide )
{
    var msg;
    var msgContent;
    if ( ! document.getElementById('msg') ) {
        msg = document.createElement('div');
        msg.id = 'msg';
        msgContent = document.createElement('div');
        msgContent.id = 'msgContent';
        document.body.appendChild ( msg );
        msg.appendChild ( msgContent );
        msg.style.filter = 'alpha(opacity=0)';
        msg.style.opacity = 0;
        msg.alpha = 0;
    }
    else {
        msg = document.getElementById('msg');
        msgContent = document.getElementById('msgContent');
    }

    msgContent.innerHTML = message;
    msg.style.display = 'block';
    var msgHeight = msg.offsetHeight;
    var targetDiv = document.getElementById(target);
    targetDiv.focus();
    var targetHeight = targetDiv.offsetHeight;
    var targetWidth = targetDiv.offsetWidth;
    var topposition = topPosition(targetDiv) - ( (msgHeight - targetHeight) / 2 );
    var leftposition = leftPosition(targetDiv) + targetWidth + MSGOFFSET;
    msg.style.top = topposition + 'px';
    msg.style.left = leftposition + 'px';
    clearInterval ( msg.timer );
    msg.timer = setInterval ( "fadeMsg(1)", MSGTIMER );
    if ( ! autohide ) {
     autohide = MSGHIDE;
    }
    window.setTimeout ( "hideMsg()", ( autohide * 1000 ) );
}

// hide the form alert //
function hideMsg ( msg ) {
    var msg = document.getElementById ( 'msg' );
    if ( ! msg.timer ) {
     msg.timer = setInterval ( "fadeMsg(0)", MSGTIMER );
    }
}

function fadeMsg ( flag ) {
    if ( flag == null ) {
        flag = 1;
    }
    var msg = document.getElementById('msg');
    var value;
    if ( flag == 1 ) {
        value = msg.alpha + MSGSPEED;
    }
    else {
        value = msg.alpha - MSGSPEED;
    }
    msg.alpha = value;
    msg.style.opacity = ( value / 100 );
    msg.style.filter = 'alpha(opacity=' + value + ')';
    if ( value >= 99 ) {
        clearInterval(msg.timer);
        msg.timer = null;
    }
    else if ( value <= 1 ) {
        msg.style.display = "none";
        clearInterval(msg.timer);
    }
}

function leftPosition ( target )
{
    var left = 0;
    if ( target.offsetParent ) {
        while ( 1 ) {
            left += target.offsetLeft;
            if ( ! target.offsetParent ) {
                break;
            }
            target = target.offsetParent;
        }
    }
    else if ( target.x ) {
        left += target.x;
    }
    return left;
}

function topPosition ( target )
{
    var top = 0;
    if ( target.offsetParent ) {
        while ( 1 ) {
            top += target.offsetTop;
            if ( !target.offsetParent ) {
                break;
            }
            target = target.offsetParent;
        }
    }
    else if ( target.y ) {
        top += target.y;
    }
    return top;
}

/*
if ( document.images ) {
    arrow = new Image(7,80);
    arrow.src = "msg_arrow.gif";
}
*/

function daysInMonth ( month, year )
{
    var days = 31;
    if ( month == "Apr" || month == "Jun" || month == "Sep" || month == "Nov" )
    {
        days = 30;
    }
    if ( month == "Feb" && ( year / 4 ) != Math.floor(year/4) ) {
        days = 28;
    }
    if ( month == "Feb" && ( year / 4 ) == Math.floor(year/4) ) {
        days = 29;
    }

    return days;
}

function changeDaysOption ( node )
{
    /*
    objDay = eval ( "document.Form1." + node + "Day" );
    objMonth = eval ( "document.Form1." + node + "Month" );
    objYear = eval ( "document.Form1." + node + "Year" );
    */
    objDay = document.getElementById ( "FirstSelectDay" );
    objMonth = document.getElementById ( "FirstSelectMonth" );
    objYear = document.getElementById ( "FirstSelectYear" );

    month = objMonth[objMonth.selectedIndex].text;
    year = objYear[objYear.selectedIndex].text;

    daysForThisSelection = daysInMonth ( month, year );
    currentDaysInSelection = objDay.length;
    if ( currentDaysInSelection > daysForThisSelection ) {
        for ( i = 0; i < ( currentDaysInSelection - daysForThisSelection );
                ++i) {
            objDay.options[objDay.options.length - 1] = null;
        }
    }

    if ( daysForThisSelection > currentDaysInSelection ) {
        for ( i = 0; i < ( daysForThisSelection - currentDaysInSelection );
                ++i ) {
            newOption = new Option ( objDay.options.length + 1 );
            objDay.add ( newOption );
        }
    }

    if ( objDay.selectedIndex < 0 ) {
        objDay.selectedIndex = 0;
    }
}

function setToToday ( node )
{
    /*
    objDay = eval ( "document.Form1." + node + "Day" );
    objMonth = eval ( "document.Form1." + node + "Month" );
    objYear = eval ( "document.Form1." + node + "Year" );
    */
    objDay = document.getElementById ( "FirstSelectDay" );
    objMonth = document.getElementById ( "FirstSelectMonth" );
    objYear = document.getElementById ( "FirstSelectYear" );

    objYear[0].selected = true;
    objMonth[NowMonth].selected = true;

    changeDaysOption ( node );

    objDay[NowDay-1].selected = true;
}

// set today
Now = new Date();
NowDay = Now.getDate();
NowMonth = Now.getMonth();
NowYear = Now.getYear();
if ( NowYear < 2000 ) {
    NowYear += 1900;
}

function writeYearOptions ( years )
{
    line = "";
    for ( i = 0; i < years; ++i ) {
        line += "<OPTION>";
        line += NowYear + i;
    }
    return line;
}

