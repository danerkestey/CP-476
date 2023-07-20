<?php

function log_message($message) {

    $logDir = __DIR__ . '/../../logs/';

    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $backtrace = debug_backtrace();
    $line = $backtrace[0]['line'];
    $file = $backtrace[0]['file'];
    $date = date('Y-m-d H:i:s');

    $log_message = "$date - Error on line $line in file $file:\n$message\n";

    error_log($log_message, 3, $logDir . 'error.log');
}

?>
