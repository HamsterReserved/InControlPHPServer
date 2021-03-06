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
        $sensor_name = $_GET['sensor_name']; // Not mandatory. This is needed for 1st registration (sent by perp device)
        
        $db = new DBOperator();
        $db->set_sensor_value($sensor_id, $device_id, $sensor_type, $sensor_value, $info_date, $sensor_name);
        echo("OK");
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=
    // Return: device_name directly
    function respond_server_name() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $result = $db->get_device_info($device_id);
        
        echo($result['name']);
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=
    // Return: id;name;trigger&id;name;trigger...
    function respond_server_sensor_list() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $info_arr = $db->get_sensor_list($device_id);
        
        foreach($info_arr as $info) {
            $result = $result . $info["sensor_id"] . ";" . $info["sensor_name"] . ";" . $info["sensor_trigger"] . "&";
        }
        $result = substr($result, 0, strlen($result) - 1); // Trailing "|"
        
        echo $result;
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&state=
    function respond_switch_state() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $state = check_get_http_param('state', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->set_device_state($device_id, $state);
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