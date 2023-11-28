<?php

require_once __DIR__ . '/../models/PedidoProducto.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class PedidoProductoController extends Pedido implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $pedidosProductos = PedidoProducto::TraerTodosLosPedidoProducto();
        $response->getBody()->write(json_encode($pedidosProductos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $idPedido = $args['idPedido'];
        $pedido = Pedido::TraerUnPedidoProducto($idPedido);
        
        if (!$pedido) {
            $payload = json_encode(['error' => 'Pedido no encontrado']);
            $response->getBody()->write($payload);
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($pedido));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();
        
        $pedido = new PedidoProducto();
        $pedido->idPedido = $args['idPedido'];
        $pedido->idProducto = $datos['idProducto'];
        // $pedido->tipoProducto = $datos['tipoProducto'];
        // $pedido->estado = $datos['estado'];
        
        $resultado = $pedido->InsertarPedidoProducto();

        if ($resultado === true) {
            $response->getBody()->write(json_encode(['respuesta' => 'Producto cargado']));
        }
        else {
            $response->getBody()->write(json_encode(['error' => 'Error al cargar el producto']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {
        $parsedBody = $request->getParsedBody(); 
    
        $pedido = new PedidoProducto();
        $pedido->id = $args['idPedido']; 
        $pedido->idMozo = $parsedBody['idProducto'];
        $pedido->estado = $parsedBody['estado'];
    
        $resultado = $pedido->ModificarPedidoProducto();
    
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $pedido = new PedidoProducto();
        $pedido->id = $args['id'];

        $resultado = $pedido->BorrarPedidoProducto();

        $response->getBody()->write(json_encode(['resultado' => $resultado]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // public function buscarPedidosPorEstado($estado) {
    //     $pedido = PedidoProducto::TraerUnPedidoProductoPorEstado($estado);
    //     if ($pedido === false) {
    //         return ['error' => 'No existen pedidos con ese estado'];
    //     } 

    //     return $pedido;
    // }
    
    public function TraerComidaPendiente($request, $response, $args) {
        $pedidos = PedidoProducto::TraerComidaPendiente('comida','pendiente');
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerBebidaPendiente($request, $response, $args) {
        $pedidos = PedidoProducto::TraerComidaPendiente('bebida','pendiente');
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerCervezaPendiente($request, $response, $args) {
        $pedidos = PedidoProducto::TraerComidaPendiente('cerveza','pendiente');
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarComida($request, $response, $args) {
        $parsedBody = $request->getParsedBody(); 
    
        $pedido = new PedidoProducto();
        $pedido->idPedido = $args['idPedido']; 
        $pedido->idProducto = $parsedBody['idProducto'];
        $pedido->estado = $parsedBody['estado'];
    
        $resultado = $pedido->ModificarPedidoProducto();
    
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarBebida($request, $response, $args) {
        $parsedBody = $request->getParsedBody(); 
    
        $pedido = new PedidoProducto();
        $pedido->idPedido = $args['idPedido']; 
        $pedido->idProducto = $parsedBody['idProducto'];
        $pedido->estado = $parsedBody['estado'];
    
        $resultado = $pedido->ModificarPedidoProducto();
    
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarCerveza($request, $response, $args) {
        $parsedBody = $request->getParsedBody(); 
    
        $pedido = new PedidoProducto();
        $pedido->idPedido = $args['idPedido']; 
        $pedido->idProducto = $parsedBody['idProducto'];
        $pedido->estado = $parsedBody['estado'];
    
        $resultado = $pedido->ModificarPedidoProducto();
    
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarPedido($request, $response, $args) {
        $idPedido = $args['idPedido'];
        $pedido = PedidoProducto::ValidarProductosPedido($idPedido);
        
        if ($pedido === false) {
            $payload = json_encode(['error' => 'Pedido aun en preparacion']);
            $response->getBody()->write($payload);
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } else if ($pedido === null) {
            $payload = json_encode(['error' => 'Pedido no encontrado']);
            $response->getBody()->write($payload);
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['status' => 'Pedido listo para servir']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
