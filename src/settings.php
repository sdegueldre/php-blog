<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => 'php://stdout',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // DataBase settings
        'db' => [
            'dbType' => 'pgsql',
            'host' => 'localhost',
            'dbname' => 'becode',
            'user' => 'becode',
            'password' => 'becode',
        ],

        //Twig settings
        'twig' => [
            'templateDir' => '../templates',
            'envSettings' => [

            ],
        ],
    ],
];
