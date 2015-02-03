<?php
/**
 * InControl API
 * Parameters: (SEE constants.php)
 *  device_type : 1 - Server (sensor, home control center)
 *                2 - Client (Mobile devices, tablets, computers)
 *                Required for every request, of course.
 *  device_id : Serial number for each server unit, may be found on the shell of box. 
 *              Required for each request.
 *  request_type : The purpose of this request by client.
 *                 1 - Query what sensor is connected to control center
 *                 2 - Query sensors info
 *                 3 - Update sensor name etc
 *                 Other values are reserved.
 *                 Required for client only.
 *  credentials : Self-explanatory. (IF ADDED PROPERLY) Required for each request.
 *  sensor_id : Required for server reporting data. Optional for client.
 *  sensor_info : Self-explanatory. Required for server only.
 *  info_date : For server: time of this status update (from board RTC)
 *              For client: last fetched date (so won't fetch repeat data. Client should take care of repeated things though)
 *
 *  Since the M660 module only supports GET method, no POST is implemented and allowed here.
 *
 *  And note that 0 == NULL == "0" here!
 */
    require_once('incontrol_constants.php');
    require_once('incontrol_common.php');
    require_once('inccon.php'); // Configuration file. Note this is not a function!
    require_once('incontrol_db.php'); // DBOperator class

    $dboperator = new dboperator();
    switch ($_GET['device_type']) {
        case DEVICE_TYPE_SERVER:
            process_server_request();
            break;
        case DEVICE_TYPE_CLIENT:
            process_client_request();
            break;
        default:
            ensure_not_null(NULL, "device_type", "Main Page");
    }
    // Main page ends here.
    
    function process_server_request() {
        $device_id = $_GET['device_id'];
        $credentials = $_GET['credentials'];
        $sensor_id = $_GET['sensor_id'];
        $sensor_info = $_GET['sensor_info'];
        $info_date = $_GET['info_date'];
        
        ensure_not_null($device_id, "device_id", __FUNCTION__);
        ensure_not_null($sensor_id, "sensor_id", __FUNCTION__);
        ensure_not_null($sensor_info, "sensor_info", __FUNCTION__);
        ensure_not_null($info_date, "info_date", __FUNCTION__);
        
        check_credentials($credentials);
        
        if (OFFLINE_TEST)
            echo "OK";
        else {
            // TODO: Do something here. Write to database, validate device ID, return result etc.
        }
    }
    
    function process_client_request() {
        global $REQUEST_TYPE_QUERY_SENSOR_LIST, $REQUEST_TYPE_QUERY_SENSOR_INFO;
        
        $device_id = $_GET['device_id'];
        $request_type = $_GET['request_type'];
        $credentials = $_GET['credentials'];
        $sensor_id = $_GET['sensor_id'];
        $info_date = $_GET['info_date'];
        
        ensure_not_null($device_id, "device_id", __FUNCTION__);
        ensure_not_null($request_type, "request_type", __FUNCTION__);
        
        check_credentials($credentials);

        switch ($request_type) {
            case REQUEST_TYPE_QUERY_SENSOR_LIST:
                respond_sensor_list($device_id);
                break;
            case REQUEST_TYPE_QUERY_SENSOR_INFO:
                respond_sensor_info($device_id, $sensor_id, $info_date);
                break;
            default:
                ensure_not_null(NULL, "request_type", __FUNCTION__, " submitted is unknown!");
        }
    }
    
    function respond_sensor_list($device_id) {
        global $TEST_SENSOR_LIST_RESPONSE;
        
        ensure_not_null($device_id, "device_id", __FUNCTION__);
        //TODO: Send request to control center (shall we accomplish this by SMS?)
        if (OFFLINE_TEST)
            if ($device_id == "1")
                echo $TEST_SENSOR_LIST_RESPONSE;
            else
                ensure_not_null(NULL, "device_id", __FUNCTION__, " unknown!");
    }
    
    function respond_sensor_info($device_id, $sensor_id, $info_date) {
        global $TEST_SENSOR_0_INFO_RESPONSE, $TEST_SENSOR_1_INFO_RESPONSE;
    
        ensure_not_null($device_id, "device_id", __FUNCTION__);
        ensure_not_null($sensor_id, "sensor_id", __FUNCTION__);
        // TODO: Query from db, note that sensor_id and info_date is optional
        if (OFFLINE_TEST)
            if ($device_id == "1")
                if ($sensor_id == "0")
                    echo $TEST_SENSOR_0_INFO_RESPONSE;
                else if ($sensor_id == "1")
                    echo $TEST_SENSOR_1_INFO_RESPONSE;
            else
                ensure_not_null(NULL, "device_id", __FUNCTION__, " unknown!");
    }
?>