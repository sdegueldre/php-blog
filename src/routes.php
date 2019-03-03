<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/~{domain}[/]', function (Request $request, Response $response, array $args) {

    $query; //todo
    $articles = [];
    $nbArticles = 0;
    $categories = [];
    $authors = [];
    $args['articles'] = $articles;
    $args['nbArticles'] = $nbArticles;
    $args['categories'] = $categories;
    $args['authors'] = $authors;
    $args['route'] = 'home';
    return ($this->render)($response, 'home.twig', $args);
})->setName('home');

$app->get('/~{domain}/article/{id}', function (Request $request, Response $response, array $args) {
    $articles = $this->db->query('SELECT * FROM articles WHERE id_article = :id', array('id' => $args['id']));
    if (count($articles) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }

    $args['article'] = $articles[0];
    return ($this->render)($response, 'article.twig', $args);
})->setName('article');

$app->get('/~{domain}/404', function (Request $request, Response $response, array $args) {
    return ($this->render)($response, '404.twig', $args);
})->setName('404');

$app->get('/~{domain}/login', function (Request $request, Response $response, array $args) {
    $args['route'] = 'login';
    return ($this->render)($response, 'login.twig', $args);
})->setName('login');

$app->get('/~{domain}/signup', function (Request $request, Response $response, array $args) {
    $args['route'] = 'signup';
    return ($this->render)($response, 'signup.twig', $args);
})->setName('signup');

//Page de creation d'articles
$app->get('/~{domain}/post', function (Request $request, Response $response, array $args) {
    $args['route'] = 'post';
    return ($this->render)($response, 'post.twig', $args);
})->setName('post');

$app->get('/~{domain}/edit/{id}', function (Request $request, Response $response, array $args) {
    $query = false;//$this->db->query(/*Todo*/);
    if ($article){
        $article = $query->fetch(PDO::FETCH_ASSOC);
        $args['article'] = $article;
        return ($this->render)($response, 'edit.twig', $args);
    } else {
        return $response->withRedirect($this->router->pathFor('404', ['domain' => $args['domain']]));
    }
})->setName('edit');

$app->get('/~{domain}/dashboard', function (Request $request, Response $response, array $args) {
    $args['route'] = 'dashboard';
    return ($this->render)($response, 'dashboard.twig', $args);
})->setName('dashboard');
