<?php

define('ROOT_PATH',realpath(__DIR__ . "/.."));

require_once ROOT_PATH . '/vendor/autoload.php';

use \Elasticsearch\ClientBuilder;
use App\Helpers\Session;

Session::init();

$config = require_once ROOT_PATH."/bootstrap/config.php";

$app = new \Slim\App($config);

$container = $app->getContainer();

$container["db"] = function ($container) {

    $mongo =  new \MongoClient();
    $dbname = $mongo->selectDB('test');
    return $dbname;
};

$container["view"] = function ($container)
{
    $view = new \Slim\Views\Twig(ROOT_PATH."/resources/views",[
        "cache" => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));


    return $view;

};

$container["page"] = function ($container)
{
    return new \App\Controllers\PageController($container);
};

$container["zakaz"] = function ($container)
{
    return new \App\Controllers\ZakazController($container);
};

$container["search"] = function ($container)
{
    return new \App\Controllers\SearchController($container);
};

$container["validator"] = function ($container)
{
    return new \App\Validation\Validator;
};

$container["elastic"] = function ($container)
{
    $elastic = ClientBuilder::create()->build();
    return $elastic;
};

$container["mongo"] = function ($container)
{
    return new \MongoClient();
};

$container["csrf"] = function ($container)
{
    return new \Slim\Csrf\Guard;
};


$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
//$app->add(new \App\Middleware\OldInputMiddleware($container));

$app->add($container->csrf);

require_once ROOT_PATH."/app/routes.php";
