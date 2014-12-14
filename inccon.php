<?php
    //error_reporting(0);
    error_reporting(E_ALL ^ E_NOTICE);

    $CREDENTIAL_ENABLED = false;
    $DEBUG = true; // More debug info when error
    $OFFLINE_TEST = true; // No database enabled, pass credential test
    
    $TEST_SENSOR_INFO = array("sensor_id" => 0, "sensor_name" => "Test Sensor", "sensor_type" => "light", "sensor_info" => 115);
    $TEST_SENSOR_INFO_RESPONSE = json_encode($TEST_SENSOR_INFO);
    
    $TEST_SENSOR_LIST = array(array("sensor_id" => 0, "sensor_name" => "Test Sensor 1", "sensor_type" => "motion"), array("sensor_id" => 1, "sensor_name" => "Test Sensor 2", "sensor_type" => "light"));
    $TEST_SENSOR_LIST_RESPONSE = json_encode($TEST_SENSOR_LIST);
?>