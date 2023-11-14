<?php

require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable {

    public function TraerTodos($request, $response, $args) {
        $pedidos = Pedido::TraerTodosLosPedidos();
        $response->getBody()->write(json_encode($pedidos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        $id = $args['id'];
        $pedido = Pedido::TraerUnPedido($id);
        
        if (!$pedido) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->write(json_encode(['error' => 'Pedido no encontrado']));
        }

        $response->getBody()->write(json_encode($pedido));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args) {
        $datos = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $directorio = __DIR__ . '/../ImagenesPedidos';
        
        if (isset($uploadedFiles['foto'])) {
            $uploadedFile = $uploadedFiles['foto'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($directorio, $uploadedFile, $datos['idMesa'], $datos['nombreCliente']);
                
                $pedido = new Pedido();
                $pedido->idMesa = $datos['idMesa'];
                $pedido->estado = $datos['estado'];
                $pedido->nombreCliente = $datos['nombreCliente'];
                $pedido->tiempoEstimado = $datos['tiempoEstimado'];
                $pedido->foto = $filename;
                
                $resultado = $pedido->InsertarPedido();
                
                $response->getBody()->write(json_encode($resultado));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }

        $response->getBody()->write(json_encode(['error' => 'Error al cargar la foto']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    private function moveUploadedFile($directory, $uploadedFile, $idMesa, $nombreCliente) {
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->generateFilename($uploadedFile->getClientFilename(), $idMesa, $nombreCliente);
            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

            return $filename;
        }

        throw new Exception('Failed to move uploaded file');
    }

    private function generateFilename($originalFilename, $idMesa, $nombreCliente) {
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $newBasename = $idMesa . '_' . $nombreCliente;

        return sprintf('%s.%s', $newBasename, $extension);
    }

    public function ModificarUno($request, $response, $args) {
        $parsedBody = $request->getParsedBody(); 
    
        $pedido = new Pedido();
        $pedido->id = $args['id']; 
        $pedido->idMesa = $parsedBody['idMesa'];
        $pedido->estado = $parsedBody['estado'];
        $pedido->nombreCliente = $parsedBody['nombreCliente'];
        $pedido->tiempoEstimado = $parsedBody['tiempoEstimado'];
    
        $resultado = $pedido->ModificarPedido();
    
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function BorrarUno($request, $response, $args) {
        $pedido = new pedido();
        $pedido->id = $args['id'];

        $resultado = $pedido->BorrarPedido();

        $response->getBody()->write(json_encode(['resultado' => $resultado]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function buscarPedidosPorEstado($estado) {
        $pedido = pedido::TraerPedidosPorEstado($estado);
        if ($pedido === false) {
            return ['error' => 'No existe el pedido'];
        } 

        return $pedido;
    }
}
?>
