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
 require_once './middlewares/ValidarToken.php';
 require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/RegistroController.php';
require_once './controllers/FacturaController.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

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
    $group->delete('/{id}', \UsuarioController::class . ':Borrar');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/crearPdf', \UsuarioController::class . ':CrearPdf');
})->add(ValidarToken::class. ':ValidarSocio');


$app->group('/usuarioCSV', function (RouteCollectorProxy $group) {
    $group->post('/importar', \UsuarioController::class . ':Importar');
    $group->get('/exportar', \UsuarioController::class . ':Exportar');
  });


$app->group('/mesa', function(RouteCollectorProxy $group){
    $group->post('[/]', \MesaController::class . ':Cargar');
    $group->put('/{codigo_mesa}', \MesaController::class . ':Modificar');
    $group->delete('/{codigo_mesa}', \MesaController::class . ':Borrar');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{id}', \MesaController::class . ':TraerUno');
    
})->add(ValidarToken::class. ':ValidarSocio');


$app->group('/producto', function(RouteCollectorProxy $group){
    $group->post('[/]', \ProductoController::class . ':Cargar');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{id}', \ProductoController::class . ':TraerUno');
    $group->put('/{id}', \ProductoController::class . ':Modificar');
    $group->delete('/{id}', \ProductoController::class . ':Borrar');
    
})->add(ValidarToken::class. ':ValidarSocio');


$app->group('/pedido', function(RouteCollectorProxy $group){
    $group->post('/cargar', \PedidoController::class . ':Cargar')->add(ValidarToken::class. ':ValidarMozo');
    $group->post('/tomarFoto', \PedidoController::class . ':tomarFoto')->add(ValidarToken::class. ':ValidarMozo');
    $group->get('/pendientes', \PedidoController::class . ':TraerPedidosPendientes');
    $group->get('/listos', \PedidoController::class . ':TraerPedidosListos')->add(ValidarToken::class. ':ValidarMozo');
    $group->get('/todosConTiempo', \PedidoController::class . ':TraerPedidosEnPreparacionYTiempo')->add(ValidarToken::class. ':ValidarSocio');
    $group->get('/estadisticaTreintaDias', \PedidoController::class . ':TraerPedidosTreintaDias'); 
    $group->get('/{id}', \PedidoController::class . ':TraerUno');
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->put('/{codigo_pedido}', \PedidoController::class . ':Modificar');
    $group->get('/consultarTiempo/{codigo_pedido}/{codigo_mesa}', \PedidoController::class . ':ConsultarTiempoPedido');
    $group->put('/listo/{codigo_pedido}', \PedidoController::class . ':ModificarListoEntregado');
    $group->put('/entregado/{codigo_pedido}', \PedidoController::class . ':ModificarListoEntregado')->add(ValidarToken::class. ':ValidarMozo');
    
});


$app->group('/factura', function (RouteCollectorProxy $group) {
    $group->post('/cargar', \FacturaController::class . ':Cargar')->add(ValidarToken::class. ':ValidarMozo');
  });


$app->group('/encuesta', function (RouteCollectorProxy $group) {
    $group->post('[/]', \EncuestaController::class . '::Cargar');
    $group->get('[/]', \RegistroController::class . '::TraerMejoresComentarios');
  });






//********* REGISTROS ********** */

$app->group('/registroEmpleados', function (RouteCollectorProxy $group) {
    $group->get('/logueo/{rol}', \RegistroController::class . '::TraerLogueosPorSector');
    $group->get('/operaciones', \RegistroController::class . '::TraerCantidadOperacionesPorSeparado');
    $group->get('/{rol}', \RegistroController::class . '::TraerCantidadOperacionesRol');
    $group->get('/{rol}/{nombreUsuario}', \RegistroController::class . '::TraerCantidadOperacionesUsuarioRol');
})->add(ValidarToken::class. ':ValidarSocio');

$app->group('/registroPedidos', function (RouteCollectorProxy $group) {
    $group->get('/productoMasVendido', \RegistroController::class . '::TraerProductoMasVendido'); 
    $group->get('/productoMenosVendido', \RegistroController::class . '::TraerProductoMenosVendido'); 
    $group->get('/productoEntregadoTarde', \RegistroController::class . '::TraerProductoEntregadoTarde');  
    $group->get('/todosCancelados', \PedidoController::class . ':TraerPedidosCancelados');

})->add(ValidarToken::class. ':ValidarSocio');

$app->group('/registroMesas', function (RouteCollectorProxy $group) {
    $group->get('/masUsada', \RegistroController::class . '::TraerMesaMasUsada');
    $group->get('/menosUsada', \RegistroController::class . '::TraerMesaMenosUsada');
    $group->get('/mayorFacturacion', \RegistroController::class . '::TraerMesaConMayorFacturacion');
    $group->get('/menorFacturacion', \RegistroController::class . '::TraerMesaConMenorFacturacion');
    $group->get('/mayorImporte', \RegistroController::class . '::TraerMesaConMayorImporte');
    $group->get('/menorImporte', \RegistroController::class . '::TraerMesaConMenorImporte');
    $group->post('/entreDosFechas', \RegistroController::class . '::TraerFacturaEntreDosFechas');
    $group->get('/mejoresComentarios', \RegistroController::class . '::TraerMejoresComentarios');
    $group->get('/peoresComentarios', \RegistroController::class . '::TraerPeoresComentarios');
})->add(ValidarToken::class. ':ValidarSocio');

    
    
$app->run();


?>