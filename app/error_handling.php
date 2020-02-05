<?php

ini_set('log_errors', 'On');
ini_set('error_log', 'syslog');
ini_set('log_errors_max_len', '0');

register_shutdown_function('fatal_error_handler');

function fatal_error_handler()
{
    $error = error_get_last();

    if (
        null !== $error
        && isset($error['type'])
        && !in_array($error['type'], [E_DEPRECATED, E_USER_DEPRECATED, E_NOTICE, E_WARNING])
    ) {
        $error['trace'] = debug_backtrace();

        error_log(json_encode($error));
    }
}
