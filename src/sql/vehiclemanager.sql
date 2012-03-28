CREATE DATABASE IF NOT EXIST vehiclemanager;

USE vehiclemanager;

CREATE TABLE t_vehicles (
        vehicle_id INTEGER NOT NULL PRIMARY KEY UNIQUE AUTO_INCREMENT,
        vehicle_make TEXT NOT NULL,
        vehicle_model TEXT NOT NULL,
        vehicle_trim TEXT,
        vehicle_mfg_year INTEGER,
        vehicle_license_number TEXT NOT NULL,
        vehicle_purchase_date DATE,
        vehicle_initial_odo INTEGER,
        vehicle_purchase_price INTEGER,
        vehicle_tank_capacity INTEGER );

CREATE TABLE t_odo (
        odo_id INTEGER NOT NULL PRIMARY KEY UNIQUE AUTO_INCREMENT,
        vehicle_id INTEGER NOT NULL,
        FOREIGN KEY (vehicle_id) REFERENCES t_vehicles ( vehicle_id ),
        odo_date DATE NOT NULL,
        odo_reading FLOAT NOT NULL,
        odo_diff FLOAT NOT NULL,
        UNIQUE INDEX(vehicle_id,odo_date) );

CREATE TABLE t_odo_monthly (
        vehicle_id INTEGER NOT NULL,
        odo_month DATE NOT NULL,
        odo_monthly_aggr FLOAT NOT NULL,
        UNIQUE INDEX(vehicle_id, odo_month) );

CREATE TABLE t_odo_yearly (
        vehicle_id INTEGER NOT NULL,
        odo_year DATE NOT NULL,
        odo_yearly_aggr FLOAT NOT NULL,
        UNIQUE INDEX(vehicle_id, odo_year) );

CREATE TABLE t_fillups (
        fillup_id INTEGER NOT NULL PRIMARY KEY UNIQUE AUTO_INCREMENT,
        vehicle_id INTEGER NOT NULL,
        fillup_odo FLOAT NOT NULL,
        fillup_date DATE NOT NULL,
        fillup_volume FLOAT NOT NULL,
        fillup_price FLOAT NOT NULL,
        fillup_amount FLOAT NOT NULL,
        fillup_partial TINYINT NOT NULL,
        fillup_fuel_brand TEXT,
        fillup_station TEXT,
        fillup_payment_type TEXT );



-- month looks something like "2012-01-01" for Jan 2012
DELIMITER //
CREATE PROCEDURE calc_monthly ( IN vehicleId INT, IN month TEXT )
BEGIN
    INSERT INTO t_odo_monthly ( vehicle_id, odo_month, odo_monthly_aggr )
    VALUES ( vehicleId, month,
        ( SELECT round(sum(odo_diff),1) FROM t_odo where vehicle_id=vehicleId AND odo_date >= month AND odo_date <= LAST_DAY(month) ) )
    ON DUPLICATE KEY UPDATE
        odo_monthly_aggr = ( SELECT round(sum(odo_diff),1) FROM t_odo where vehicle_id=vehicleId AND odo_date >= month AND odo_date <= LAST_DAY(month) );
END
//

DELIMITER ;

-- year is something like "2012"
DELIMITER //
CREATE PROCEDURE calc_yearly ( IN vehicleId INT, IN year text )
BEGIN
    INSERT INTO t_odo_yearly ( vehicle_id, odo_year, odo_yearly_aggr )
    VALUES ( vehicleId, CONCAT(year,'-01-01'),
        ( SELECT round(sum(odo_monthly_aggr),1) FROM t_odo_monthly WHERE vehicle_id=vehicleId AND odo_month >= CONCAT(year,'-01-01') and odo_month <= CONCAT(year,'-12-01') ) )
    ON DUPLICATE KEY UPDATE
        odo_yearly_aggr = ( SELECT round(sum(odo_monthly_aggr),1) FROM t_odo_monthly WHERE vehicle_id=vehicleId AND odo_month >= CONCAT(year,'-01-01') AND odo_month <= CONCAT(year,'-12-01') );
END
//

DELIMITER ;

