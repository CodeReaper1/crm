<?php
/**
 * Debug functions for the CRM system
 */

/**
 * Logs a message to the debug log file
 *
 * @param string $message The message to log
 * @param string $level The log level (INFO, WARNING, ERROR)
 * @return void
 */
function debug_log($message, $level = 'INFO') {
    $log_file = 'debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Dumps a variable to the debug log file
 *
 * @param mixed $var The variable to dump
 * @param string $label A label for the dump
 * @return void
 */
function debug_dump($var, $label = '') {
    ob_start();
    var_dump($var);
    $dump = ob_get_clean();
    
    if ($label) {
        $message = "$label: $dump";
    } else {
        $message = $dump;
    }
    
    debug_log($message, 'DEBUG');
}
?>
