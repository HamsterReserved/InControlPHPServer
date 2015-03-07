<?php
    define("DEVICE_TYPE_SERVER", 1);
    define("DEVICE_TYPE_CLIENT", 2);

    define("SENSOR_DEFAULT_NAME", "Sensor");
    define("DEVICE_DEFAULT_NAME", "InControl");

    define("REQUEST_TYPE_SET_BASE", 100);

    /******** Client requests *********/
    define("REQUEST_TYPE_QUERY_SENSOR_LIST", 1);
    // define("REQUEST_TYPE_QUERY_SENSOR_INFO", 2); DEPRECATED! Now return full info in list.
    define("REQUEST_TYPE_QUERY_SENSOR_HISTORY", 3);
    define("REQUEST_TYPE_QUERY_DEVICE_INFO", 4);
    define("REQUEST_TYPE_SET_DEVICE_NAME", REQUEST_TYPE_SET_BASE + 1);
    define("REQUEST_TYPE_SET_SENSOR_TRIGGER", REQUEST_TYPE_SET_BASE + 2);
    define("REQUEST_TYPE_SET_SENSOR_NAME", REQUEST_TYPE_SET_BASE + 3);

    /******** Server requests *********/
    define("REQUEST_TYPE_DATA_REPORT", 1);
    define("REQUEST_TYPE_QUERY_SERVER_NAME", 2);
    define("REQUEST_TYPE_QUERY_SENSOR_NAME", 3);
    define("REQUEST_TYPE_QUERY_SENSOR_TRIGGER", 4);
    define("REQUEST_TYPE_SWITCH_STATE", 97);
    define("REQUEST_TYPE_USER_REGISTRATION", 98);
    define("REQUEST_TYPE_FACTORY_REGISTRATION", 99);

    /******** Server states *********/
    define("STATE_NORMAL", 0); // Server is normally working, do not accept any new pair request
    define("STATE_NEW_CLIENT", 1); // Let the server accept new clients
    define("STATE_NEW_SENSOR", 2); // Accept new sensors. Will this be ever used? We don't have any network/remote sensors.
    define("STATE_MAX", STATE_NEW_SENSOR);
    // Remember to update incontrol_db.php/set_device_state

    /******** Sensor types *********/
    define("SENSOR_TYPE_LIGHT", 0);
    define("SENSOR_TYPE_ELECTRICITY", 1);
    define("SENSOR_TYPE_MOTION", 2);
    define("SENSOR_TYPE_SWITCH", 3);
    define("SENSOR_TYPE_IR", 4);
    // keep in sync with STM32 and Android
?>