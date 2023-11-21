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

// Routes
$app->post('/login', \LoginController::class . ':Login');

$app->group('/usuarios', function (RouteCollectorProxy $group) use ($authMiddlewareSocio) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos')
    ->add($authMiddlewareSocio);
  $group->get('/{id}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add($authMiddlewareSocio);
  $group->put('/{id}', \UsuarioController::class . ':ModificarUno')
    ->add($authMiddlewareSocio);
  $group->delete('/{id}', \UsuarioController::class . ':BorrarUno')
    ->add($authMiddlewareSocio);
});

$app->group('/pedidos', function (RouteCollectorProxy $group) use ($authMiddlewareSocio, $authMiddlewareMozo) {
  $group->get('/downloadCSV', \PedidoController::class . ':DescargarComoCSV');
  $group->post('/uploadCSV', \PedidoController::class . ':CargarDesdeCSV');
  $group->post('/pedidos/{idPedido}/agregar-producto', \PedidoProductoController::class . ':CargarUno')
    ->add($authMiddlewareMozo);
  $group->get('[/]', \PedidoController::class . ':TraerTodos')
    ->add($authMiddlewareSocio);
  $group->get('/{codigoUnico}', \PedidoController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno')
    ->add($authMiddlewareMozo);
  $group->put('/{id}', PedidoController::class . ':ModificarUno')
    ->add($authMiddlewareMozo);
  $group->delete('/{id}', PedidoController::class . ':BorrarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
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