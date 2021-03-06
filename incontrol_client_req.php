<?php
/**
 * InControl: process requests from client (Android)
 * Requests are dispatched in incontrol_api.php
 * Hamster Tian 2015/02
 */
    require_once('inccon_const.php');
    require_once('inccon_config.php'); // Configuration file

    require_once('incontrol_common.php'); // Common helper functions
    require_once('incontrol_db.php'); // DBOperator class

    // api.php?device_id=&device_type=&credentials=&request_type=
    // Return: see test example
    function respond_sensor_list() {
        global $TEST_SENSOR_LIST_RESPONSE;
        
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        //TODO: Send request to control center (shall we accomplish this by SMS?)
        if (OFFLINE_TEST) {
            if ($device_id == "1") {
                echo $TEST_SENSOR_LIST_RESPONSE;
                exit();
            }
        }

        $db = new DBOperator();
        $info_arr = $db->get_sensor_list($device_id);
        // Is this really needed?
        if (!is_null($info_arr))
            echo(json_encode($info_arr));
        else
            echo("[]"); // Empty json array
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&sensor_id=&count=
    // Return: see incontrol_db.php/get_sensor_data_history
    function respond_sensor_history() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $count = check_get_http_param('count', __FUNCTION__, NULL);
        $sensor_id = check_get_http_param('sensor_id', __FUNCTION__, NULL);
        
        // TODO: Offline test?
        $db = new DBOperator();
        echo(json_encode($db->get_sensor_data_history($sensor_id, $device_id, $count)));
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=
    // Return: {device_id, device_name, man_date, reg_date}
    function respond_device_info() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $result = $db->get_device_info($device_id);
        
        $ret_array = array("device_id" => $device_id,
                           "device_name"=>$result['name'],
                           "man_date"=>$result['man_date'],
                           "reg_date"=>$result['reg_date']);
        echo(json_encode($ret_array));
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&name=
    // If name is empty, original name will be preserved
    function respond_user_registration() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $name = $_GET['name']; // Not mandatory.
        
        $db = new DBOperator();
        $db->register_device($device_id, base64_decode($name));
        echo(json_encode(array("result" => "ok")));
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&name=(BASE64)
    // Return: {result:ok} {error_msg:xxx}(with HTTP 501) The latter is generated in ensure_not_null
    function respond_set_device_name() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $name = check_get_http_param('name', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->set_device_name($device_id, base64_decode($name));
        echo(json_encode(array("result" => "ok")));
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&trigger=(BASE64)&sensor_id=
    // Return: {result:ok} {error_msg:xxx}(with HTTP 501) The latter is generated in ensure_not_null
    function respond_set_sensor_trigger() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $sensor_id = check_get_http_param('sensor_id', __FUNCTION__, NULL);
        $trigger = check_get_http_param('trigger', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->set_sensor_trigger($sensor_id, $device_id, base64_decode($trigger));
        echo(json_encode(array("result" => "ok")));
    }
    
    // api.php?device_id=&device_type=&credentials=&request_type=&name=(BASE64)&sensor_id=
    // Return: {result:ok} {error_msg:xxx}(with HTTP 501) The latter is generated in ensure_not_null
    function respond_set_sensor_name() {
        $device_id = check_get_http_param('device_id', __FUNCTION__, NULL);
        $sensor_id = check_get_http_param('sensor_id', __FUNCTION__, NULL);
        $name = check_get_http_param('name', __FUNCTION__, NULL);
        
        $db = new DBOperator();
        $db->set_sensor_name($sensor_id, $device_id, base64_decode($name));
        echo(json_encode(array("result" => "ok")));
    }
?>