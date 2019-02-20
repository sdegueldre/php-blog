<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

$app->get('/[{pagename}]', function (Request $request, Response $response, array $args) {
    global $twig;
    $route = $args['pagename'];
    // Sample log message
    $this->logger->info("Slim-Skeleton '/$route' route");


    $PDO = new PDO('pgsql:host=localhost;dbname=becode', 'becode', 'becode');
    $users = $PDO->query('SELECT user, email from users')->fetch(PDO::FETCH_ASSOC);
    foreach ($users as $data)
      $this->logger->info("query result: $data");

    // Render index view
    return $response->getBody()->write($twig->render('home.twig', $args));
});
