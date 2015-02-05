<?php
    define("DEVICE_TYPE_SERVER", 1);
    define("DEVICE_TYPE_CLIENT", 2);

    define("REQUEST_TYPE_QUERY_SENSOR_LIST", 1);
    define("REQUEST_TYPE_QUERY_SENSOR_INFO", 2);

    define("STATE_NORMAL", 0); // Server is normally working, do not accept any new pair request
    define("STATE_NEW_CLIENT", 1); // Let the server accept new clients
    define("STATE_NEW_SENSOR", 2); // Accept new sensors. Will this be ever used? We don't have any network/remote sensors.
    define("STATE_MAX", STATE_NEW_SENSOR);
    // Remember to update incontrol_db.php/set_device_state

    define("SENSOR_TYPE_LIGHT", 0);
    define("SENSOR_TYPE_ELECTRICITY", 1);
    define("SENSOR_TYPE_MOTION", 2);
    define("SENSOR_TYPE_SWITCH", 3);
    define("SENSOR_TYPE_IR", 4);
    // keep in sync with STM32 and Android
?>