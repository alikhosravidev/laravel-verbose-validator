<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Verbose Validator
    |--------------------------------------------------------------------------
    |
    | This option controls whether the verbose validation reporting is active.
    | It's highly recommended to set this to `false` in production to
    | avoid any performance overhead. A good practice is to use an
    | environment variable for this setting.
    |
    */
    'enabled' => env('VERBOSE_VALIDATOR_ENABLED', env('APP_DEBUG', false)),

    // Determines which type of report should be attached on failed validation ('failed', 'passed', or 'all')
    'failure_report_type' => env('VERBOSE_VALIDATOR_FAILURE_REPORT', 'failed'),
];
