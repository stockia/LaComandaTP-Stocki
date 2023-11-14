<?php

require_once __DIR__ . '/../db/AccesoDatos.php';

class Producto {
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $tipo;
    public $statusProducto;

    public function InsertarProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $statusProducto = 'activo';
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "INSERT INTO productos (nombre, descripcion, precio, tipo, statusProducto)
            VALUES (:nombre, :descripcion, :precio, :tipo, :statusProducto)"
        );
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':statusProducto', $statusProducto, PDO::PARAM_STR);
        $consulta->execute();
        
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosProductos() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'producto');
    }

    public static function TraerUnProducto($id) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM productos WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $productoBuscado = $consulta->fetchObject('producto');

        return $productoBuscado;
    }

    public static function TraerUnProductoPorNombre($nombre) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM productos WHERE nombre=:nombre');
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $productoBuscado = $consulta->fetchObject('producto');

        return $productoBuscado;
    }

    public static function TraerProductosPorTipo($tipoProducto) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM productos WHERE tipo=:tipo');
        $consulta->bindValue(':tipo', $tipoProducto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'producto');
    }

    public function ModificarProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE productos
            SET nombre=:nombre, descripcion=:descripcion, precio=:precio, tipo=:tipo
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public function BorrarProducto() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE productos
            SET statusProducto = 'borrado'
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getTipo() {
        return $this->tipo;
    }
}
    