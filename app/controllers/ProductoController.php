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
        

        $
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

    public function CargarDesdeCSV($request, $response, $args) {
        $uploadedFiles = $request->getUploadedFiles();
        $csvFile = $uploadedFiles['archivoCSV'] ?? null;
        
        if ($csvFile && $csvFile->getError() === UPLOAD_ERR_OK) {
            $rutaTemporal = __DIR__ . '/../archivosTemporales/' . $csvFile->getClientFilename();
            $csvFile->moveTo($rutaTemporal);
            $archivo = new SplFileObject($rutaTemporal);
            $archivo->setFlags(SplFileObject::READ_CSV);
            foreach ($archivo as $fila) {
                $producto = new Producto();
                $producto->nombre = $fila[0];
                $producto->descripcion = $fila[1];
                $producto->precio = $fila[2];
                $producto->tipo = $fila[3];

                $producto->InsertarProducto();
            }

            $responseBody = $response->getBody();
            $responseBody->write(json_encode(["mensaje" => "Productos cargados correctamente."]));

            return $response->withHeader('Content-Type', 'application/json');
        }
    
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(["error" => "Error al subir el archivo."]));
    }
    
    public function DescargarComoCSV($request, $response, $args) {
        $productos = Producto::TraerTodosLosProductos();
    
        $directory = __DIR__ . '/../archivosTemporales/';
        $filename = 'productos.csv';
        $filepath = $directory . $filename;
    
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); 
        }
    
        $handle = fopen($filepath, 'w');
    
        foreach ($productos as $producto) {
            fputcsv($handle, get_object_vars($producto));
        }
        fclose($handle);
    
        $csvContent = file_get_contents($filepath);
    
        $responseBody = $response->getBody();
        $responseBody->write($csvContent);
    
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }   
}
?>
