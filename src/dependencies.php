<?php
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

        public function query(string $statement, array $inputParameters){
            $query = $this->pdo->prepare($statement);

            if($query->execute($inputParameters)) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }
    };
};

// view renderer
$container['renderer'] = function ($c) {
    $loader = new \Twig\Loader\FilesystemLoader('../templates');
    $twig = new \Twig\Environment($loader);
    $twig->addGlobal('router', $c->get('router'));

    return function($response, $template, $args) use ($twig){
        return $response->getBody()->write($twig->render($template, $args));
    };
};
