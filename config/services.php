<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sensors' => [
        'ingest_token' => env('SENSOR_INGEST_TOKEN'),
        'accept_legacy_token' => env('SENSOR_ACCEPT_LEGACY_TOKEN', true),
        'reading_retention_days' => (int) env('SENSOR_READING_RETENTION_DAYS', 30),
        'auto_alert_push' => env('SENSOR_AUTO_ALERT_PUSH', true),
        'auto_alert_cooldown_seconds' => (int) env('SENSOR_AUTO_ALERT_COOLDOWN_SECONDS', 180),
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'openai'),
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    'firebase' => [
        'web_api_key' => env('FIREBASE_WEB_API_KEY'),
        'web_auth_domain' => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'web_project_id' => env('FIREBASE_WEB_PROJECT_ID'),
        'web_storage_bucket' => env('FIREBASE_WEB_STORAGE_BUCKET'),
        'web_messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'web_app_id' => env('FIREBASE_WEB_APP_ID'),
        'web_measurement_id' => env('FIREBASE_WEB_MEASUREMENT_ID'),
        'web_vapid_key' => env('FIREBASE_WEB_VAPID_KEY'),
        'project_id' => env('FIREBASE_PROJECT_ID', env('FIREBASE_WEB_PROJECT_ID')),
        'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH'),
        'enable_push' => env('FIREBASE_ENABLE_PUSH', false),
    ],

];
