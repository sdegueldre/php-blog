<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);
$twig->addGlobal('router', $app->getContainer()->get('router'));
$twig->addGlobal('navbar', [
  'home' => 'Home',
  'users' => 'Users',
  'login' => 'Login',
  'about' => 'About',
]);

$app->get('/', function (Request $request, Response $response) {
    global $twig;
    $args['pagename'] = 'Home';
    return $response->getBody()->write($twig->render('home.twig', $args));
})->setName('home');

$app->get('/users', function (Request $request, Response $response, array $args) {
    global $twig;
    $args['pagename'] = 'Users';

    $PDO = new PDO('pgsql:host=localhost;dbname=becode', 'becode', 'becode');
    $users = $PDO->query('SELECT user, username, email FROM users')->fetchAll(PDO::FETCH_ASSOC);
    $args['users'] = $users;

    // Render index view
    return $response->getBody()->write($twig->render('users.twig', $args));
})->setName('users');

$app->get('/login', function (Request $request, Response $response, array $args) {
    global $twig;
    $args['pagename'] = 'Login';
    return $response->getBody()->write($twig->render('login.twig', $args));
})->setName('login');


$app->get('/about', function (Request $request, Response $response, array $args) {
    global $twig;
    $args['pagename'] = 'About';
    return $response->getBody()->write($twig->render('about.twig', $args));
})->setName('about');



$app->get('/{pagename}', function (Request $request, Response $response, array $args) {
    global $twig;
    $route = $args['pagename'];
    $this->logger->info("Slim route: '/$route'");

    // Render index view
    return $response->getBody()->write($twig->render('home.twig', $args));
});
