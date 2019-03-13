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
            '$dbType' => 'pgsql',
            '$host' => 'localhost',
            '$dbname' => 'becode',
            '$username' => 'becode',
            '$password' => 'becode',
        ],

        //Twig settings
        'twig' => [
            'templateDir' => '../templates',
            'envSettings' => [

            ],
            // display name => route name
            'navbar' => [
                'home' => [
                    'name' => 'Blog',
                    'permissions' => 0
                    ],
                'login' => [
                    'name' => 'Log in',
                    'permission' => 0
                    ],
                'signup' => [
                    'name' => 'Sign up',
                    'permissions' => 0
                    ],
                'post' => [
                    'name' => 'New article',
                    'permissions' => 1
                    ],
                'dashboard' => [
                    'name' => 'Dashboard',
                    'permissions' => 2
                    ],
            ],
        ],
    ],
];
