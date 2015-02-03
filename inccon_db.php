<?php
    define("DB_HOST", 'localhost');
    define("DB_USERNAME", 'stub_user');
    define("DB_PASSWORD", 'stub_password');
    define("DB_PORT", "3367"); // May not use this
    define("DB_NAME", "app_incontrol");
    define("CONTROL_CENTER_TBL_NAME", "control_centers");
    define("SENSOR_DATA_TBL_NAME", "sensor_data");
    
    $CONTROL_CENTER_COLUMNS = array(
                        "row_id" => "int NOT NULL AUTO_INCREMENT",
                        "machine_id" => "text",
                        "man_date" => "int",
                        "reg_date" => "int",
                        "clients" => "text",
                        "triggers" => "text"
                        );
    define("CONTROL_CENTER_PRIMARY_KEY", "row_id");
    
    $SENSOR_COLUMNS = array(
                        "row_id" => "int NOT NULL AUTO_INCREMENT",
                        "sensor_id" => "text",
                        "sensor_type" => "int",
                        "sensor_name" => "text",
                        "sensor_date" => "int",
                        "sensor_value" => "int",
                        "machine_id" => "text"
                        );
    define("SENSOR_DATA_PRIMARY_KEY", "row_id");
?>