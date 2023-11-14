<?php
class Pedido {
    public $id;
    public $idMesa;
    public $estado;
    public $nombreCliente;
    public $codigoUnico;
    public $tiempoEstimado;
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
            "INSERT INTO pedidos (idMesa, estado, nombreCliente, codigoUnico, tiempoEstimado, foto, statusPedido)
            VALUES (:idMesa, :estado, :nombreCliente, :codigoUnico, :tiempoEstimado, :foto, :statusPedido)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigoUnico', $codigoUnico, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':statusPedido', $statusPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function ModificarPedido() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE pedidos 
            SET idMesa = :idMesa, estado = :estado, nombreCliente = :nombreCliente
            , tiempoEstimado = :tiempoEstimado
            WHERE id = :id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_STR);
        // $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        
        return $consulta->execute();
    }

    public static function TraerTodosLosPedidos() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");
    }

    public static function TraerUnPedido($id) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "SELECT * FROM pedidos 
            WHERE id = :id"
        );
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado = $consulta->fetchObject('pedido');

        return $pedidoBuscado;
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
}
