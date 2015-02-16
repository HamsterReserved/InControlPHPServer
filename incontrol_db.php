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
            $this->check_mysqli_err(__FUNCTION__, false);
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
            $this->check_mysqli_err(__FUNCTION__, false);
        }

        function create_tables() {
            global $CONTROL_CENTER_COLUMNS, $SENSOR_DATA_COLUMNS, $SENSOR_INFO_COLUMNS;

            $this->create_tables_for_array(CONTROL_CENTER_TBL_NAME, $CONTROL_CENTER_COLUMNS, NULL);
            $this->create_tables_for_array(SENSOR_DATA_TBL_NAME, $SENSOR_DATA_COLUMNS, NULL);
            $this->create_tables_for_array(SENSOR_INFO_TBL_NAME, $SENSOR_INFO_COLUMNS, SENSOR_INFO_PRIMARY_KEY);
        }

        function check_mysqli_err($func ,$check_for_affected_rows = true) {
            if ($this->mysqli->connect_errno != 0) {
                    ensure_not_null(NULL, 
                            "DB Connection Failed with ", 
                            $func, 
                            "error " . $this->mysqli->connect_error . " (" . $this->mysqli->connect_errno . ")");
            }
            if ($this->mysqli->errno != 0) {
                    ensure_not_null(NULL, 
                            "DB Operation Failed with ", 
                            $func, 
                            "error " . $this->mysqli->error . " (" . $this->mysqli->errno . ")");
            }
            
            if ($check_for_affected_rows && $this->mysqli->affected_rows < 1) {
                ensure_not_null(NULL, NULL, $func, "set failed");
            }
        }

        /************************** Setters *******************************/

        function add_new_device($machine_id) { // For manufacturing, use a default name: InControl
            // This is rare, no need to use prepared statement. Am I lazy?
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $sql = "INSERT INTO " . CONTROL_CENTER_TBL_NAME . " (machine_id, man_date, state, name) " . 
                   "VALUES ('$machine_id', " . time() . ", " . STATE_NORMAL . ", '" . DEVICE_DEFAULT_NAME . "')";
            $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
        }

        function register_device($machine_id, $device_name) { // For users first use of a device
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $device_name = $this->mysqli->real_escape_string($device_name);
            if ($device_name != NULL) {
                $sql = "UPDATE " . CONTROL_CENTER_TBL_NAME . " SET name = '$device_name', reg_date = " . time() . 
                   " WHERE machine_id = '$machine_id' AND state = " . STATE_NEW_CLIENT;
            } else {
                $sql = "UPDATE " . CONTROL_CENTER_TBL_NAME . " SET reg_date = " . time() . 
                   " WHERE machine_id = '$machine_id' AND state = " . STATE_NEW_CLIENT;
            }
            $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__); // TODO Shall we expose machine_id not found error to normal user?
        }

        function set_device_name($machine_id, $device_name) { // Nearly same as above, except we don't set reg_date
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $device_name = $this->mysqli->real_escape_string($device_name);
            $sql = "UPDATE " . CONTROL_CENTER_TBL_NAME . " SET name = '$device_name' WHERE machine_id = '$machine_id'";
            $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
        }

        function check_device_credentials($machine_id, $cred) {
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $cred = $this->mysqli->real_escape_string($cred);
            $cred = crypt($cred, CRED_SALT);
            $sql = "SELECT name FROM " . CONTROL_CENTER_TBL_NAME . " WHERE machine_id = '$machine_id' AND cred_md5 = '$cred'";
            if ($this->mysqli->affected_rows <= 0)
                return false;
            return true;
        }

        function set_device_state($machine_id, $new_state) {
            if (!is_numeric($new_state) || $new_state < 0 || $new_state > STATE_MAX) {
                ensure_not_null(NULL, "new state should be a valid integer!", __FUNCTION__, "");
            }
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $sql = "UPDATE " . CONTROL_CENTER_TBL_NAME . " SET state = $new_state, last_state_date = " . time() . " WHERE machine_id = '$machine_id'";
            $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
            
            if ($this->mysqli->affected_rows < 1)
                ensure_not_null(NULL, "State set failed", __FUNCTION__, "");
        }

        function set_sensor_value($sensor_id, $machine_id, $sensor_type, $sensor_value, $upd_date, $sensor_name) { // Register and update are in one function
            if (!is_numeric($sensor_type) || !is_numeric($sensor_value) || !is_numeric($upd_date)) {
                ensure_not_null(NULL, "sensor_type and sensor_value", __FUNCTION__, "should be integer!");
            }

            $sensor_id = $this->mysqli->real_escape_string($sensor_id);
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $sensor_name = $this->mysqli->real_escape_string($sensor_name);

            if ($sensor_name == NULL) $sensor_name = SENSOR_DEFAULT_NAME; // Default name for 1st reg

            $check_sql = "SELECT row_id FROM " . SENSOR_INFO_TBL_NAME . " WHERE sensor_id='$sensor_id' AND assoc_machine_id='$machine_id'";
            $check_sql_result = $this->mysqli->query($check_sql);
            if ($this->mysqli->affected_rows == 0) { // This sensor hasn't been registered yet, register now.
                $create_sql = "INSERT INTO " . SENSOR_INFO_TBL_NAME . " (sensor_id, assoc_machine_id, type, name) VALUES " .
                        "('$sensor_id', '$machine_id', $sensor_type, '$sensor_name')";
                $this->mysqli->query($create_sql);
                $this->check_mysqli_err(__FUNCTION__);
                $row_id = $this->mysqli->insert_id;
            } else if($this->mysqli->affected_rows >= 1) { // Already registered. >1 is abnormal but we can't do anything... for now
                $result_row = $check_sql_result->fetch_row(); // Only first row
                $row_id = $result_row[0];
            }
            $check_sql_result->close();

            $insert_data_sql = "INSERT INTO " . SENSOR_DATA_TBL_NAME . " (data_row_id, date, value) VALUES ".
                        "($row_id, $upd_date, $sensor_value)";
            $this->mysqli->query($insert_data_sql);
            $this->check_mysqli_err(__FUNCTION__);
        }

        function set_sensor_name($sensor_id, $machine_id, $new_name) { // Am I implementing this too complex?
            $sensor_id = $this->mysqli->real_escape_string($sensor_id);
            $machine_id = $this->mysqli->real_escape_string($machine_id);

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
                $this->check_mysqli_err(__FUNCTION__);
            }
        }
        
        function set_sensor_trigger($sensor_id, $machine_id, $trg) { // No error response?
            $sensor_id = $this->mysqli->real_escape_string($sensor_id);
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $trg = $this->mysqli->real_escape_string($trg);
            
            $sql = "UPDATE " . SENSOR_INFO_TBL_NAME . " SET triggers = '$trg' WHERE assoc_machine_id = '$machine_id' AND sensor_id = '$sensor_id'";
            $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
            
            if ($this->mysqli->affected_rows < 1)
                ensure_not_null(NULL, "Trigger set failed", __FUNCTION__, "");
        }

        /************************** Getters *******************************/
        function get_device_info($machine_id) { // Be sure you checked credentials first!
            $machine_id = $this->mysqli->real_escape_string($machine_id);
            $sql = "SELECT * FROM " . CONTROL_CENTER_TBL_NAME . " WHERE machine_id = '$machine_id'";
            $result = $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
            
            return $result->fetch_assoc(); // One line only
        }

        function get_sensor_info($sensor_id, $assoc_machine_id) {
            return get_sensor_data_history($sensor_id, $assoc_machine_id, 1);
        }
        
        /**
         * Query a known and signal sensor's data
         * Return format:
         * when count=1 {id, name, type, date, value}
         * when count>1 {{date,value}, {date,value},...}
         */
        function get_sensor_data_history($sensor_id, $assoc_machine_id, $count) {
            if (!is_numeric($count))
                ensure_not_null(NULL, "count has to be an integer!", __FUNCTION__, "");
            
            $assoc_machine_id = $this->mysqli->real_escape_string($assoc_machine_id);
            $sensor_id = $this->mysqli->real_escape_string($sensor_id);
            
            // Basic info (name, type, place in data table)
            $sql = "SELECT * FROM " . SENSOR_INFO_TBL_NAME . " WHERE sensor_id = '$sensor_id' AND assoc_machine_id = '$assoc_machine_id'";
            $result = $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
            
            $info_array = $result->fetch_assoc();
            $row_id = $info_array["row_id"];
            
            // Latest data
            $value_sql = "SELECT * FROM " . SENSOR_DATA_TBL_NAME . " WHERE data_row_id = $row_id ORDER BY date DESC LIMIT $count";
            $value_result = $this->mysqli->query($value_sql);
            $this->check_mysqli_err(__FUNCTION__, false); // No need to stop here. We have custom null handling below.
            
            $real_rows = min($count, $this->mysqli->affected_rows);
            if ($real_rows < 1)
                ensure_not_null(NULL, "No data in db", __FUNCTION__,"");
            else if ($real_rows == 1) {
                $value_array = $value_result->fetch_assoc();
                $return_array = array(
                            "sensor_id" => $sensor_id,
                            "sensor_type" => $info_array["type"],
                            "sensor_name" => $info_array["name"],
                            "sensor_date" => $value_array["date"],
                            "sensor_value" => $value_array["value"]
                            );
            } else {
                $return_array = array();
                while($value_array = $value_result->fetch_assoc()) {
                    $return_array[] = array(
                                "sensor_date" => $value_array["date"],
                                "sensor_value" => $value_array["value"]
                                );
                }
            }
            return $return_array;
        }
        
        /**
         * Return {{_id, _name, _type, _value, _date}, {...}}
         */
        function get_sensor_list($assoc_machine_id) {
            $assoc_machine_id = $this->mysqli->real_escape_string($assoc_machine_id);
            
            $sql = "SELECT * FROM " . SENSOR_INFO_TBL_NAME . " WHERE assoc_machine_id = '$assoc_machine_id'";
            $result = $this->mysqli->query($sql);
            $this->check_mysqli_err(__FUNCTION__);
            
            if ($this->mysqli->affected_rows < 1)
                return NULL;
            
            $ret_array = array();
            while ($row = $result->fetch_assoc()) {
                $ret_array[] = $this->get_sensor_data_history($row['sensor_id'], $assoc_machine_id, 1);
            }
            return $ret_array;
        }
    }
?>