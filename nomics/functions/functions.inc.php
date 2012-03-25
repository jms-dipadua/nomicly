<?php 
function log_errors ($message) {
    $error_message = $message;
    $log = "INSERT INTO error_logs (message) VALUES ('$message')";
    $log_query = mysql_query ($log);
    
    if (!$log_query) {
        echo mysql_error();
    }
}
?>