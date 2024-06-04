<?php

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';

 //Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();



$app->group('/usuario', function(RouteCollectorProxy $group){
    $group->post('[/]', \UsuarioController::class . ':Cargar');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');

});

$app->group('/mesa', function(RouteCollectorProxy $group){
    $group->post('[/]', \MesaController::class . ':Cargar');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    
});

$app->group('/producto', function(RouteCollectorProxy $group){
    $group->post('[/]', \ProductoController::class . ':Cargar');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    
});

$app->group('/pedido', function(RouteCollectorProxy $group){
    $group->post('[/]', \PedidoController::class . ':Cargar');
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    
});

$app->run();


?>