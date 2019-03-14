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
    $pdo = new PDO($connstring);

    return new class($pdo) {
        private $pdo;
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function query(string $statement, array $inputParameters = []){
            $query = $this->pdo->prepare($statement);

            if($query->execute($inputParameters)) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }
    };
};

// view renderer
$container['render'] = function ($c) {
    $settings = $c->get('settings')['twig'];
    $loader = new \Twig\Loader\FilesystemLoader($settings['templateDir']);
    $twig = new \Twig\Environment($loader, $settings['envSettings']);
    $twig->addGlobal('router', $c->get('router'));
    $twig->addGlobal('session', $_SESSION);
    $twig->addGlobal('navItems', $settings['navbar']);

    $categories = $c->db->query("SELECT * FROM categories");
    $authors = $c->db->query("SELECT * FROM users WHERE permissions >= 1");
    $twig->addGlobal('categories', $categories);
    $twig->addGlobal('authors', $authors);


    $function = new \Twig\TwigFunction('dump', 'print_r');
    $twig->addFunction($function);


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
