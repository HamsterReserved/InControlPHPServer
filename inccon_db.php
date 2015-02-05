<?php
    define("DB_HOST", 'localhost');
    define("DB_USERNAME", 'stub_user');
    define("DB_PASSWORD", 'stub_password');
    define("DB_PORT", "3367"); // May not use this
    define("DB_NAME", "app_incontrol");
    define("CONTROL_CENTER_TBL_NAME", "control_centers");
    define("SENSOR_DATA_TBL_NAME", "sensor_data");
    define("SENSOR_INFO_TBL_NAME", "sensor_info");

    $CONTROL_CENTER_COLUMNS = array(
                        "machine_id" => "text", // Device ID
                        "man_date" => "int", // Manufacturing date
                        "reg_date" => "int", // First time registered (user activation)
                        "name" => "text", // Device name defined by user
                        "cred_md5" => "text", // User credentials, MD5 of course
                        "clients" => "text", // Allowed client IDs
                        "triggers" => "text", // Send SMS etc
                        "state" => "tinyint", // Will I accept new clients/sensors? 0=normal 1=new clients 2=new sensors
                        "last_state_date" => "int" // Reject new clients/sensors after 2mins
                        );
    define("CONTROL_CENTER_PRIMARY_KEY", "machine_id");

    $SENSOR_DATA_COLUMNS = array(
                        "data_row_id" => "text", // Same as SENSOR_INFO
                        "date" => "int",
                        "value" => "int",
                        );
    define("SENSOR_DATA_PRIMARY_KEY", "sensor_id"); // Mainly search by this

    $SENSOR_INFO_COLUMNS = array(
                        "row_id" => "int NOT NULL AUTO_INCREMENT",
                        "sensor_id" => "text",
                        "type" => "int",
                        "name" => "text",
                        "assoc_machine_id" => "text" // Paired host machine
                        );
    define("SENSOR_INFO_PRIMARY_KEY", "assoc_machine_id"); // Mainly search by this
?>