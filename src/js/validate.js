function validateNewVehicleForm ( theForm )
{
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

