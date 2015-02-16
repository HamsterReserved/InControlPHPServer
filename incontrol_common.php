<?php
    require_once('inccon_config.php');

    // If the first parameter is null, just show a error and exit
    // Commonly used as an exit function in the rest of program
    function ensure_not_null($var_to_check, $var_name, $function_name, $prompt_msg = NULL) {
        if ($prompt_msg == NULL)
            $prompt_msg = " not defined!"; // TODO: Is this needed or just take this as default value?
        
        if ($var_to_check == NULL) {
            header('HTTP/1.1 501 Not implemented');
            header("status: 501 Not implemented");
            if (DEBUG) {
                $err_desc_array = array("error_msg" => $function_name . ": " . $var_name . $prompt_msg);
            } else {
                $err_desc_array = array("error_msg" => $function_name);
            }
            die(json_encode($err_desc_array));
        }
    }

    function check_credentials() {
        // TODO: Do check things here, such as NULL and not match, then die or not
        if (CREDENTIALS_ENABLED || OFFLINE_TEST)
            return true;
        else
            ensure_not_null(NULL, "cred", __FUNCTION__, "does not comply!");
    }
    
    function array_combine_key_value($arr, $delim) {
        $ret = array();
        foreach($arr as $key=>$value) {
            $ret[]="$key$delim$value";
        }
        return $ret;
    }

    function check_get_http_param($param_name, $func, $prompt) {
        $ret = $_GET[$param_name];
        ensure_not_null($ret, $prompt, $func, NULL);
        return $ret;
    }
?>