<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/~{domain}', function (Request $request, Response $response) {
    $args['pagename'] = 'Home';
    $args['users'] = $this->db->query('SELECT user, username, email FROM users')->fetchAll(PDO::FETCH_ASSOC);
    return ($this->renderer)($response, 'home.twig', $args);
})->setName('home');

$app->get('/[{pagename}]', function (Request $request, Response $response, array $args) {
    $route = $args['pagename'];
    $this->logger->info("Slim: Unknown route '/$route'");

    return ($this->renderer)($response, 'home.twig', $args);
});
