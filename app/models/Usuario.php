<?php

require_once __DIR__ . '/../db/AccesoDatos.php';

class Usuario {
    public $id;
    public $nombre;
    public $tipoUsuario;
    public $statusUsuario;

    public function InsertarUsuario() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $statusNuevoUser = 'activo';
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO usuarios (nombre, tipoUsuario, statusUsuario) VALUES (:nombre, :tipoUsuario, :statusUsuario)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipoUsuario', $this->tipoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':statusUsuario', $statusNuevoUser, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosUsuarios() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "usuario");
    }

    public static function TraerUnUsuario($id) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public static function TraerUnUsuarioPorNombre($nombre) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios WHERE nombre=:nombre');
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public static function TraerUsuariosPorTipo($tipoUsuario) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta('SELECT * FROM usuarios WHERE tipoUsuario=:tipoUsuario');
        $consulta->bindValue(':tipoUsuario', $tipoUsuario, PDO::PARAM_STR);
        $consulta->execute(array(':tipoUsuario' => $tipoUsuario));
        $usuarioBuscado = $consulta->fetchObject('usuario');

        return $usuarioBuscado;
    }

    public function ModificarUsuario() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE usuarios 
            SET nombre=:nombre, tipoUsuario=:tipoUsuario 
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipoUsuario', $this->tipoUsuario, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public function BorrarUsuario() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE usuarios
            SET statusUsuario='borrado'
            WHERE id=:id"
        );
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getTipoUsuario() {
        return $this->tipoUsuario;
    }
}
?>
