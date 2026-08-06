<?php

require_once __DIR__ . '/../env.php';

class UsuarioModel {

    // Método de conexión (retorna null para no usar MySQL)
    public static function getConexion() {
        return null;
    }

    // Simula la obtención del usuario por email sin tocar la base de datos
    public static function obtenerPorEmail($email) {
        // Devolvemos un array con datos simulados del usuario
        return [
            'id' => 1,
            'nombre' => 'Usuario Demo',
            'email' => $email,
            // Clave genérica o hash para que no falle el login
            'password' => password_hash('123456', PASSWORD_DEFAULT)
        ];
    }

    // Si tu código llama a registrar o guardar, simplemente devuelve true
    public static function guardar($datos) {
        return true;
    }

    // Si tu código busca por ID
    public static function obtenerPorId($id) {
        return [
            'id' => $id,
            'nombre' => 'Usuario Demo',
            'email' => 'demo@correo.com'
        ];
    }
}
?>
