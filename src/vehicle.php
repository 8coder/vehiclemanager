<?php

class Vehicle
{
    static private $vehicleArrayInitialized = false;
    static private $vehicleArray;

    private $vehicleMake;
    private $vehicleModel;
    private $vehicleTrim;
    private $vehicleMfgYear;
    private $vehicleLicense;
    private $vehiclePurchaseDate;
    private $vehicleInitialOdo;
    private $vehiclePurchasePrice;
    private $vehicleTankCapacity;

    // populate vehicleArray
    // return 0 = success
    // return 1 = mysql query error
    // return 2 = no vehicles
    static function getVehicles()
    {
        $queryStr = 'SELECT * FROM vehiclemanager.t_vehicles ORDER BY vehicle_id;';
        $results = mysql_query ( $queryStr );
        if ( ! $results ) {
            return 1;
        }
        if ( mysql_num_rows ( $results ) == 0 ) {
            return 2;
        }

        while ( $row = mysql_fetch_assoc ( $results ) ) {
            $v = new Vehicle;
            sscanf ( $row['vehicle_id'], '%d', $v->vehicleId );
            $v->vehicleMake = $row['vehicle_make'];
            $v->vehicleModel = $row['vehicle_model'];
            $v->vehicleTrim = $row['vehicle_trim'];
            sscanf ( $row['vehicle_mfg_year'], '%d', $v->vehicleMfgYear );
            $v->vehicleLicense = $row['vehicle_license_number'];
            $v->vehiclePurchaseDate = $row['vehicle_purchase_date'];
            sscanf ( $row['vehicle_initial_odo'], '%d', $v->vehicleInitialOdo );
            sscanf ( $row['vehicle_purchase_price'], '%d',
                    $v->vehiclePurchasePrice );
            sscanf ( $row['vehicle_tank_capacity'], '%d',
                    $v->vehicleTankCapacity );

            if ( ! Vehicle::$vehicleArrayInitialized ) {
                Vehicle::$vehicleArray = array ( $v->vehicleId => $v );
                Vehicle::$vehicleArrayInitialized = true;
            }
            else {
                Vehicle::$vehicleArray[$v->vehicleId] = $v;
            }
        }

        return 0;
    }

    // $vehicleId is a string!!!!
    static function getVehicle ( $vehicleId )
    {
        $vid = -1;
        sscanf ( $vehicleId, '%d', $vid );
        if ( array_key_exists ( $vid, Vehicle::$vehicleArray ) ) {
            return Vehicle::$vehicleArray[$vid];
        }

        return false;
    }

    function getMake()
    {
        return $this->vehicleMake;
    }

    function getModel()
    {
        return $this->vehicleModel;
    }
}

?>

