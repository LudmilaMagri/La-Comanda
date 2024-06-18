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
use Slim\Cookie\Cookie;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
 require_once './middlewares/AutentificadorJWT.php';
 require_once './middlewares/ValidarSocio.php';
 require_once './middlewares/ValidarToken.php';
 require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';
require_once './middlewares/AuthMiddleware.php';

 //Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
//$app->RoutingMiddleware();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();


$app->group('/login', function (RouteCollectorProxy $group){
    $group->post('[/]', \UsuarioController::class . ':LogIn')->add(\Logger::class . ':ValidarLogin');
});


$app->group('/usuario', function(RouteCollectorProxy $group){
    $group->post('[/]', \UsuarioController::class . ':Cargar');
    $group->put('/{id}', \UsuarioController::class . ':Modificar');
    $group->post('/{id}', \UsuarioController::class . ':Borrar');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');

})->add(ValidarToken::class. ':ValidarSocio');


$app->group('/usuarioCSV', function (RouteCollectorProxy $group) {
    $group->post('/importar', \UsuarioController::class . ':Importar');
    $group->get('/exportar', \UsuarioController::class . ':Exportar');
  });

$app->group('/mesa', function(RouteCollectorProxy $group){
    $group->post('[/]', \MesaController::class . ':Cargar');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    
});

$app->group('/producto', function(RouteCollectorProxy $group){
    $group->post('[/]', \ProductoController::class . ':Cargar');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    
})->add(ValidarToken::class. ':ValidarSocio');

$app->group('/pedido', function(RouteCollectorProxy $group){
    $group->post('/cargar', \PedidoController::class . ':Cargar')->add(ValidarToken::class. ':ValidarMozo');
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->put('/codigo_pedido', \PedidoController::class . ':modificar');
    $group->get('/pendientes', \PedidoController::class . ':traerPedidosPendientes');

});

$app->run();


?>