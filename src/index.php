<?php

require ( 'vehicle.php' );

# MAIN STARTS HERE

$output_string = '';
$selectedVehicleIndex = "-1";

add_output (
'<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF8">
  <title>Vehicle Manager</title>
  <link rel="stylesheet" type="text/css" href="css/style.css"/>
  <script type="text/javascript" src="js/vehiclemanager.js"></script>
 </head>
 <body>
' );

if ( $dbh = mysql_connect ( 'localhost', 'root', '' ) ) {
    $result = Vehicle::getVehicles();
    if ( $result == 1 ) {
        add_output ( '<h1>MySQL error while retrieving vehicles.</h1>' );
    }
    else if ( $result == 2 ) {
        add_output ( '<h1>No vehicles in database.</h1>' );
    }

    $submitStr = "";
    if ( array_key_exists ( 'submit', $_POST ) ) {
        $submitStr = $_POST['submit'];
    }
    if ( $submitStr == "Add Trip Record" ) {
        addTripRecord();
    }

    $actionStr = "";
    if ( array_key_exists ( 'action', $_GET ) ) {
        $actionStr = $_GET['action'];
    }

    showHomeLink();

    if ( $actionStr == "addTripRecord" ) {
        showAddTripRecord();
    }
    else if ( $actionStr == "addVehicle" ) {
        showAddVehicleScreen();
    }
    else if ( $actionStr == "browseRecords" ) {
        browseRecords();
    }
    else {
        showVehicleSelector();
        showMainMenu();
    }
}
else {
    add_output ( '
  <h1>Could not open database connection.</h1>
');
}

add_output ( '
 </body>
</html>
' );

print $output_string;

# MAIN ENDS HERE #

function add_output ( $str )
{
    global $output_string;
    $output_string = $output_string . $str;
}

function showHomeLink()
{
    add_output ( '
<div id="toplayer">
 <table id="toplayertable" rules="none" border="0" width="100%"><tbody>
  <tr>
   <td><a href=".">Home</a></td> ' );
}

function showVehicleSelector()
{
    // extract vehicle id from _GET and validate if it exists.
    global $selectedVehicleIndex;
    $noVehicleSelected = 1;
    if ( array_key_exists ( 'vehicleId', $_GET ) ) {
        $selectedVehicleIndex = $_GET['vehicleId'];
        $noVehicleSelected = 0;
        $selectedVehicle = Vehicle::getVehicle ( $selectedVehicleIndex );
        if ( ! $selectedVehicle ) {
            $selectedVehicleIndex = "-1";
            $noVehicleSelected = 1;
        }
    }

    add_output ( '<td align="right">' );
    add_output ( '<select id="vehicleSelector" name="vehicleSelector" onload="setSelectedVehicleId();" onchange="updateVehicleIdRequestParm();">' );
    $nrow = 1;
    while ( 1 ) {
        $row = Vehicle::getVehicle ( sprintf('%s', $nrow) );
        if ( ! $row ) {
            break;
        }
        add_output ( '<option' );

        // determine if this option is to be set as "selected"
        if ( $noVehicleSelected == 1 ) {
            if ( $nrow == 1 ) {
                add_output ( ' selected' );
                $selectedVehicleIndex = sprintf ( '%s', $nrow );
            }
        }
        else {
            if ( $selectedVehicleIndex == sprintf ( '%s', $nrow ) ) {
                add_output ( ' selected' );
            }
        }

        add_output ( '>' . sprintf('%s',$row->vehicleId) . ' ' .
                $row->getMake() . ' ' .  $row->getModel() . '</option>' );
        $nrow++;
    }
    add_output ( '</select>' );
    add_output ( '</td></tr></tbody></table></div>' );
}

function showMainMenu()
{
    global $selectedVehicleIndex;

    add_output ( '
 <div id="bottomlayer">
  <table id="mainTable" align="center" width="80%"><tbody>
   <tr>
    <td><a id="addFillupRecord" href="?action=addFillupRecord&amp;vehicleId=' . $selectedVehicleIndex . '">Add Fillup Record</a></td>
    <td><a id="addServiceRecord" href="?action=addServiceRecord&amp;vehicleId=' . $selectedVehicleIndex . '">Add Service Record</a></td>
    <td><a id="addExpenseRecord" href="?action=addExpenseRecord&amp;vehicleId=' . $selectedVehicleIndex . '">Add Expense Record</a></td>
   </tr>
   <tr>
    <td><a id="addTripRecord" href="?action=addTripRecord&amp;vehicleId=' . $selectedVehicleIndex . '">Add Trip Record</a></td>
    <td><a href="?action=addVehicle">Add Vehicle</a></td>
    <td><a id="browseRecords" href="?action=browseRecords&amp;vehicleId=' . $selectedVehicleIndex . '">Browse Records</a></td>
   </tr>
   <tr>
    <td><a id="viewStats" href="?action=viewStats&amp;vehicleId=' . $selectedVehicleIndex . '">Stats</a></td>
    <td><a id="viewCharts" href="?action=viewCharts&amp;vehicleId=' . $selectedVehicleIndex . '">Charts</a></td>
    <td><a href="?action=viewSettings">Settings</a></td>
   </tr>
   <tr>
    <td><a href="?action=importExport">Import/Export</a></td>
   </tr>
  </tbody></table>
 </div>' );
}

function showAddTripRecord()
{
    add_output ( '</tr></tbody></table></div>' );

    $vehicleId = $_GET['vehicleId'];
    $selectedVehicle = Vehicle::getVehicle ( $vehicleId );

    // last odo date and reading query
    $lastOdoQueryStr = 'SELECT odo_date, odo_reading from vehiclemanager.t_odo where vehicle_id = ' . $vehicleId . ' and odo_date = ( select max(odo_date) from vehiclemanager.t_odo where vehicle_id = ' . $vehicleId . ');';
    $results = mysql_query ( $lastOdoQueryStr );
    $result = mysql_fetch_assoc ( $results );
    $lastOdoDate = $result['odo_date'];
    $lastOdoReading = $result['odo_reading'];

    add_output ( '
 <div id="bottomlayer">
  <h2>Add Trip Record for ' . $selectedVehicle->getMake() . ' ' .
  $selectedVehicle->getModel() . '</h2>' );
    add_output ( '
  <form name="addTripRecordForm" id="addTripRecordForm" method="post" action=".?vehicleId=' . $vehicleId . '" enctype="multipart/form-data" onsubmit="return ValidateAddTripRecordForm(this, \'' . $lastOdoDate . '\', ' . $lastOdoReading . ');">
  <table id="addTripRecordTable" align="center"><tbody>
   <tr>
    <td><label>Date (YYYY-MM-DD)</label></td>
    <td><input type="text" id="entryDate" name="entryDate" size="16">
    <label>Last Odo Date: ' . $lastOdoDate . '</label></td>
   </tr>
   <tr>
    <td><label>Odo Reading</label></td>
    <td>
     <input type="text" id="entryOdo" name="entryOdo" size="8">
     <label>Last Odo: ' . $lastOdoReading . '</label>
     <input type="hidden" id="vehicleId" name="vehicleId" value="' . $vehicleId . '">
    </td>
   </tr>
   <tr><td></td><td><input name="submit" type="submit" value="Add Trip Record"/>
   <input name="cancel" type="submit" value="Cancel" onclick="doCancel();"/></td></tr>
  </tbody></table></form></div>' );
}

function addTripRecord()
{
    $odoDate = $_POST['entryDate'];
    $odoReading = $_POST['entryOdo'];
    $vehicleId = $_POST['vehicleId'];
    $selectedVehicle = Vehicle::getVehicle ( $vehicleId );

    // last odo reading query
    $lastOdoQueryStr = 'SELECT odo_reading from vehiclemanager.t_odo where vehicle_id = '
        . $vehicleId
        . ' and odo_date = ( select max(odo_date) from vehiclemanager.t_odo where vehicle_id = '
        . $vehicleId . ');';
    $results = mysql_query ( $lastOdoQueryStr );
    $result = mysql_fetch_assoc ( $results );
    $lastOdoReading = $result['odo_reading'];

    $odoDiff = $odoReading + 0.0 - $lastOdoReading;
    $odoDiffStr = sprintf ( "%.1f", $odoDiff );

    $queryStr = 'INSERT INTO vehiclemanager.t_odo ( vehicle_id, odo_date, odo_reading, odo_diff ) VALUES ( ';
    $queryStr = $queryStr . $vehicleId . ', "' . $odoDate . '", ' . $odoReading . ', '
        . $odoDiffStr . ' );';

    $result = mysql_query ( $queryStr );
    if ( $result ) {
        add_output ( 'Successfully inserted record ( ' . $odoDate . ', ' .
                $odoReading . ' ) for vehicle ' .  $selectedVehicle->getMake()
                . $selectedVehicle->getModel() . '.' );

        // update aggregates
        $queryStr = 'CALL calc_monthly ( ' . $vehicleId . ', "' . $odoDate . '" );';
        $result = mysql_query ( $queryStr );
    }
    else {
        add_output ( 'Could not insert record ( ' . $odoDate . ', ' .
                    $odoReading . ' ) for vehicle ' .
                $selectedVehicle->getMake() . $selectedVehicle->getModel() . '.'
                );
    }
}

// $monthYearStr looks something like "2012-02-01".. th 2 digits of the day are ignored
function decrementMonth ( $monthYearStr )
{
    $year = 0;
    $month = 0;
    $day = 0;

    if ( sscanf ( $monthYearStr, '%4d-%2d-%2d', $year, $month, $day ) != 3 ) {
        return FALSE;
    }

    if ( $month == 1 ) {
        $month = 12;
        $year = $year - 1;
    }
    else {
        $month = $month - 1;
    }

    $returnStr = sprintf ( "%4d-%02d-%02d", $year, $month, $day );
    return $returnStr;
}

function incrementMonth ( $monthYearStr )
{
    $year = 0;
    $month = 0;
    $day = 0;

    if ( sscanf ( $monthYearStr, '%4d-%2d-%2d', $year, $month, $day ) != 3 ) {
        return FALSE;
    }

    if ( $month == 12 ) {
        $month = 1;
        $year = $year + 1;
    }
    else {
        $month = $month + 1;
    }

    $returnStr = sprintf ( "%4d-%02d-%02d", $year, $month, $day );
    return $returnStr;
}

function getEndDate ( $startDate )
{
    $year = 0;
    $month = 0;
    $day = 0;

    if ( sscanf ( $startDate, '%4d-%2d-%2d', $year, $month, $day ) != 3 ) {
        return FALSE;
    }

    if ( $month == 1 or $month == 3 or $month == 5 or $month == 7 or $month == 8
            or $month == 10 or $month == 12 ) {
        $retval = sprintf ( '%4d-%02d-%02d', $year, $month, 31 );
        return $retval;
    }

    if ( $month == 4 or $month == 6 or $month == 9 or $month == 11 ) {
        $retval = sprintf ( '%4d-%02d-%02d', $year, $month, 30 );
        return $retval;
    }

    if ( $month == 2 ) {
        if ( ( $year % 400 ) == 0 ) {
            $retval = sprintf ( '%4d-%02d-%02d', $year, $month, 29 );
        }
        else if ( ( $year % 4 ) == 0 ) {
            $retval = sprintf ( '%4d-%02d-%02d', $year, $month, 29 );
        }
        else {
            $retval = sprintf ( '%4d-%02d-%02d', $year, $month, 28 );
        }

        return $retval;
    }

    return FALSE;
}

function checkForRecords ( $vehicleId, $startDate, $endDate )
{
    $queryStr = 'SELECT COUNT(*) FROM vehiclemanager.t_odo WHERE vehicle_id = ' .
        $vehicleId . ' AND odo_date >= "' . $startDate . '" AND odo_date <= "' .
        $endDate . '";';

    $results = mysql_query ( $queryStr );
    if ( ! $results ) {
        return FALSE;
    }

    $row = mysql_fetch_assoc ( $results );
    if ( ! $row ) {
        return FALSE;
    }

    $numRecords = $row['COUNT(*)'];
    if ( $numRecords == '0' ) {
        return FALSE;
    }

    return TRUE;
}

function browseRecords()
{
    $vehicleId = $_GET['vehicleId'];
    $selectedVehicle = Vehicle::getVehicle ( $vehicleId );

    add_output ( '</tr></tbody></table></div>' );

    if ( array_key_exists ( 'startDate', $_GET ) ) {
        $startDate = $_GET['startDate'];
    }
    else {
        $startDate = date ( 'Y-m-' );
        $startDate = $startDate . '01';
    }

    if ( array_key_exists ( 'endDate', $_GET ) ) {
        $endDate = $_GET['endDate'];
    }
    else {
        $endDate = getEndDate ( $startDate );
        if ( ! $endDate ) {
            add_output ( '<h2>Invalid start date: ' . $startDate );
            return;
        }
    }

    $prevStartDate = decrementMonth ( $startDate );
    $prevEndDate = getEndDate ( $prevStartDate );

    $nextStartDate = incrementMonth ( $startDate );
    $nextEndDate = getEndDate ( $nextStartDate );

    $hasPrevData = checkForRecords ( $vehicleId, $prevStartDate, $prevEndDate );
    $hasNextData = checkForRecords ( $vehicleId, $nextStartDate, $nextEndDate );

    $queryStr = 'SELECT * FROM vehiclemanager.t_odo WHERE vehicle_id = ' .
        $vehicleId . ' AND odo_date >= "' . $startDate . '" and odo_date <= "' .
        $endDate . '";';
    $results = mysql_query ( $queryStr );
    if ( ! $results ) {
        add_output ( '<h2>Could not retrieve records.' );
        return;
    }

    if ( mysql_num_rows ( $results ) <= 0 ) {
        add_output ( '<h2>No records for date >= ' + $startDate );
        return;
    }

    add_output ( '
 <div id="bottomlayer">
  <h2>Records for ' . $selectedVehicle->getMake() . ' ' .
  $selectedVehicle->getModel() . '</h2>' );

    add_output ( ' <table id="browseRecordsTable" rules="all" border="1" align="center" cellspacing="5" cellpadding="5"><tbody> ' );
    add_output ( '<tr><th>Date</th><th>Odo Reading</th><th>Odo Diff</th></tr>' );
    $monthlyTotal = 0.0;
    while ( $row = mysql_fetch_assoc ( $results ) ) {
        add_output ( '<tr><td>' . $row['odo_date'] . '</td><td align="right">' . $row['odo_reading'] . '</td><td align="right">' . $row['odo_diff'] . '</td></tr>' );
        $thisReading = 0.0;
        sscanf ( $row['odo_diff'], '%f', $thisReading );
        $monthlyTotal += $thisReading;
    }

    // Display monthly total
    add_output ( '<tr><td></td><td align="right"><b>Total</b></td><td align="right"><b>' . $monthlyTotal . '</b></td></tr>' );

    add_output ( '<tr align="right"><td colspan="3">' );
    if ( $hasPrevData ) {
        add_output ( '<a href="?action=browseRecords&amp;vehicleId=' .
                $vehicleId . '&amp;startDate=' . $prevStartDate .
                '&amp;endDate=' . $prevEndDate . '">' . '&lt;' . $prevStartDate . '</a>' );
        add_output ( '&nbsp;&nbsp;' );
    }
    if ( $hasNextData ) {
        add_output ( '<a href="?action=browseRecords&amp;vehicleId=' .
                $vehicleId . '&amp;startDate=' . $nextStartDate .
                '&amp;endDate=' . $nextEndDate . '">' . $nextStartDate . '&gt;</a>' );
    }
    add_output ( '</td></tr></tbody></table></div>' );
}

function addYearSelectOptions ( $numYears )
{
    $resultStr = '';
    $firstItem = 1;
    $year = idate('Y');
    while ( $numYears > 0 ) {
        $resultStr = $resultStr . '<option';
        if ( $firstItem == 1 ) {
            $firstItem = 0;
            $resultStr = $resultStr . ' selected';
        }
        $resultStr = $resultStr . '>' . sprintf('%d', $year )
            .  '</option>';
        $year--;
        $numYears--;
    }

    return $resultStr;
}

function showAddVehicleScreen()
{
    add_output ( '</tr></tbody></table></div>' );
    add_output ( '
<div id="bottomlayer">
<form name="addVehicleForm" id="addVehicleForm" method="post" action="." enctype="multipart/form-data" onsubmit="return validateNewVehicleForm(this);" onload="setToToday(\'FirstSelect\');">
 <table align="center" rules="all" border="1"><tbody>
  <tr>
   <td><b>Make:</b></td>
   <td><input type="text" id="vehicle_make" name="vehicle_make" size="24"/></td>
  </tr>
  <tr>
   <td><b>Model:</b></td>
   <td><input type="text" id="vehicle_model" name="vehicle_model" size="24"/></td>
  </tr>
  <tr>
   <td><b>Trim:</b></td>
   <td><input type="text" name="vehicle_trim" size="24"/></td>
  </tr>
  <tr>
   <td><b>Mfg Year:</b></td>
   <td><select name="vehicle_mfg_year">' . addYearSelectOptions(20) . '</select></td>
  </tr>
  <tr>
   <td><b>Registration Number:</b></td>
   <td><input type="text" name="vehicle_license_number" size="20"/></td>
  </tr>
  <tr>
   <td><b>Purchase Date:</b></td>
   <td>
    <select id="FirstSelectDay" name="FirstSelectDay">
     <option>1</option>
     <option>2</option>
     <option>3</option>
     <option>4</option>
     <option>5</option>
     <option>6</option>
     <option>7</option>
     <option>8</option>
     <option>9</option>
     <option>10</option>
     <option>11</option>
     <option>12</option>
     <option>13</option>
     <option>14</option>
     <option>15</option>
     <option>16</option>
     <option>17</option>
     <option>18</option>
     <option>19</option>
     <option>20</option>
     <option>21</option>
     <option>22</option>
     <option>23</option>
     <option>24</option>
     <option>25</option>
     <option>26</option>
     <option>27</option>
     <option>28</option>
     <option>29</option>
     <option>30</option>
     <option>31</option>
    </select>
    <select id="FirstSelectMonth" name="FirstSelectMonth" onchange="changeDaysOption(\'FirstSelect\')">
     <option>Jan</option>
     <option>Feb</option>
     <option>Mar</option>
     <option>Apr</option>
     <option>May</option>
     <option>Jun</option>
     <option>Jul</option>
     <option>Aug</option>
     <option>Sep</option>
     <option>Oct</option>
     <option>Nov</option>
     <option>Dec</option>
    </select>
    <select id="FirstSelectYear" name="FirstSelectYear" onchange="changeDaysOption(\'FirstSelect\')">
     <script type="text/javascript">
      document.write(writeYearOptions(50));
     </script>
    </select>
   </td>
  </tr>
  <tr>
   <td><b>Initial Odo Reading:</b></td>
   <td><input type="text" name="vehicle_initial_odo" size="20"/></td>
  </tr>
  <tr>
   <td><b>Purchase Price:</b></td>
   <td><input type="text" name="vehicle_purchase_price" size="10"/></td>
  </tr>
  <tr>
   <td><b>Tank Capacity:</b></td>
   <td><input type="text" name="vehicle_tank_capacity" size="10"/></td>
  </tr>
  <tr><td></td><td><input name="submit" type="submit" value="Add Vehicle"/>
  <input name="cancel" type="submit" value="Cancel" onclick="doCancel();"/></td></tr>
 </tbody></table>
</form></div>
' );
}

?>
