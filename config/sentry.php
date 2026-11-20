<?php

/*
|--------------------------------------------------------------------------
| Sentry error reports (server only)
|--------------------------------------------------------------------------
|
| SENTRY_LARAVEL_DSN empty (default) = Sentry is off and nothing is sent.
|
| The settings below keep personal and business data out of the reports:
| no user, no IP address, no request body, no cookies, no SQL values, no
| log lines, no performance tracing, no profiling, no browser SDK.
| App\Support\SentryScrubber removes the remaining request data. The privacy
| policy (section 7) describes this. Change the policy first if you change
| what a report contains.
|
*/

return [

    'dsn' => env('SENTRY_LARAVEL_DSN'),

    'release' => env('SENTRY_RELEASE'),

    'environment' => env('SENTRY_ENVIRONMENT'),

    'sample_rate' => 1.0,

    // Performance tracing and profiling stay off.
    'traces_sample_rate' => null,
    'profiles_sample_rate' => null,

    'enable_logs' => false,
    'enable_metrics' => false,

    'send_default_pii' => false,

    // Never read the request body (form contents, uploaded files).
    'max_request_body_size' => 'none',

    'before_send' => [App\Support\SentryScrubber::class, 'beforeSend'],

    'breadcrumbs' => [
        // Log lines and notifications can contain names and email addresses.
        'logs' => false,
        'cache' => false,
        'livewire' => false,
        // The SQL text without its values.
        'sql_queries' => true,
        'sql_bindings' => false,
        'queue_info' => true,
        'command_info' => true,
        'http_client_requests' => false,
        'notifications' => false,
    ],

    'crons' => [
        'monitor_scheduled_tasks' => false,
        'exclude_scheduled_tasks' => [],
    ],

    'tracing' => [
        'default_integrations' => false,
    ],

];
