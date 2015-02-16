<?php
/**
 * InControl: process requests from server (control center)
 * Requests are dispatched in incontrol_api.php
 * Hamster Tian 2015/02
 */
    require_once('inccon_const.php');
    require_once('inccon_config.php'); // Configuration file

    require_once('incontrol_common.php'); // Common helper functions
    require_once('incontrol_db.php'); // DBOperator class

    // api.php?device_id=&device_type=&credentials=&request_type=&sensor_id=&sensor_value=&info_date=&sensor_type=
    // Return OK or {error_msg:xxx}
    function respond_data_report() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $sensor_id = check_get_http_param('sensor_id', __FUNCTION__, NULL);
        $sensor_value = check_get_http_param('sensor_value', __FUNCTION__, NULL);
        $info_date = check_get_http_param('info_date', __FUNCTION__, NULL);
        $sensor_type = check_get_http_param('sensor_type', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->set_sensor_value($sensor_id, $device_id, $sensor_type, $sensor_value, $info_date);
        echo("OK");
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&state=
    function respond_switch_state() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $state = check_get_http_param('state', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->set_device_state($device_id, $state);
        echo("OK");
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&name=
    // If name is empty, original name will be preserved
    function respond_user_registration() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $name = check_get_http_param('name', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->register_device($device_id, $name);
        echo("OK");
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=
    // Will define a default name (InControl) in add_new_device
    // Cred here should be something internal to factory
    // This request should be sent by some device in the factory, not the control center itself. But device_type is still SERVER.
    function respond_factory_registration() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->add_new_device($device_id);
        echo("OK");
    }
?>