<?php
    /* Database Operator */
    /* Hamster Tian @ 2015/02 */

    require_once('inccon_db.php');
    require_once('incontrol_common.php');
    
    class DBOperator {
        var $mysqli;
        
        function DBOperator() {
            $this->mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if ($this->mysqli->connect_errno != 0) {
                ensure_not_null(NULL, 
                            "DB Connection Failed with ", 
                            __FUNCTION__, 
                            "error " . $this->mysqli->connect_error . " (" . $this->mysqli->connect_errno . ")");
            }
        }
        
        function create_tables() {
            $sql = "CREATE TABLE " . CONTROL_CENTER_TBL_NAME;
        }
    }
?>