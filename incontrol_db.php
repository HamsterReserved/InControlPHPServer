<?php
    /* Database Operator */
    /* Hamster Tian @ 2015/02 */

    require_once('inccon_db.php');
    require_once('inccon_const.php');
    require_once('incontrol_common.php');

    class DBOperator {
        var $mysqli;

        function DBOperator() {
            $this->mysqli = new mysqli(DB_HOST . ":" . DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
            $this->create_tables();
            $this->check_mysqli_err();
        }

        function create_tables_for_array($table_name, $arr, $primary_key) {
            if ($primary_key != NULL)
                $sql = "CREATE TABLE IF NOT EXISTS " . 
                    $table_name . 
                    "(" . 
                    implode(", ", array_combine_key_value($arr, " ")) .
                    ", PRIMARY KEY($primary_key))";
            else
                $sql = "CREATE TABLE IF NOT EXISTS " . 
                    $table_name . 
                    "(" . 
                    implode(", ", array_combine_key_value($arr, " ")) .
                    ")";
            $this->mysqli->query($sql);
            $this->check_mysqli_err();
        }

        function create_tables() {
            global $CONTROL_CENTER_COLUMNS, $SENSOR_DATA_COLUMNS, $SENSOR_INFO_COLUMNS;

            $this->create_tables_for_array(CONTROL_CENTER_TBL_NAME, $CONTROL_CENTER_COLUMNS, NULL);
            $this->create_tables_for_array(SENSOR_DATA_TBL_NAME, $SENSOR_DATA_COLUMNS, NULL);
            $this->create_tables_for_array(SENSOR_INFO_TBL_NAME, $SENSOR_INFO_COLUMNS, SENSOR_INFO_PRIMARY_KEY);
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

        function add_new_device($machine_id) { // For manufacturing
            // This is rare, no need to use prepared statement. Am I lazy?
            $machine_id = $mysqli->real_escape_string($machine_id);
            $sql = "INSERT INTO " . CONTROL_CENTER_TBL_NAME . " (machine_id, man_date) VALUES ('$machine_id', " . time() . ")";
            $this->mysqli->query($sql);
            $this->check_mysqli_err();
        }

        function register_device($machine_id, $device_name) { // For users first use of a device
            $machine_id = $mysqli->real_escape_string($machine_id);
            $device_name = $mysqli->real_escape_string($device_name);
            $sql = "UPDATE " . CONTROL_CENTER_TBL_NAME . " SET name = '$device_name', reg_date = " . time() . " WHERE machine_id = '$machine_id'";
            $this->mysqli->query($sql);
            $this->check_mysqli_err(); // TODO Shall we expose machine_id not found error to normal user?
        }

        function set_device_name($machine_id, $device_name) { // Nearly same as above, except we don't set reg_date
            $machine_id = $mysqli->real_escape_string($machine_id);
            $device_name = $mysqli->real_escape_string($device_name);
            $sql = "UPDATE " . CONTROL_CENTER_TBL_NAME . " SET name = '$device_name' WHERE machine_id = '$machine_id'";
            $this->mysqli->query($sql);
            $this->check_mysqli_err();
        }

        function check_device_credentials($machine_id, $cred) {
            $machine_id = $mysqli->real_escape_string($machine_id);
            $cred = $mysqli->real_escape_string($cred);
            $cred = crypt($cred, CRED_SALT);
            $sql = "SELECT name FROM " . CONTROL_CENTER_TBL_NAME . " WHERE machine_id = '$machine_id' AND cred_md5 = '$cred'";
            if ($mysqli->affected_rows <= 0)
                return false;
            return true;
        }

        function set_device_state($machine_id, $new_state) {
            if (!is_numeric($new_state) || new_state < 0 || new_state > STATE_MAX) {
                ensure_not_null(NULL, "new state should be a valid integer!");
            }
            // TODO
        }

        function update_sensor_value($sensor_id, $machine_id, $sensor_type, $sensor_value) { // Register and update are in one function
            if (!is_numeric($sensor_type) || !is_numeric($sensor_value)) {
                ensure_not_null(NULL, "sensor_type and sensor_value", __FUNCTION__, "should be integer!");
            }

            $sensor_id = $mysqli->real_escape_string($sensor_id);
            $machine_id = $mysqli->real_escape_string($machine_id);

            $check_sql = "SELECT row_id FROM " . SENSOR_INFO_TBL_NAME . " WHERE sensor_id='$sensor_id' AND assoc_machine_id='$machine_id'";
            $check_sql_result = $this->mysqli->query($check_sql);
            if ($this->mysqli->affected_rows == 0) { // This sensor hasn't been registered yet, register now.
                $create_sql = "INSERT INTO " . SENSOR_INFO_COLUMNS . " (sensor_id, assoc_machine_id, type, value) VALUES " .
                        "('$sensor_id', '$machine_id', $sensor_type, $sensor_value)";
                $this->mysqli->query($create_sql);
                $this->check_mysqli_err();
                $row_id = $this->mysqli->insert_id;
            } else if($this->mysqli->affected_rows >= 1) { // Already registered. >1 is abnormal but we can't do anything... for now
                $result_row = $check_sql_result->fetch_row(); // Only first row
                $row_id = $result_row[0];
            }
            $check_sql_result->close();

            $insert_data_sql = "INSERT INTO " . SENSOR_DATA_TBL_NAME . " (data_row_id, date, value) VALUES ".
                        "($row_id, " . time() . ", $sensor_value)";
            $this->mysqli->query($insert_data_sql);
            $this->check_mysqli_err();
        }

        function set_sensor_name($sensor_id, $machine_id, $new_name) {
            $sensor_id = $mysqli->real_escape_string($sensor_id);
            $machine_id = $mysqli->real_escape_string($machine_id);

            $check_sql = "SELECT row_id FROM " . SENSOR_INFO_TBL_NAME . " WHERE sensor_id='$sensor_id' AND assoc_machine_id='$machine_id'";
            $check_sql_result = $this->mysqli->query($check_sql);
            if ($this->mysqli->affected_rows == 0) { // This sensor hasn't been registered yet, DIE!
                $check_sql_result->close();
                ensure_not_null(NULL, "This sensor hasn't been registered!", __FUNCTION__, "");
            } else if ($this->mysqli->affected_rows >= 1) { // Already registered. >1 is abnormal but we can't do anything... for now
                $result_row = $check_sql_result->fetch_row(); // Only first row
                $row_id = $result_row[0];
                $check_sql_result->close();
                $update_sql = "UPDATE " . SENSOR_INFO_TBL_NAME . " SET name = '$new_name' WHERE row_id = $row_id";
                $this->mysqli->query($update_sql);
                $this->check_mysqli_err();
            }
        }
    }
?>