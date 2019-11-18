<?php

use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$repo = new App\Repository();

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});

$app->get('/posts', function ($request, $response) use ($repo) {
    $posts = $repo->all();
    $params = ['posts' => $posts];
    return $this->get('renderer')->render($response, 'posts/index.phtml', $params);
});

$app->get('/posts/show/{id}', function ($request, $response, $args) use ($repo) {
    $posts = $repo->all();
    $post = $posts[$args['id']];
    if (!isset($post)) {
        $response->getBody()->write('Page not found');
        return $response->withStatus(404);
    }
    $params = [
        'post' => $post,
    ];
    return $this->get('renderer')->render($response, 'posts/show.phtml', $params);
})->setName('posts');

$app->run();