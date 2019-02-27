<?php
use Slim\Http\Response;
// DIC configuration

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['db'] = function($c) {
    $settings = $c->get('settings')['db'];
    $connstring = '$dbType:host=$host;dbname=$dbname;user=$username;password=$password';
    $connstring = strtr($connstring, $settings);

    return new PDO($connstring);
};

// view renderer
$container['render'] = function ($c) {
    $loader = new \Twig\Loader\FilesystemLoader('../templates');
    $twig = new \Twig\Environment($loader);
    $twig->addGlobal('router', $c->get('router'));
    $twig->addGlobal('session', $_SESSION);

    return function(Response $response, $template, $args) use ($twig){
        $response->getBody()->write($twig->render($template, $args));
        return $response;
    };
};

unset($container['notFoundHandler']);
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $response = $response->withStatus(404, "Page not found");
        return ($c->render)($response, '404.twig', []);
    };
};
