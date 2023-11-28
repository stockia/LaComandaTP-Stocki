<?php
class Mesa {
    public $id;
    public $idMozo;
    public $idPedido;
    public $estado;
    public $statusMesa;

    public function InsertarMesa() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $statusMesa = 'activo';
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "INSERT INTO mesas (idMozo, idPedido, estado, statusMesa)
            VALUES (:idMozo, :idPedido, :estado, :statusMesa)"
        );
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':statusMesa', $statusMesa, PDO::PARAM_STR);
        $consulta->execute();
        
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodasLasMesas() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesa');
    }

    public static function TraerUnaMesa($id) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $mesaBuscada = $consulta->fetchObject('mesa');

        return $mesaBuscada;
    }

    public static function TraerUnaMesaPorPedido($idPedido) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM mesas WHERE idPedido=:idPedido');
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $mesaBuscada = $consulta->fetchObject('mesa');

        return $mesaBuscada;
    }

    public static function buscarMesasPorMozo($idMozo) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM mesas WHERE idMozo=:idMozo');
        $consulta->bindValue(':idMozo', $idMozo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesa');
    }

    public static function buscarMesasPorMozoEstado($idMozo, $estado) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "SELECT * FROM mesas 
            WHERE idMozo=:idMozo AND estado=:estado"
        );
        $consulta->bindValue(':idMozo', $idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesa');
    }

    public static function buscarMesasPorEstado($estado) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "SELECT * FROM mesas 
            WHERE estado=:estado"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'mesa');
    }

    public function ModificarMesa() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE mesas
            SET idMozo=:idMozo, idPedido=:idPedido, estado=:estado
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public function ModificarEstadoMesa() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE mesas
            SET estado=:estado
            WHERE id=:id
            AND idMozo=:idMozo
            AND idPedido=:idPedido"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public function BorrarMesa() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE mesas
            SET statusMesa = 'cerrada', estado = 'con cliente pagando'
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public function getMozo() {
        return $this->idMozo;
    }

    public function getPedido() {
        return $this->idPedido;
    }

    public function getEstado() {
        return $this->estado;
    }
}
    