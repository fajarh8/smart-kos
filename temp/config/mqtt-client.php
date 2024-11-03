<?php

declare(strict_types=1);

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Repositories\MemoryRepository;

return [

    /*
    |--------------------------------------------------------------------------
    | Default MQTT Connection
    |--------------------------------------------------------------------------
    |
    | This setting defines the default MQTT connection returned when requesting
    | a connection without name from the facade.
    |
    */

    'default_connection' => 'default',

    /*
    |--------------------------------------------------------------------------
    | MQTT Connections
    |--------------------------------------------------------------------------
    |
    | These are the MQTT connections used by the application. You can also open
    | an individual connection from the application itself, but all connections
    | defined here can be accessed via name conveniently.
    |
    */

    'connections' => [
        'default' => [

            // The host and port to which the client shall connect.
            'host' => env('MQTT_HOST', 'test.mosquitto.org'),
            'port' => env('MQTT_PORT', 1883),

            // The MQTT protocol version used for the connection.
            'protocol' => MqttClient::MQTT_3_1,

            // A specific client id to be used for the connection. If omitted,
            // a random client id will be generated for each new connection.
            'client_id' => env('MQTT_CLIENT_ID', substr(md5(microtime()),rand(0>

            // Whether a clean session shall be used and requested by the clien>
            // A clean session will let the broker forget about subscriptions a>
            // queued messages when the client disconnects. Also, if available,
            // data of a previous session will be deleted when connecting.
            'use_clean_session' => env('MQTT_CLEAN_SESSION', false),

            // Whether logging shall be enabled. The default logger will be used
            // with the log level as configured.
            'enable_logging' => env('MQTT_ENABLE_LOGGING', true),

            // Which logging channel to use for logs produced by the MQTT clien>
            // If left empty, the default log channel or stack is being used.
            'log_channel' => env('MQTT_LOG_CHANNEL', null),

            // Defines which repository implementation shall be used. Currently,
            // only a MemoryRepository is supported.
            'repository' => MemoryRepository::class,

            // Additional settings used for the connection to the broker.
            // All of these settings are entirely optional and have sane defaul>
            'connection_settings' => [
                // The TLS settings used for the connection. Must match the spe>
                'tls' => [
                    'enabled' => env('MQTT_TLS_ENABLED', false),
                    'allow_self_signed_certificate' => env('MQTT_TLS_ALLOW_SELF>
                    'verify_peer' => env('MQTT_TLS_VERIFY_PEER', true),
                    'verify_peer_name' => env('MQTT_TLS_VERIFY_PEER_NAME', true>
                    'ca_file' => env('MQTT_TLS_CA_FILE'),
                    'ca_path' => env('MQTT_TLS_CA_PATH'),
                    'client_certificate_file' => env('MQTT_TLS_CLIENT_CERT_FILE>
                    'client_certificate_key_file' => env('MQTT_TLS_CLIENT_CERT_>
                    'client_certificate_key_passphrase' => env('MQTT_TLS_CLIENT>
                ],

                // Credentials used for authentication and authorization.
                'auth' => [
                    'username' => env('MQTT_AUTH_USERNAME', null),
                    'password' => env('MQTT_AUTH_PASSWORD', null),
                ],

                // Can be used to declare a last will during connection. The la>
                // is published by the broker when the client disconnects abnor>
                // (e.g. in case of a disconnect).
                'last_will' => [
                    'topic' => env('MQTT_LAST_WILL_TOPIC'),
                    'message' => env('MQTT_LAST_WILL_MESSAGE'),
                    'quality_of_service' => env('MQTT_LAST_WILL_QUALITY_OF_SERV>
                    'retain' => env('MQTT_LAST_WILL_RETAIN', false),
                ],

                // The timeouts (in seconds) used for the connection. Some of t>
                // are only relevant when using the event loop of the MQTT clie>
                'connect_timeout' => env('MQTT_CONNECT_TIMEOUT', 60),
                'socket_timeout' => env('MQTT_SOCKET_TIMEOUT', 5),
                'resend_timeout' => env('MQTT_RESEND_TIMEOUT', 10),


                // The interval (in seconds) in which the client will send a pi>
                // if no other message has been sent.
                'keep_alive_interval' => env('MQTT_KEEP_ALIVE_INTERVAL', 60),

                // Additional settings for the optional auto-reconnect. The del>
                'auto_reconnect' => [
                    'enabled' => env('MQTT_AUTO_RECONNECT_ENABLED', true),
                    'max_reconnect_attempts' => env('MQTT_AUTO_RECONNECT_MAX_RE>
                    'delay_between_reconnect_attempts' => env('MQTT_AUTO_RECONN>
                ],

            ],

        ],

    ],

];
