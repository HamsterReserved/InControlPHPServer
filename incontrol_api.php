<?php
/**
 * InControl API
 * Parameters: (All possible parameters are listed here. See constants.php and corresponding function for specific command)
 *  device_type : 1 - Server (sensor, home control center)
 *                2 - Client (Mobile devices, tablets, computers)
 *                Required for every request, of course.
 *  device_id : Serial number for each server unit, may be found on the shell of box. 
 *              Required for each request.
 *  request_type : The purpose of this request by client.
 *                 1 - Query what sensor is connected to control center
 *                 (DEPRECATED) 2 - Query sensors info
 *                 Check inccon_const.php for more types.
 *                 Required for client only.
 *  credentials : Self-explanatory. (IF IMPLEMENTED PROPERLY) Required for each request.
 *                Should be something like hidden mysterious ID hardcoded into Flash ROM for server.
 *                Should be user specified string for client queries. When 1st set-up, should be written on device screen.
 *  sensor_id : Required for server reporting data. Optional for client.
 *  sensor_value : Self-explanatory. Required for server only.
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
    require_once('incontrol_client_req.php');
    require_once('incontrol_server_req.php');

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
        $req_type = check_get_http_param('request_type', __FUNCTION__, NULL);
        check_credentials();
        
        if (OFFLINE_TEST) {
            echo "OK";
            exit();
        }

        switch ($req_type) {
            case REQUEST_TYPE_DATA_REPORT:
                respond_data_report();
                break;
            case REQUEST_TYPE_QUERY_SERVER_NAME:
                respond_server_name();
                break;
            case REQUEST_TYPE_QUERY_SENSOR_NAME:
                respond_sensor_name();
                break;
            case REQUEST_TYPE_QUERY_SENSOR_TRIGGER:
                respond_sensor_trigger();
                break;
            case REQUEST_TYPE_SWITCH_STATE:
                respond_switch_state();
                break;
            case REQUEST_TYPE_USER_REGISTRATION:
                respond_user_registration();
                break;
            case REQUEST_TYPE_FACTORY_REGISTRATION:
                respond_factory_registration();
                break;
             default:
                ensure_not_null(NULL, "request_type", __FUNCTION__, " submitted is unknown!");
        }
    }
    
    function process_client_request() {
        check_credentials();

        $req_type = check_get_http_param('request_type', __FUNCTION__, NULL);

        switch ($req_type) {
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
                respond_set_device_name();
                break;
            case REQUEST_TYPE_SET_SENSOR_TRIGGER:
                respond_set_sensor_trigger();
                break;
            case REQUEST_TYPE_SET_SENSOR_NAME:
                respond_set_sensor_name();
                break;
            default:
                ensure_not_null(NULL, "request_type", __FUNCTION__, " submitted is unknown!");
        }
    }
?>