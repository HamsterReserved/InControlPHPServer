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
    require_once('inccon_const.php');
    require_once('inccon_config.php'); // Configuration file

    require_once('incontrol_common.php'); // Common helper functions
    require_once('incontrol_db.php'); // DBOperator class

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
        /* $device_id = $_GET['device_id'];
        $credentials = $_GET['credentials'];
        $sensor_id = $_GET['sensor_id'];
        $sensor_info = $_GET['sensor_info'];
        $info_date = $_GET['info_date'];
        
        ensure_not_null($device_id, "device_id", __FUNCTION__);
        ensure_not_null($sensor_id, "sensor_id", __FUNCTION__);
        ensure_not_null($sensor_info, "sensor_info", __FUNCTION__);
        ensure_not_null($info_date, "info_date", __FUNCTION__);
        */
        $req_type = check_get_http_param('request_type', __FUNCTION__, NULL);
        check_credentials();
        
        if (OFFLINE_TEST) {
            echo "OK";
            exit();
        }
        // TODO: Do something here. Write to database, validate device ID, return result etc.
        switch ($req_type) {
            case REQUEST_TYPE_DATA_REPORT:
                break;
            case REQUEST_TYPE_SWITCH_STATE:
                break;
            case REQUEST_TYPE_USER_REGISTRATION:
                break;
            case REQUEST_TYPE_FACTORY_REGISTRATION:
                break;
             default:
                ensure_not_null(NULL, "request_type", __FUNCTION__, " submitted is unknown!");
        }
    }
    
    function process_client_request() {
        check_credentials($credentials);

        $req_type = check_get_http_param('request_type', __FUNCTION__, NULL);

        switch ($request_type) {
            case REQUEST_TYPE_QUERY_SENSOR_LIST:
                respond_sensor_list();
                break;
            case REQUEST_TYPE_QUERY_SENSOR_HISTORY:
                respond_sensor_history();
                break;
            case REQUEST_TYPE_QUERY_DEVICE_INFO:
                respond_device_info();
                break;
            case REQUEST_TYPE_SET_DEVICE_NAME:
                break;
            case REQUEST_TYPE_SET_SENSOR_TRIGGER:
                break;
            default:
                ensure_not_null(NULL, "request_type", __FUNCTION__, " submitted is unknown!");
        }
    }
?>