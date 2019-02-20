<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

$app->get('/[{pagename}]', function (Request $request, Response $response, array $args) {
    global $twig;
    $route = $args['pagename'];
    $this->logger->info("Slim route: '/$route'");


    $PDO = new PDO('pgsql:host=localhost;dbname=becode', 'becode', 'becode');
    $users = $PDO->query('SELECT user, username, email from users')->fetchAll(PDO::FETCH_ASSOC);
    $args['users'] = $users;

    // Render index view
    return $response->getBody()->write($twig->render('home.twig', $args));
});
