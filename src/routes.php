<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

//supply variables to the home page
$app->get('/~{domain}[/]', function (Request $request, Response $response, array $args) {
    // Select last 5 articles with their author
    $articles = $this->db->query('
        SELECT articles.id, title, timestamp as date, text, username as author
        FROM articles
        INNER JOIN users
            ON author_id = users.id
        ORDER BY date DESC LIMIT 5');
    if(count($articles) > 0) {
        // Add categories to the articles
        $selectedArticleIds = array();
        for ($i=0; $i < 5 ; $i++) {
            $selectedArticleIds[$i] = $articles[$i%count($articles)]['id'];
        }
        $catArticles = $this->db->query('
            SELECT article_id, name
            FROM cat_art
                INNER JOIN articles
                    ON cat_art.article_id = articles.id
                INNER JOIN categories
                    ON cat_art.category_id = categories.id
            WHERE article_id IN (?, ?, ?, ?, ?)
        ', $selectedArticleIds);

        foreach ($articles as &$article) {
            $article['categories'] = array();
        }
        foreach ($catArticles as $catArticle) {
            foreach ($articles as &$article) {
                if($catArticle['article_id'] == $article['id']){
                    array_push($article['categories'], $catArticle['name']);
                }
            }
        }
    }

    $nbArticles = $this->db->query('SELECT COUNT(*) FROM articles')[0]['count'];
    $categories = $this->db->query('SELECT * FROM categories');
    $authors = $this->db->query('SELECT username FROM users WHERE permissions >= 1');

    $args['articles'] = $articles;
    $args['nbArticles'] = $nbArticles;
    $args['categories'] = array_map(function($v){return $v['name'];}, $categories);
    $args['authors'] = $authors;
    $args['route'] = 'home';
    return ($this->render)($response, 'home.twig', $args);
})->setName('home');

//Supply variables to the article page
$app->get('/~{domain}/article/{id}', function (Request $request, Response $response, array $args) {
    // Select article from id
    $article = $this->db->query('
        SELECT title, timestamp as date, text, username as author
        FROM articles
        INNER JOIN users
            ON articles.author_id = users.id
        WHERE articles.id = ?
    ', [$args['id']])[0];
    if (count($article) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }

    // Add categories to the article
    $article['categories'] = array();
    $catArticles = $this->db->query('
        SELECT name
        FROM cat_art
            INNER JOIN articles
                ON cat_art.article_id = articles.id
            INNER JOIN categories
                ON cat_art.category_id = categories.id
        WHERE article_id = ?;
    ', array($args['id']));
    foreach ($catArticles as $catArticle) {
        array_push($article['categories'], $catArticle['name']);
    }

    // Add comments to the article
    $article['comments'] = $this->db->query('
        SELECT comments.text, comments.timestamp as date, username as author
        FROM comments
            INNER JOIN articles
                ON comments.article_id = articles.id
            INNER JOIN users
                ON comments.author_id = users.id
        WHERE article_id = ?;
    ', array($args['id']));

    $args['article'] = $article;
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

// article creation page
$app->get('/~{domain}/post', function (Request $request, Response $response, array $args) {
    $categories = $this->db->query('SELECT nom_cat FROM categories');
    $args['categories'] = array_map(function($v){return $v['name'];}, $categories);
    $args['route'] = 'post';
    return ($this->render)($response, 'post.twig', $args);
})->setName('post');

//Supply variables to the edit page (appears when clicking on an element from the dashboard)
$app->get('/~{domain}/edit/{id}', function (Request $request, Response $response, array $args) {
    $article = $this->db->query('
        SELECT title, timestamp as date, text, username as author
        FROM articles
        INNER JOIN users
            ON articles.author_id = users.id
        WHERE articles.id = ?
    ', [$args['id']])[0];
    if (count($article) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }

    // Add categories to the article
    $article['categories'] = array();
    $catArticles = $this->db->query('
        SELECT name
        FROM cat_art
            INNER JOIN articles
                ON cat_art.article_id = articles.id
            INNER JOIN categories
                ON cat_art.category_id = categories.id
        WHERE article_id = ?;
    ', array($args['id']));
    foreach ($catArticles as $catArticle) {
        array_push($article['categories'], $catArticle['name']);
    }

    // Add comments to the article
    $article['comments'] = $this->db->query('
        SELECT comments.text, comments.timestamp as date, username as author
        FROM comments
            INNER JOIN articles
                ON comments.article_id = articles.id
            INNER JOIN users
                ON comments.author_id = users.id
        WHERE article_id = ?;
    ', array($args['id']));

    $args['article'] = $article;
    return ($this->render)($response, 'edit.twig', $args);
})->setName('edit');


//Supply variables for dashboard
$app->get('/~{domain}/dashboard', function (Request $request, Response $response, array $args) {
    $articles = $this->db->query('
        SELECT title, articles.id, username as author
        FROM articles
            INNER JOIN users
                ON articles.author_id = users.id;
    ');

    $catArticles = $this->db->query('
        SELECT name, article_id
        FROM cat_art
            INNER JOIN articles
                ON cat_art.article_id = articles.id
            INNER JOIN categories
                ON cat_art.category_id = categories.id;
        ');

    foreach ($articles as &$article) {
        $article['categories'] = array();
    }
    foreach ($catArticles as $catArticle) {
        foreach ($articles as &$article) {
            if($catArticle['article_id'] == $article['id']){
                array_push($article['categories'], $catArticle['name']);
            }
        }
    }
    $categories = $this->db->query('SELECT * FROM categories');

    $args['categories'] = array_map(function($v){return $v['name'];}, $categories);
    $args['articles'] = $articles;
    $args['route'] = 'dashboard';
    return ($this->render)($response, 'dashboard.twig', $args);
})->setName('dashboard');

// Post Routes
$app->post('/~{domain}/signup', function (Request $request, Response $response, array $args) {
    $params = $request->getParsedBody();
    $username = $params['username'];
    $email =  $params['email'];
    $password_hash = password_hash($params['password'], PASSWORD_DEFAULT);

    $registered = $this->db->query('
        INSERT INTO users (username, email, password_hash)
        VALUES (:username, :email, :password_hash)', [
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $password_hash,
    ]);

    $args['route'] = 'signup';
    $args['alerts'] = array([
        'type' => $registered ? 'success' : 'danger',
        'message' => $registered ?
            'You have registered successfully!' :
            'This email address is already in use.'
    ]);
    return ($this->render)($response, 'signup.twig', $args);
})->setName('signup');
