<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/~{domain}[/]', function (Request $request, Response $response, array $args) {
    return $response->withRedirect($this->router->pathFor('home', ['domain' => $args['domain']]), 301);
});
//supply variables to the home page
$app->get('/~{domain}/blog[/[{page}]]', function (Request $request, Response $response, array $args) {
    if (!isset($args['page'])){
        $args['page'] = 1;
    }
    // Select last 5 articles with their author
    $articles = $this->db->query('
        SELECT articles.id, title, timestamp as date, text, username as author
        FROM articles
        INNER JOIN users
            ON author_id = users.id
        ORDER BY date DESC LIMIT 5 OFFSET ((:page -1) * 5)',
        array(':page' => $args['page'])
    );
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
    $authors = $this->db->query('SELECT username FROM users WHERE permissions >= 1');

    $args['articles'] = $articles;
    $args['nbArticles'] = $nbArticles;
    $args['authors'] = $authors;
    $args['route'] = 'home';
    return ($this->render)($response, 'home.twig', $args);
})->setName('home');

//Supply variables to the article page
$app->get('/~{domain}/article/{id}', function (Request $request, Response $response, array $args) {
    // Select article from id
    $article = $this->db->query('
        SELECT articles.id, title, timestamp as date, text, username as author
        FROM articles
        INNER JOIN users
            ON articles.author_id = users.id
        WHERE articles.id = ?
    ', array($args['id']))[0];
    if (count($article) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }

    $authorIds = $this->db->query('SELECT id FROM users');

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
        WHERE article_id = ? ORDER BY date DESC;
    ', array($args['id']));

    $args['authorId'] = $authorIds;
    $args['article'] = $article;
    $args['route'] = 'article';
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
    if ($_SESSION['permissions'] < 1) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }
    $args['route'] = 'post';
    return ($this->render)($response, 'post.twig', $args);
})->setName('post');

//Supply variables to the edit page (appears when clicking on an element from the dashboard)
$app->get('/~{domain}/edit/{id}', function (Request $request, Response $response, array $args) {
    $article = $this->db->query('
        SELECT articles.id, title, timestamp as date, text, username as author, author_id
        FROM articles
        INNER JOIN users
            ON articles.author_id = users.id
        WHERE articles.id = ?
    ', [$args['id']]);
    if (count($article) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }
    $article = $article[0];

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
        SELECT comments.text, comments.timestamp as date, username as author, comments.id as id
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

//Supply variables for category page
$app->get('/~{domain}/category/{category}', function (Request $request, Response $response, array $args) {
    $category = $this->db->query('
        SELECT name
        FROM categories
        WHERE name = ?',
        array($args['category'])
    )[0];
    if (count($category) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }

    $articles = $this->db->query('
        SELECT articles.id, title, timestamp as date, text, username as author
        FROM cat_art
            INNER JOIN articles
                ON cat_art.article_id = articles.id
            INNER JOIN users
                ON articles.author_id = users.id
            INNER JOIN categories
                ON cat_art.category_id = categories.id
        WHERE categories.name = ?',
        array($category['name'])
    );

    foreach ($articles as &$article) {
        $categories = $this->db->query('
            SELECT name, categories.id
            FROM cat_art
                INNER JOIN categories
                    on category_id = categories.id
            WHERE article_id = ?', [$article['id']]);
        $article['categories'] = array_map(function($v){return $v['name'];}, $categories);
    }

    $args['articles'] = $articles;
    return ($this->render)($response, 'category.twig', $args);
})->setName('category');

//Supply variables for the author's page

$app->get('/~{domain}/author/{author}', function (Request $request, Response $response, array $args) {
    $author = $this->db->query('
        SELECT username, id
        FROM users
        WHERE permissions >= 1
            AND username = :author',
        array($args['author']))[0];


    if (count($author) == 0) {
        return ($this->notFoundHandler)($request, $response);
    }

    $articles = $this->db->query('
        SELECT articles.id, title, timestamp as date, text, username as author
        FROM articles
            INNER JOIN users
            ON articles.author_id = users.id
        WHERE users.id = ?',
        array($author['id'])
    );

    foreach ($articles as &$article) {
        $categories = $this->db->query('
            SELECT name, categories.id
            FROM cat_art
                INNER JOIN categories
                    on category_id = categories.id
            WHERE article_id = ?', [$article['id']]);
        $article['categories'] = array_map(function($v){return $v['name'];}, $categories);
    }

    $args['articles'] = $articles;
    return ($this->render)($response, 'author.twig', $args);
})->setName('author');




//Supply variables for dashboard
$app->get('/~{domain}/dashboard', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $articles = $this->db->query('
        SELECT title, articles.id, timestamp as date, text, username as author
        FROM articles
            INNER JOIN users
                ON articles.author_id = users.id
        ORDER BY date DESC;
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
    $users = $this->db->query('
    SELECT id, username, email, permissions
    FROM users
    ');
    $args['users'] = $users;
    $args['articles'] = $articles;
    $args['route'] = 'dashboard';
    return ($this->render)($response, 'dashboard.twig', $args);
})->setName('dashboard');

$app->get('/~{domain}/logout', function (Request $request, Response $response, array $args) {
    session_destroy();
    session_start();

    return $response->withRedirect($this->router->pathFor('login', ['domain' => $args['domain']]));
})->setName('logout');

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
    $_SESSION['alerts'] = array([
        'type' => $registered ? 'success' : 'danger',
        'message' => $registered ?
            'You have registered successfully!' :
            'This email address is already in use.'
    ]);
    return $response->withRedirect($this->router->pathFor('login', ['domain' => $args['domain']]));
});

$app->post('/~{domain}/login', function (Request $request, Response $response, array $args) {
    $user = $request->getParsedBody();
    $username = $user['username'];
    $args['route'] = 'login';

    $result = $this->db->query("SELECT password_hash, permissions, id from users WHERE username = :username", [
        'username' => $username
    ])[0];
    if(count($result) == 0){
        $args['status'] = 'userNotFound';
        return ($this->render)($response, 'login.twig', $args);
    }
    $password_hash = $result['password_hash'];

    if(password_verify($user['password'], $password_hash)) {
      $_SESSION['username'] = $username;
      $_SESSION['permissions'] = $result['permissions'];
      $_SESSION['userID'] = $result['id'];
      $args['status'] = 'success';
  } else {
      $args['status'] = 'wrongPassword';
  }
    return ($this->render)($response, 'login.twig', $args);
});


$app->post('/~{domain}/post', function(Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 1) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $article = $request->getParsedBody();

    $title = $article['title'];
    $text = $article['text'];
    $authorID = $_SESSION['userID'];
    $categoriesID = array();


    $this->logger->info(print_r($article, true));
    foreach ($article as $key => $value) {
      if ($key == 'title' || $key == 'text'){
        continue;
      }
      array_push($categoriesID, $key);
    }
    $this->logger->info(print_r($categoriesID, true));

    $articleID = $this->db->query('
        INSERT INTO articles (title, text, author_id)
        VALUES (:title, :text, :author_id)
        RETURNING id', [
            ':title' => $title,
            ':text' => $text,
            ':author_id' => $authorID,
    ])[0]['id'];

    foreach($categoriesID as $categoryID) {
        $status = $this->db->query('
            INSERT INTO cat_art (article_id, category_id)
            VALUES (:articleID, :categoryID)',
            array(
                ':articleID' => $articleID,
                ':categoryID' => $categoryID,
            )
        );
    }

    $_SESSION['alerts'] = array([
        'type' => $articleID ? 'success' : 'danger',
        'message' => $articleID ?
            'You have successfully posted your article' :
            'You should fill every field in this form'
    ]);

    return ($this->render)($response, 'post.twig', $args);
});

//

$app->put('/~{domain}/article/{id}', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $article = $request->getParsedBody();
    $this->logger->info(print_r($article, true));
    $title = $article['title'];
    $text = $article['text'];
    $date = $article['date'];
    $author_id = $article['authorId'];
    $categoriesID = array();

    foreach($article as $key => $value) {
        if(in_array($key, ['title', 'text', '_METHOD', 'authorId'])){
            continue;
        }
        array_push($categoriesID, $key);
    }

    $updateArticleTable = $this->db->query('
        UPDATE articles
        SET
            title = :title,
            timestamp = to_timestamp(:date, \'YYYY-MM-DD HH24:MI\'),
            text = :text,
            author_id = :author_id
        WHERE articles.id = :articles_id',
        array(
            ':title' => $title,
            'date' => $date,
            ':text'=> $text,
            ':author_id' => $author_id,
            ':articles_id' => $args['id'],
    ));

    $deleteArtCategories = $this->db->query('
        DELETE FROM cat_art
        WHERE article_id = ?',
        array($args['id'])
    );

    foreach($categoriesID as $IDElement) {
        $updateArtCategories = $this->db->query('
            INSERT INTO cat_art (article_id, category_id)
            VALUES (:articleID, :category_id)',
            array(
                ':articleID' => $args['id'],
                ':category_id' => $IDElement,
            )
        );
    }

    return $response->withRedirect($this->router->pathFor('edit', ['domain' => $args['domain'], 'id' => $args['id']]));
});

$app->post('/~{domain}/article/{id}', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['permissions'])) {
        return $response->withStatus(401);
    }

    $comment = $request->getParsedBody();
    $permission = $_SESSION['permissions'];
    $authorID = $_SESSION['userID'];
    $articleID = $args['id'];
    $text = $comment['text'];

    $insertComment = $this->db->query('
        INSERT INTO comments (article_id, author_id, text)
        VALUES (:article_id, :author_id, :text)',
        array(
            ':article_id' => $articleID,
            ':author_id' => $authorID,
            ':text' => $text,
        )
    );

    return $response->withRedirect($this->router->pathFor('article', ['domain' => $args['domain'], 'id' => $args['id']]));
});

//Create a category from the Dashboard.

$app->post('/~{domain}/dashboard', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }
    $category  = $request->getParsedBody();
    $name = $category['name'];

    $insertCategory = $this->db->query('
    INSERT INTO categories (id,name)
    VALUES (DEFAULT, :name)',
    array(
        ':name' => $name,
      )
    );
    return $response->withRedirect($this->router->pathFor('dashboard', ['domain' => $args['domain']]), 301);
});



//update db when editing article


$app->put('/~{domain}/category/{id}', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $category = $request->getParsedBody();
    $name = $category['name'];


    $updateCategory = $this->db->query('
        UPDATE categories
        SET name = :name
        WHERE id = :id',
        array(
            ':name' => $category['name'],
            ':id' => $args['id'],
        )
    );

    return $response->withRedirect($this->router->pathFor('dashboard', ['domain' => $args['domain']]));
});


$app->delete('/~{domain}/article/{id}', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $deleteArticle = $this->db->query('
        DELETE FROM articles
        WHERE id = :id',
        array($args['id'])
    );

    return $response->withRedirect($this->router->pathFor('dashboard', ['domain' => $args['domain']]));
});

$app->delete('/~{domain}/category/{id}', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $deleteCategory = $this->db->query('
        DELETE FROM categories
        WHERE id = :id',
        array($args['id'])
    );

    return $response->withRedirect($this->router->pathFor('dashboard', ['domain' => $args['domain']]));
})->setName('categoryById');
// update db when editing user.

$app->put('/~{domain}/user/{id}', function (Request $request, Response $response, array $args) {
  if ($_SESSION['permissions'] < 2) {
      $response = $response->withStatus(401);
      $response->getBody()->write('Not Authorized');
      return $response;
  }

  $permissions = $request->getParsedBody()['permissions'];

  $this->logger->info(print_r($permissions, true));

  $updatePermission = $this->db->query('
  UPDATE users
    SET
      permissions = :permissions
    WHERE id = :user_id',
    array(
      ':permissions' => $permissions,
      ':user_id' => $args['id'],
    ));
    return $response->withRedirect($this->router->pathFor('dashboard', ['domain' => $args['domain'],]));
})->setName('user');

//delete user from the db.

$app->delete('/~{domain}/user/{id}', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }

    $deleteUser = $this->db->query('
        DELETE FROM users
        WHERE users.id = ?',
        array($args['id'])
    );
    return $response->withRedirect($this->router->pathFor('dashboard', ['domain' => $args['domain'],]));
});

//delete comments from the db.

$app->delete('/~{domain}/comment/{id}', function (Request $request, Response $response, array $args) {
    if ($_SESSION['permissions'] < 2) {
        $response = $response->withStatus(401);
        $response->getBody()->write('Not Authorized');
        return $response;
    }
    $commentid = $args['id'];

    $id = $deleteComment =$this->db->query('
        DELETE FROM comments
        WHERE comments.id = :comment
        RETURNING article_id',
        array(
        ':comment' => $commentid,
        )
    )[0]['article_id'];
    return $response->withRedirect($this->router->pathFor('edit', ['domain' => $args['domain'], 'id' => $id]));
})->setName('comment');
