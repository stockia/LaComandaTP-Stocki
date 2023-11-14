<?php

require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $mesas = Mesa::TraerTodasLasMesas();
        $response->getBody()->write(json_encode($mesas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $id = $args['id'];
        $mesa = Mesa::TraerUnaMesa($id);
        
        if (!$mesa) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Mesa no encontrada']));
        }

        $response->getBody()->write(json_encode($mesa));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->idMozo = $datos['idMozo'];
        $mesa->idPedido = $datos['idPedido'];
        $mesa->estado = $datos['estado'];
                
        $resultado = $mesa->InsertarMesa();
                
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->id = $id;
        $mesa->idMozo = $datos['idMozo'];
        $mesa->idPedido = $datos['idPedido'];
        $mesa->estado = $datos['estado'];

        $resultado = $mesa->ModificarMesa();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $id = $args['id'];

        $mesa = new Mesa();
        $mesa->id = $id;

        $resultado = $mesa->BorrarMesa();

        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
