<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);
$twig->addGlobal('router', $app->getContainer()->get('router'));

$app->get('/', function (Request $request, Response $response) {
    global $twig;
    $args['pagename'] = 'Home';
    $args['users'] = $this->db->query('SELECT user, username, email FROM users')->fetchAll(PDO::FETCH_ASSOC);
    return $response->getBody()->write($twig->render('home.twig', $args));
})->setName('home');

$app->get('/{pagename}', function (Request $request, Response $response, array $args) {
    global $twig;
    $route = $args['pagename'];
    $this->logger->info("Slim: Unknown route '/$route'");

    // Render index view
    return $response->getBody()->write($twig->render('home.twig', $args));
});
