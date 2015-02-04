<?php
    define("DB_HOST", 'localhost');
    define("DB_USERNAME", 'stub_user');
    define("DB_PASSWORD", 'stub_password');
    define("DB_PORT", "3367"); // May not use this
    define("DB_NAME", "app_incontrol");
    define("CONTROL_CENTER_TBL_NAME", "control_centers");
    define("SENSOR_DATA_TBL_NAME", "sensor_data");
    
    $CONTROL_CENTER_COLUMNS = array(
                        "machine_id" => "text", // Device ID
                        "man_date" => "int", // Manufacturing date
                        "reg_date" => "int", // First time registered (user activation)
                        "clients" => "text", // Allowed client IDs
                        "triggers" => "text", // Send SMS etc
                        "state" => "tinyint", // Will I accept new clients/sensors? 0=normal 1=new clients 2=new sensors
                        "last_state_date" => "int" // Reject new clients/sensors after 2mins
                        );
    define("CONTROL_CENTER_PRIMARY_KEY", "row_id");
    
    $SENSOR_DATA_COLUMNS = array(
                        "sensor_id" => "text",
                        "sensor_type" => "int",
                        "sensor_name" => "text",
                        "sensor_date" => "int",
                        "sensor_value" => "int",
                        "machine_id" => "text" // Paired host machine
                        );
    define("SENSOR_DATA_PRIMARY_KEY", "row_id");
?>