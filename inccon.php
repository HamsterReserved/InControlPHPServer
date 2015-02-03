<?php
    //error_reporting(0);
    error_reporting(E_ALL ^ E_NOTICE);

    define("CREDENTIAL_ENABLED", false);
    define("DEBUG", true); // More debug info when error
    define("OFFLINE_TEST", true); // No database enabled, pass credential test

    // Tests
    $TEST_SENSOR_INFO_0 = array("sensor_id" => 0, "sensor_name" => "Test Sensor 2", "sensor_type" => $SENSOR_TYPE_LIGHT, "sensor_info" => 115);
    $TEST_SENSOR_0_INFO_RESPONSE = json_encode($TEST_SENSOR_INFO_0);

    $TEST_SENSOR_INFO_1 = array("sensor_id" => 1, "sensor_name" => "Test Sensor 1", "sensor_type" => $SENSOR_TYPE_MOTION, "sensor_info" => 1420987135);
    $TEST_SENSOR_1_INFO_RESPONSE = json_encode($TEST_SENSOR_INFO_1);

    $TEST_SENSOR_LIST = array(array("sensor_id" => 1, "sensor_name" => "Test Sensor 1", "sensor_type" => $SENSOR_TYPE_MOTION),
                              array("sensor_id" => 0, "sensor_name" => "Test Sensor 2", "sensor_type" => $SENSOR_TYPE_LIGHT));
    $TEST_SENSOR_LIST_RESPONSE = json_encode($TEST_SENSOR_LIST);
?>