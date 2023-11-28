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
        $codigoUnico = $args['codigoUnico'];
        $pedido = Pedido::TraerUnPedido($codigoUnico);
        
        if (!$pedido) {
            $payload = json_encode(['error' => 'Pedido no encontrado']);
            $response->getBody()->write($payload);
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['tiempo estimado' => $pedido . ' minutos']));
        // $response->getBody()->write(json_encode($pedido));
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
                $pedido->idMozo = $datos['idMozo'];
                $pedido->idMesa = $datos['idMesa'];
                $pedido->estado = $datos['estado'];
                $pedido->nombreCliente = $datos['nombreCliente'];
                // $pedido->tiempoEstimado = $datos['tiempoEstimado'];
                $pedido->foto = $filename;
                
                $resultado = $pedido->InsertarPedido();
                $responseBody = $response->getBody();
                // $responseBody->write(json_encode(["idMozo" => $datos['idMozo'], "idMesa" => $datos['idMesa'], "estado" => $datos['estado'], "nombreCliente" => $datos['nombreCliente'], "tiempoEstimado" => $datos['tiempoEstimado']]));
                $responseBody->write(json_encode(["idMozo" => $datos['idMozo'], "idMesa" => $datos['idMesa'], "estado" => $datos['estado'], "nombreCliente" => $datos['nombreCliente']]));
                // $response->getBody()->write(json_encode($resultado));
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
        $pedido->idMozo = $parsedBody['idMozo'];
        $pedido->estado = $parsedBody['estado'];
        // $pedido->tiempoEstimado = $parsedBody['tiempoEstimado'];
    
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

    public function CargarDesdeCSV($request, $response, $args) {
        $uploadedFiles = $request->getUploadedFiles();
        $csvFile = $uploadedFiles['archivoCSV'] ?? null;
        
        if ($csvFile && $csvFile->getError() === UPLOAD_ERR_OK) {
            $rutaTemporal = __DIR__ . '/../archivosTemporales/' . $csvFile->getClientFilename();
            $csvFile->moveTo($rutaTemporal);
            $archivo = new SplFileObject($rutaTemporal);
            $archivo->setFlags(SplFileObject::READ_CSV);
            foreach ($archivo as $fila) {
                $pedido = new Pedido();
                $pedido->idMozo = $fila[0];
                $pedido->idMesa = $fila[1];
                $pedido->estado = $fila[2];
                $pedido->nombreCliente = $fila[3];
                // $pedido->tiempoEstimado = $fila[4];
                $pedido->foto = $fila[4];

                $pedido->InsertarPedido();
            }

            $responseBody = $response->getBody();
            $responseBody->write(json_encode(["mensaje" => "Pedidos cargados correctamente."]));

            return $response->withHeader('Content-Type', 'application/json');
        }
    
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(["error" => "Error al subir el archivo."]));
    }
    
    public function DescargarComoCSV($request, $response, $args) {
        $pedidos = Pedido::TraerTodosLosPedidos();
    
        $directory = __DIR__ . '/../archivosTemporales/';
        $filename = 'pedidos.csv';
        $filepath = $directory . $filename;
    
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); 
        }
    
        $handle = fopen($filepath, 'w');
    
        foreach ($pedidos as $pedido) {
            fputcsv($handle, get_object_vars($pedido));
        }
        fclose($handle);
    
        $csvContent = file_get_contents($filepath);
    
        $responseBody = $response->getBody();
        $responseBody->write($csvContent);
    
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }  
    
    public function ServirUno($request, $response, $args) {
        $id = $args['id'];
        $datos = $request->getParsedBody();

        $pedido = new Pedido();
        $pedido->id = $id;
        $pedido->idMozo = $datos['idMozo'];
        // $pedido->estado = $datos['estado'];
        // $pedido->tiempoEstimado = $datos['tiempoEstimado'];

        $resultado = $pedido->ServirPedido();

        $response->getBody()->write(json_encode(["pedido estado" => 'servido', "mesa estado" => 'con clientes comiendo']));
        // $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
