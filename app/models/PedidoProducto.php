<?php

require_once __DIR__ . '/../db/AccesoDatos.php';

class PedidoProducto {
    public $id;
    public $idPedido;
    public $idProducto;
    public $tipoProducto;
    public $tiempoEstimado;
    public $estado;

    // public function InsertarPedidoProducto() {
    //     $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    //     $estado = 'pendiente';
    //     $this->tiempoEstimado = rand(5, 15);

    //     $consulta = $objetoAccesoDato->RetornarConsulta(
    //         "INSERT INTO pedido_producto (idPedido, idProducto, tipoProducto, tiempoEstimado, estado)
    //         VALUES (:idPedido, :idProducto, :tipoProducto, :tiempoEstimado, :estado)"
    //     );
    //     $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
    //     $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
    //     $consulta->bindValue(':tipoProducto', $this->tipoProducto, PDO::PARAM_STR);
    //     $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
    //     $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
    //     $consulta->execute();
        
    //     return $objetoAccesoDato->RetornarUltimoIdInsertado();
    // }

    public function InsertarPedidoProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $tiempoEstimado = rand(5, 15);
        $tipoProducto = null;
        $estado = 'pendiente';
    
        $consultaTipoProducto = $objetoAccesoDato->RetornarConsulta(
            "SELECT tipo FROM productos WHERE id = :idProducto"
        );
        $consultaTipoProducto->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consultaTipoProducto->execute();
        $resultado = $consultaTipoProducto->fetch(PDO::FETCH_ASSOC);
    
        if ($resultado) {
            $tipoProducto = $resultado['tipo'];
        } else {
            return false;
        }
    
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "INSERT INTO pedido_producto (idPedido, idProducto, tipoProducto, tiempoEstimado, estado)
            VALUES (:idPedido, :idProducto, :tipoProducto, :tiempoEstimado, :estado)"
        );
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':tipoProducto', $tipoProducto, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        
        return true;
    }
    

    public static function TraerTodosLosPedidoProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedido_producto");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function TraerUnPedidoProducto($idPedido) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedido_producto WHERE idPedido=:idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $productoBuscado = $consulta->fetchObject('PedidoProducto');

        return $productoBuscado;
    }

    public static function TraerUnPedidoProductoPorEstado($estado) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM pedido_producto WHERE estado=:estado');
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        $productosBuscado = $consulta->fetchObject('PedidoProducto');

        return $productosBuscado;
    }

    public static function TraerPedidoProductoPorTipo($tipoProducto) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM pedido_producto WHERE tipoProducto=:tipoProducto');
        $consulta->bindValue(':tipoProducto', $tipoProducto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public function ModificarPedidoProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE pedido_producto
            SET estado = :estado
            WHERE idPedido = :idPedido
            AND idProducto = :idProducto"
        );
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public function BorrarPedidoProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE pedido_producto
            SET estado = 'borrado'
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function TraerComidaPendiente($tipoProducto, $estado) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "SELECT * 
            FROM pedido_producto
            WHERE tipoProducto=:tipoProducto
            AND estado=:estado"
        );

        $consulta->bindValue(':tipoProducto', $tipoProducto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function ValidarProductosPedido($idPedido) {
        $todosListos = true;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "SELECT estado 
            FROM pedido_producto 
            WHERE idPedido = :idPedido"
        );
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultados) === 0) {
            return null;
        }

        foreach ($resultados as $fila) {
            if ($fila['estado'] !== 'listo') {
                $todosListos = false;
                break;
            }
        }

        if ($todosListos) {
            $consultaUpdate = $objetoAccesoDato->RetornarConsulta(
                "UPDATE pedidos 
                SET estado = 'listo para servir' 
                WHERE id = :idPedido"
            );
            $consultaUpdate->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
            $consultaUpdate->execute();

            // $consultaUpdateMesa = $objetoAccesoDato->RetornarConsulta(
            //     "UPDATE mesas 
            //     SET estado = 'con clientes comiendo' 
            //     WHERE idPedido = :idPedido"
            // );
            // $consultaUpdateMesa->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
            // $consultaUpdateMesa->execute();
        }

        return $todosListos;
    }
}
    