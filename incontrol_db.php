<?php
    /* Database Operator */
    /* Hamster Tian @ 2015/02 */

    require_once('inccon_db.php');
    require_once('incontrol_common.php');
    
    class DBOperator {
        var $mysqli;

        function DBOperator() {
            $this->mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            $this->create_tables();
            $this->check_mysqli_err();
        }

        function create_tables() {
            global $CONTROL_CENTER_COLUMNS, $SENSOR_DATA_COLUMNS;

            $sql = "CREATE TABLE IF NOT EXISTS " . 
                    CONTROL_CENTER_TBL_NAME . 
                    "(" . 
                    implode(", ", array_combine_key_value($CONTROL_CENTER_COLUMNS, " ")) .
                    ")";
            $this->mysqli->query($sql);
            $this->check_mysqli_err();

            $sql = "CREATE TABLE IF NOT EXISTS " . 
                    SENSOR_DATA_TBL_NAME . 
                    "(" . 
                    implode(", ", array_combine_key_value($SENSOR_DATA_COLUMNS, " ")) .
                    ")";
            $this->mysqli->query($sql);
            $this->check_mysqli_err();
        }

        function check_mysqli_err() {
            if ($this->mysqli->connect_errno != 0) {
                    ensure_not_null(NULL, 
                            "DB Connection Failed with ", 
                            __FUNCTION__, 
                            "error " . $this->mysqli->connect_error . " (" . $this->mysqli->connect_errno . ")");
            }
            if ($this->mysqli->errno != 0) {
                    ensure_not_null(NULL, 
                            "DB Operation Failed with ", 
                            __FUNCTION__, 
                            "error " . $this->mysqli->error . " (" . $this->mysqli->errno . ")");
            }
        }
    }
?>