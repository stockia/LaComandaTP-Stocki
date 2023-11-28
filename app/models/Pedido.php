<?php
class Pedido {
    public $id;
    public $idMozo;
    public $idMesa;
    public $estado;
    public $nombreCliente;
    public $codigoUnico;
    // public $tiempoEstimado;
    public $foto;
    public $statusPedido;

    private function generarCodigoAlfanumerico($id) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $idLength = strlen((string)$id);
        $randomString = (string)$id;
      
        for ($i = 0; $i < 5 - $idLength; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
      
        return $randomString;
    }

    public function InsertarPedido() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $codigoUnico = $this->generarCodigoAlfanumerico($this->id);
        $statusPedido = 'activo';
        $consulta = $objetoAccesoDato->RetornarConsulta(
            // "INSERT INTO pedidos (idMozo, idMesa, estado, nombreCliente, codigoUnico, tiempoEstimado, foto, statusPedido)
            // VALUES (:idMozo, :idMesa, :estado, :nombreCliente, :codigoUnico, :tiempoEstimado, :foto, :statusPedido)");
            "INSERT INTO pedidos (idMozo, idMesa, estado, nombreCliente, codigoUnico, foto, statusPedido)
            VALUES (:idMozo, :idMesa, :estado, :nombreCliente, :codigoUnico, :foto, :statusPedido)");
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigoUnico', $codigoUnico, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':statusPedido', $statusPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function ModificarPedido() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE pedidos 
            SET estado = :estado
            WHERE id = :id
            AND idMozo = :idMozo"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        // $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_STR);
        
        if ($consulta->execute()) {
            return $consulta->rowCount();
        } else {
            return 'No modificado';
        }
    }

    public static function TraerTodosLosPedidos() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");
    }

    public static function TraerUnPedido($codigoUnico) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            // "SELECT tiempoEstimado FROM pedidos 
            // WHERE codigoUnico = :codigoUnico"
            "SELECT sum(pedido_producto.tiempoEstimado)
            FROM pedidos
                INNER JOIN pedido_producto
                ON pedidos.id = pedido_producto.idPedido
                AND pedido_producto.estado = 'pendiente'
            WHERE codigoUnico = :codigoUnico"
        );
        $consulta->bindValue(':codigoUnico', $codigoUnico, PDO::PARAM_STR);
        $consulta->execute();
        $pedidoBuscado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $pedidoBuscado['sum(pedido_producto.tiempoEstimado)'];; 
    }

    public static function TraerPedidosPorEstado($estado) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "SELECT * FROM pedidos 
            WHERE estado = :estado"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'pedido');
    }

    public function BorrarPedido() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE pedidos
            SET statusPedido = 'borrado'
            WHERE id = :id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public function ServirPedido() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $estado = 'servido';
        $idPedido = $this->id;
    
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE pedidos 
            SET estado = :estado
            WHERE id = :id
            AND idMozo = :idMozo"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        
        if ($consulta->execute()) {
            $consultaUpdateMesa = $objetoAccesoDato->RetornarConsulta(
                "UPDATE mesas 
                SET estado = 'con clientes comiendo' 
                WHERE idPedido = :idPedido"
            );
            $consultaUpdateMesa->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
            $consultaUpdateMesa->execute();
        } else {
            return null;
        }

        return $consulta->rowCount();
    }
}
