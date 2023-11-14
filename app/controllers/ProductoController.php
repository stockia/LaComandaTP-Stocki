<?php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $productos = Producto::TraerTodosLosProductos();
        $response->getBody()->write(json_encode($productos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $id = $args['id'];
        $producto = Producto::TraerUnProducto($id);
        
        if (!$producto) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Producto no encontrado']));
        }

        $response->getBody()->write(json_encode($producto));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();

        $producto = new Producto();
        $producto->nombre = $datos['nombre'];
        $producto->descripcion = $datos['descripcion'];
        $producto->precio = $datos['precio'];
        $producto->tipo = $datos['tipo'];
                
        $resultado = $producto->InsertarProducto();
                
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();

        $producto = new Producto();
        $producto->id = $id;
        $producto->nombre = $datos['nombre'];
        $producto->descripcion = $datos['descripcion'];
        $producto->precio = $datos['precio'];
        $producto->tipo = $datos['tipo'];

        $resultado = $producto->ModificarProducto();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $id = $args['id'];

        $producto = new Producto();
        $producto->id = $id;

        $resultado = $producto->BorrarProducto();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
