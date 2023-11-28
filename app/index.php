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

require_once './controllers/LoginController.php';
require_once './utils/AutentificadorJWT.php';

require_once './middlewares/LoggerMiddleware.php';
require_once './middlewares/AuthMiddleware.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoProductoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

$authMiddlewareSocio = new AuthMiddleware('socio');
$authMiddlewareMozo = new AuthMiddleware('mozo');
$authMiddlewareBartender = new AuthMiddleware('bartender');
$authMiddlewareCocinero = new AuthMiddleware('cocinero');
$authMiddlewareCervecero = new AuthMiddleware('cervecero');

// Routes
$app->post('/login', \LoginController::class . ':Login');

$app->group('/usuarios', function (RouteCollectorProxy $group) use ($authMiddlewareSocio) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos')
    ->add($authMiddlewareSocio);
  $group->get('/{id}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
    // ->add($authMiddlewareSocio);
  $group->put('/{id}', \UsuarioController::class . ':ModificarUno')
    ->add($authMiddlewareSocio);
  $group->delete('/{id}', \UsuarioController::class . ':BorrarUno')
    ->add($authMiddlewareSocio);
});

$app->group('/pedidos', function (RouteCollectorProxy $group) use ($authMiddlewareSocio, $authMiddlewareMozo) {
  // $group->get('/downloadCSV', \PedidoController::class . ':DescargarComoCSV');
  // $group->post('/uploadCSV', \PedidoController::class . ':CargarDesdeCSV');
  $group->post('[/]', \PedidoController::class . ':CargarUno')
    ->add($authMiddlewareMozo);
  $group->post('/{idPedido}/agregar-producto', \PedidoProductoController::class . ':CargarUno')
    ->add($authMiddlewareMozo);
  $group->get('[/]', \PedidoController::class . ':TraerTodos')
    ->add($authMiddlewareSocio);
  $group->get('/{codigoUnico}', \PedidoController::class . ':TraerUno');
  $group->put('/{id}', PedidoController::class . ':ModificarUno')
    ->add($authMiddlewareMozo);
  $group->put('/servir/{id}', PedidoController::class . ':ServirUno');
    // ->add($authMiddlewareMozo);
  $group->delete('/{id}', PedidoController::class . ':BorrarUno');
});

// $app->group('/pedidos-productos', function (RouteCollectorProxy $group) use ($authMiddlewareSocio, $authMiddlewareMozo, $authMiddlewareBartender, $authMiddlewareCocinero, $authMiddlewareCervecero) {
//   $group->get('[/]', \PedidoProductoController::class . ':TraerTodos')
//     ->add($authMiddlewareMozo);
//   $group->get('/{idPedido}', \PedidoProductoController::class . ':VerificarPedido')
//     ->add($authMiddlewareMozo);
//   $group->get('/comida', \PedidoProductoController::class . ':TraerComidaPendiente')
//     ->add($authMiddlewareCocinero);
//   $group->put('/comida/{idPedido}', \PedidoProductoController::class . ':ModificarComida')
//     ->add($authMiddlewareCocinero);
//   $group->get('/bebida', \PedidoProductoController::class . ':TraerBebidaPendiente')
//     ->add($authMiddlewareBartender);
//   $group->put('/bebida/{idPedido}', \PedidoProductoController::class . ':ModificarBebida')
//     ->add($authMiddlewareBartender);
//   $group->get('/cerveza', \PedidoProductoController::class . ':TraerCervezaPendiente')
//     ->add($authMiddlewareCervecero);
//   $group->put('/cerveza/{idPedido}', \PedidoProductoController::class . ':ModificarCerveza')
//     ->add($authMiddlewareCervecero);
// });
$app->group('/pedidos-productos', function (RouteCollectorProxy $group) use ($authMiddlewareSocio, $authMiddlewareMozo, $authMiddlewareBartender, $authMiddlewareCocinero, $authMiddlewareCervecero) {
  $group->get('/comida', \PedidoProductoController::class . ':TraerComidaPendiente')
      ->add($authMiddlewareCocinero);
  $group->put('/comida/{idPedido}', \PedidoProductoController::class . ':ModificarComida')
      ->add($authMiddlewareCocinero);
  $group->get('/bebida', \PedidoProductoController::class . ':TraerBebidaPendiente')
      ->add($authMiddlewareBartender);
  $group->put('/bebida/{idPedido}', \PedidoProductoController::class . ':ModificarBebida')
      ->add($authMiddlewareBartender);
  $group->get('/cerveza', \PedidoProductoController::class . ':TraerCervezaPendiente')
      ->add($authMiddlewareCervecero);
  $group->put('/cerveza/{idPedido}', \PedidoProductoController::class . ':ModificarCerveza')
      ->add($authMiddlewareCervecero);
  $group->get('[/]', \PedidoProductoController::class . ':TraerTodos')
      ->add($authMiddlewareMozo);
  $group->get('/{idPedido}', \PedidoProductoController::class . ':VerificarPedido')
      ->add($authMiddlewareMozo);
});


$app->group('/productos', function (RouteCollectorProxy $group) use ($authMiddlewareSocio){
  $group->get('/downloadCSV', \ProductoController::class . ':DescargarComoCSV');
  $group->post('/uploadCSV', \ProductoController::class . ':CargarDesdeCSV');
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{id}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->put('/{id}', \ProductoController::class . ':ModificarUno')
    ->add($authMiddlewareSocio);
  $group->delete('/{id}', \ProductoController::class . ':BorrarUno')
    ->add($authMiddlewareSocio);
});

$app->group('/mesas', function (RouteCollectorProxy $group) use ($authMiddlewareSocio, $authMiddlewareMozo) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
    // ->add($authMiddlewareSocio);
  $group->get('/{id}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->put('/{id}', \MesaController::class . ':ModificarUno')
    ->add($authMiddlewareMozo);
  $group->put('/modificar/{id}', \MesaController::class . ':ModificarUnoEstado')
    ->add($authMiddlewareMozo);
  $group->delete('/{id}', \MesaController::class . ':BorrarUno')
    ->add($authMiddlewareSocio);
});

$app->run();